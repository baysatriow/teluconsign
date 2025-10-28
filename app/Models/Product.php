<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    private $primaryKey = 'product_id';
    private $incrementing = true;
    private $keyType = 'int';

    private $fillable = [
        'seller_id',
        'category_id',
        'title',
        'description',
        'price',
        'stock',
        'location',
        'condition',
        'status',
        'main_image'
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function createProduct(array $data): bool
    {
        return $this->create($data) ? true : false;
    }

    public function updateProduct(int $id, array $data): bool
    {
        return $this->where('product_id', $id)->update($data) > 0;
    }

    public function deleteProduct(int $id): bool
    {
        return $this->where('product_id', $id)->delete() > 0;
    }

    public function changeStatus(string $status): void
    {
        $this->update(['status' => $status]);
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
        return (float) $this->reviews()->avg('rating') ?? 0.0;
    }
}
