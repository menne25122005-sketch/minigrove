<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm yêu thích.
     */
    public function index(Request $request): View
    {
        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $wishlist->load([
            'items.product.images',
        ]);

        return view('wishlist.index', [
            'wishlist' => $wishlist,
        ]);
    }

    /**
     * Thêm sản phẩm vào yêu thích.
     */
    public function store(
        Request $request,
        Product $product
    ): RedirectResponse|JsonResponse {

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        WishlistItem::firstOrCreate([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'wishlisted' => true,
                'count' => $wishlist
                    ->items()
                    ->count(),
            ]);
        }

        return back()->with(
            'success',
            'Đã thêm sản phẩm vào danh sách yêu thích.'
        );
    }

    /**
     * Xóa sản phẩm khỏi yêu thích.
     */
    public function destroy(
        Request $request,
        Product $product
    ): RedirectResponse|JsonResponse {

        $wishlist = Wishlist::where(
            'user_id',
            $request->user()->id
        )->first();

        if ($wishlist) {
            WishlistItem::where(
                'wishlist_id',
                $wishlist->id
            )
                ->where(
                    'product_id',
                    $product->id
                )
                ->delete();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'wishlisted' => false,
                'count' => $wishlist
                    ? $wishlist->items()->count()
                    : 0,
            ]);
        }

        return back()->with(
            'success',
            'Đã xóa sản phẩm khỏi danh sách yêu thích.'
        );
    }
}