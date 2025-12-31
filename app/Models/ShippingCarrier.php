<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingCarrier extends Model
{
    use HasFactory;

    protected $primaryKey = 'shipping_carrier_id';
    protected $keyType = 'int';
    public $incrementing = true;
    
    protected $fillable = [
        'code',
        'name',
        'provider_type',
        'mode',
        'is_enabled',
        'config_json',
    ];

    protected $casts = [
        'config_json' => 'array',
    ];

    public function registerCarrier(ShippingCarrier $data): bool
    {
        return $data->save();
    }

    public function enableCarrier(): void
    {
        $this->update([
            'is_enabled' => 1,
        ]);
    }

    public function disableCarrier(): void
    {
        $this->update([
            'is_enabled' => 0,
        ]);
    }
}
