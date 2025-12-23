<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    /**
     * Show custom payment page
     */
    public function show(Payment $payment)
    {
        // Auth check
        $order = $payment->order;
        
        if (!$order || $order->buyer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to payment');
        }
        
        // If already paid, redirect to success page
        if (in_array($payment->status, ['settlement', 'capture'])) {
            return redirect()->route('orders.show', $order->order_id)
                ->with('success', 'Pembayaran sudah berhasil!');
        }
        
        // Get available payment methods
        $methods = $this->getAvailableMethods();
        
        // Get all orders with same payment code (grouped payment)
        $relatedOrders = Order::where('notes', 'LIKE', "%{$payment->provider_order_id}%")->get();
        
        return view('payment.show', compact('payment', 'order', 'methods', 'relatedOrders'));
    }

    /**
     * Create charge via Midtrans Core API
     */
    public function createCharge(Request $request, Payment $payment)
    {
        $request->validate([
            'method' => 'required|string'
        ]);

        $methodCode = $request->input('method');
        
        // Update payment method
        $payment->update(['method_code' => $methodCode]);
        
        try {
            // Prepare charge parameters based on method
            $params = $this->prepareChargeParams($payment, $methodCode);
            
            // Call Midtrans Core API
            $result = $this->midtrans->createCharge($params);
            
            // Save response
            $payment->update([
                'provider_txn_id' => $result['transaction_id'] ?? null,
                'raw_response' => $result
            ]);
            
            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Payment charge failed', [
                'payment_id' => $payment->payment_id,
                'method' => $methodCode,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status (for polling)
     */
    public function checkStatus(Payment $payment)
    {
        // Auth check
        $order = $payment->order;
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'status' => $payment->status,
            'paid_at' => $payment->paid_at ? $payment->paid_at->toISOString() : null
        ]);
    }

    /**
     * Get available payment methods
     */
    private function getAvailableMethods()
    {
        return [
            [
                'code' => 'qris',
                'name' => 'QRIS',
                'description' => 'Scan QR Code dengan aplikasi pembayaran favorit Anda',
                'icon' => '/images/payment/qris.png',
                'category' => 'qr'
            ],
            [
                'code' => 'gopay',
                'name' => 'GoPay',
                'description' => 'Bayar dengan saldo GoPay',
                'icon' => '/images/payment/gopay.png',
                'category' => 'ewallet'
            ],
            [
                'code' => 'shopeepay',
                'name' => 'ShopeePay',
                'description' => 'Bayar dengan saldo ShopeePay',
                'icon' => '/images/payment/shopeepay.png',
                'category' => 'ewallet'
            ],
            [
                'code' => 'bca_va',
                'name' => 'BCA Virtual Account',
                'description' => 'Transfer ke Virtual Account BCA',
                'icon' => '/images/payment/bca.png',
                'category' => 'bank_transfer'
            ],
            [
                'code' => 'bni_va',
                'name' => 'BNI Virtual Account',
                'description' => 'Transfer ke Virtual Account BNI',
                'icon' => '/images/payment/bni.png',
                'category' => 'bank_transfer'
            ],
            [
                'code' => 'bri_va',
                'name' => 'BRI Virtual Account',
                'description' => 'Transfer ke Virtual Account BRI',
                'icon' => '/images/payment/bri.png',
                'category' => 'bank_transfer'
            ],
            [
                'code' => 'mandiri_va',
                'name' => 'Mandiri Virtual Account',
                'description' => 'Transfer ke Virtual Account Mandiri',
                'icon' => '/images/payment/mandiri.png',
                'category' => 'bank_transfer'
            ],
            [
                'code' => 'permata_va',
                'name' => 'Permata Virtual Account',
                'description' => 'Transfer ke Virtual Account Permata',
                'icon' => '/images/payment/permata.png',
                'category' => 'bank_transfer'
            ]
        ];
    }

    /**
     * Prepare charge parameters for Midtrans
     */
    private function prepareChargeParams(Payment $payment, string $methodCode)
    {
        $order = $payment->order;
        
        $baseParams = [
            'transaction_details' => [
                'order_id' => $payment->provider_order_id,
                'gross_amount' => (int) $payment->amount
            ],
            'customer_details' => [
                'first_name' => $order->buyer->name,
                'email' => $order->buyer->email,
                'phone' => $order->buyer->profile->phone ?? ''
            ]
        ];

        // Add payment type specific params
        if ($methodCode === 'qris') {
            $baseParams['payment_type'] = 'qris';
            
        } elseif (str_ends_with($methodCode, '_va')) {
            $bank = str_replace('_va', '', $methodCode);
            $baseParams['payment_type'] = 'bank_transfer';
            $baseParams['bank_transfer'] = ['bank' => $bank];
            
        } elseif (in_array($methodCode, ['gopay', 'shopeepay'])) {
            $baseParams['payment_type'] = $methodCode;
        }

        return $baseParams;
    }
}
