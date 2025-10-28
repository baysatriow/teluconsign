<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    private $primaryKey = 'product_image_id';
    private $incrementing = true;
    private $keyType = 'int';

    private $fillable = [
        'product_id',
        'url',
        'is_primary',
        'sort_order'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function addImage(int $product_id, string $url, bool $is_primary): bool
    {
        if ($is_primary) {
            $this->where('product_id', $product_id)->update(['is_primary' => 0]);
        }

        return $this->create([
            'product_id' => $product_id,
            'url' => $url,
            'is_primary' => $is_primary ? 1 : 0,
            'sort_order' => $this->where('product_id', $product_id)->count() + 1
        ]) ? true : false;
    }

    public function setPrimary(int $image_id): void
    {
        $image = $this->find($image_id);
        if ($image) {
            $this->where('product_id', $image->product_id)->update(['is_primary' => 0]);
            $image->update(['is_primary' => 1]);
        }
    }

    public function deleteImage(int $image_id): bool
    {
        return $this->where('product_image_id', $image_id)->delete() > 0;
    }

    public function reorderImages(array $order): void
    {
        foreach ($order as $sort => $image_id) {
            $this->where('product_image_id', $image_id)->update(['sort_order' => $sort + 1]);
        }
    }
}
