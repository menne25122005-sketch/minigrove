<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        /*
         * Danh mục đang Active.
         */
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount([
                'products as active_products_count' => function ($query) {
                    $query->where('is_active', true);
                },
            ])
            ->orderBy('display_order')
            ->orderBy('id')
            ->take(10)
            ->get();


        /*
         * Sản phẩm nổi bật.
         */
        $featuredProducts = Product::query()
            ->with([
                'category',
                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('is_featured', true)
            ->latest('id')
            ->take(8)
            ->get();


        /*
         * Sản phẩm mới.
         */
        $newProducts = Product::query()
            ->with([
                'category',
                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->latest('id')
            ->take(8)
            ->get();


        return view(
            'home',
            compact(
                'categories',
                'featuredProducts',
                'newProducts'
            )
        );
    }
}