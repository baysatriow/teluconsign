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
    /**
     * Handle Midtrans payment notification webhook
     */
    public function midtransNotification(Request $request)
    {
        try {
            // Log incoming webhook
            WebhookLog::create([
                'provider_code' => 'midtrans',
                'event_type' => 'payment_notification',
                'related_id' => $request->input('order_id'),
                'payload' => $request->all(),
                'received_at' => now()
            ]);

            // Get transaction data
            $orderId = $request->input('order_id'); // This is our payment_code (PAY-...)
            $transactionStatus = $request->input('transaction_status');
            $fraudStatus = $request->input('fraud_status');
            $transactionId = $request->input('transaction_id');

            // Find payment by provider_order_id
            $payment = Payment::where('provider_order_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Webhook: Payment not found for order_id: ' . $orderId);
                return response()->json(['status' => 'payment_not_found'], 404);
            }

            // Determine final status
            $status = $this->mapMidtransStatus($transactionStatus, $fraudStatus);

            DB::beginTransaction();

            // Update payment record
            $payment->update([
                'status' => $status,
                'provider_txn_id' => $transactionId,
                'raw_response' => array_merge($payment->raw_response ?? [], $request->all()),
                'paid_at' => ($status === 'settlement') ? now() : $payment->paid_at
            ]);

            // Get all related orders (grouped payment)
            $orders = Order::where('notes', 'LIKE', "%{$orderId}%")->get();

            if ($status === 'settlement') {
                // PAYMENT SUCCESS - Reduce stock & credit wallet
                foreach ($orders as $order) {
                    // Update order status
                    $order->update([
                        'payment_status' => 'settlement',
                        'status' => 'paid'
                    ]);

                    // ⭐ REDUCE STOCK (Payment confirmed!)
                    foreach ($order->items as $item) {
                        $product = Product::find($item->product_id);
                        
                        if ($product) {
                            // Check if stock is sufficient (should be, but double check)
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

                    // ⭐ CREDIT SELLER WALLET (when order completes, but for now on payment)
                    // Note: You may want to credit wallet later when order status = 'completed'
                    // For now, we'll credit immediately after payment
                    $this->creditSellerWallet($order);
                }

            } elseif (in_array($status, ['cancel', 'expire', 'deny'])) {
                // PAYMENT FAILED/CANCELLED - No stock was reduced, so nothing to restore
                foreach ($orders as $order) {
                    $order->update([
                        'payment_status' => $status,
                        'status' => 'cancelled'
                    ]);
                }

            } elseif ($status === 'refund') {
                // REFUND - Restore stock
                foreach ($orders as $order) {
                    $order->update([
                        'payment_status' => 'refund',
                        'status' => 'refunded'
                    ]);

                    // ⭐ RESTORE STOCK (Refunded)
                    foreach ($order->items as $item) {
                        Product::where('product_id', $item->product_id)
                            ->increment('stock', $item->quantity);
                        
                        Log::info('Stock restored (refund)', [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity
                        ]);
                    }

                    // Debit wallet (remove earnings) if already credited
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

    /**
     * Map Midtrans status to our payment status
     */
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

    /**
     * Credit seller wallet
     */
    private function creditSellerWallet(Order $order)
    {
        // Check if already credited
        $existing = WalletLedger::where('source_type', 'order')
            ->where('source_id', $order->order_id)
            ->where('direction', 'credit')
            ->exists();

        if ($existing) {
            return; // Already credited
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

    /**
     * Debit seller wallet (for refunds)
     */
    private function debitSellerWallet(Order $order)
    {
        // Check if was credited before
        $creditEntry = WalletLedger::where('source_type', 'order')
            ->where('source_id', $order->order_id)
            ->where('direction', 'credit')
            ->first();

        if (!$creditEntry) {
            return; // Was never credited
        }

        // Check if already debited (refund already processed)
        $existing = WalletLedger::where('source_type', 'order_refund')
            ->where('source_id', $order->order_id)
            ->where('direction', 'debit')
            ->exists();

        if ($existing) {
            return; // Already debited
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
