<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Address extends Model
{
    use HasFactory;

    protected $primaryKey = 'address_id';

    protected $fillable = [
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
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
    ];

    public static function addAddress(array $data): bool
    {
        return (bool) self::create($data);
    }

    public static function updateAddress(int $id, array $data): bool
    {
        $address = self::find($id);

        if (!$address) {
            return false;
        }

        $address->fill($data);

        return $address->save();
    }

    public static function deleteAddress(int $id): bool
    {
        $address = self::find($id);

        if (!$address) {
            return false;
        }

        return (bool) $address->delete();
    }

    public static function setDefault(int $id): void
    {
        $address = self::find($id);

        if (!$address) {
            return;
        }

        DB::transaction(function () use ($address) {
            self::where('user_id', $address->user_id)
                ->update(['is_default' => false]);

            $address->is_default = true;
            $address->save();
        });
    }

    public function getFullAddress(): string
    {
        $parts = [
            $this->line1,
            $this->line2,
            $this->city,
            $this->province,
            $this->postal_code,
            $this->country,
        ];

        $parts = array_filter($parts, fn($value) => !empty($value));

        return implode(', ', $parts);
    }
}
