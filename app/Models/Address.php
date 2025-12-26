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

        'province',
        'city',
        'district',
        'village',
        'postal_code',
        'detail_address',

        'location_id',
        'country',
        'is_default',
        'is_shop_default',
    ];

    protected $casts = [
        'is_default'      => 'boolean',
        'is_shop_default' => 'boolean',
        'created_at'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public static function setDefault(int $id): void
    {
        $address = self::find($id);
        if (!$address) return;

        DB::transaction(function () use ($address) {
            self::where('user_id', $address->user_id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });
    }

    public static function setShopDefault(int $id): void
    {
        $address = self::find($id);
        if (!$address) return;

        DB::transaction(function () use ($address) {
            self::where('user_id', $address->user_id)->update(['is_shop_default' => false]);
            $address->update(['is_shop_default' => true]);
        });
    }

    public static function deleteAddress(int $id): bool
    {
        $address = self::find($id);
        if (!$address) return false;

        DB::transaction(function () use ($address) {
            $userId = $address->user_id;
            $wasDefault = $address->is_default;
            $wasShopDefault = $address->is_shop_default;

            $address->delete();

            if ($wasDefault) {
                self::where('user_id', $userId)
                    ->orderBy('created_at')
                    ->first()
                    ?->update(['is_default' => true]);
            }

            if ($wasShopDefault) {
                self::where('user_id', $userId)
                    ->orderBy('created_at')
                    ->first()
                    ?->update(['is_shop_default' => true]);
            }
        });

        return true;
    }

    public function getFullAddress(): string
    {
        return "{$this->detail_address}, {$this->village}, {$this->district}, {$this->city}, {$this->province} {$this->postal_code}";
    }
}
