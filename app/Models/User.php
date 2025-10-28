<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    private $primaryKey = 'user_id';
    private $incrementing = true;
    private $keyType = 'int';

    private $fillable = [
        'role',
        'status',
        'username',
        'name',
        'email',
        'password_hash',
        'photo_url'
    ];

    private $hidden = [
        'password_hash',
        'remember_token',
    ];

    private $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function register(string $name, string $email, string $password): bool
    {
        $this->name = $name;
        $this->email = $email;
        $this->password_hash = Hash::make($password);
        $this->role = 'buyer';
        $this->status = 'active';
        return $this->save();
    }

    public function login(string $email, string $password): bool
    {
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return false;
        }
        return true;
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function updateProfile(string $name, string $photo_url): void
    {
        $this->update([
            'name' => $name,
            'photo_url' => $photo_url
        ]);
    }

    public function suspendAccount(string $reason): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function getOrders(string $role)
    {
        return $this->orders;
    }

    public function getProducts()
    {
        return $this->products;
    }
}
