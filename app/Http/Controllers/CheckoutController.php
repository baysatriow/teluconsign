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
        
        // Redirect to address page if no default address
        if (!$mainAddress) {
            return redirect()->route('profile.index')
                ->with('error', 'Silakan tambahkan alamat pengiriman terlebih dahulu.');
        }

        // Hitung total dari item yang dipilih SAJA
        $subtotal = $cartItems->sum('subtotal');
        // Calculate Platform Fee (buyer pays)
        $platformFee = 2500;
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

        // Fix: Ensure weight is never 0 to avoid RajaOngkir error
        if ($totalWeight <= 0) {
            $totalWeight = 1000;
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

        // Validasi Ongkir: shipping_data dikirim dari frontend dalam format {seller_id: {cost, service, ...}}
        $shippingData = $request->input('shipping_data', []);

        $groupedItems = $cartItems->groupBy(function($item){ return $item->product->seller_id; });

        try {
            DB::beginTransaction();

            $paymentCode = 'PAY-' . time() . '-' . Str::random(5);
            $subtotalAll = $cartItems->sum('subtotal');
            $totalShipping = 0;
            foreach ($shippingData as $data) {
                $totalShipping += $data['cost'] ?? 0;
            }
            $platformFee = 2500; // Buyer pays 2,500
            $grossAmount = $subtotalAll + $platformFee + $totalShipping;

            $itemDetails = [];

            // Create Order per Seller
            $orderCount = $groupedItems->count();
            $buyerFeeTotal = 2500;
            $buyerFeePerOrder = floor($buyerFeeTotal / $orderCount);
            $buyerFeeRemainder = $buyerFeeTotal % $orderCount;
            
            $loopIndex = 0;

            foreach ($groupedItems as $sellerId => $items) {
                $loopIndex++;
                $seller = $items->first()->product->seller; // Define Seller to avoid undefined variable error
                $sellerShipping = $shippingData[$sellerId] ?? [];
                $shippingCost = $sellerShipping['cost'] ?? 0;
                $serviceCode = $sellerShipping['service'] ?? 'REG'; // Fallback
                $courierCode = $sellerShipping['courier'] ?? 'jne';
                $etd = $sellerShipping['etd'] ?? '';
                $description = $sellerShipping['description'] ?? '';

                $storeSubtotal = $items->sum('subtotal');
                $orderCode = 'ORD-' . strtoupper(Str::random(10));

                // Fee Logic
                $currentBuyerFee = $buyerFeePerOrder;
                if ($loopIndex === 1) {
                    $currentBuyerFee += $buyerFeeRemainder;
                }
                
                $currentSellerFee = 2500; // Fixed per seller order
                $sellerEarnings = $storeSubtotal - $currentSellerFee;

                $order = Order::create([
                    'code' => $orderCode,
                    'buyer_id' => $user->user_id,
                    'seller_id' => $sellerId,
                    'shipping_address_id' => $address->address_id,
                    'shipping_address_snapshot' => $address->toArray(),
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'subtotal_amount' => $storeSubtotal,
                    'shipping_cost' => $shippingCost,
                    'platform_fee_buyer' => $currentBuyerFee,
                    'platform_fee_seller' => $currentSellerFee,
                    'seller_earnings' => $sellerEarnings,
                    'total_amount' => $storeSubtotal + $shippingCost + $currentBuyerFee, 
                    'notes' => 'Group Payment: ' . $paymentCode
                ]);

                foreach ($items as $item) {
                     // 1. Create Order Item
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'product_id' => $item->product_id,
                        'product_title_snapshot' => $item->product->title,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal
                    ]);

                     // 2. Reduce Stock
                    $item->product->decrement('stock', $item->quantity);

                    // Item untuk Midtrans
                    $itemDetails[] = [
                        'id' => $item->product_id,
                        'price' => (int) $item->unit_price,
                        'quantity' => $item->quantity,
                        'name' => substr($item->product->title, 0, 50)
                    ];
                }

                // Get Carrier ID
                $carrier = \App\Models\ShippingCarrier::where('code', $courierCode)->first();
                $carrierId = $carrier ? $carrier->shipping_carrier_id : 1; // Default fallback

                // Create Shipment record
                \App\Models\Shipment::create([
                    'order_id' => $order->order_id,
                    'carrier_id' => $carrierId,
                    'service_code' => $serviceCode,
                    'status' => 'pending',
                    'cost' => $shippingCost,
                    'metadata' => [
                        'etd' => $etd,
                        'description' => $description,
                        'courier_code' => $courierCode
                    ]
                ]);

                // Ongkir per Toko untuk Midtrans
                if ($shippingCost > 0) {
                     $itemDetails[] = [
                        'id' => 'SHIP-' . $sellerId,
                        'price' => (int) $shippingCost,
                        'quantity' => 1,
                        'name' => 'Ongkir ' . strtoupper($courierCode) . ' - ' . $seller->name
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

            // Create Payment record
            // Note: gateway_id references integration_providers
            $midtransProvider = \App\Models\IntegrationProvider::where('code', 'midtrans')->first();
            
            if (!$midtransProvider) {
                // Create if not exists (first time setup)
                $midtransProvider = \App\Models\IntegrationProvider::create([
                    'code' => 'midtrans',
                    'name' => 'Midtrans Payment Gateway'
                ]);
            }

            // Get first order for payment relation
            $firstOrderRecord = Order::where('notes', 'LIKE', "%{$paymentCode}%")->first();

            $payment = \App\Models\Payment::create([
                'order_id' => $firstOrderRecord->order_id,
                'provider_id' => $midtransProvider->integration_provider_id,  // Changed from gateway_id
                'method_code' => null, // User akan pilih di payment page
                'amount' => $grossAmount,
                'currency' => 'IDR',
                'status' => 'pending',
                'provider_order_id' => $paymentCode,
                'raw_response' => ['items' => $itemDetails] // Simpan item details untuk reference
            ]);

            // Hapus Item yang Dibeli dari Keranjang
            $cart->items()->whereIn('cart_item_id', $selectedItemIds)->delete();
            $cart->calculateTotal();

            DB::commit();

            // Log Checkout
            \App\Models\WebhookLog::create([
                'provider_code' => 'midtrans',
                'event_type' => 'checkout_completed',
                'related_id' => $paymentCode,
                'payload' => [
                    'payment_id' => $payment->payment_id,
                    'order_ids' => Order::where('notes', 'LIKE', "%{$paymentCode}%")->pluck('order_id'),
                    'gross_amount' => $grossAmount
                ],
                'received_at' => now()
            ]);

            // Redirect ke halaman payment custom (bukan Snap)
            return response()->json([
                'status' => 'success',
                'redirect_url' => route('payment.show', $payment->payment_id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
