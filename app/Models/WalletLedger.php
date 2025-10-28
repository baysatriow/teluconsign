<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class WalletLedger extends Model
{
    use HasFactory;

    protected $table = 'wallet_ledgers';
    protected $primaryKey = 'wallet_ledger_id';

    protected $fillable = [
        'user_id',
        'direction',
        'source_type',
        'source_id',
        'amount',
        'balance_after',
        'memo',
        'posted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function credit(int $user_id, float $amount, int $source_id): void
    {
        $balance = $this->getBalance($user_id) + $amount;
        $this->create([
            'user_id' => $user_id,
            'direction' => 'credit',
            'source_type' => 'order_settlement',
            'source_id' => $source_id,
            'amount' => $amount,
            'balance_after' => $balance,
            'posted_at' => now()
        ]);
    }

    public function debit(int $user_id, float $amount, int $source_id): void
    {
        $balance = $this->getBalance($user_id) - $amount;
        $this->create([
            'user_id' => $user_id,
            'direction' => 'debit',
            'source_type' => 'payout',
            'source_id' => $source_id,
            'amount' => $amount,
            'balance_after' => $balance,
            'posted_at' => now()
        ]);
    }

    public function getBalance(int $user_id): float
    {
        return (float)$this->where('user_id', $user_id)
            ->orderBy('wallet_ledger_id', 'desc')
            ->value('balance_after') ?? 0;
    }

    public function getTransactionHistory(int $user_id)
    {
        return $this->where('user_id', $user_id)
            ->orderBy('wallet_ledger_id', 'desc')
            ->get();
    }
}
