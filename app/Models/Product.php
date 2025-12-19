<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\SoftDeletes; // Tambahkan SoftDeletes jika perlu
class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'seller_id',
        'category_id',
        'title',
        'description',
        'price',
        'stock',
        'location',
        'condition',
        'status',
        'main_image',
    ];

    protected $casts = [
        'status' => ProductStatus::class, // Casting ke Enum
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /* =======================
     * Relasi
     * ======================= */

    public function seller()
    {
        // PERBAIKAN DISINI:
        // Sebelumnya: return $this->belongsTo(User::class, 'seller_id', 'id');
        // Penyebab Error: Tabel users tidak punya kolom 'id', tapi 'user_id'.
        return $this->belongsTo(User::class, 'seller_id', 'user_id');
    }

    public function category()
    {
        // Pastikan parameter ke-3 adalah primary key dari tabel categories (category_id)
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function images()
    {
        // hasMany(RelatedModel, Foreign Key di tabel sana, Local Key di tabel ini)
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }


    public static function createProduct(array $data): bool
    {
        return (bool) static::create($data);
    }

    public static function updateProduct(int $id, array $data): bool
    {
        $product = static::find($id);
        if (!$product) {
            return false;
        }

        return $product->update($data);
    }

    public static function deleteProduct(int $id): bool
    {
        $product = static::find($id);
        if (!$product) {
            return false;
        }

        return (bool) $product->delete();
    }

    public function changeStatus(string|ProductStatus $status): void
    {
        $this->status = $status instanceof ProductStatus ? $status : ProductStatus::from($status);
        $this->save();
    }

    public function getImages()
    {
        return $this->images()->get();
    }

    public function getReviews()
    {
        return $this->reviews()->get();
    }

    public function calculateAverageRating(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? (float) $avg : 0.0;
    }
}
