<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'code',
        'buyer_id',
        'seller_id',
        'shipping_address_id',
        'shipping_address_snapshot',
        'status',
        'payment_status',
        'payment_method_id',
        'subtotal_amount',
        'shipping_cost',
        'platform_fee',
        'total_amount',
        'seller_earnings',
        'notes',
    ];

    protected $casts = [
        'subtotal_amount'           => 'decimal:2',
        'shipping_cost'             => 'decimal:2',
        'platform_fee'              => 'decimal:2',
        'total_amount'              => 'decimal:2',
        'seller_earnings'           => 'decimal:2',
        'shipping_address_snapshot' => 'array',
        'created_at'                => 'datetime',
        'updated_at'                => 'datetime',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id', 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'user_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id', 'address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function createOrder(int $buyer_id, Cart $cart, int $address_id): bool
    {
        $this->buyer_id            = $buyer_id;
        $this->seller_id           = $cart->getSellerId();
        $this->shipping_address_id = $address_id;
        $this->subtotal_amount     = $cart->getSubtotal();
        $this->shipping_cost       = 0;
        $this->platform_fee        = $this->subtotal_amount * 0.05;
        $this->total_amount        = $this->subtotal_amount;
        $this->seller_earnings     = $this->subtotal_amount - $this->platform_fee;
        $this->status              = 'pending';
        $this->payment_status      = 'pending';
        $this->code                = 'ORD-' . time();

        return $this->save();
    }

    public function cancelOrder(int $order_id, string $reason): bool
    {
        $order = $this->find($order_id);
        if (!$order) {
            return false;
        }

        $order->status = 'cancelled';
        $order->notes  = $reason;

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

    public function confirmPayment(): void
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

    public function assignShipment(Shipment $shipment): void
    {
        $shipment->order_id = $this->order_id;
        $shipment->save();
    }

    public function calculateTotalAmount(): float
    {
        return (float) ($this->subtotal_amount + $this->shipping_cost);
    }

    public function getFormattedAddressAttribute(): string
    {
        $data = $this->shipping_address_snapshot ?? $this->shippingAddress;
        if (!$data) {
            return 'N/A';
        }

        $addr = is_array($data) ? (object) $data : $data;

        $recipient = $addr->recipient ?? $addr->recipient_name ?? $addr->name ?? 'N/A';
        $phone     = $addr->phone ?? $addr->phone_number ?? 'N/A';
        $line      = $addr->detail_address ?? $addr->address_line ?? $addr->address ?? 'N/A';
        $province  = $addr->province ?? '';
        $postal    = $addr->postal_code ?? '';

        $city = preg_replace('/^(KOTA|KABUPATEN)\s+/i', '', $addr->city ?? '');
        $city = \Illuminate\Support\Str::title(strtolower($city));

        return "{$recipient} ({$phone})<br>{$line}<br>{$city}, {$province}, {$postal}";
    }
}
