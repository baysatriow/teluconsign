<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
        'weight',
        'stock',
        'condition',
        'status',
        'suspension_reason',
        'main_image',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
        'price' => 'decimal:2',
        'stock' => 'integer',
        'weight' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title) . '-' . Str::random(8);
            }
        });
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    public function currentUserReview()
    {
        return $this->hasOne(Review::class, 'product_id', 'product_id')
                    ->where('user_id', auth()->id());
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    public function getSoldCountAttribute()
    {
        if (array_key_exists('order_items_sum_quantity', $this->attributes)) {
            return (int) $this->attributes['order_items_sum_quantity'];
        }

        return (int) $this->orderItems()
            ->whereHas('order', fn ($q) => $q->where('status', 'completed'))
            ->sum('quantity');
    }

    public function getRatingAttribute()
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            return (float) $this->attributes['reviews_avg_rating'];
        }

        return (float) ($this->reviews()->avg('rating') ?? 0.0);
    }

    public function getLocationAttribute()
    {
        $address = $this->seller
            ->addresses()
            ->where('is_shop_default', true)
            ->first();

        if (!$address) {
            return 'Indonesia';
        }

        $city = preg_replace('/^(KOTA|KABUPATEN)\s+/i', '', $address->city);

        return Str::title(strtolower($city));
    }

    public static function createProduct(array $data): bool
    {
        return (bool) static::create($data);
    }

    public static function updateProduct(int $id, array $data): bool
    {
        $product = static::find($id);
        return $product ? $product->update($data) : false;
    }

    public static function deleteProduct(int $id): bool
    {
        $product = static::find($id);
        return $product ? (bool) $product->delete() : false;
    }

    public function changeStatus(string|ProductStatus $status): void
    {
        $this->status = $status instanceof ProductStatus
            ? $status
            : ProductStatus::from($status);

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
        return (float) ($this->reviews()->avg('rating') ?? 0.0);
    }
}
