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

        // Struktur Baru
        'province',
        'city',
        'district', // Kecamatan
        'village',  // Desa
        'postal_code',
        'detail_address', // Alamat manual

        'location_id', 
        'country',
        'is_default',
        'is_shop_default', // Baru
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_shop_default' => 'boolean',
        'created_at' => 'datetime',
    ];

    public static function setDefault(int $id): void
    {
        $address = self::find($id);
        if (!$address) return;

        DB::transaction(function () use ($address) {
            self::where('user_id', $address->user_id)->update(['is_default' => false]);
            $address->is_default = true;
            $address->save();
        });
    }

    public static function setShopDefault(int $id): void
    {
        $address = self::find($id);
        if (!$address) return;

        DB::transaction(function () use ($address) {
            self::where('user_id', $address->user_id)->update(['is_shop_default' => false]);
            $address->is_shop_default = true;
            $address->save();
        });
    }

    // Helper untuk menampilkan alamat lengkap satu baris
    public function getFullAddress(): string
    {
        return "{$this->detail_address}, {$this->village}, {$this->district}, {$this->city}, {$this->province} {$this->postal_code}";
    }
}
