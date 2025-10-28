<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebhookLog extends Model
{
    use HasFactory;

    private $primaryKey = 'webhook_log_id';
    private $fillable = [
        'provider_code',
        'event_type',
        'related_id',
        'payload',
        'received_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime'
    ];

    public function recordWebhook($provider_code, $event_type, $payload): void
    {
        $this->create([
            'provider_code' => $provider_code,
            'event_type' => $event_type,
            'payload' => $payload,
            'received_at' => now()
        ]);
    }

    public function getLogsByProvider($provider_code)
    {
        return self::where('provider_code', $provider_code)->get();
    }

    public function parsePayload($payload): array
    {
        return is_array($payload) ? $payload : json_decode($payload, true);
    }

    public function replayEvent($log_id): bool
    {
        $log = self::find($log_id);
        return $log !== null;
    }
}
