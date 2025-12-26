<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebhookLog extends Model
{
    use HasFactory;
    protected $primaryKey = 'webhook_log_id';
    public $timestamps = false;

    protected $fillable = [
        'provider_code',
        'event_type',
        'related_id',
        'payload',
        'received_at',
    ];

    protected $casts = [
        'payload'     => 'array',
        'received_at' => 'datetime',
    ];

    public function recordWebhook(string $provider_code, string $event_type, $payload): void
    {
        self::create([
            'provider_code' => $provider_code,
            'event_type'    => $event_type,
            'payload'       => $payload,
            'received_at'   => now(),
        ]);
    }

    public function getLogsByProvider(string $provider_code)
    {
        return self::where('provider_code', $provider_code)->get();
    }

    public function parsePayload($payload): array
    {
        return is_array($payload)
            ? $payload
            : (json_decode($payload, true) ?? []);
    }

    public function replayEvent(int $log_id): bool
    {
        return self::find($log_id) !== null;
    }
}
