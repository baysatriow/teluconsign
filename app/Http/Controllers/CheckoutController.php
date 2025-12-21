<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Address;
use App\Services\MidtransService;
use App\Services\RajaOngkirService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    // ... constructor ...
    protected $midtrans;
    protected $rajaOngkir;

    public function __construct(MidtransService $midtrans, RajaOngkirService $rajaOngkir)
    {
        $this->midtrans = $midtrans;
        $this->rajaOngkir = $rajaOngkir;
    }

    /**
     * Tampilkan Halaman Checkout
     * Menerima input 'selected_items' dari form keranjang
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil ID item yang dipilih dari Request (Form Submit)
        $selectedItemIds = $request->input('selected_items', []);

        // Jika kosong, cek session (mungkin refresh halaman)
        if (empty($selectedItemIds)) {
            $selectedItemIds = session('checkout_item_ids', []);
        }

        if (empty($selectedItemIds)) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu barang untuk checkout.');
        }

        // 2. Simpan ID ke Session agar persist saat refresh atau proses ajax selanjutnya
        session(['checkout_item_ids' => $selectedItemIds]);

        $cart = Cart::where('buyer_id', $user->user_id)->first();

        if (!$cart) {
            return redirect()->route('cart.index');
        }

        // 3. Ambil item HANYA yang dipilih & Valid (milik user ini)
        $cartItems = $cart->items()
            ->whereIn('cart_item_id', $selectedItemIds)
            ->with('product.seller.addresses')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Item tidak valid atau sudah dihapus.');
        }

        // 4. Grouping & Perhitungan
        $groupedItems = $cartItems->groupBy(function ($item) {
            return $item->product->seller_id;
        });

        // Validasi Max 20 Toko
        if ($groupedItems->count() > 20) {
            return redirect()->route('cart.index')->with('error', 'Maksimal checkout dari 20 toko sekaligus.');
        }

        $mainAddress = $user->addresses()->where('is_default', true)->first();

        // Hitung total dari item yang dipilih SAJA
        $subtotal = $cartItems->sum('subtotal');
        $platformFee = 5000;
        $totalPayment = $subtotal + $platformFee;

        // Ambil Kurir dari Database
        $couriers = \App\Models\ShippingCarrier::where('is_enabled', true)->pluck('code')->toArray();

        // Fallback jika kosong
        if(empty($couriers)) {
            $couriers = ['jne', 'pos', 'tiki']; 
        }

        return view('checkout.index', compact('groupedItems', 'mainAddress', 'subtotal', 'platformFee', 'totalPayment', 'couriers'));
    }

    // ... method checkShippingCost dan process tetap sama ...
    /**
     * AJAX: Check Shipping Cost
     */
    public function checkShippingCost(Request $request)
    {
        $request->validate([
            'seller_id' => 'required',
            'courier' => 'required'
        ]);

        $user = Auth::user();
        $buyerAddress = $user->addresses()->where('is_default', true)->first();

        if (!$buyerAddress) {
            return response()->json(['status' => 'error', 'message' => 'Alamat pembeli tidak ditemukan.']);
        }

        // Get Seller Address
        $seller = User::find($request->seller_id);
        $sellerAddress = $seller->addresses()->where('is_default', true)->first() ?? $seller->addresses()->first();

        if (!$sellerAddress) {
            return response()->json(['status' => 'error', 'message' => 'Alamat penjual tidak tersedia.']);
        }

        // Hitung Berat Total (Default 1000g jika tidak ada data berat)
        $selectedItemIds = session('checkout_item_ids', []);
        $cart = Cart::where('buyer_id', $user->user_id)->first();

        $items = $cart->items()
            ->whereIn('cart_item_id', $selectedItemIds)
            ->whereHas('product', function($q) use ($request) {
                $q->where('seller_id', $request->seller_id);
            })->get();

        $totalWeight = 0;
        foreach($items as $item) {
            $weight = $item->product->weight ?? 1000; // Asumsi berat 1kg jika null
            $totalWeight += ($weight * $item->quantity);
        }

        // Integrasi RajaOngkir (Komerce)
        // Prioritaskan location_id (ID Kecamatan/Kelurahan dari Autocomplete)
        
        $origin = $sellerAddress->location_id 
            ?? (is_numeric($sellerAddress->district) ? $sellerAddress->district : (is_numeric($sellerAddress->city) ? $sellerAddress->city : 501));
            
        $destination = $buyerAddress->location_id 
            ?? (is_numeric($buyerAddress->district) ? $buyerAddress->district : (is_numeric($buyerAddress->city) ? $buyerAddress->city : 114));

        $result = $this->rajaOngkir->checkCost(
            origin: $origin,
            originType: 'subdistrict', // Komerce uses subdistrict ID usually
            destination: $destination,
            destinationType: 'subdistrict',
            weight: $totalWeight,
            courier: $request->courier
        );

        if (!$result['status']) {
            return response()->json(['status' => 'error', 'message' => $result['message']]);
        }

        // Log Shipping Cost Check
        \App\Models\WebhookLog::create([
            'provider_code' => 'rajaongkir',
            'event_type' => 'cost_check',
            'related_id' => 'CHK-' . $user->user_id . '-' . time(),
            'payload' => [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $totalWeight,
                'courier' => $request->courier,
                'result' => $result['data'] ?? 'error'
            ],
            'received_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'costs' => $result['data']
        ]);
    }

    public function process(Request $request)
    {
        $user = Auth::user();

        // Ambil ID item dari session
        $selectedItemIds = session('checkout_item_ids', []);

        if (empty($selectedItemIds)) {
             return response()->json(['status' => 'error', 'message' => 'Sesi checkout kadaluarsa.'], 400);
        }

        $cart = Cart::where('buyer_id', $user->user_id)->first();
        $cartItems = $cart->items()->whereIn('cart_item_id', $selectedItemIds)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Item tidak ditemukan.'], 400);
        }

        if (!$user->addresses()->where('is_default', true)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Alamat utama belum diatur.'], 400);
        }

        $address = $user->addresses()->where('is_default', true)->first();

        // Validasi Ongkir: shipping_costs dikirim dari frontend dalam format {seller_id: cost}
        $shippingCosts = $request->input('shipping_costs', []);

        $groupedItems = $cartItems->groupBy(function($item){ return $item->product->seller_id; });

        try {
            DB::beginTransaction();

            $paymentCode = 'PAY-' . time() . '-' . Str::random(5);
            $subtotalAll = $cartItems->sum('subtotal');
            $totalShipping = array_sum($shippingCosts);
            $platformFee = 5000;
            $grossAmount = $subtotalAll + $platformFee + $totalShipping;

            $itemDetails = [];

            // Create Order per Seller
            foreach ($groupedItems as $sellerId => $items) {
                $storeSubtotal = $items->sum('subtotal');
                $shippingCost = $shippingCosts[$sellerId] ?? 0;

                $orderCode = 'ORD-' . strtoupper(Str::random(10));

                $order = Order::create([
                    'code' => $orderCode,
                    'buyer_id' => $user->user_id,
                    'seller_id' => $sellerId,
                    'shipping_address_id' => $address->address_id,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'subtotal_amount' => $storeSubtotal,
                    'shipping_cost' => $shippingCost,
                    'platform_fee' => 0, // Fee ditaruh global atau per order, disini kita anggap global di payment
                    'total_amount' => $storeSubtotal + $shippingCost,
                    'notes' => 'Group Payment: ' . $paymentCode
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'product_id' => $item->product_id,
                        'product_title_snapshot' => $item->product->title,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal
                    ]);

                    // Item untuk Midtrans
                    $itemDetails[] = [
                        'id' => $item->product_id,
                        'price' => (int) $item->unit_price,
                        'quantity' => $item->quantity,
                        'name' => substr($item->product->title, 0, 50)
                    ];
                }

                // Ongkir per Toko untuk Midtrans
                if ($shippingCost > 0) {
                     $itemDetails[] = [
                        'id' => 'SHIP-' . $sellerId,
                        'price' => (int) $shippingCost,
                        'quantity' => 1,
                        'name' => 'Ongkir Toko'
                    ];
                }
            }

            // Fee Aplikasi
            $itemDetails[] = [
                'id' => 'FEE-PLATFORM',
                'price' => (int) $platformFee,
                'quantity' => 1,
                'name' => 'Biaya Layanan'
            ];

            // Hapus Item yang Dibeli dari Keranjang (Bukan semua isi keranjang)
            // Menggunakan whereIn cart_item_id
            $cart->items()->whereIn('cart_item_id', $selectedItemIds)->delete();
            $cart->calculateTotal(); // Update total sisa keranjang

            DB::commit();

            // Request Snap Token
            $params = [
                'transaction_details' => [
                    'order_id' => $paymentCode,
                    'gross_amount' => (int) $grossAmount,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->profile->phone ?? '',
                    'shipping_address' => [
                        'first_name' => $user->name,
                        'address' => $address->getFullAddress(),
                        'city' => $address->city,
                        'postal_code' => $address->postal_code,
                        'phone' => $address->phone,
                        'country_code' => 'IDN'
                    ]
                ]
            ];

            $snapToken = $this->midtrans->createSnapToken($params);

            if (!$snapToken) {
                return response()->json(['status' => 'error', 'message' => 'Gagal koneksi ke Payment Gateway.'], 500);
            }

            // Return Token (handle array/string return type from service)
            $tokenString = is_array($snapToken) ? ($snapToken['token'] ?? '') : $snapToken;

            // Log Checkout Payment Request
            \App\Models\WebhookLog::create([
                'provider_code' => 'midtrans',
                'event_type' => 'token_request',
                'related_id' => $paymentCode,
                'payload' => [
                    'order_id' => $paymentCode,
                    'gross_amount' => $grossAmount,
                    'customer' => $user->email,
                    'result_token' => $tokenString
                ],
                'received_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'snap_token' => $tokenString,
                'redirect_url' => route('orders.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
