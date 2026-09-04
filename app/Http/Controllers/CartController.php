<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {
    }


    /**
     * Hiển thị giỏ hàng của Customer.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $cart = $this->cartService
            ->getOrCreateCart($user);


        $cart->load([
            'items.product.category',

            'items.product.images' => function ($query) {
                $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id');
            },
        ]);


        $total = 0;


        foreach ($cart->items as $item) {

            /*
             * Bước này mới xử lý PRODUCT.
             * Custom Design sẽ nối vào Cart sau.
             */
            if (
                $item->item_type !== 'PRODUCT' ||
                !$item->product
            ) {
                continue;
            }


            $product = $item->product;


            /*
             * Luôn lấy giá hiện tại từ Database.
             */
            $unitPrice =
                $product->sale_price !== null
                    ? (float) $product->sale_price
                    : (float) $product->list_price;


            $subtotal =
                $unitPrice * $item->quantity;


            /*
             * Gán tạm để View sử dụng.
             * Không lưu giá vào cart_items.
             */
            $item->unit_price =
                $unitPrice;

            $item->subtotal =
                $subtotal;


            $total += $subtotal;
        }


        return view(
            'cart.index',
            compact(
                'cart',
                'total'
            )
        );
    }


    /**
     * Thêm Product vào Cart.
     */
    public function add(
        Request $request,
        Product $product
    ): RedirectResponse {

        $validated = $request->validate(
            [
                'quantity' => [
                    'nullable',
                    'integer',
                    'min:1',
                ],
            ],
            [
                'quantity.integer' =>
                    'Số lượng phải là số nguyên.',

                'quantity.min' =>
                    'Số lượng phải từ 1 trở lên.',
            ]
        );


        $quantity =
            (int) ($validated['quantity'] ?? 1);


        $this->cartService->addProduct(
            $request->user(),
            $product,
            $quantity
        );


        return redirect()
            ->back()
            ->with(
                'success',
                'Đã thêm sản phẩm vào giỏ hàng.'
            );
    }


    /**
     * Cập nhật số lượng.
     */
    /**
 * Cập nhật số lượng Product trong Cart.
 */
public function update(
    Request $request,
    CartItem $cartItem
) {

    $validated = $request->validate(
        [
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ],
        [
            'quantity.required' =>
                'Vui lòng nhập số lượng.',

            'quantity.integer' =>
                'Số lượng phải là số nguyên.',

            'quantity.min' =>
                'Số lượng phải từ 1 trở lên.',
        ]
    );


    $updatedItem =
        $this->cartService
            ->updateProductQuantity(
                $request->user(),
                $cartItem,
                (int) $validated['quantity']
            );


    /*
     * Lấy lại Product sau khi cập nhật.
     */
    $product = Product::findOrFail(
        $updatedItem->product_id
    );


    /*
     * Giá hiện tại phía Server.
     */
    $unitPrice =
        $product->sale_price !== null
            ? (float) $product->sale_price
            : (float) $product->list_price;


    $subtotal =
        $unitPrice
        * $updatedItem->quantity;


    /*
     * Nếu request từ JavaScript:
     * trả JSON, KHÔNG reload trang.
     */
    if ($request->expectsJson()) {

        return response()->json([
            'success' => true,

            'message' =>
                'Đã cập nhật giỏ hàng.',

            'item' => [
                'id' =>
                    $updatedItem->id,

                'quantity' =>
                    $updatedItem->quantity,

                'unit_price' =>
                    $unitPrice,

                'subtotal' =>
                    $subtotal,

                'stock_quantity' =>
                    $product->stock_quantity,
            ],
        ]);
    }


    /*
     * Trường hợp submit bình thường.
     */
    return redirect()
        ->route('cart.index')
        ->with(
            'success',
            'Đã cập nhật giỏ hàng.'
        );
}


    /**
     * Xóa item khỏi Cart.
     */
    public function destroy(
        Request $request,
        CartItem $cartItem
    ): RedirectResponse {

        $this->cartService
            ->removeItem(
                $request->user(),
                $cartItem
            );


        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Đã xóa sản phẩm khỏi giỏ hàng.'
            );
    }
/**
 * Xóa nhiều CartItem được Customer chọn.
 */
public function destroySelected(
    Request $request
): RedirectResponse {

    $validated = $request->validate(
        [
            'selected_items' => [
                'required',
                'array',
                'min:1',
            ],

            'selected_items.*' => [
                'required',
                'integer',
            ],
        ],
        [
            'selected_items.required' =>
                'Vui lòng chọn ít nhất một sản phẩm.',

            'selected_items.min' =>
                'Vui lòng chọn ít nhất một sản phẩm.',
        ]
    );


    $items = CartItem::query()
        ->whereIn(
            'id',
            $validated['selected_items']
        )
        ->get();


    foreach ($items as $item) {

        /*
         * CartService sẽ kiểm tra
         * item có thuộc Customer hiện tại không.
         */
        $this->cartService->removeItem(
            $request->user(),
            $item
        );
    }


    return redirect()
        ->route('cart.index')
        ->with(
            'success',
            'Đã xóa các sản phẩm được chọn khỏi giỏ hàng.'
        );
}


}