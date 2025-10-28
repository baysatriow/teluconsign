<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    private $primaryKey = 'order_id';
    private $incrementing = true;
    private $keyType = 'int';

    private $fillable = [
        'code',
        'buyer_id',
        'seller_id',
        'shipping_address_id',
        'status',
        'payment_status',
        'payment_method_id',
        'subtotal_amount',
        'shipping_cost',
        'platform_fee',
        'total_amount',
        'seller_earnings',
        'notes'
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'order_id');
    }

    public function createOrder(int $buyer_id, Cart $cart, int $address_id): bool
    {
        $this->buyer_id = $buyer_id;
        $this->seller_id = $cart->getSellerId();
        $this->shipping_address_id = $address_id;
        $this->subtotal_amount = $cart->getSubtotal();
        $this->shipping_cost = 0;
        $this->platform_fee = $this->subtotal_amount * 0.05;
        $this->total_amount = $this->subtotal_amount + $this->shipping_cost;
        $this->seller_earnings = $this->subtotal_amount - $this->platform_fee;
        $this->status = 'pending';
        $this->payment_status = 'pending';
        $this->code = 'ORD-' . time();
        return $this->save();
    }

    public function cancelOrder(int $order_id, string $reason): bool
    {
        $order = $this->find($order_id);
        if (!$order) {
            return false;
        }
        $order->status = 'cancelled';
        $order->notes = $reason;
        return $order->save();
    }

    public function updateStatus(int $order_id, string $status): void
    {
        $order = $this->find($order_id);
        if ($order) {
            $order->status = $status;
            $order->save();
        }
    }

    public function calculateTotalAmount(): float
    {
        return $this->subtotal_amount + $this->shipping_cost;
    }

    public function getOrderItems()
    {
        return $this->items()->get();
    }

    public function assignShipment(Shipment $shipment): void
    {
        $shipment->order_id = $this->order_id;
        $shipment->save();
    }

    public function confirmPayment(int $payment_id): void
    {
        $this->payment_status = 'settlement';
        $this->status = 'paid';
        $this->save();
    }

    public function completeOrder(): void
    {
        $this->status = 'completed';
        $this->save();
    }
}
