<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    private $primaryKey = 'address_id';
    private $fillable = [
        'user_id',
        'label',
        'recipient',
        'phone',
        'line1',
        'line2',
        'city',
        'province',
        'postal_code',
        'country',
        'is_default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function addAddress(Address $data): bool
    {
        return $this->create($data->toArray()) ? true : false;
    }

    public function updateAddress(int $id, Address $data): bool
    {
        return $this->where('address_id', $id)->update($data->toArray());
    }

    public function deleteAddress(int $id): bool
    {
        return $this->where('address_id', $id)->delete();
    }

    public function setDefault(int $id): void
    {
        self::where('user_id', $this->user_id)->update(['is_default' => 0]);
        $this->where('address_id', $id)->update(['is_default' => 1]);
    }

    public function getFullAddress(): string
    {
        return $this->line1 . ' ' . $this->line2 . ', ' . $this->city . ', ' . $this->province . ' ' . $this->postal_code . ', ' . $this->country;
    }
}
