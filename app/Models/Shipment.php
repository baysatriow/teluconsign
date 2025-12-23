<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;

    protected $primaryKey = 'shipment_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'order_id',
        'carrier_id',
        'service_code',
        'tracking_number',
        'label_url',
        'status',
        'shipped_at',
        'delivered_at',
        'cost',
        'metadata',
    ];

    protected $casts = [
        'shipped_at'   => 'datetime',
        'delivered_at' => 'datetime',
        'metadata'     => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function carrier()
    {
        return $this->belongsTo(ShippingCarrier::class, 'carrier_id', 'carrier_id');
    }

    public function createShipment(int $order_id, int $carrier_id, string $service_code): bool
    {
        $this->order_id     = $order_id;
        $this->carrier_id   = $carrier_id;
        $this->service_code = $service_code;
        $this->status       = 'pending';

        return $this->save();
    }

    public function updateStatus(string $status): void
    {
        $this->status = $status;

        if ($status === 'in_transit') {
            $this->shipped_at = now();
        }

        if ($status === 'delivered') {
            $this->delivered_at = now();
        }

        $this->save();
    }

    public function trackShipment(string $tracking_number): array
    {
        return [
            'tracking_number' => $tracking_number,
            'status'          => $this->status,
            'updated_at'      => now(),
        ];
    }

    public function calculateShippingCost(float $weight, float $distance): float
    {
        return ($weight * 1000) + ($distance * 200);
    }

    public function markDelivered(): void
    {
        $this->status       = 'delivered';
        $this->delivered_at = now();

        $this->save();
    }
}
