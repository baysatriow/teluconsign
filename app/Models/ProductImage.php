<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_image_id';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'url',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public static function addImage(
        int $productId,
        string $url,
        bool $isPrimary = false,
        ?int $sortOrder = null
    ): bool {
        if ($isPrimary) {
            static::where('product_id', $productId)->update(['is_primary' => 0]);
        }

        if ($sortOrder === null) {
            $sortOrder = (static::where('product_id', $productId)->max('sort_order') ?? 0) + 1;
        }

        return (bool) static::create([
            'product_id' => $productId,
            'url' => $url,
            'is_primary' => $isPrimary,
            'sort_order' => $sortOrder,
        ]);
    }

    public static function setPrimary(int $imageId): void
    {
        $image = static::find($imageId);
        if (!$image) {
            return;
        }

        static::where('product_id', $image->product_id)->update(['is_primary' => 0]);

        $image->update(['is_primary' => true]);
    }

    public static function deleteImage(int $imageId): bool
    {
        return (bool) static::where('product_image_id', $imageId)->delete();
    }

    public static function reorderImages(array $order): void
    {
        foreach ($order as $index => $imageId) {
            static::where('product_image_id', $imageId)
                ->update(['sort_order' => $index + 1]);
        }
    }
}
