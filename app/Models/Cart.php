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

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id')->withDefault();
    }

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function addItem(int $product_id, int $quantity): void
    {
        $item = $this->items()->where('product_id', $product_id)->first();
        if ($item) {
            $item->quantity += $quantity;
            $item->save();
        } else {
            $this->items()->create([
                'product_id' => $product_id,
                'quantity' => $quantity
            ]);
        }
        $this->calculateTotal();
    }

    public function removeItem(int $product_id): void
    {
        $this->items()->where('product_id', $product_id)->delete();
        $this->calculateTotal();
    }

    public function updateQuantity(int $product_id, int $quantity): void
    {
        $this->items()->where('product_id', $product_id)->update(['quantity' => $quantity]);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $total = $this->items()
                      ->join('products', 'cart_items.product_id', '=', 'products.product_id')
                      ->sum(\DB::raw('cart_items.quantity * products.price'));

        $this->update(['total_price' => $total]);
        return $total;
    }

    public function clearCart(): void
    {
        $this->items()->delete();
        $this->update(['total_price' => 0]);
    }
}
