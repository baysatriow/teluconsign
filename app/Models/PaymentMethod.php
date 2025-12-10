<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_method_id';

    protected $fillable = [
        'gateway_id',
        'code',
        'name',
        'is_enabled',
        'min_amount',
        'max_amount',
        'extra_config',
    ];

    protected $casts = [
        'extra_config' => 'array',
    ];

    /**
     * Relasi ke PaymentGateway.
     */
    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_id');
    }

    public function enableMethod(): void
    {
        $this->update([
            'is_enabled' => 1,
        ]);
    }

    public function disableMethod(): void
    {
        $this->update([
            'is_enabled' => 0,
        ]);
    }

    public function validateAmountRange(float $amount): bool
    {
        if ($this->min_amount !== null && $amount < $this->min_amount) {
            return false;
        }

        if ($this->max_amount !== null && $amount > $this->max_amount) {
            return false;
        }

        return true;
    }

    public function getGatewayInfo(): ?PaymentGateway
    {
        return $this->gateway()->first();
    }
}
