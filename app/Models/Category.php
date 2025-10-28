<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    private $primaryKey = 'category_id';
    private $incrementing = true;
    private $keyType = 'int';

    private $fillable = [
        'parent_id',
        'name',
        'slug'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id')->withDefault();
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function addCategory(string $name, int $parent_id = null): bool
    {
        return $this->create([
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'parent_id' => $parent_id
        ]) ? true : false;
    }

    public function updateCategory(int $id, array $data): bool
    {
        return $this->where('category_id', $id)->update($data) > 0;
    }

    public function deleteCategory(int $id): bool
    {
        return $this->where('category_id', $id)->delete() > 0;
    }

    public function getSubCategories(int $parent_id)
    {
        return $this->where('parent_id', $parent_id)->get();
    }

    public function getProducts()
    {
        return $this->products()->get();
    }
}
