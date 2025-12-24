<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\PayoutRequest;
use App\Models\Category; 
use App\Enums\ProductStatus; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Profile;
use App\Models\WalletLedger;
use App\Models\IntegrationProvider;
use App\Models\IntegrationKey;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::where('status', 'completed')->count(),
            'pending_payouts' => PayoutRequest::where('status', 'requested')->count(),
            'total_revenue' => Order::where('status', 'completed')->sum(DB::raw('platform_fee_seller + platform_fee_buyer')),
        ];

        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6);

        $dates = [];
        $counts = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $dates[$dateString] = 0; 
        }

        $salesData = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate->format('Y-m-d 00:00:00'), $endDate->format('Y-m-d 23:59:59')])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        foreach ($salesData as $data) {
            $dates[$data->date] = $data->count;
        }

        $chartLabels = [];
        $chartValues = [];
        foreach ($dates as $date => $count) {
            $chartLabels[] = date('d M', strtotime($date));
            $chartValues[] = $count;
        }

        $recentUsers = User::latest()->limit(5)->get();
        $recentProducts = Product::with('seller')->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'chartLabels', 'chartValues', 'recentUsers', 'recentProducts'));
    }

    public function products(Request $request)
    {
        $query = Product::with(['seller', 'category']);

        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('seller', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('status', ProductStatus::Active)->count(),
            'suspended' => Product::where('status', ProductStatus::Suspended)->count(),
            'sold' => Product::where('status', ProductStatus::Sold)->count(),
        ];

        $categories = Category::all();

        return view('admin.products.index', compact('products', 'stats', 'categories'));
    }

    public function showProduct($id)
    {
        $product = Product::with(['seller', 'category', 'images', 'reviews'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    public function toggleProductStatus(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $reason = $request->input('reason');

        if ($product->status === ProductStatus::Suspended) {
            $product->status = ProductStatus::Active;
            $msg = 'Produk berhasil diaktifkan kembali.';
        } else {
            $product->status = ProductStatus::Suspended;
            $product->suspension_reason = $reason; 
            $msg = 'Produk berhasil disuspend' . ($reason ? " (Alasan: $reason)" : '.');
        }

        $product->save();

        return back()->with('success', $msg);
    }

    public function users(Request $request)
    {
        $adminQuery = User::where('role', 'admin')->latest();
        
        if (Auth::id() != 1) {
            $adminQuery->where('user_id', '!=', 1);
        }

        $admins = $adminQuery->get();

        $query = User::where('role', '!=', 'admin');

        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'total_admins' => $admins->count(),
            'active_users' => User::where('role', '!=', 'admin')->where('status', 'active')->count(),
            'suspended_users' => User::where('role', '!=', 'admin')->where('status', 'suspended')->count(),
        ];

        return view('admin.users.index', compact('admins', 'users', 'stats'));
    }

    public function usersShow(Request $request, $id)
    {
        $user = User::with(['profile', 'addresses', 'bankAccounts'])->findOrFail($id);
        
        $productQuery = \App\Models\Product::where('seller_id', $user->user_id)->with('category');

        if ($request->has('q') && $request->q != '') {
            $productQuery->where('title', 'like', '%' . $request->q . '%');
        }

        if ($request->has('category') && $request->category != '') {
            $productQuery->where('category_id', $request->category);
        }

        if ($request->has('status') && $request->status != '') {
             $productQuery->where('status', $request->status);
        }

        $products = $productQuery->latest()->paginate(8)->withQueryString(); 
        $categories = \App\Models\Category::orderBy('name')->get();

        $buyCount = \App\Models\Order::where('buyer_id', $user->user_id)->count();
        $sellCount = \App\Models\Order::where('seller_id', $user->user_id)->count();

        return view('admin.users.show', compact('user', 'products', 'categories', 'buyCount', 'sellCount'));
    }

    public function usersCreate()
    {
        if (Auth::id() != 1) {
            abort(403, 'Akses Ditolak. Hanya Super Admin yang dapat menambahkan admin baru.');
        }
        return view('admin.users.create');
    }

    public function storeAdmin(Request $request)
    {
        if (Auth::id() != 1) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'name' => 'required|string|max:120',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|max:191|unique:users',
            'phone' => 'required|numeric|digits_between:8,15|unique:profiles,phone', 
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $admin = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin', 
                'status' => 'active',
            ]);

            $phone = $request->phone;
            if (substr($phone, 0, 1) == '0') {
               $phone = '62' . substr($phone, 1);
            } elseif (substr($phone, 0, 2) != '62') {
               $phone = '62' . $phone;
            }

            Profile::create([
                'user_id' => $admin->user_id,
                'phone' => $phone
            ]);

            DB::commit();

            return redirect()->route('admin.users')->with('success', 'Administrator baru berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan admin: ' . $e->getMessage());
        }
    }

    public function usersEdit($id)
    {
        if (Auth::id() != 1 && Auth::id() != $id) {
            abort(403, 'Akses Ditolak. Anda hanya dapat mengedit akun sendiri.');
        }

        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateAdmin(Request $request, $id)
    {
        if (Auth::id() != 1 && Auth::id() != $id) {
            abort(403, 'Akses Ditolak.');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:191|unique:users,email,'.$id.',user_id',
            'username' => 'required|string|max:50|unique:users,username,'.$id.',user_id',
            'password' => 'nullable|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->username;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            DB::commit();

            return redirect()->route('admin.users')->with('success', 'Data pengguna berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function sendResetLink(Request $request, $id)
    {
        $user = User::with('profile')->findOrFail($id);
        
        $token = app('auth.password.broker')->createToken($user);
        
        $resetLink = route('password.reset', ['token' => $token, 'email' => $user->email]);

        $phone = $user->profile->phone ?? null;
        
        if ($phone) {
            $fonnte = new \App\Services\FonnteService();
            $message = "*RESET PASSWORD ADMIN REQUEST*\n\nHalo {$user->name},\nAdmin telah merequest reset password untuk akun Anda.\n\nSilakan klik link berikut untuk membuat password baru:\n{$resetLink}\n\nLink ini valid untuk 60 menit.";
            
            $result = $fonnte->sendMessage($phone, $message);

            if ($result['status']) {
                return back()->with('success', 'Link reset password telah dikirim ke WhatsApp pengguna.');
            } else {
                return back()->with('error', 'Gagal kirim WA: ' . ($result['error'] ?? 'Unknown error'));
            }
        } else {
            return back()->with('error', 'Pengguna ini tidak memiliki nomor HP yang terdaftar untuk dikirimi link.');
        }
    }

    public function destroyUser($id)
    {
        if ($id == 1) {
            return back()->with('error', 'Super Admin tidak dapat dihapus.');
        }

        if ($id == auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user = User::findOrFail($id);

        if ($user->role === 'admin' && auth()->id() != 1) {
             return back()->with('error', 'Hanya Super Admin yang dapat menghapus Administrator.');
        }

        try {
            DB::beginTransaction();

            \App\Models\WalletLedger::where('user_id', $user->user_id)->delete();
            \App\Models\PayoutRequest::where('seller_id', $user->user_id)->delete();
            \App\Models\BankAccount::where('user_id', $user->user_id)->delete();

            \App\Models\Address::where('user_id', $user->user_id)->delete();
            \App\Models\Review::where('user_id', $user->user_id)->delete(); 

            \App\Models\Order::where('buyer_id', $user->user_id)->delete();
            \App\Models\Order::where('seller_id', $user->user_id)->delete();

            $productIds = \App\Models\Product::where('seller_id', $user->user_id)->pluck('product_id');

            DB::table('cart_items')->whereIn('product_id', $productIds)->delete();

            $products = \App\Models\Product::where('seller_id', $user->user_id)->get();
            foreach ($products as $product) {
                $product->delete();
            }

            \App\Models\Profile::where('user_id', $user->user_id)->delete();
            $user->delete();

            DB::commit();

            return back()->with('success', 'Pengguna beserta seluruh data (produk, order, wallet) berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
    }

    public function toggleUserStatus(Request $request, $id)
    {
        if ($id == auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah status akun sendiri.');
        }

        $user = User::findOrFail($id);

        if ($user->status === 'suspended') {
            $user->status = 'active';
            $msg = 'Akun pengguna berhasil diaktifkan kembali.';
        } else {
            $user->status = 'suspended';
            $msg = 'Akun pengguna berhasil disuspend (dinonaktifkan).';
        }

        $user->save();

        return back()->with('success', $msg);
    }

    public function payouts(Request $request)
    {
        $query = PayoutRequest::with(['seller', 'bankAccount']);

        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->whereHas('seller', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('bankAccount', function($bq) use ($search) {
                    $bq->where('account_no', 'like', "%{$search}%")
                       ->orWhere('bank_name', 'like', "%{$search}%")
                       ->orWhere('account_name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'approved') {
                $query->whereIn('status', ['approved', 'paid']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $payouts = $query->latest('requested_at')->paginate(10)->withQueryString();

        $stats = [
            'total_requests' => PayoutRequest::count(),
            'pending_requests' => PayoutRequest::where('status', 'requested')->count(),
            'approved_requests' => PayoutRequest::whereIn('status', ['approved', 'paid'])->count(),
            'rejected_requests' => PayoutRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.payouts.index', compact('payouts', 'stats'));
    }

    public function updatePayoutStatus(Request $request, $id)
    {
        $payout = PayoutRequest::findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            if ($request->status === 'approved') {
                $payout->update([
                    'status' => 'paid', 
                    'processed_at' => now(),
                    'processed_by' => Auth::id(),
                    'notes' => 'Pencairan dana disetujui oleh Admin.'
                ]);

            } elseif ($request->status === 'rejected') {
                $payout->update([
                    'status' => 'rejected',
                    'processed_at' => now(),
                    'processed_by' => Auth::id(),
                    'notes' => $request->notes ?? 'Ditolak oleh Admin.'
                ]);

                WalletLedger::create([
                    'user_id' => $payout->seller_id,
                    'direction' => 'credit', 
                    'source_type' => 'payout', 
                    'source_id' => $payout->payout_request_id,
                    'amount' => $payout->amount,
                    'balance_after' => WalletLedger::where('user_id', $payout->seller_id)->orderBy('wallet_ledger_id', 'desc')->value('balance_after') + $payout->amount,
                    'memo' => 'Pengembalian dana payout #' . $payout->payout_request_id . ' (Ditolak)',
                    'posted_at' => now()
                ]);
            }

            DB::commit();

            return back()->with('success', 'Status payout berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses payout: ' . $e->getMessage());
        }
    }

    public function paymentGateway()
    {
        $midtransProvider = IntegrationProvider::firstOrCreate(['code' => 'midtrans'], ['name' => 'Midtrans Payment Gateway']);
        $midtrans = IntegrationKey::where('provider_id', $midtransProvider->integration_provider_id)->first();
        if(!$midtrans) {
            $midtrans = new IntegrationKey();
            $midtrans->meta_json = ['merchant_id' => '', 'environment' => 'sandbox'];
        } else {
             if(is_string($midtrans->meta_json)) $midtrans->meta_json = json_decode($midtrans->meta_json, true);
             try {
                $midtrans->server_key = Crypt::decryptString($midtrans->encrypted_k);
             } catch (\Exception $e) { $midtrans->server_key = $midtrans->encrypted_k; }
        }

        return view('admin.integrations.payment', compact('midtrans'));
    }

    public function shipping()
    {
        $rajaongkirProvider = IntegrationProvider::firstOrCreate(['code' => 'rajaongkir'], ['name' => 'RajaOngkir']);
        $rajaongkir = IntegrationKey::where('provider_id', $rajaongkirProvider->integration_provider_id)->first();
        if(!$rajaongkir) {
            $rajaongkir = new IntegrationKey();
            $rajaongkir->meta_json = ['base_url' => 'https://pro.rajaongkir.com/api', 'type' => 'pro'];
        } else {
            if(is_string($rajaongkir->meta_json)) $rajaongkir->meta_json = json_decode($rajaongkir->meta_json, true);
        }

        $shippingCarriers = \App\Models\ShippingCarrier::paginate(10);

        return view('admin.integrations.shipping', compact('rajaongkir', 'shippingCarriers'));
    }

    public function whatsapp()
    {
        $fonnteProvider = IntegrationProvider::firstOrCreate(['code' => 'whatsapp'], ['name' => 'WhatsApp (Fonnte)']);
        $fonnte = IntegrationKey::where('provider_id', $fonnteProvider->integration_provider_id)->first();
        if(!$fonnte) {
            $fonnte = new IntegrationKey();
        }

        return view('admin.integrations.whatsapp', compact('fonnte'));
    }

    public function getPaymentTestToken(Request $request) 
    {
        try {
            $midtrans = new \App\Services\MidtransService();
            
            $amount = $request->input('amount', 10000); 
            $dummyId = 'TEST-' . time();

            $params = [
                'transaction_details' => [
                    'order_id' => $dummyId,
                    'gross_amount' => (int) $amount,
                ],
                'customer_details' => [
                    'first_name' => 'Admin Tester',
                    'email' => 'admin@tester.com',
                ],
            ];

            $result = $midtrans->createSnapToken($params);

            if ($result && isset($result['token'])) {
                
                \App\Models\WebhookLog::create([
                    'provider_code' => 'midtrans',
                    'event_type' => 'token_request_test',
                    'related_id' => $dummyId,
                    'payload' => [
                        'order_id' => $dummyId,
                        'amount' => $amount,
                        'result_token' => $result['token']
                    ],
                    'received_at' => now()
                ]);

                return response()->json([
                    'status' => 'success',
                    'token' => $result['token']
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to obtain Snap Token from Midtrans (Check credentials/logs).'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storeCarrier(Request $request)
    {
        $request->validate(['code' => 'required', 'name' => 'required']);
        \App\Models\ShippingCarrier::create([
            'code' => strtolower($request->code),
            'name' => $request->name,
            'is_enabled' => true
        ]);
        return back()->with('success', 'Kurir berhasil ditambahkan.');
    }

    public function updateCarrier(Request $request, $id)
    {
        $request->validate(['code' => 'required', 'name' => 'required']);
        $carrier = \App\Models\ShippingCarrier::findOrFail($id);
        $carrier->update([
            'code' => strtolower($request->code),
            'name' => $request->name
        ]);
        return back()->with('success', 'Informasi kurir berhasil diperbarui.');
    }

    public function toggleCarrierStatus($id)
    {
        $carrier = \App\Models\ShippingCarrier::findOrFail($id);
        $carrier->is_enabled = !$carrier->is_enabled;
        $carrier->save();

        $status = $carrier->is_enabled ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kurir {$carrier->name} berhasil {$status}.");
    }

    public function deleteCarrier($id)
    {
        \App\Models\ShippingCarrier::destroy($id);
        return back()->with('success', 'Kurir dihapus.');
    }

    public function checkShippingCostTest(Request $request)
    {
        $request->validate([
            'origin' => 'required|numeric',
            'destination' => 'required|numeric', 
            'weight' => 'required|numeric',
            'courier' => 'required'
        ]);

        try {
            $rajaOngkir = new \App\Services\RajaOngkirService();
            $result = $rajaOngkir->checkCost(
                $request->origin,
                'subdistrict',
                $request->destination,
                'subdistrict',
                $request->weight,
                $request->courier
            );

            if($result['status'] && !empty($result['data'])) {
                $costs = $result['data'];
                $courierName = $costs[0]['name'] ?? strtoupper($request->courier);
                
                $originName = 'ID: ' . $request->origin;
                $destName = 'ID: ' . $request->destination;
                
                \App\Models\WebhookLog::create([
                    'provider_code' => 'rajaongkir',
                    'event_type' => 'cost_check_test',
                    'related_id' => 'ADMIN-TEST-' . time(),
                    'payload' => [
                        'origin' => $request->origin,
                        'destination' => $request->destination,
                        'weight' => $request->weight,
                        'courier' => $request->courier,
                        'result' => $result
                    ],
                    'received_at' => now()
                ]);

                return back()
                    ->with('cost_results', $costs)
                    ->with('origin_name', $originName)
                    ->with('dest_name', $destName)
                    ->with('courier_name', $courierName)
                    ->withInput();

            } else {
                 $errMsg = $result['message'] ?? 'Data ongkir tidak ditemukan.';
                 
                 \App\Models\WebhookLog::create([
                    'provider_code' => 'rajaongkir',
                    'event_type' => 'cost_check_test_failed',
                    'payload' => ['error' => $errMsg, 'request' => $request->all()],
                    'received_at' => now()
                 ]);

                 return back()->with('error', 'Cek Ongkir Gagal: ' . $errMsg)->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi Kesalahan API: ' . $e->getMessage())->withInput();
        }
    }

    public function sendTestWhatsapp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'message' => 'nullable|string'
        ]);

        $fonnte = new \App\Services\FonnteService();
        $target = $request->phone;
        $message = $request->message ?? "Test Notification from Admin Panel.\nTime: " . now();

        $result = $fonnte->sendMessage($target, $message);

        \App\Models\WebhookLog::create([
            'provider_code' => 'whatsapp',
            'event_type' => 'send_message_test',
            'related_id' => $target,
            'payload' => [
                'target' => $target,
                'message' => $message,
                'result' => $result
            ],
            'received_at' => now()
        ]);

        if ($result['status']) {
            return back()->with('success', 'Pesan Test Terkirim ke ' . $target);
        } else {
            return back()->with('error', 'Gagal kirim pesan: ' . ($result['error'] ?? 'Unknown Error'));
        }
    }

    public function webhookLogs(Request $request) 
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $query = \App\Models\WebhookLog::latest('received_at');

        if ($request->has('provider') && $request->provider != '') {
            $query->where('provider_code', $request->provider);
        }

        $query->whereDate('received_at', '>=', $startDate)
              ->whereDate('received_at', '<=', $endDate);

        $logs = $query->paginate(10);
        
        $providers = \App\Models\WebhookLog::select('provider_code')->distinct()->pluck('provider_code');

        return view('admin.integrations.webhook_logs', compact('logs', 'providers', 'startDate', 'endDate'));
    }

    public function updatePaymentGateway(Request $request)
    {
        $request->validate([
            'server_key' => 'required|string',
            'client_key' => 'required|string',
            'merchant_id' => 'nullable|string',
            'mode' => 'required|in:sandbox,production',
        ]);

        $provider = IntegrationProvider::where('code', 'midtrans')->firstOrFail();

        $meta = [
            'merchant_id' => $request->merchant_id,
            'environment' => $request->mode
        ];

        IntegrationKey::updateOrCreate(
            ['provider_id' => $provider->integration_provider_id],
            [
                'label' => 'Midtrans ' . ucfirst($request->mode),
                'public_k' => $request->client_key, 
                'encrypted_k' => Crypt::encryptString($request->server_key), 
                'is_active' => true, 
                'meta_json' => $meta
            ]
        );

        return back()->with('success', 'Konfigurasi Midtrans berhasil diperbarui.');
    }

    public function testPaymentConnection()
    {
        try {
            $midtrans = new \App\Services\MidtransService();
            
            $dummyId = 'TEST-CONN-' . time();
            $params = [
                'transaction_details' => [
                    'order_id' => $dummyId,
                    'gross_amount' => 10000,
                ],
                'item_details' => [
                    [
                        'id' => 'TEST-1',
                        'price' => 10000,
                        'quantity' => 1,
                        'name' => 'Connection Test'
                    ]
                ],
                'customer_details' => [
                    'first_name' => 'Admin Tester',
                    'email' => 'admin@tester.com',
                ]
            ];

            $result = $midtrans->createSnapToken($params);

            if ($result) {
                 return back()->with('success', 'Koneksi Berhasil! Token diterima: ' . (is_array($result) ? $result['token'] : $result));
            } else {
                 return back()->with('error', 'Koneksi Gagal. Cek Log Laravel untuk detail error.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Exception: ' . $e->getMessage());
        }
    }

    public function updateShippingApi(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'base_url' => 'required|url',
        ]);

        $provider = IntegrationProvider::where('code', 'rajaongkir')->firstOrFail();

        IntegrationKey::updateOrCreate(
            ['provider_id' => $provider->integration_provider_id],
            [
                'label' => 'RajaOngkir Key',
                'public_k' => $request->api_key, 
                'is_active' => true,
                'meta_json' => ['base_url' => $request->base_url]
            ]
        );

        return back()->with('success', 'Konfigurasi RajaOngkir berhasil diperbarui.');
    }

    public function updateWhatsappApi(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $provider = IntegrationProvider::where('code', 'whatsapp')->firstOrFail();

        IntegrationKey::updateOrCreate(
            ['provider_id' => $provider->integration_provider_id],
            [
                'label' => 'Fonnte Token',
                'public_k' => $request->token, 
                'is_active' => true,
                'meta_json' => []
            ]
        );

        return back()->with('success', 'Konfigurasi Fonnte berhasil diperbarui.');
    }
}