<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // primary key
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    // kolom yang boleh diisi mass-assignment
    protected $fillable = [
        'role',
        'status',
        'username',
        'name',
        'email',
        'password_hash',
        'photo_url',
    ];

    // hidden saat toArray()
    protected $hidden = [
        'password_hash',
    ];

    // casting waktu
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // dipakai Auth::attempt() agar pakai password_hash
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // relasi profile
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'user_id');
    }

    // relasi produk milik user (seller)
    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id', 'user_id');
    }

    // relasi order di mana user jadi buyer / seller nanti difilter manual
    public function ordersAsBuyer()
    {
        return $this->hasMany(Order::class, 'buyer_id', 'user_id');
    }

    public function ordersAsSeller()
    {
        return $this->hasMany(Order::class, 'seller_id', 'user_id');
    }

    // register(name, email, password): bool
    public function register(string $name, string $email, string $password): bool
    {
        $this->name = $name;
        $this->email = $email;
        $this->password_hash = Hash::make($password);
        $this->username = $this->username ?? null;
        $this->role = $this->role ?? 'buyer';
        $this->status = $this->status ?? 'active';
        return $this->save();
    }

    // login(email, password): bool
    public function login(string $email, string $password): bool
    {
        return Auth::attempt([
            'email' => $email,
            'password' => $password,
            'status' => 'active',
        ]);
    }

    // logout(): void
    public function logout(): void
    {
        Auth::logout();
    }

    // updateProfile(name, photo_url): void
    public function updateProfile(string $name, string $photo_url): void
    {
        $this->update([
            'name' => $name,
            'photo_url' => $photo_url,
        ]);
    }

    // suspendAccount(reason): void
    public function suspendAccount(string $reason): void
    {
        $this->update(['status' => 'suspended']);
        // reason bisa disimpan di tabel log lain kalau diperlukan
    }

    // getOrders(role): List<Order>
    public function getOrders(string $role)
    {
        if ($role === 'buyer') {
            return $this->ordersAsBuyer;
        }
        if ($role === 'seller') {
            return $this->ordersAsSeller;
        }
        return collect();
    }

    // getProducts(): List<Product>
    public function getProducts()
    {
        return $this->products;
    }
}
