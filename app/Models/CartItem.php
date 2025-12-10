<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'cart_item_id';
    public $incrementing = true;
    protected $keyType = 'int';

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
