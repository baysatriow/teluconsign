<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    public function show(Payment $payment)
    {
        $order = $payment->order;
        
        $isBuyer = $order && (int)$order->buyer_id === (int)Auth::id();
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$order || (!$isBuyer && !$isAdmin)) {
            abort(403, 'Unauthorized access to payment');
        }
        
        if (in_array($payment->status, ['settlement', 'capture'])) {
            return redirect()->route('orders.show', $order->order_id)
                ->with('success', 'Pembayaran sudah berhasil!');
        }
        
        $methods = $this->getAvailableMethods();
        
        $relatedOrders = Order::where('notes', 'LIKE', "%{$payment->provider_order_id}%")->get();
        
        $isSandbox = false;
        try {
            $provider = \Illuminate\Support\Facades\DB::table('integration_providers')
                ->where('code', 'midtrans')
                ->first();
            
            if ($provider) {
                $midtransKey = \App\Models\IntegrationKey::where('provider_id', $provider->integration_provider_id)
                    ->where('is_active', true)
                    ->first();
                    
                if ($midtransKey && isset($midtransKey->meta_json['environment'])) {
                    $isSandbox = $midtransKey->meta_json['environment'] === 'sandbox';
                }
            }
        } catch (\Exception $e) {
        }
        
        return view('payment.show', compact('payment', 'order', 'methods', 'relatedOrders', 'isSandbox'));
    }

    public function createCharge(Request $request, Payment $payment)
    {
        $request->validate([
            'method' => 'required|string'
        ]);

        $methodCode = $request->input('method');
        
        $payment->update(['method_code' => $methodCode]);
        
        try {
            $params = $this->prepareChargeParams($payment, $methodCode);
            
            $result = $this->midtrans->createCharge($params);
            
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

    public function checkStatus(Payment $payment)
    {
        $order = $payment->order;
        
        $isBuyer = (int)$order->buyer_id === (int)Auth::id();
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isBuyer && !$isAdmin) {
            abort(403);
        }

        return response()->json([
            'status' => $payment->status,
            'paid_at' => $payment->paid_at ? $payment->paid_at->toISOString() : null
        ]);
    }

    public function demoPayment(Payment $payment)
    {
        $order = $payment->order;
        
        $isBuyer = (int)$order->buyer_id === (int)Auth::id();
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isBuyer && !$isAdmin) {
            abort(403);
        }

        try {
            $provider = \Illuminate\Support\Facades\DB::table('integration_providers')
                ->where('code', 'midtrans')
                ->first();
                
            $isSandbox = false;
            if ($provider) {
                $midtransKey = \App\Models\IntegrationKey::where('provider_id', $provider->integration_provider_id)
                    ->where('is_active', true)
                    ->first();
                    
                if ($midtransKey && isset($midtransKey->meta_json['environment'])) {
                    $isSandbox = $midtransKey->meta_json['environment'] === 'sandbox';
                }
            }
            
            if (!$isSandbox) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Demo payment only available in sandbox mode'
                ], 403);
            }

            $result = $this->midtrans->simulatePaymentSuccess($payment->provider_order_id);

            $payment->update([
                'status' => 'settlement',
                'paid_at' => now(),
                'provider_txn_id' => $payment->provider_txn_id ?? 'demo-'.rand(1000,9999)
            ]);

            $relatedOrders = \App\Models\Order::where('notes', 'LIKE', "%{$payment->provider_order_id}%")->get();
            foreach ($relatedOrders as $relatedOrder) {
                if (method_exists($relatedOrder, 'confirmPayment')) {
                    $relatedOrder->confirmPayment();
                } else {
                    $relatedOrder->update([
                        'payment_status' => 'settlement',
                        'status' => 'paid'
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment simulation successful'
            ]);

        } catch (\Exception $e) {
            Log::error('Demo payment failed', [
                'payment_id' => $payment->payment_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Simulation error: ' . $e->getMessage()
            ], 500);
        }
    }

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