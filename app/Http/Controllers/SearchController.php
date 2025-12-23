<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Menampilkan halaman pencarian dengan filter.
     */
    public function index(Request $request)
    {
        $query = Product::with('seller')
            ->where('status', 'active')
            ->withAvg('reviews', 'rating')
            ->withSum(['orderItems' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('status', 'completed');
                });
            }], 'quantity');

        // Initialize selected category variable
        $selectedCategory = null;

        // 1. Keyword Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 2. Category Filter (Handle Parent + Children Hierarchically)
        if ($request->has('category') && $request->category != '') {
            $categorySlug = $request->category;
            
            // Find category by slug
            $category = Category::where('slug', $categorySlug)->first();
            
            if ($category) {
                // Get all child IDs (including parent itself) recursively
                $categoryIds = $category->getAllChildIds();
                
                // Filter products by category IDs
                $query->whereIn('category_id', $categoryIds);
                
                // Pass to view for breadcrumb and subcategory filter
                $selectedCategory = $category;
            }
        }

        // 3. Price Filter
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        // 4. Condition Filter
        if ($request->has('condition') && in_array($request->condition, ['new', 'used'])) {
            $query->where('condition', $request->condition);
        }

        // 5. Sorting
        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(24)->withQueryString();
        
        // Get all parent categories with children for filter sidebar
        $categories = Category::whereNull('parent_id')->with('children')->get();
        
        return view('search.index', compact('products', 'categories', 'selectedCategory'));
    }
}
