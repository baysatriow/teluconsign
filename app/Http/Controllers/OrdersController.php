<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    /**
     * Tampilkan Daftar Pesanan dengan Statistik
     */
    public function index()
    {
        $userId = auth()->id();
        
        // Calculate Statistics
        $stats = [
            'total' => Order::where('buyer_id', $userId)->count(),
            'pending_payment' => Order::where('buyer_id', $userId)
                ->where('payment_status', 'pending')
                ->count(),
            'paid' => Order::where('buyer_id', $userId)
                ->whereIn('payment_status', ['settlement', 'paid'])
                ->count(),
            'completed' => Order::where('buyer_id', $userId)
                ->where('status', 'completed')
                ->count(),
        ];
        
        // Get Orders with Pagination
        $orders = Order::where('buyer_id', $userId)
                    ->with(['items.product', 'seller'])
                    ->latest()
                    ->paginate(10);

        return view('orders.index', compact('orders', 'stats'));
    }

    /**
     * Bayar Pesanan (Individual)
     * Regenerate Snap Token untuk order spesifik
     */
    public function pay($id)
    {
        $order = Order::where('buyer_id', auth()->id())
            ->where('order_id', $id)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        try {
            // Gunakan Code Order (ORD-...) sebagai Transaction ID baru
            // Jika sebelumnya Group Payment (PAY-...), kita abaikan dan buat transaksi baru per order ini.
            
            // Item Details
            $itemDetails = [];
            foreach($order->items as $item) {
                $itemDetails[] = [
                    'id' => $item->product_id,
                    'price' => (int) $item->unit_price,
                    'quantity' => $item->quantity,
                    'name' => substr($item->product_title, 0, 50)
                ];
            }

            // Ongkir
            if($order->shipping_cost > 0) {
                 $itemDetails[] = [
                    'id' => 'SHIP-' . $order->seller_id,
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim'
                ];
            }
            
            // Fee Platform (Jika ada di order ini, atau 0)
            if($order->platform_fee > 0) {
                 $itemDetails[] = [
                    'id' => 'FEE-PLATFORM',
                    'price' => (int) $order->platform_fee,
                    'quantity' => 1,
                    'name' => 'Biaya Layanan'
                ];
            }

            // --- MODE DEBUG / DUMMY ---
            // Menggunakan data dummy hardcoded untuk test koneksi Midtrans
            $dummyId = 'TEST-' . time();
            $params = [
                'transaction_details' => [
                    'order_id' => $dummyId,
                    'gross_amount' => 10000,
                ],
                'item_details' => [
                    [
                        'id' => 'DUMMY-1',
                        'price' => 10000,
                        'quantity' => 1,
                        'name' => 'Test Item Midtrans'
                    ]
                ],
                'customer_details' => [
                    'first_name' => 'Tester',
                    'email' => 'test@example.com',
                    'phone' => '08123456789',
                ]
            ];
            
            $snapToken = $this->midtrans->createSnapToken($params);
            // --- END DUMMY ---

            // $snapToken = $this->midtrans->createSnapToken($params);
            
            // Return Token
            $tokenString = is_array($snapToken) ? ($snapToken['token'] ?? '') : $snapToken;

            // Log Order Payment Request
            \App\Models\WebhookLog::create([
                'provider_code' => 'midtrans',
                'event_type' => 'token_request_retry',
                'related_id' => $dummyId ?? $order->code, // Use actual order code in production logic
                'payload' => [
                    'order_id' => $dummyId ?? $order->code,
                    'gross_amount' => $params['transaction_details']['gross_amount'] ?? 0,
                    'result_token' => $tokenString
                ],
                'received_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'snap_token' => $tokenString
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
