<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $midtrans;
    protected $rajaOngkir;

    public function __construct(MidtransService $midtrans, RajaOngkirService $rajaOngkir)
    {
        $this->midtrans = $midtrans;
        $this->rajaOngkir = $rajaOngkir;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $selectedItemIds = $request->input('selected_items', []);

        if (empty($selectedItemIds)) {
            $selectedItemIds = session('checkout_item_ids', []);
        }

        if (empty($selectedItemIds)) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu barang untuk checkout.');
        }

        session(['checkout_item_ids' => $selectedItemIds]);

        $cart = Cart::where('buyer_id', $user->user_id)->first();

        if (!$cart) {
            return redirect()->route('cart.index');
        }

        $cartItems = $cart->items()
            ->whereIn('cart_item_id', $selectedItemIds)
            ->with('product.seller.addresses')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Item tidak valid atau sudah dihapus.');
        }

        $groupedItems = $cartItems->groupBy(function ($item) {
            return $item->product->seller_id;
        });

        if ($groupedItems->count() > 20) {
            return redirect()->route('cart.index')->with('error', 'Maksimal checkout dari 20 toko sekaligus.');
        }

        $mainAddress = $user->addresses()->where('is_default', true)->first();
        
        if (!$mainAddress) {
            return redirect()->route('profile.index')
                ->with('error', 'Silakan tambahkan alamat pengiriman terlebih dahulu.');
        }

        $subtotal = $cartItems->sum('subtotal');
        $platformFee = 2500;
        $totalPayment = $subtotal + $platformFee;

        $couriers = \App\Models\ShippingCarrier::where('is_enabled', true)->pluck('code')->toArray();

        if(empty($couriers)) {
            $couriers = ['jne', 'pos', 'tiki']; 
        }

        return view('checkout.index', compact('groupedItems', 'mainAddress', 'subtotal', 'platformFee', 'totalPayment', 'couriers'));
    }

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

        $seller = User::find($request->seller_id);
        $sellerAddress = $seller->addresses()->where('is_default', true)->first() ?? $seller->addresses()->first();

        if (!$sellerAddress) {
            return response()->json(['status' => 'error', 'message' => 'Alamat penjual tidak tersedia.']);
        }

        $selectedItemIds = session('checkout_item_ids', []);
        $cart = Cart::where('buyer_id', $user->user_id)->first();

        $items = $cart->items()
            ->whereIn('cart_item_id', $selectedItemIds)
            ->whereHas('product', function($q) use ($request) {
                $q->where('seller_id', $request->seller_id);
            })->get();

        $totalWeight = 0;
        foreach($items as $item) {
            $weight = $item->product->weight ?? 1000; 
            $totalWeight += ($weight * $item->quantity);
        }

        if ($totalWeight <= 0) {
            $totalWeight = 1000;
        }

        $origin = $sellerAddress->location_id 
            ?? (is_numeric($sellerAddress->district) ? $sellerAddress->district : (is_numeric($sellerAddress->city) ? $sellerAddress->city : 501));
            
        $destination = $buyerAddress->location_id 
            ?? (is_numeric($buyerAddress->district) ? $buyerAddress->district : (is_numeric($buyerAddress->city) ? $buyerAddress->city : 114));

        $result = $this->rajaOngkir->checkCost(
            origin: $origin,
            originType: 'subdistrict', 
            destination: $destination,
            destinationType: 'subdistrict',
            weight: $totalWeight,
            courier: $request->courier
        );

        if (!$result['status']) {
            return response()->json(['status' => 'error', 'message' => $result['message']]);
        }

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
            $platformFee = 2500; 
            $grossAmount = $subtotalAll + $platformFee + $totalShipping;

            $itemDetails = [];

            $orderCount = $groupedItems->count();
            $buyerFeeTotal = 2500;
            $buyerFeePerOrder = floor($buyerFeeTotal / $orderCount);
            $buyerFeeRemainder = $buyerFeeTotal % $orderCount;
            
            $loopIndex = 0;

            foreach ($groupedItems as $sellerId => $items) {
                $loopIndex++;
                $seller = $items->first()->product->seller; 
                $sellerShipping = $shippingData[$sellerId] ?? [];
                $shippingCost = $sellerShipping['cost'] ?? 0;
                $serviceCode = $sellerShipping['service'] ?? 'REG'; 
                $courierCode = $sellerShipping['courier'] ?? 'jne';
                $etd = $sellerShipping['etd'] ?? '';
                $description = $sellerShipping['description'] ?? '';

                $storeSubtotal = $items->sum('subtotal');
                $orderCode = 'ORD-' . strtoupper(Str::random(10));

                $currentBuyerFee = $buyerFeePerOrder;
                if ($loopIndex === 1) {
                    $currentBuyerFee += $buyerFeeRemainder;
                }
                
                $currentSellerFee = 2500; 
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
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'product_id' => $item->product_id,
                        'product_title_snapshot' => $item->product->title,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal
                    ]);

                    $item->product->decrement('stock', $item->quantity);

                    $itemDetails[] = [
                        'id' => $item->product_id,
                        'price' => (int) $item->unit_price,
                        'quantity' => $item->quantity,
                        'name' => substr($item->product->title, 0, 50)
                    ];
                }

                $carrier = \App\Models\ShippingCarrier::where('code', $courierCode)->first();
                $carrierId = $carrier ? $carrier->shipping_carrier_id : 1; 

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

                if ($shippingCost > 0) {
                     $itemDetails[] = [
                        'id' => 'SHIP-' . $sellerId,
                        'price' => (int) $shippingCost,
                        'quantity' => 1,
                        'name' => 'Ongkir ' . strtoupper($courierCode) . ' - ' . $seller->name
                    ];
                }
            }

            $itemDetails[] = [
                'id' => 'FEE-PLATFORM',
                'price' => (int) $platformFee,
                'quantity' => 1,
                'name' => 'Biaya Layanan'
            ];

            $midtransProvider = \App\Models\IntegrationProvider::where('code', 'midtrans')->first();
            
            if (!$midtransProvider) {
                $midtransProvider = \App\Models\IntegrationProvider::create([
                    'code' => 'midtrans',
                    'name' => 'Midtrans Payment Gateway'
                ]);
            }

            $firstOrderRecord = Order::where('notes', 'LIKE', "%{$paymentCode}%")->first();

            $payment = \App\Models\Payment::create([
                'order_id' => $firstOrderRecord->order_id,
                'provider_id' => $midtransProvider->integration_provider_id,
                'method_code' => null,
                'amount' => $grossAmount,
                'currency' => 'IDR',
                'status' => 'pending',
                'provider_order_id' => $paymentCode,
                'raw_response' => ['items' => $itemDetails] 
            ]);

            $cart->items()->whereIn('cart_item_id', $selectedItemIds)->delete();
            $cart->calculateTotal();

            DB::commit();

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