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
use App\Models\PaymentGateway;
use App\Models\IntegrationProvider;
use App\Models\IntegrationKey;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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

        // Jika statusnya suspended -> kembalikan ke active
        // Jika statusnya active/lainnya -> ubah ke suspended

        if ($product->status === ProductStatus::Suspended) {
            $product->status = ProductStatus::Active;
            $msg = 'Produk berhasil diaktifkan kembali.';
        } else {
            $product->status = ProductStatus::Suspended;
            $msg = 'Produk berhasil disuspend (ditangguhkan).';
        }

        $product->save();

        return back()->with('success', $msg);
    }

/**
     * Manajemen Pengguna (Admin & User Biasa)
     */
    public function users()
    {
        // 1. Ambil Semua Admin
        $admins = User::where('role', 'admin')->latest()->get();

        // 2. Ambil Semua User Biasa (DataTables akan handle search/paging di frontend jika data < 10k)
        // Jika data sangat besar (>10k), sebaiknya gunakan Server-Side DataTables (yajra/laravel-datatables)
        // Untuk saat ini kita load semua user biasa agar fitur search JS DataTables jalan full
        $users = User::where('role', '!=', 'admin')->latest()->get();

        // Statistik
        $stats = [
            'total_users' => $users->count(),
            'total_admins' => $admins->count(),
            'active_users' => $users->where('status', 'active')->count(),
            'suspended_users' => $users->where('status', 'suspended')->count(),
        ];

        return view('admin.users.index', compact('admins', 'users', 'stats'));
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
    public function payouts()
    {
        // Ambil semua payout request dengan relasi seller dan bank
        $payouts = PayoutRequest::with(['seller', 'bankAccount'])
            ->latest('requested_at')
            ->get();

        $stats = [
            'total_requests' => $payouts->count(),
            'pending_requests' => $payouts->where('status', 'requested')->count(),
            'approved_requests' => $payouts->whereIn('status', ['approved', 'paid'])->count(),
            'rejected_requests' => $payouts->where('status', 'rejected')->count(),
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
     * Halaman Integrasi API (Payment Gateway, Shipping, dll)
     */
    public function integrations()
    {
        // 1. Midtrans (PaymentGateway Model)
        $midtrans = PaymentGateway::firstOrCreate(
            ['code' => 'midtrans'],
            ['name' => 'Midtrans', 'is_enabled' => false, 'config_json' => []]
        );

        // 2. RajaOngkir (IntegrationProvider Model)
        $rajaongkirProvider = IntegrationProvider::firstOrCreate(
            ['code' => 'rajaongkir'],
            ['name' => 'RajaOngkir']
        );
        // Ambil key aktif pertama atau buat baru dummy
        $rajaongkir = IntegrationKey::where('provider_id', $rajaongkirProvider->integration_provider_id)->first();
        if(!$rajaongkir) {
            $rajaongkir = new IntegrationKey(); // Object kosong agar view tidak error
            $rajaongkir->meta_json = [];
        }

        // 3. Fonnte (IntegrationProvider Model)
        $fonnteProvider = IntegrationProvider::firstOrCreate(
            ['code' => 'whatsapp'], // Sesuai dengan seed: 'whatsapp'
            ['name' => 'WhatsApp (Fonnte)']
        );
        $fonnte = IntegrationKey::where('provider_id', $fonnteProvider->integration_provider_id)->first();
        if(!$fonnte) {
            $fonnte = new IntegrationKey();
        }

        return view('admin.integrations.index', compact('midtrans', 'rajaongkir', 'fonnte'));
    }

    /**
     * Update Payment Gateway (Midtrans)
     */
    public function updatePaymentGateway(Request $request)
    {
        $request->validate([
            'server_key' => 'required|string',
            'client_key' => 'required|string',
            'merchant_id' => 'nullable|string',
            'mode' => 'required|in:sandbox,production',
        ]);

        $gateway = PaymentGateway::where('code', 'midtrans')->firstOrFail();
        $config = [
            'server_key' => $request->server_key,
            'client_key' => $request->client_key,
            'merchant_id' => $request->merchant_id,
            'mode' => $request->mode,
            'is_production' => $request->mode === 'production'
        ];

        $gateway->update([
            'config_json' => $config,
            'is_enabled' => $request->has('is_enabled')
        ]);

        return back()->with('success', 'Konfigurasi Midtrans berhasil diperbarui.');
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
