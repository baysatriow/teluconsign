<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankAccount extends Model
{
    use HasFactory;

    protected $primaryKey = 'bank_account_id';
    protected $fillable = [
        'user_id',
        'bank_name',
        'account_name',
        'account_no',
        'is_default'
    ];

    public function addBankAccount(array $data): bool
    {
        if (!isset($data['is_default'])) {
            $data['is_default'] = 0;
        }
        return $this->create($data) ? true : false;
    }

    public function setDefaultAccount(int $account_id): void
    {
        $this->where('user_id', $this->user_id)->update(['is_default' => 0]);
        $this->where('bank_account_id', $account_id)->update(['is_default' => 1]);
    }

    public function validateAccount(string $account_no): bool
    {
        return preg_match('/^[0-9]{5,20}$/', $account_no) ? true : false;
    }

    public function deleteAccount(int $account_id): bool
    {
        return $this->where('bank_account_id', $account_id)->delete();
    }
}
