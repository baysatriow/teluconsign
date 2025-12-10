<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BannerSlide extends Model
{
    use HasFactory;

    protected $primaryKey = 'banner_slide_id';

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'link_url',
        'is_active',
        'sort_order',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function createBanner(array $data): bool
    {
        return (bool) self::create($data);
    }

    public static function updateBanner(int $id, array $data): bool
    {
        $banner = self::find($id);

        if (!$banner) {
            return false;
        }

        $banner->fill($data);

        return $banner->save();
    }

    public function deactivateBanner(): void
    {
        $this->is_active = false;
        $this->save();
    }

    public function setOrder(int $order_index): void
    {
        $this->sort_order = $order_index;
        $this->save();
    }

    public static function showActiveBanners()
    {
        $now = Carbon::now();

        return self::where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            })
            ->orderBy('sort_order')
            ->get();
    }
}
