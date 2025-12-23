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
        'product_title_snapshot',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'quantity'   => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

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
        if (!$product) {
            return;
        }

        $this->product_title_snapshot = $product->title;
        $this->calculateSubtotal();
    }

    public function getProductTitleAttribute()
    {
        return $this->product_title_snapshot;
    }
}
