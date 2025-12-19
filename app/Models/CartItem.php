<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes; // HAPUS INI

class CartItem extends Model
{
    use HasFactory; // HAPUS SoftDeletes DARI SINI

    protected $primaryKey = 'cart_item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Nonaktifkan timestamps otomatis karena tabel cart_items tidak punya kolom created_at & updated_at
    public $timestamps = false;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Relasi ke Produk (PENTING untuk CartController)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Relasi ke Cart (PENTING untuk CartController)
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }

    public function updateQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
        $this->calculateSubtotal(); // Panggil fungsi internal untuk update subtotal & save
    }

    public function calculateSubtotal()
    {
        $this->subtotal = $this->unit_price * $this->quantity;
        $this->save();

        return $this->subtotal;
    }
}
