<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayoutRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'payout_request_id';

    protected $fillable = [
        'seller_id',
        'amount',
        'status',
        'bank_account_id',
        'requested_at',
        'processed_at',
        'processed_by',
        'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'user_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'bank_account_id');
    }

    public function processedByAdmin()
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }

    public function createRequest(int $seller_id, float $amount): bool
    {
        return (bool) static::create([
            'seller_id'    => $seller_id,
            'amount'       => $amount,
            'status'       => 'requested',
            'requested_at' => now(),
        ]);
    }

    public function approveRequest(int $admin_id): bool
    {
        return $this->update([
            'status'       => 'approved',
            'processed_at' => now(),
            'processed_by' => $admin_id,
        ]);
    }

    public function rejectRequest(int $admin_id, string $reason): bool
    {
        return $this->update([
            'status'       => 'rejected',
            'processed_at' => now(),
            'processed_by' => $admin_id,
            'notes'        => $reason,
        ]);
    }

    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    public function cancelRequest(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
