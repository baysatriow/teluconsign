<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory, SoftDeletes;

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

    public function addItem(int $product_id, int $quantity): void
    {
        $item = CartItem::where('cart_id', $this->cart_id)
            ->where('product_id', $product_id)
            ->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->save();
        } else {
            CartItem::create([
                'cart_id' => $this->cart_id,
                'product_id' => $product_id,
                'quantity' => $quantity,
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
        CartItem::where('cart_id', $this->cart_id)
            ->where('product_id', $product_id)
            ->update(['quantity' => $quantity]);

        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $total = CartItem::where('cart_id', $this->cart_id)
            ->join('products', 'cart_items.product_id', '=', 'products.product_id')
            ->sum(\DB::raw('cart_items.quantity * products.price'));

        $this->update(['total_price' => $total]);

        return $total;
    }

    public function clearCart(): void
    {
        CartItem::where('cart_id', $this->cart_id)->delete();
        $this->update(['total_price' => 0]);
    }
}
