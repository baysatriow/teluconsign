<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_method_id';

    protected $fillable = [
        'provider_id', // Changed from gateway_id
        'code', // Original
        'name', // Original
        'is_active', // Changed from is_enabled (based on controller usage, checking db schema in mind)
        'min_amount',
        'max_amount',
        'extra_config',
    ];

    protected $casts = [
        'extra_config' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Relasi ke IntegrationProvider.
     */
    public function provider()
    {
        return $this->belongsTo(IntegrationProvider::class, 'provider_id');
    }

    public function enableMethod(): void
    {
        $this->update(['is_active' => 1]);
    }

    public function disableMethod(): void
    {
        $this->update(['is_active' => 0]);
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
}
