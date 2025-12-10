<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'user_id',
        'type',
        'payload',
        'read_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    public function sendNotification(int $user_id, string $type, array $payload): void
    {
        $this->create([
            'user_id' => $user_id,
            'type' => $type,
            'payload' => $payload
        ]);
    }

    public function markAsRead(int $id): void
    {
        $this->where('notification_id', $id)->update(['read_at' => now()]);
    }

    public function getUnreadNotifications(int $user_id)
    {
        return $this->where('user_id', $user_id)
            ->whereNull('read_at')
            ->get();
    }
}
