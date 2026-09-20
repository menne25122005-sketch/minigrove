<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $category = $request->get('category');
        $ecosystem = $request->get('ecosystem');
        $care = $request->get('care');
        $sort = $request->get('sort', 'newest');

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $query = Product::query()
            ->with([
                'category',
                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->where('is_active', true);

        if ($keyword !== '') {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery
                    ->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('sku', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%');
            });
        }

        if ($category) {
            $query->whereHas('category', function ($subQuery) use ($category) {
                $subQuery->where('slug', $category);
            });
        }

        if (in_array($ecosystem, ['closed', 'open', 'dry'], true)) {
            $query->where('ecosystem_type', $ecosystem);
        }

        if (in_array($care, ['easy', 'medium', 'high'], true)) {
            $query->where('care_level', $care);
        }

        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('
                    CASE
                        WHEN sale_price IS NOT NULL
                        THEN sale_price
                        ELSE list_price
                    END ASC
                ');
                break;

            case 'price_desc':
                $query->orderByRaw('
                    CASE
                        WHEN sale_price IS NOT NULL
                        THEN sale_price
                        ELSE list_price
                    END DESC
                ');
                break;

            case 'featured':
                $query
                    ->orderByDesc('is_featured')
                    ->orderByDesc('id');
                break;

            default:
                $query->orderByDesc('id');
                break;
        }

        $products = $query
            ->paginate(12)
            ->withQueryString();

        return view(
            'products.index',
            compact(
                'products',
                'categories',
                'keyword',
                'category',
                'ecosystem',
                'care',
                'sort'
            )
        );
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'category',

            'images' => function ($query) {
                $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id');
            },

            'reviews' => function ($query) {
                $query
                    ->where('is_visible', true)
                    ->with([
                        'user',
                        'images' => function ($imageQuery) {
                            $imageQuery
                                ->orderBy('sort_order')
                                ->orderBy('id');
                        },
                        'replies' => function ($replyQuery) {
                            $replyQuery
                                ->where('is_visible', true)
                                ->with('user')
                                ->latest('id');
                        },
                    ])
                    ->latest('id');
            },
        ]);

        $averageRating = $product->reviews->isNotEmpty()
            ? round((float) $product->reviews->avg('rating'), 1)
            : 0;

        $reviewCount = $product->reviews->count();

        $relatedProducts = Product::query()
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
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view(
            'products.show',
            compact(
                'product',
                'relatedProducts',
                'averageRating',
                'reviewCount'
            )
        );
    }
}
