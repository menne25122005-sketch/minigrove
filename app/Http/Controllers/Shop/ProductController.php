<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm phía Customer / Guest.
     */
    public function index(Request $request)
    {
        $keyword = trim(
            (string) $request->get('keyword', '')
        );

        $category = $request->get('category');

        $ecosystem = $request->get('ecosystem');

        $care = $request->get('care');

        $sort = $request->get(
            'sort',
            'newest'
        );


        /*
         * Danh mục dùng cho bộ lọc.
         */
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();


        /*
         * Chỉ hiển thị sản phẩm Active.
         *
         * Sản phẩm hết hàng vẫn có thể xem,
         * nhưng bước Cart sau này sẽ không cho mua.
         */
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


        /*
         * Tìm kiếm.
         */
        if ($keyword !== '') {

            $query->where(
                function ($subQuery) use ($keyword) {

                    $subQuery
                        ->where(
                            'name',
                            'like',
                            '%' . $keyword . '%'
                        )
                        ->orWhere(
                            'sku',
                            'like',
                            '%' . $keyword . '%'
                        )
                        ->orWhere(
                            'description',
                            'like',
                            '%' . $keyword . '%'
                        );
                }
            );
        }


        /*
         * Lọc Category theo slug.
         */
        if ($category) {

            $query->whereHas(
                'category',
                function ($subQuery) use ($category) {

                    $subQuery->where(
                        'slug',
                        $category
                    );
                }
            );
        }


        /*
         * Lọc loại hệ sinh thái.
         */
        if (
            in_array(
                $ecosystem,
                [
                    'closed',
                    'open',
                    'dry',
                ],
                true
            )
        ) {
            $query->where(
                'ecosystem_type',
                $ecosystem
            );
        }


        /*
         * Lọc mức chăm sóc.
         */
        if (
            in_array(
                $care,
                [
                    'easy',
                    'medium',
                    'high',
                ],
                true
            )
        ) {
            $query->where(
                'care_level',
                $care
            );
        }


        /*
         * Sắp xếp.
         */
        switch ($sort) {

            case 'price_asc':

                $query->orderByRaw(
                    '
                    CASE
                        WHEN sale_price IS NOT NULL
                        THEN sale_price
                        ELSE list_price
                    END ASC
                    '
                );

                break;


            case 'price_desc':

                $query->orderByRaw(
                    '
                    CASE
                        WHEN sale_price IS NOT NULL
                        THEN sale_price
                        ELSE list_price
                    END DESC
                    '
                );

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


    /**
     * Chi tiết sản phẩm.
     */
    public function show(Product $product)
    {
        /*
         * Sản phẩm Inactive không được
         * xuất hiện ở website Customer.
         */
        abort_unless(
            $product->is_active,
            404
        );


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
                'images',
                'replies.user',
            ])
            ->latest();
    },
]);


        /*
         * Một số sản phẩm liên quan.
         */
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
            ->where(
                'category_id',
                $product->category_id
            )
            ->where(
                'id',
                '!=',
                $product->id
            )
            ->take(4)
            ->get();
            $reviewableOrderDetails = collect();

if (
    auth()->check()
    && auth()->user()->role?->name === 'customer'
) {
    $reviewedOrderDetailIds = $product->reviews
        ->where('user_id', auth()->id())
        ->pluck('order_detail_id')
        ->filter()
        ->values();

    $reviewableOrderDetails = OrderDetail::query()
        ->where('product_id', $product->id)
        ->whereHas('order', function ($query) {
            $query
                ->where('user_id', auth()->id())
                ->where('order_status', 'completed');
        })
        ->whereNotIn(
            'id',
            $reviewedOrderDetailIds
        )
        ->orderByDesc('id')
        ->get();
}

        $reviewCount = $product->reviews->count();

        $averageRating = $reviewCount > 0
            ? round($product->reviews->avg('rating'), 1)
            : 0;


                return view(
    'products.show',
    compact(
        'product',
        'relatedProducts',
        'reviewCount',
        'averageRating',
        'reviewableOrderDetails'
    )
);
    }
}