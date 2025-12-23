<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'order_id',
        'provider_id',  // Changed from gateway_id
        'method_code',
        'amount',
        'currency',
        'status',
        'provider_txn_id',
        'provider_order_id',
        'raw_response',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw_response' => 'array',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function provider()
    {
        return $this->belongsTo(IntegrationProvider::class, 'provider_id', 'integration_provider_id');
    }

    public function initiatePayment(int $order_id, string $method_code): bool
    {
        $this->order_id = $order_id;
        $this->method_code = $method_code;
        $this->status = 'pending';

        return $this->save();
    }

    public function verifyPaymentStatus(string $txn_id): string
    {
        $this->provider_txn_id = $txn_id;
        $this->status = 'settlement';
        $this->paid_at = now();
        $this->save();

        return $this->status;
    }

    public function refund(int $order_id, float $amount): bool
    {
        if ($this->order_id == $order_id && $this->status === 'settlement') {
            $this->status = 'refund';

            return $this->save();
        }

        return false;
    }

    public function cancelPayment(int $order_id): bool
    {
        if ($this->order_id == $order_id && $this->status === 'pending') {
            $this->status = 'cancel';

            return $this->save();
        }

        return false;
    }

    public function recordTransaction(array $data): void
    {
        $this->raw_response = $data;
        $this->save();
    }
}
