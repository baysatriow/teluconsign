<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentGateway extends Model
{
    use HasFactory;

    private $primaryKey = 'payment_gateway_id';
    private $fillable = [
        'code',
        'name',
        'is_enabled',
        'config_json'
    ];

    protected $casts = [
        'config_json' => 'array'
    ];

    public function connect($api_key): bool
    {
        return !empty($api_key);
    }

    public function getGatewayInfo(): array
    {
        return $this->config_json ?? [];
    }

    public function isActive(): bool
    {
        return $this->is_enabled === 1;
    }

    public function configure($settings): void
    {
        $this->update(['config_json' => $settings]);
    }
}
