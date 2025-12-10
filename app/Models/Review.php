<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'review_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'comment',
        'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function addReview(int $user_id, int $product_id, int $rating, string $comment): bool
    {
        return (bool) $this->create([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'rating' => $rating,
            'comment' => $comment,
            'status' => 'visible'
        ]);
    }

    public function editReview(int $id, int $rating, string $comment): bool
    {
        return $this->where('review_id', $id)->update([
            'rating' => $rating,
            'comment' => $comment
        ]) > 0;
    }

    public function deleteReview(int $id): bool
    {
        return $this->where('review_id', $id)->delete() > 0;
    }

    public function hideReview(int $id): void
    {
        $this->where('review_id', $id)->update(['status' => 'hidden']);
    }

    public function calculateAverageRating(int $product_id): float
    {
        return (float) ($this->where('product_id', $product_id)
            ->where('status', 'visible')
            ->avg('rating') ?? 0.0);
    }
}
