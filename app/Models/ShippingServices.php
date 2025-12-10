<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingServices extends Model
{
    use HasFactory;

    protected $primaryKey = 'shipping_services_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'carrier_id',
        'service_code',
        'service_name',
        'is_enabled'
    ];

    public function carrier()
    {
        return $this->belongsTo(ShippingCarrier::class, 'carrier_id', 'shipping_carrier_id');
    }

    public function addService(ShippingService $data): bool
    {
        return $data->save();
    }

    public function updateService(int $id, ShippingService $data): bool
    {
        $service = self::find($id);

        if (!$service) {
            return false;
        }

        return $service->update([
            'carrier_id' => $data->carrier_id,
            'service_code' => $data->service_code,
            'service_name' => $data->service_name,
            'is_enabled' => $data->is_enabled
        ]);
    }

    public function disableService(int $id): void
    {
        $service = self::find($id);

        if ($service) {
            $service->is_enabled = 0;
            $service->save();
        }
    }

    public function getCarrierInfo(): ShippingCarrier
    {
        return $this->carrier;
    }
}
