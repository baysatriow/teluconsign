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

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = \Illuminate\Support\Str::slug($product->title) . '-' . \Illuminate\Support\Str::random(8); // Randomize for security
            }
        });
    }

    protected $fillable = [
        'seller_id',
        'category_id',
        'title',
        'description',
        'price',
        'weight',
        'stock',
        'condition',
        'status',
        'suspension_reason',
        'main_image',
    ];

    protected $casts = [
        'status' => ProductStatus::class, // Casting ke Enum
        'price' => 'decimal:2',
        'stock' => 'integer',
        'weight' => 'integer',
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

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    /**
     * Get the total number of items sold (only completed orders).
     */
    public function getSoldCountAttribute()
    {
        // Check if the attribute is already loaded via withSum
        if (array_key_exists('order_items_sum_quantity', $this->attributes)) {
            return (int) $this->attributes['order_items_sum_quantity'];
        }

        // Fallback or lazy load
        return (int) $this->orderItems()
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })
            ->sum('quantity');
    }

    /**
     * Get the average rating.
     */
    public function getRatingAttribute()
    {
         // Check if the attribute is already loaded via withAvg
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            return (float) $this->attributes['reviews_avg_rating'];
        }

        return (float) $this->reviews()->avg('rating') ?? 0.0;
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

    /**
     * Get dynamic location from Shop Default Address.
     * Format: Title Case, remove "KOTA"/"KABUPATEN".
     */
    public function getLocationAttribute()
    {
        $address = $this->seller->addresses()->where('is_shop_default', true)->first();
        
        if (!$address) return 'Indonesia';

        $city = $address->city;
        
        // Remove "KOTA " or "KABUPATEN " (Case Insensitive)
        $city = preg_replace('/^(KOTA|KABUPATEN)\s+/i', '', $city);
        
        // Convert to Title Case
        return \Illuminate\Support\Str::title(strtolower($city));
    }
}
