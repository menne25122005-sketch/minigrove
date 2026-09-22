<?php

namespace App\Services;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    protected GHNService $ghnService;

    public function __construct(GHNService $ghnService)
    {
        $this->ghnService = $ghnService;
    }

    /**
     * Lấy lại toàn bộ dữ liệu Checkout
     * từ Session và Database.
     */
    public function getCheckoutData(
        User $user
    ): array {

        $mode = session('checkout.mode');

        $checkoutItems = collect();

        $subtotal = 0;

        /*
        |--------------------------------------------------------------------------
        | CHECKOUT TỪ CART
        |--------------------------------------------------------------------------
        */

        if ($mode === 'cart') {

            $ids = session(
                'checkout.cart_item_ids',
                []
            );

            if (empty($ids)) {

                throw ValidationException::withMessages([
                    'checkout' =>
                        'Vui lòng chọn sản phẩm cần thanh toán.',
                ]);
            }

            $items = CartItem::query()
                ->whereIn('id', $ids)

                /*
                 * Bắt buộc CartItem phải thuộc
                 * Customer đang đăng nhập.
                 */
                ->whereHas(
                    'cart',
                    function ($query) use ($user) {

                        $query->where(
                            'user_id',
                            $user->id
                        );
                    }
                )

                ->where(
                    'item_type',
                    'PRODUCT'
                )

                ->with([
                    'product.category',

                    'product.images' => function ($query) {

                        $query
                            ->orderByDesc('is_primary')
                            ->orderBy('sort_order')
                            ->orderBy('id');
                    },
                ])

                ->get();

            if ($items->isEmpty()) {

                throw ValidationException::withMessages([
                    'checkout' =>
                        'Không tìm thấy sản phẩm cần thanh toán.',
                ]);
            }

            foreach ($items as $item) {

                $product = $item->product;

                /*
                 * Kiểm tra lại Product từ DB.
                 */
                if (
                    !$product ||
                    !$product->is_active
                ) {

                    throw ValidationException::withMessages([
                        'checkout' =>
                            'Có sản phẩm không còn được bán.',
                    ]);
                }

                /*
                 * Kiểm tra lại Stock.
                 */
                if (
                    $item->quantity < 1 ||
                    $item->quantity >
                    $product->stock_quantity
                ) {

                    throw ValidationException::withMessages([
                        'checkout' =>
                            'Số lượng sản phẩm không còn phù hợp với tồn kho.',
                    ]);
                }

                /*
                 * Giá luôn lấy lại từ DB.
                 */
                $unitPrice =
                    $product->sale_price !== null
                        ? (float) $product->sale_price
                        : (float) $product->list_price;

                $lineTotal =
                    $unitPrice
                    * $item->quantity;

                $checkoutItems->push(
                    (object) [
                        'cart_item_id' =>
                            $item->id,

                        'product' =>
                            $product,

                        'quantity' =>
                            $item->quantity,

                        'unit_price' =>
                            $unitPrice,

                        'subtotal' =>
                            $lineTotal,
                    ]
                );

                $subtotal += $lineTotal;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MUA NGAY
        |--------------------------------------------------------------------------
        */

        elseif ($mode === 'buy_now') {

            $productId =
                session(
                    'checkout.product_id'
                );

            $quantity =
                (int) session(
                    'checkout.quantity',
                    1
                );

            $product = Product::query()
                ->with([
                    'category',

                    'images' => function ($query) {

                        $query
                            ->orderByDesc('is_primary')
                            ->orderBy('sort_order')
                            ->orderBy('id');
                    },
                ])
                ->find($productId);

            if (
                !$product ||
                !$product->is_active
            ) {

                throw ValidationException::withMessages([
                    'checkout' =>
                        'Sản phẩm hiện không còn được bán.',
                ]);
            }

            if (
                $quantity < 1 ||
                $quantity >
                $product->stock_quantity
            ) {

                throw ValidationException::withMessages([
                    'checkout' =>
                        'Số lượng mua không còn phù hợp với tồn kho.',
                ]);
            }

            $unitPrice =
                $product->sale_price !== null
                    ? (float) $product->sale_price
                    : (float) $product->list_price;

            $lineTotal =
                $unitPrice * $quantity;

            $checkoutItems->push(
                (object) [
                    'cart_item_id' =>
                        null,

                    'product' =>
                        $product,

                    'quantity' =>
                        $quantity,

                    'unit_price' =>
                        $unitPrice,

                    'subtotal' =>
                        $lineTotal,
                ]
            );

            $subtotal =
                $lineTotal;
        }

        /*
        |--------------------------------------------------------------------------
        | KHÔNG CÓ CHECKOUT SESSION
        |--------------------------------------------------------------------------
        */

        else {

            throw ValidationException::withMessages([
                'checkout' =>
                    'Chưa có sản phẩm để thanh toán.',
            ]);
        }

        return [
            'mode' =>
                $mode,

            'checkoutItems' =>
                $checkoutItems,

            'subtotal' =>
                $subtotal,
        ];
    }

    /**
     * Tính phí vận chuyển bằng GHN.
     *
     * GHN chỉ được gọi để tính phí,
     * không tạo đơn giao hàng.
     *
     * Trọng lượng sản phẩm tính bằng gram.
     */
    public function calculateShippingFee(
        ?Address $address,
        $checkoutItems
    ): float {

        /*
         * Không có địa chỉ hoặc sản phẩm
         * thì chưa thể tính phí.
         */
        if (
            !$address ||
            $checkoutItems->isEmpty()
        ) {
            return 0;
        }

        /*
         |--------------------------------------------------------------------------
         | TÍNH TỔNG TRỌNG LƯỢNG
         |--------------------------------------------------------------------------
         |
         | Ví dụ:
         |
         | Forest Calm: 500g × 2 = 1000g
         | Mushroom:    500g × 1 =  500g
         |
         | Tổng = 1500g
         |
         */

        $weight = 0;

        foreach ($checkoutItems as $item) {

            $productWeight =
                (int) ($item->product->weight ?? 500);

            $quantity =
                (int) $item->quantity;

            $weight +=
                $productWeight * $quantity;
        }

        /*
         * Đảm bảo trọng lượng tối thiểu là 1 gram.
         */
        $weight = max(
            1,
            $weight
        );

        /*
         |--------------------------------------------------------------------------
         | DỮ LIỆU GỬI GHN
         |--------------------------------------------------------------------------
         |
         | Shop MiniGrove:
         * district_id = 3440
         * ward_code   = 13005
         |
         | Địa chỉ khách:
         * lấy trực tiếp từ Address.
         |
         */

        $data = [
            'service_type_id' => 2,

            // Địa chỉ shop GHN
            'from_district_id' => 3440,
            'from_ward_code' => '13005',

            // Địa chỉ khách hàng
            'to_district_id' =>
                (int) $address->ghn_district_id,

            'to_ward_code' =>
                $address->ghn_ward_code,

            // Trọng lượng tính bằng gram
            'weight' => $weight,
        ];

        /*
         |--------------------------------------------------------------------------
         | GỌI GHN API
         |--------------------------------------------------------------------------
         */

        $result =
            $this->ghnService->calculateFee(
                $data
            );

        /*
         * GHN trả về:
         *
         * [
         *     'total' => 29001,
         *     ...
         * ]
         *
         * Trả phí về CheckoutController.
         */

        return (float) (
            $result['total'] ?? 0
        );
    }
}