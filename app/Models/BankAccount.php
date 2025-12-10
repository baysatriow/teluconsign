<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class BankAccount extends Model
{
    use HasFactory;

    protected $primaryKey = 'bank_account_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_name',
        'account_no',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public static function addBankAccount(array $data): bool
    {
        return (bool) self::create($data);
    }

    public static function setDefaultAccount(int $account_id): void
    {
        $account = self::find($account_id);

        if (!$account) {
            return;
        }

        DB::transaction(function () use ($account) {
            self::where('user_id', $account->user_id)
                ->update(['is_default' => false]);

            $account->is_default = true;
            $account->save();
        });
    }

    public static function validateAccount(string $account_no): bool
    {
        return preg_match('/^[0-9]{6,30}$/', $account_no) === 1;
    }

    public static function deleteAccount(int $account_id): bool
    {
        $account = self::find($account_id);

        if (!$account) {
            return false;
        }

        return (bool) $account->delete();
    }
}
