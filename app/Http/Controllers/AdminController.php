<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\PayoutRequest;
use App\Models\Category; // Tambahkan Category
use App\Enums\ProductStatus; // Tambahkan Enum
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
    // ... (method dashboard tetap sama) ...

    /**
     * Dashboard Utama Admin
     */
    public function dashboard()
    {
        // 1. Kartu Statistik Utama
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::where('status', 'completed')->count(),
            'pending_payouts' => PayoutRequest::where('status', 'requested')->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('platform_fee'),
        ];

        // 2. Data Grafik Penjualan (7 Hari Terakhir)
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6);

        // Inisialisasi array kosong untuk 7 hari
        $dates = [];
        $counts = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $dates[$dateString] = 0; // Default value 0
        }

        // Query Data Real
        $salesData = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate->format('Y-m-d 00:00:00'), $endDate->format('Y-m-d 23:59:59')])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Merge Data Query ke Array Tanggal
        foreach ($salesData as $data) {
            $dates[$data->date] = $data->count;
        }

        // Siapkan Labels dan Values untuk Chart.js
        $chartLabels = [];
        $chartValues = [];
        foreach ($dates as $date => $count) {
            $chartLabels[] = date('d M', strtotime($date));
            $chartValues[] = $count;
        }

        // 3. Data Terbaru
        $recentUsers = User::latest()->limit(5)->get();
        $recentProducts = Product::with('seller')->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'chartLabels', 'chartValues', 'recentUsers', 'recentProducts'));
    }

    /**
     * Manajemen Produk / Postingan
     */
    public function products(Request $request)
    {
        // Query Dasar (Eager Load Seller & Category)
        $query = Product::with(['seller', 'category']);

        // 1. Filter Pencarian (Judul / Deskripsi / Nama Penjual)
        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('seller', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // 3. Filter Kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Sorting Default Terbaru
        $products = $query->latest()->paginate(10)->withQueryString();

        // Data Statistik Kecil di Atas Tabel
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('status', ProductStatus::Active)->count(),
            'suspended' => Product::where('status', ProductStatus::Suspended)->count(),
            'reported' => 0 // Nanti bisa diisi jika ada tabel report
        ];

        $categories = Category::all();

        return view('admin.products.index', compact('products', 'stats', 'categories'));
    }

    /**
     * Detail Produk (Untuk Review Admin)
     */
    public function showProduct($id)
    {
        $product = Product::with(['seller', 'category', 'images', 'reviews'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Aksi Suspend / Restore Produk
     */
    public function toggleProductStatus(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $reason = $request->input('reason');

        // Jika statusnya suspended -> kembalikan ke active
        // Jika statusnya active/lainnya -> ubah ke suspended

        if ($product->status === ProductStatus::Suspended) {
            $product->status = ProductStatus::Active;
            $msg = 'Produk berhasil diaktifkan kembali.';
        } else {
            $product->status = ProductStatus::Suspended;
            // Simpan alasan ke database
            $product->suspension_reason = $reason; 
            $msg = 'Produk berhasil disuspend' . ($reason ? " (Alasan: $reason)" : '.');
        }

        $product->save();

        return back()->with('success', $msg);
    }

/**
     * Manajemen Pengguna (Admin & User Biasa)
     */
    public function users(Request $request)
    {
        // 1. Ambil Semua Admin (tidak perlu pagination karena jumlahnya sedikit)
        $admins = User::where('role', 'admin')->latest()->get();

        // 2. Query Users (Biasa/Seller) dengan Filter
        $query = User::where('role', '!=', 'admin');

        // Filter Pencarian
        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter Role
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        // Statistik (Count langsung dari DB agar akurat meski ada pagination)
        $stats = [
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'total_admins' => $admins->count(),
            'active_users' => User::where('role', '!=', 'admin')->where('status', 'active')->count(),
            'suspended_users' => User::where('role', '!=', 'admin')->where('status', 'suspended')->count(),
        ];

        return view('admin.users.index', compact('admins', 'users', 'stats'));
    }

    public function usersShow($id)
    {
        $user = User::with(['profile', 'addresses', 'products', 'bankAccounts'])->findOrFail($id);
        
        // Data pendukung
        $products = $user->products; 
        $orders = \App\Models\Order::where('buyer_id', $user->user_id)->orWhere('seller_id', $user->user_id)->latest()->get();

        return view('admin.users.show', compact('user', 'products', 'orders'));
    }

    /**
     * Simpan Admin Baru
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|max:191|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $admin = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin', // Role Admin
                'status' => 'active',
            ]);

            // Buat Profile kosong agar tidak error relasi
            Profile::create(['user_id' => $admin->user_id]);

            DB::commit();

            return back()->with('success', 'Administrator baru berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan admin: ' . $e->getMessage());
        }
    }

    /**
     * Aksi Suspend / Restore User
     */
    public function toggleUserStatus(Request $request, $id)
    {
        // Cegah Admin men-suspend dirinya sendiri
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
    // Placeholder Payouts
     /**
     * Manajemen Payout Request
     */
    public function payouts(Request $request)
    {
        // Query Dasar
        $query = PayoutRequest::with(['seller', 'bankAccount']);

        // Filter Pencarian (Seller Name / Bank Account)
        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->whereHas('seller', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('bankAccount', function($bq) use ($search) {
                    $bq->where('account_number', 'like', "%{$search}%")
                       ->orWhere('bank_name', 'like', "%{$search}%")
                       ->orWhere('account_holder', 'like', "%{$search}%");
                });
            });
        }

        // Filter Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
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

    /**
     * Update Status Payout (Approve/Reject)
     */
    public function updatePayoutStatus(Request $request, $id)
    {
        $payout = PayoutRequest::findOrFail($id);

        // Validasi input
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            if ($request->status === 'approved') {
                // 1. Update Status Payout jadi 'paid' / 'approved'
                // Menggunakan method di Model PayoutRequest (jika ada) atau manual
                $payout->update([
                    'status' => 'paid', // Atau 'approved' tergantung enum database
                    'processed_at' => now(),
                    'processed_by' => Auth::id(),
                    'notes' => 'Pencairan dana disetujui oleh Admin.'
                ]);

                // Disini biasanya ada integrasi Payment Gateway (Disbursement)
                // Jika manual transfer, admin melakukan transfer bank dulu baru klik approve.

            } elseif ($request->status === 'rejected') {
                // 1. Update Status Payout jadi 'rejected'
                $payout->update([
                    'status' => 'rejected',
                    'processed_at' => now(),
                    'processed_by' => Auth::id(),
                    'notes' => $request->notes ?? 'Ditolak oleh Admin.'
                ]);

                // 2. KEMBALIKAN SALDO ke Wallet User (Refund)
                // Asumsi saat request dibuat, saldo user sudah didebit.
                // Jadi kalau reject, harus di-credit balik.

                WalletLedger::create([
                    'user_id' => $payout->seller_id,
                    'direction' => 'credit', // Masuk kembali
                    'source_type' => 'payout', // atau adjustment
                    'source_id' => $payout->payout_request_id,
                    'amount' => $payout->amount,
                    // Balance after perlu dihitung real-time atau ambil last balance + amount
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
        /**
     * Halaman Payment Gateway (Midtrans)
     */
    /**
     * Halaman Payment Gateway (Midtrans)
     */
    public function paymentGateway()
    {
        // 1. Midtrans
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

    /**
     * Halaman Logistik (RajaOngkir)
     */
    public function shipping()
    {
        // 2. RajaOngkir
        $rajaongkirProvider = IntegrationProvider::firstOrCreate(['code' => 'rajaongkir'], ['name' => 'RajaOngkir']);
        $rajaongkir = IntegrationKey::where('provider_id', $rajaongkirProvider->integration_provider_id)->first();
        if(!$rajaongkir) {
            $rajaongkir = new IntegrationKey();
            $rajaongkir->meta_json = ['base_url' => 'https://pro.rajaongkir.com/api', 'type' => 'pro'];
        } else {
            if(is_string($rajaongkir->meta_json)) $rajaongkir->meta_json = json_decode($rajaongkir->meta_json, true);
        }

        // List Carriers with pagination
        $shippingCarriers = \App\Models\ShippingCarrier::paginate(10);

        return view('admin.integrations.shipping', compact('rajaongkir', 'shippingCarriers'));
    }

    /**
     * Halaman WhatsApp (Fonnte)
     */
    public function whatsapp()
    {
        // 3. Fonnte
        $fonnteProvider = IntegrationProvider::firstOrCreate(['code' => 'whatsapp'], ['name' => 'WhatsApp (Fonnte)']);
        $fonnte = IntegrationKey::where('provider_id', $fonnteProvider->integration_provider_id)->first();
        if(!$fonnte) {
            $fonnte = new IntegrationKey();
        }

        return view('admin.integrations.whatsapp', compact('fonnte'));
    }

    // --- PAYMENT GATEWAY UTILS ---

    /**
     * Get Token for Admin Test (JSON Response)
     */
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

            // Use the service method to get token (createSnapToken returns array with token)
            $result = $midtrans->createSnapToken($params);

            if ($result && isset($result['token'])) {
                
                // Log Midtrans Test
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


    // --- LOGISTIK UTILS ---
    
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
                // Modified: Data is a flat array of services
                $costs = $result['data'];
                // Get name from first item if available
                $courierName = $costs[0]['name'] ?? strtoupper($request->courier);
                
                // Fallback names (since API might not return details in this specific structure)
                $originName = 'ID: ' . $request->origin;
                $destName = 'ID: ' . $request->destination;
                
                // Log Request
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
                 
                 // Log Error
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

    // --- WHATSAPP UTILS ---
    
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

        // Log WA
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

    // --- WEBHOOK LOGS ---
    public function webhookLogs(Request $request) 
    {
        // Default Date: First day of current month to Today
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $query = \App\Models\WebhookLog::latest('received_at');

        if ($request->has('provider') && $request->provider != '') {
            $query->where('provider_code', $request->provider);
        }

        // Apply Date Filter
        $query->whereDate('received_at', '>=', $startDate)
              ->whereDate('received_at', '<=', $endDate);

        $logs = $query->paginate(10);
        
        // Get unique providers for filter
        $providers = \App\Models\WebhookLog::select('provider_code')->distinct()->pluck('provider_code');

        return view('admin.integrations.webhook_logs', compact('logs', 'providers', 'startDate', 'endDate'));
    }

    // --- PAYMENT GATEWAY UTILS ---
    public function updatePaymentGateway(Request $request)
    {
        $request->validate([
            'server_key' => 'required|string',
            'client_key' => 'required|string',
            'merchant_id' => 'nullable|string',
            'mode' => 'required|in:sandbox,production',
        ]);

        $provider = IntegrationProvider::where('code', 'midtrans')->firstOrFail();

        // Config JSON Structure
        $meta = [
            'merchant_id' => $request->merchant_id,
            'environment' => $request->mode
        ];

        IntegrationKey::updateOrCreate(
            ['provider_id' => $provider->integration_provider_id],
            [
                'label' => 'Midtrans ' . ucfirst($request->mode),
                'public_k' => $request->client_key, // Client Key
                'encrypted_k' => Crypt::encryptString($request->server_key), // Server Key Encrypted
                'is_active' => true, // Auto active on save
                'meta_json' => $meta
            ]
        );

        return back()->with('success', 'Konfigurasi Midtrans berhasil diperbarui.');
    }

    /**
     * Test Koneksi Midtrans (Dummy Request)
     */
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
                // Item details optional here, but good practice
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

    /**
     * Update Logistik (RajaOngkir)
     */
    public function updateShippingApi(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'base_url' => 'required|url',
        ]);

        $provider = IntegrationProvider::where('code', 'rajaongkir')->firstOrFail();

        // Update atau Create Key
        IntegrationKey::updateOrCreate(
            ['provider_id' => $provider->integration_provider_id],
            [
                'label' => 'RajaOngkir Key',
                'public_k' => $request->api_key, // Simpan API Key disini
                'is_active' => true,
                'meta_json' => ['base_url' => $request->base_url]
            ]
        );

        return back()->with('success', 'Konfigurasi RajaOngkir berhasil diperbarui.');
    }

    /**
     * Update WhatsApp (Fonnte)
     */
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
                'public_k' => $request->token, // Simpan Token disini
                'is_active' => true,
                'meta_json' => []
            ]
        );

        return back()->with('success', 'Konfigurasi Fonnte berhasil diperbarui.');
    }
}
