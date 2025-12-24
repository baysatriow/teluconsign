<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Category;
use App\Enums\ProductStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;

class ShopController extends Controller
{
    public function show($id)
    {
        $seller = User::where('role', 'seller')
            ->where(function($query) use ($id) {
                if (is_numeric($id)) {
                    $query->where('user_id', $id);
                } else {
                    $query->where('username', $id);
                }
            })
            ->firstOrFail();
        
        $query = Product::with(['category', 'seller.addresses', 'seller.profile', 'images'])
            ->withAvg('reviews', 'rating')
            ->where('seller_id', $seller->user_id)
            ->where('status', ProductStatus::Active);
        
        if ($searchTerm = request('search')) {
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }
        
        $products = $query->latest()->paginate(12)->withQueryString();

        $totalSales = OrderItem::whereHas('product', function($q) use ($id) {
            $q->where('seller_id', $id);
        })->whereHas('order', function($q){
            $q->where('status', 'completed');
        })->count();

        $ratingStats = \App\Models\Review::whereHas('product', function($q) use ($seller) {
            $q->where('seller_id', $seller->user_id);
        })->selectRaw('avg(rating) as average, count(*) as count')->first();

        $rating = $ratingStats->average ? round($ratingStats->average, 1) : 0;
        $totalReviews = $ratingStats->count; 

        return view('shop.show', compact('seller', 'products', 'totalSales', 'rating'));
    }

    private function ensureShopAddressSet()
    {
        $hasAddress = Auth::user()->addresses()->where('is_shop_default', true)->exists();
        if (!$hasAddress) {
            return redirect()->route('shop.address.index')->with('error', 'Harap atur alamat toko utama terlebih dahulu sebelum mengakses fitur toko.');
        }
        return null;
    }

    public function index()
    {
        $user = User::find(Auth::id());

        if ($user->role !== 'seller') {
            return view('shop.onboarding');
        }

        $userId = $user->user_id;

        $stats = [
            'new' => Order::where('seller_id', $userId)->where('status', 'pending')->count(),
            'shipping' => Order::where('seller_id', $userId)->where('status', 'shipped')->count(),
            'completed' => Order::where('seller_id', $userId)->where('status', 'completed')->count(),
            'total_sales' => Order::where('seller_id', $userId)->where('status', 'completed')->sum('subtotal_amount'),
            'low_stock' => Product::where('seller_id', $userId)->where('stock', '<=', 3)->count(),
        ];

        $recentOrders = Order::with('items')
            ->where('seller_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $chartData = Order::where('seller_id', $userId)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(subtotal_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();
            
        $dates = collect();
        $totals = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates->push(now()->subDays($i)->format('d M'));
            $totals->push($chartData[$date] ?? 0);
        }

        return view('shop.index', compact('stats', 'recentOrders', 'dates', 'totals'));
    }

    public function addressIndex()
    {
        $addresses = Auth::user()->addresses()
            ->orderByDesc('is_shop_default')
            ->latest()
            ->get();
        return view('shop.address.index', compact('addresses'));
    }

    public function addressSetDefault($id)
    {
        \App\Models\Address::setShopDefault($id);
        return back()->with('success', 'Alamat toko utama berhasil diperbarui.');
    }

    public function addressCreate()
    {
        return view('shop.address.create');
    }

    public function addressStore(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'recipient' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'city' => 'required|string',
            'province' => 'required|string',
            'district' => 'required|string',
            'village' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'detail_address' => 'required|string',
        ]);

        $isFirst = !Auth::user()->addresses()->exists();

        $address = \App\Models\Address::create([
            'user_id' => Auth::id(),
            'label' => $request->label,
            'recipient' => $request->recipient,
            'phone' => $request->phone,
            'city' => $request->city,
            'province' => $request->province,
            'district' => $request->district,
            'village' => $request->village,
            'postal_code' => $request->postal_code,
            'detail_address' => $request->detail_address,
            'country' => 'ID',
            'is_default' => $isFirst, 
            'is_shop_default' => $isFirst 
        ]);

        if ($request->has('is_shop_default') && $request->is_shop_default == '1') {
            \App\Models\Address::setShopDefault($address->address_id);
        }

        return redirect()->route('shop.address.index')->with('success', 'Alamat baru berhasil ditambahkan.');
    }

    public function addressEdit($id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        return view('shop.address.edit', compact('address'));
    }

    public function addressUpdate(Request $request, $id)
    {
         $request->validate([
            'label' => 'required|string|max:50',
            'recipient' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'city' => 'required|string',
            'province' => 'required|string',
            'district' => 'required|string',
            'village' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'detail_address' => 'required|string',
        ]);

        $address = Auth::user()->addresses()->findOrFail($id);
        
        $address->update([
            'label' => $request->label,
            'recipient' => $request->recipient,
            'phone' => $request->phone,
            'city' => $request->city,
            'province' => $request->province,
            'district' => $request->district,
            'village' => $request->village,
            'postal_code' => $request->postal_code,
            'detail_address' => $request->detail_address,
        ]);

        if ($request->has('is_shop_default') && $request->is_shop_default == '1') {
            \App\Models\Address::setShopDefault($address->address_id);
        }

        return redirect()->route('shop.address.index')->with('success', 'Alamat berhasil diperbarui.');
    }

    public function addressDestroy($id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        
        if ($address->is_shop_default) {
            return back()->with('error', 'Tidak bisa menghapus alamat toko utama. Harap set alamat lain sebagai utama terlebih dahulu.');
        }

        $address->delete();
        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function products(Request $request)
    {
        if ($redirect = $this->ensureShopAddressSet()) return $redirect;

        $userId = Auth::id();
        $tab = $request->query('tab', 'all');

        $query = Product::where('seller_id', $userId);

        if ($search = $request->input('q')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $stats = [
            'total' => Product::where('seller_id', $userId)->count(),
            'active' => Product::where('seller_id', $userId)->where('status', ProductStatus::Active)->count(),
            'draft' => Product::where('seller_id', $userId)->where('status', ProductStatus::Archived)->count(),
            'suspended' => Product::where('seller_id', $userId)->where('status', ProductStatus::Suspended)->count(),
            'empty' => Product::where('seller_id', $userId)->where('stock', '<=', 0)->count(),
        ];

        switch ($tab) {
            case 'active':
                $query->where('status', ProductStatus::Active);
                break;
            case 'draft':
                $query->where('status', ProductStatus::Archived);
                break;
            case 'suspended':
                $query->where('status', ProductStatus::Suspended);
                break;
            case 'empty':
                $query->where('stock', '<=', 0);
                break;
            default:
                break;
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        return view('shop.products.index', compact('products', 'stats', 'tab'));
    }

    public function reports(Request $request)
    {
        if ($redirect = $this->ensureShopAddressSet()) return $redirect;

        $userId = Auth::id();
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $dailySales = Order::where('seller_id', $userId)
            ->where('status', 'completed')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->select(
                DB::raw('DATE(created_at) as date'), 
                DB::raw('COUNT(*) as count'), 
                DB::raw('SUM(subtotal_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = $dailySales->sum('revenue');
        $totalOrders = $dailySales->sum('count');

        return view('shop.reports.index', compact('dailySales', 'totalRevenue', 'totalOrders', 'month', 'year'));
    }

    public function payouts()
    {
        if ($redirect = $this->ensureShopAddressSet()) return $redirect;

        $userId = Auth::id();
        $user = Auth::user();

        $currentBalance = \App\Models\WalletLedger::where('user_id', $userId)
            ->orderBy('wallet_ledger_id', 'desc')
            ->value('balance_after') ?? 0;

        $bankAccounts = \App\Models\BankAccount::where('user_id', $userId)->get();

        $query = \App\Models\PayoutRequest::with('bankAccount')->where('seller_id', $userId);
        
        if (request('date')) {
            $query->whereDate('created_at', request('date'));
        }
        
        if (request('q')) {
            $search = request('q');
            $query->where(function($q) use ($search) {
                $q->where('amount', 'like', '%' . str_replace('.', '', $search) . '%')
                  ->orWhereHas('bankAccount', function($bq) use ($search) {
                      $bq->where('bank_name', 'like', "%{$search}%")
                         ->orWhere('account_no', 'like', "%{$search}%");
                  });
            });
        }
        
        $payouts = $query->latest()->paginate(10)->withQueryString();

        return view('shop.payouts.index', compact('currentBalance', 'bankAccounts', 'payouts'));
    }

    public function storePayout(Request $request)
    {
        $request->merge([
            'amount' => str_replace('.', '', $request->input('amount'))
        ]);

        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_account_id' => 'required|exists:bank_accounts,bank_account_id'
        ]);

        $userId = Auth::id();

        $lastBalance = \App\Models\WalletLedger::where('user_id', $userId)
            ->orderBy('wallet_ledger_id', 'desc')
            ->value('balance_after') ?? 0;
        
        $availableBalance = $lastBalance;

        if ($request->amount > $availableBalance) {
            return back()->with('error', 'Saldo tidak mencukupi.');
        }

        $isWeekend = now()->isWeekend();
        
        $payout = \App\Models\PayoutRequest::create([
            'seller_id' => $userId,
            'amount' => $request->amount,
            'bank_account_id' => $request->bank_account_id,
            'status' => 'requested',
            'requested_at' => now(),
            'notes' => $isWeekend ? 'Request dibuat saat weekend, akan diproses hari kerja.' : null
        ]);

        \App\Models\WalletLedger::create([
             'user_id' => $userId,
             'direction' => 'debit',
             'source_type' => 'payout',
             'source_id' => $payout->payout_request_id,
             'amount' => $request->amount,
             'balance_after' => $lastBalance - $request->amount,
             'memo' => 'Penarikan Dana #' . $payout->payout_request_id,
             'posted_at' => now()
        ]);

        return back()->with('success', 'Permintaan penarikan berhasil dikirim!');
    }

    public function storeBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_no' => 'required|numeric',
            'account_name' => 'required|string',
        ]);

        \App\Models\BankAccount::create([
            'user_id' => Auth::id(),
            'bank_name' => $request->bank_name,
            'account_no' => $request->account_no,
            'account_name' => $request->account_name,
            'is_default' => !\App\Models\BankAccount::where('user_id', Auth::id())->exists()
        ]);

        return back()->with('success', 'Rekening bank berhasil ditambahkan');
    }

    public function updateBank(Request $request, $id)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_no' => 'required|numeric',
            'account_name' => 'required|string',
        ]);

        $bank = \App\Models\BankAccount::where('user_id', Auth::id())->findOrFail($id);
        $bank->update([
            'bank_name' => $request->bank_name,
            'account_no' => $request->account_no,
            'account_name' => $request->account_name,
        ]);

        return back()->with('success', 'Rekening bank berhasil diperbarui');
    }

    public function checkBankDeletion($id)
    {
        $bank = \App\Models\BankAccount::where('user_id', Auth::id())->findOrFail($id);
        
        $pendingPayoutsCount = \App\Models\PayoutRequest::where('bank_account_id', $id)
            ->where('status', 'requested')
            ->count();

        $otherBanks = \App\Models\BankAccount::where('user_id', Auth::id())
            ->where('bank_account_id', '!=', $id)
            ->get();

        return response()->json([
            'pending_count' => $pendingPayoutsCount,
            'other_banks' => $otherBanks
        ]);
    }

    public function deleteBank(Request $request, $id)
    {
        $bank = \App\Models\BankAccount::where('user_id', Auth::id())->findOrFail($id);
        
        $transferToId = $request->input('transfer_to');
        if ($transferToId) {
            $targetBank = \App\Models\BankAccount::where('user_id', Auth::id())->findOrFail($transferToId);
            \App\Models\PayoutRequest::where('bank_account_id', $id)
                ->where('status', 'requested')
                ->update(['bank_account_id' => $targetBank->bank_account_id]);
        }

        $bank->delete();
        return back()->with('success', 'Rekening bank dihapus');
    }

    public function registerStore()
    {
        $user = User::find(Auth::id());

        if ($user->role === 'buyer') {
            $user->update(['role' => 'seller']);
            return redirect()->route('shop.index')->with('success', 'Selamat! Toko Anda berhasil dibuat. Mulai jualan sekarang!');
        }

        return redirect()->route('shop.index');
    }

    public function orders(Request $request)
    {
        if ($redirect = $this->ensureShopAddressSet()) return $redirect;

        $userId = Auth::id();
        $tab = $request->query('tab', 'all');
        $search = $request->input('q');

        $query = Order::with(['buyer', 'items.product'])
            ->where('seller_id', $userId);

        $stats = [
            'total'     => Order::where('seller_id', $userId)->count(),
            'pending'   => Order::where('seller_id', $userId)->where('status', 'pending')->count(),
            'paid'      => Order::where('seller_id', $userId)->where('status', 'paid')->count(),
            'processed' => Order::where('seller_id', $userId)->where('status', 'processed')->count(), 
            'shipped'   => Order::where('seller_id', $userId)->where('status', 'shipped')->count(),
            'completed' => Order::where('seller_id', $userId)->where('status', 'completed')->count(),
            'cancelled' => Order::where('seller_id', $userId)->where('status', 'cancelled')->count(),
            'refunded'  => Order::where('seller_id', $userId)->where('status', 'refunded')->count(),
        ];

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        switch ($tab) {
            case 'pending':
                $query->where('status', 'pending');
                break;
            case 'paid':
                $query->where('status', 'paid');
                break;
            case 'processed':
                $query->where('status', 'processed');
                break;
            case 'shipped':
                $query->where('status', 'shipped');
                break;
            case 'completed':
                $query->where('status', 'completed');
                break;
            case 'cancelled':
                $query->where('status', 'cancelled');
                break;
            case 'refunded':
                $query->where('status', 'refunded');
                break;
            default:
                break;
        }
        
        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('shop.orders.index', compact('orders', 'stats', 'tab'));
    }

    public function orderDetail(Order $order)
    {
        $isSeller = (int)$order->seller_id === (int)Auth::id();
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isSeller && !$isAdmin) {
            abort(403, 'Unauthorized access to shop order');
        }

        $order->load(['items.product.images', 'buyer', 'shippingAddress', 'shipment.carrier']);

        return view('shop.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $isSeller = (int)$order->seller_id === (int)Auth::id();
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isSeller && !$isAdmin) {
            abort(403, 'Unauthorized access to update order status');
        }

        $status = $request->input('status');

        if ($status === 'paid') {
            $order->status = 'paid';
            $order->payment_status = 'settlement';
        }
        elseif ($status === 'processed') {
            $order->status = 'shipped';
        }
        elseif ($status === 'delivered') {
            $order->status = 'delivered'; 
        }
        elseif ($status === 'delivered') {
            $order->status = 'delivered'; 
        }
        elseif ($status === 'completed') {
            if ($order->status !== 'completed') {
                $order->status = 'completed';
                
                if($order->payment_status !== 'settlement') {
                    $order->payment_status = 'settlement';
                }

                $creditAmount = $order->seller_earnings;
                $lastBalance = \App\Models\WalletLedger::where('user_id', $order->seller_id)
                    ->orderBy('wallet_ledger_id', 'desc')
                    ->value('balance_after') ?? 0;

                \App\Models\WalletLedger::create([
                     'user_id' => $order->seller_id,
                     'direction' => 'credit',
                     'source_type' => 'order',
                     'source_id' => $order->order_id,
                     'amount' => $creditAmount,
                     'balance_after' => $lastBalance + $creditAmount,
                     'memo' => 'Pendapatan Pesanan #' . $order->code,
                     'posted_at' => now()
                ]);
            }
        }
        elseif ($status === 'cancelled') {
            $order->status = 'cancelled';
        }

        $order->save();

        return back()->with('success', 'Status pesanan berhasil diperbarui menjadi ' . ucfirst($status));
    }

    public function createProduct()
    {
        if (Auth::user()->role !== 'seller') {
            return redirect()->route('shop.index')->with('error', 'Anda harus menjadi penjual untuk mengakses halaman ini.');
        }

        if ($redirect = $this->ensureShopAddressSet()) return $redirect;

        $categories = Category::all();

        return view('shop.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->merge([
            'price' => str_replace('.', '', $request->input('price')),
            'weight' => str_replace('.', '', $request->input('weight')),
            'stock' => str_replace('.', '', $request->input('stock')),
        ]);

        $request->validate([
            'title'       => 'required|string|min:3|max:160',
            'category_id' => 'required|exists:categories,category_id',
            'price'       => ['required', 'numeric', 'min:1000', function ($attribute, $value, $fail) {
                if ($value % 1000 !== 0) {
                    $fail('Harga harus dalam kelipatan Rp 1.000 (contoh: 10.000, 25.000, 150.000)');
                }
            }],
            'weight'      => ['required', 'numeric', 'min:1000', function ($attribute, $value, $fail) {
                if ($value % 500 !== 0) {
                    $fail('Berat harus dalam kelipatan 500 gram (contoh: 1.000, 1.500, 2.000)');
                }
            }],
            'stock'       => 'required|numeric|min:1',
            'condition'   => 'required|in:new,used',
            'description' => 'required|string',
            'status_input'=> ['required', 'in:active,archived'],
            'images'      => 'required|array|min:1|max:5',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $sellerId = Auth::id();
            $mainImagePath = null;

            $statusEnum = $request->status_input === 'active' ? ProductStatus::Active : ProductStatus::Archived;

            $product = Product::create([
                'seller_id'   => $sellerId,
                'category_id' => $request->category_id,
                'title'       => $request->title,
                'description' => $request->description,
                'price'       => $request->price,
                'weight'      => $request->weight, 
                'stock'       => $request->stock,
                'condition'   => $request->condition,
                'status'      => $statusEnum,
                'main_image'  => null,
            ]);

            if ($request->hasFile('images')) {
                $folderName = 'uploads/products';
                if (!Storage::disk('public')->exists($folderName)) {
                    Storage::disk('public')->makeDirectory($folderName);
                }

                foreach ($request->file('images') as $index => $file) {
                    $filename = time() . '_' . $index . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs($folderName, $filename, 'public');

                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'url'        => $path,
                        'is_primary' => ($index === 0),
                        'sort_order' => $index,
                    ]);

                    if ($index === 0) {
                        $mainImagePath = $path;
                    }
                }
            }

            if ($mainImagePath) {
                $product->update(['main_image' => $mainImagePath]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $statusEnum === ProductStatus::Active ? 'Produk berhasil diterbitkan!' : 'Produk disimpan sebagai draft.',
                    'redirect_url' => route('shop.products.index')
                ]);
            }

            return redirect()->route('shop.products.index')->with('success', 'Produk berhasil diterbitkan!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan produk: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Gagal menyimpan produk: ' . $e->getMessage());
        }
    }

    public function editProduct(Product $product)
    {
        $isSeller = (int)$product->seller_id === (int)Auth::id();
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isSeller && !$isAdmin) {
            abort(403);
        }

        $categories = Category::all();

        return view('shop.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $isSeller = (int)$product->seller_id === (int)Auth::id();
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isSeller && !$isAdmin) {
            abort(403);
        }

        $request->merge([
            'price' => str_replace('.', '', $request->input('price')),
            'weight' => str_replace('.', '', $request->input('weight')),
            'stock' => str_replace('.', '', $request->input('stock')),
        ]);

        $request->validate([
            'title'       => 'required|string|min:3|max:160',
            'category_id' => 'required|exists:categories,category_id',
            'price'       => ['required', 'numeric', 'min:1000', function ($attribute, $value, $fail) {
                if ($value % 1000 !== 0) {
                    $fail('Harga harus dalam kelipatan Rp 1.000 (contoh: 10.000, 25.000, 150.000)');
                }
            }],
            'weight'      => ['required', 'numeric', 'min:1000', function ($attribute, $value, $fail) {
                if ($value % 500 !== 0) {
                    $fail('Berat harus dalam kelipatan 500 gram (contoh: 1.000, 1.500, 2.000)');
                }
            }],
            'stock'       => 'required|numeric|min:1',
            'condition'   => 'required|in:new,used',
            'description' => 'required|string',
            'status_input'=> ['required', 'in:active,archived'],
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $statusEnum = $request->status_input === 'active' ? ProductStatus::Active : ProductStatus::Archived;

            $product->update([
                'category_id' => $request->category_id,
                'title'       => $request->title,
                'description' => $request->description,
                'price'       => $request->price,
                'weight'      => $request->weight, 
                'stock'       => $request->stock,
                'condition'   => $request->condition,
                'status'      => $statusEnum,
            ]);

            if ($request->hasFile('images')) {
                $folderName = 'uploads/products';
                if (!Storage::disk('public')->exists($folderName)) {
                    Storage::disk('public')->makeDirectory($folderName);
                }

                $lastSort = $product->images()->max('sort_order') ?? 0;

                foreach ($request->file('images') as $index => $file) {
                    $filename = time() . '_u_' . $index . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs($folderName, $filename, 'public');

                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'url'        => $path,
                        'is_primary' => false,
                        'sort_order' => $lastSort + $index + 1,
                    ]);
                }
            }

            if (!$product->main_image || !ProductImage::where('url', $product->main_image)->exists()) {
                $firstImg = $product->images()->orderBy('sort_order')->first();
                if ($firstImg) {
                    $product->update(['main_image' => $firstImg->url]);
                    $firstImg->update(['is_primary' => true]);
                }
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Produk berhasil diperbarui!',
                    'redirect_url' => route('shop.products.index')
                ]);
            }
            return redirect()->route('shop.products.index')->with('success', 'Produk diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal update: ' . $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function checkProductDeletion(Product $product)
    {
        $isSeller = (int)$product->seller_id === (int)Auth::id();
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isSeller && !$isAdmin) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $activeTransactions = OrderItem::where('product_id', $product->product_id)
            ->whereHas('order', function ($query) {
                $query->whereNotIn('status', ['completed', 'cancelled']);
            })->count();

        return response()->json([
            'status' => 'success',
            'active_transactions' => $activeTransactions,
            'message' => $activeTransactions > 0 
                ? "Terdapat {$activeTransactions} transaksi berjalan untuk produk ini. Produk tidak dapat dihapus." 
                : "Aman untuk dihapus."
        ]);
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        
        $isSeller = (int)$product->seller_id === (int)Auth::id();
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isSeller && !$isAdmin) {
            if (request()->ajax()) return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            abort(403);
        }

        $hasActiveTransactions = OrderItem::where('product_id', $product->product_id)
            ->whereHas('order', function ($query) {
                $query->whereNotIn('status', ['completed', 'cancelled']);
            })->exists();

        if ($hasActiveTransactions) {
            if (request()->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Tidak bisa menghapus produk karena masih ada transaksi berjalan.'], 400);
            }
            return back()->with('error', 'Tidak bisa menghapus produk karena masih ada transaksi berjalan.');
        }

        CartItem::where('product_id', $product->product_id)->delete();

        foreach ($product->images as $img) {
            if (Storage::disk('public')->exists($img->url)) {
                Storage::disk('public')->delete($img->url);
            }
            $img->delete();
        }

        $product->delete();

        if (request()->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Produk berhasil dihapus dan dibersihkan dari keranjang pengguna.']);
        }

        return redirect()->route('shop.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function deleteProductImage($id)
    {
        $image = ProductImage::findOrFail($id);

        $product = Product::where('product_id', $image->product_id)->firstOrFail();
        
        $isSeller = (int)$product->seller_id === (int)Auth::id();
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isSeller && !$isAdmin) {
             abort(403, 'Unauthorized');
        }

        if (Storage::disk('public')->exists($image->url)) {
            Storage::disk('public')->delete($image->url);
        }

        $image->delete();

        if ($product->main_image === $image->url) {
            $nextImg = $product->images()->first();
            if ($nextImg) {
                $product->update(['main_image' => $nextImg->url]);
                $nextImg->update(['is_primary' => true]);
            } else {
                $product->update(['main_image' => null]);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Foto berhasil dihapus']);
    }
}