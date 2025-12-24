<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Product;
use App\Models\WalletLedger;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function midtransNotification(Request $request)
    {
        try {
            WebhookLog::create([
                'provider_code' => 'midtrans',
                'event_type' => 'payment_notification',
                'related_id' => $request->input('order_id'),
                'payload' => $request->all(),
                'received_at' => now()
            ]);

            $orderId = $request->input('order_id'); 
            $transactionStatus = $request->input('transaction_status');
            $fraudStatus = $request->input('fraud_status');
            $transactionId = $request->input('transaction_id');

            $payment = Payment::where('provider_order_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Webhook: Payment not found for order_id: ' . $orderId);
                return response()->json(['status' => 'payment_not_found'], 404);
            }

            $status = $this->mapMidtransStatus($transactionStatus, $fraudStatus);

            DB::beginTransaction();

            $payment->update([
                'status' => $status,
                'provider_txn_id' => $transactionId,
                'raw_response' => array_merge($payment->raw_response ?? [], $request->all()),
                'paid_at' => ($status === 'settlement') ? now() : $payment->paid_at
            ]);

            $orders = Order::where('notes', 'LIKE', "%{$orderId}%")->get();

            if ($status === 'settlement') {
                foreach ($orders as $order) {
                    $order->update([
                        'payment_status' => 'settlement',
                        'status' => 'paid'
                    ]);

                    foreach ($order->items as $item) {
                        $product = Product::find($item->product_id);
                        
                        if ($product) {
                            if ($product->stock >= $item->quantity) {
                                $product->decrement('stock', $item->quantity);
                                
                                Log::info('Stock reduced', [
                                    'product_id' => $product->product_id,
                                    'quantity' => $item->quantity,
                                    'remaining_stock' => $product->stock - $item->quantity
                                ]);
                            } else {
                                Log::warning('Insufficient stock during payment', [
                                    'product_id' => $product->product_id,
                                    'required' => $item->quantity,
                                    'available' => $product->stock
                                ]);
                            }
                        }
                    }

                    $this->creditSellerWallet($order);
                }

            } elseif (in_array($status, ['cancel', 'expire', 'deny'])) {
                foreach ($orders as $order) {
                    $order->update([
                        'payment_status' => $status,
                        'status' => 'cancelled'
                    ]);
                }

            } elseif ($status === 'refund') {
                foreach ($orders as $order) {
                    $order->update([
                        'payment_status' => 'refund',
                        'status' => 'refunded'
                    ]);

                    foreach ($order->items as $item) {
                        Product::where('product_id', $item->product_id)
                            ->increment('stock', $item->quantity);
                        
                        Log::info('Stock restored (refund)', [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity
                        ]);
                    }

                    $this->debitSellerWallet($order);
                }
            }

            DB::commit();

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook processing failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function mapMidtransStatus(string $transactionStatus, ?string $fraudStatus = null): string
    {
        if ($transactionStatus === 'capture') {
            return ($fraudStatus === 'accept') ? 'settlement' : 'pending';
        }

        return match($transactionStatus) {
            'settlement' => 'settlement',
            'pending' => 'pending',
            'deny' => 'deny',
            'expire' => 'expire',
            'cancel' => 'cancel',
            'refund', 'partial_refund' => 'refund',
            'chargeback' => 'chargeback',
            default => 'pending'
        };
    }

    private function creditSellerWallet(Order $order)
    {
        $existing = WalletLedger::where('source_type', 'order')
            ->where('source_id', $order->order_id)
            ->where('direction', 'credit')
            ->exists();

        if ($existing) {
            return;
        }

        $creditAmount = $order->seller_earnings;
        $lastBalance = WalletLedger::where('user_id', $order->seller_id)
            ->orderBy('wallet_ledger_id', 'desc')
            ->value('balance_after') ?? 0;

        WalletLedger::create([
            'user_id' => $order->seller_id,
            'direction' => 'credit',
            'source_type' => 'order',
            'source_id' => $order->order_id,
            'amount' => $creditAmount,
            'balance_after' => $lastBalance + $creditAmount,
            'memo' => 'Pendapatan Pesanan #' . $order->code,
            'posted_at' => now()
        ]);

        Log::info('Wallet credited', [
            'order_id' => $order->order_id,
            'seller_id' => $order->seller_id,
            'amount' => $creditAmount
        ]);
    }

    private function debitSellerWallet(Order $order)
    {
        $creditEntry = WalletLedger::where('source_type', 'order')
            ->where('source_id', $order->order_id)
            ->where('direction', 'credit')
            ->first();

        if (!$creditEntry) {
            return; 
        }

        $existing = WalletLedger::where('source_type', 'order_refund')
            ->where('source_id', $order->order_id)
            ->where('direction', 'debit')
            ->exists();

        if ($existing) {
            return; 
        }

        $debitAmount = $order->seller_earnings;
        $lastBalance = WalletLedger::where('user_id', $order->seller_id)
            ->orderBy('wallet_ledger_id', 'desc')
            ->value('balance_after') ?? 0;

        WalletLedger::create([
            'user_id' => $order->seller_id,
            'direction' => 'debit',
            'source_type' => 'order_refund',
            'source_id' => $order->order_id,
            'amount' => $debitAmount,
            'balance_after' => $lastBalance - $debitAmount,
            'memo' => 'Pengembalian Pendapatan #' . $order->code . ' (Refund)',
            'posted_at' => now()
        ]);

        Log::info('Wallet debited (refund)', [
            'order_id' => $order->order_id,
            'seller_id' => $order->seller_id,
            'amount' => $debitAmount
        ]);
    }
}