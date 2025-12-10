<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_gateway_id';

    protected $fillable = [
        'code',
        'name',
        'is_enabled',
        'config_json',
    ];

    protected $casts = [
        'config_json' => 'array',
        'created_at' => 'datetime',
    ];

    public function connect(string $api_key): bool
    {
        return !empty($api_key);
    }

    public function getGatewayInfo(): array
    {
        return $this->config_json ?? [];
    }

    public function isActive(): bool
    {
        return (int) $this->is_enabled === 1;
    }

    public function configure(array $settings): void
    {
        $this->update([
            'config_json' => $settings,
        ]);
    }
}
