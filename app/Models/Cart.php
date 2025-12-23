<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $primaryKey = 'cart_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'buyer_id',
        'total_price'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'cart_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id', 'user_id');
    }

    public function addItem(int $product_id, int $quantity): void
    {
        $item = CartItem::where('cart_id', $this->cart_id)
            ->where('product_id', $product_id)
            ->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->calculateSubtotal();
            $item->save();
        } else {
            $product = Product::find($product_id);
            CartItem::create([
                'cart_id' => $this->cart_id,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'subtotal' => $product->price * $quantity
            ]);
        }

        $this->calculateTotal();
    }

    public function removeItem(int $product_id): void
    {
        CartItem::where('cart_id', $this->cart_id)
            ->where('product_id', $product_id)
            ->delete();

        $this->calculateTotal();
    }

    public function updateQuantity(int $product_id, int $quantity): void
    {
        $item = CartItem::where('cart_id', $this->cart_id)
            ->where('product_id', $product_id)
            ->first();

        if ($item) {
            $item->quantity = $quantity;
            $item->calculateSubtotal();
            $item->save();
        }

        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $total = $this->items()->sum('subtotal');
        $this->update(['total_price' => $total]);

        return $total;
    }

    public function clearCart(): void
    {
        $this->items()->delete();
        $this->update(['total_price' => 0]);
    }
}
