<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'role',
        'status',
        'username',
        'name',
        'email',
        'password_hash',
        'photo_url',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id', 'user_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'user_id', 'user_id');
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'user_id', 'user_id');
    }

    public function register(string $name, string $email, string $password): bool
    {
        return (bool) self::create([
            'name' => $name,
            'email' => $email,
            'password_hash' => $password,
            'status' => 'active',
            'role' => 'buyer',
        ]);
    }

    public function login(string $email, string $password): bool
    {
        return self::where('email', $email)
            ->where('password_hash', $password)
            ->exists();
    }

    public function logout(): void
    {
    }

    public function updateProfile(string $name, string $photo_url): void
    {
        $this->update([
            'name' => $name,
            'photo_url' => $photo_url,
        ]);
    }

    public function suspendAccount(string $reason): void
    {
        $this->update([
            'status' => 'suspended',
        ]);
    }

    public function getOrders(string $role)
    {
        if ($role === 'buyer') {
            return Order::where('buyer_id', $this->user_id)->get();
        }

        if ($role === 'seller') {
            return Order::where('seller_id', $this->user_id)->get();
        }

        return collect();
    }

    public function getProducts()
    {
        return Product::where('seller_id', $this->user_id)->get();
    }
}
