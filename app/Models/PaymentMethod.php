<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    private $primaryKey = 'payment_method_id';
    private $fillable = [
        'gateway_id',
        'code',
        'name',
        'is_enabled',
        'min_amount',
        'max_amount',
        'extra_config'
    ];

    protected $casts = [
        'extra_config' => 'array'
    ];

    public function enableMethod(): void
    {
        $this->update(['is_enabled' => 1]);
    }

    public function disableMethod(): void
    {
        $this->update(['is_enabled' => 0]);
    }

    public function validateAmountRange($amount): bool
    {
        if ($this->min_amount !== null && $amount < $this->min_amount) {
            return false;
        }
        if ($this->max_amount !== null && $amount > $this->max_amount) {
            return false;
        }
        return true;
    }

    public function getGatewayInfo(): PaymentGateway
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_id')->first();
    }
}
