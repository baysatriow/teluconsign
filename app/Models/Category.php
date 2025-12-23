<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public static function addCategory(string $name, int $parent_id = null): bool
    {
        $category = self::create([
            'name' => $name,
            'parent_id' => $parent_id,
        ]);

        return (bool) $category;
    }

    public static function updateCategory(int $id, array $data): bool
    {
        $category = self::find($id);

        if (! $category) {
            return false;
        }

        $category->fill($data);

        return $category->save();
    }

    public static function deleteCategory(int $id): bool
    {
        $category = self::find($id);

        if (! $category) {
            return false;
        }

        return (bool) $category->delete();
    }

    public static function getSubCategories(int $parent_id)
    {
        return self::where('parent_id', $parent_id)->get();
    }

    public function getProducts()
    {
        return Product::where('category_id', $this->category_id)->get();
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'category_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    public function getAllChildIds()
    {
        $ids = [$this->category_id];
        
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllChildIds());
        }
        
        return $ids;
    }
}
