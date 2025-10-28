<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BannerSlide extends Model
{
    use HasFactory;

    private $primaryKey = 'banner_slide_id';
    private $fillable = [
        'title',
        'description',
        'image_path',
        'link_url',
        'is_active',
        'sort_order',
        'start_date',
        'end_date',
        'created_by',
        'updated_by'
    ];

    public function createBanner(array $data): bool
    {
        return $this->create($data) ? true : false;
    }

    public function updateBanner(int $id, array $data): bool
    {
        return $this->where('banner_slide_id', $id)->update($data);
    }

    public function deactivateBanner(): void
    {
        $this->update(['is_active' => 0]);
    }

    public function setOrder(int $order_index): void
    {
        $this->update(['sort_order' => $order_index]);
    }

    public function showActiveBanners()
    {
        return $this->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->orderBy('sort_order', 'asc')
            ->get();
    }
}
