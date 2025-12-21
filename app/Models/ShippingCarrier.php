<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingCarrier extends Model
{
    use HasFactory;

    protected $primaryKey = 'shipping_carrier_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'code',
        'name',
        'provider_type',
        'mode',
        'is_enabled',
        'config_json'
    ];

    protected $casts = [
        'config_json' => 'array',
    ];

    // Services relationship removed as table is dropped
    
    public function registerCarrier(ShippingCarrier $data): bool
    {
        return $data->save();
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
