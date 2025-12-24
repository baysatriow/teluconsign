<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_active', 1)
            ->withCount('products')
            ->limit(8)
            ->get();

        $products = Product::with('seller')
            ->where('status', 'active')
            ->withAvg('reviews', 'rating')
            ->withSum([
                'orderItems' => function ($query) {
                    $query->whereHas('order', function ($q) {
                        $q->where('status', 'completed');
                    });
                }
            ], 'quantity')
            ->latest()
            ->take(20)
            ->get();

        return view('home', compact('products', 'categories'));
    }
}