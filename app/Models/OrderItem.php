<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    private $primaryKey = 'order_item_id';
    private $incrementing = true;
    private $keyType = 'int';

    private $fillable = [
        'order_id',
        'product_id',
        'product_title',
        'unit_price',
        'quantity',
        'subtotal'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function calculateSubtotal(): float
    {
        $this->subtotal = $this->unit_price * $this->quantity;
        $this->save();
        return $this->subtotal;
    }

    public function updateQuantity(int $qty): void
    {
        $this->quantity = $qty;
        $this->calculateSubtotal();
    }

    public function linkProduct(int $product_id): void
    {
        $this->product_id = $product_id;
        $product = Product::find($product_id);
        if ($product) {
            $this->product_title = $product->title;
            $this->unit_price = $product->price;
            $this->calculateSubtotal();
        }
    }
}
