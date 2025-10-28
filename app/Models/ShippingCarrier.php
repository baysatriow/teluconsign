<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingCarrier extends Model
{
    use HasFactory;

    private $primaryKey = 'shipping_carrier_id';
    private $incrementing = true;
    private $keyType = 'int';

    private $fillable = [
        'code',
        'name',
        'provider_type',
        'mode',
        'is_enabled',
        'config_json'
    ];

    public function services()
    {
        return $this->hasMany(ShippingServices::class, 'carrier_id');
    }

    public function registerCarrier(ShippingCarrier $data): bool
    {
        return $data->save();
    }

    public function getServices(): array
    {
        return $this->services()->get()->toArray();
    }

    public function enableCarrier(): void
    {
        $this->is_enabled = 1;
        $this->save();
    }

    public function disableCarrier(): void
    {
        $this->is_enabled = 0;
        $this->save();
    }
}
