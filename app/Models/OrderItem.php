<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_title',
        'unit_price',
        'quantity',
        'subtotal'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function calculateSubtotal(): float
    {
        $this->subtotal = $this->unit_price * $this->quantity;
        $this->save();

        return (float) $this->subtotal;
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
