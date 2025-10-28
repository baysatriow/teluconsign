<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use HasFactory, SoftDeletes;

    private $primaryKey = 'cart_item_id';
    private $incrementing = true;
    private $keyType = 'int';

    private $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal'
    ];

    private $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id')->withDefault();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withDefault();
    }

    public function updateQuantity(int $quantity): void
    {
        $this->update([
            'quantity' => $quantity,
            'subtotal' => $quantity * $this->unit_price
        ]);
    }

    public function calculateSubtotal()
    {
        $this->subtotal = $this->unit_price * $this->quantity;
        $this->save();

        return $this->subtotal;
    }
}
