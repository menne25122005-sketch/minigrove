<?php

namespace App\Services;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\PaymentSetting;
use App\Services\GHNService;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
       protected GHNService $ghnService;

    public function __construct(GHNService $ghnService)
    {
        $this->ghnService = $ghnService;
    }
    /**
     * Tạo Order thật.
     *
     * Toàn bộ nghiệp vụ được đặt
     * trong một Database Transaction.
     */
    public function placeOrder(
        User $user,
        int $addressId,
        string $paymentMethod,
        ?string $note = null
    ): Order {

        return DB::transaction(
            function () use (
                $user,
                $addressId,
                $paymentMethod,
                $note
            ) {

                /*
                |--------------------------------------------------------------------------
                | 1. KIỂM TRA ĐỊA CHỈ
                |--------------------------------------------------------------------------
                |
                | Chỉ được sử dụng Address
                | thuộc chính Customer hiện tại.
                |
                */

                $address = Address::query()
                    ->where(
                        'id',
                        $addressId
                    )
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->first();


                if (!$address) {

                    throw ValidationException::withMessages([
                        'address' =>
                            'Địa chỉ nhận hàng không hợp lệ.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | 2. KIỂM TRA PAYMENT METHOD
                |--------------------------------------------------------------------------
                */

                $paymentMethod =
                    strtoupper(
                        trim(
                            $paymentMethod
                        )
                    );


                if (
                    !in_array(
                        $paymentMethod,
                        [
                            'COD',
                            'QR',
                            'ONEPAY',
                        ],
                        true
                    )
                ) {

                    throw ValidationException::withMessages([
                        'payment_method' =>
                            'Phương thức thanh toán không hợp lệ.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | 3. LẤY CHECKOUT MODE
                |--------------------------------------------------------------------------
                */

                $mode =
                    session(
                        'checkout.mode'
                    );


                $orderItems =
                    collect();


                $subtotal = 0;


                /*
                |--------------------------------------------------------------------------
                | 4A. CHECKOUT TỪ CART
                |--------------------------------------------------------------------------
                */

                if ($mode === 'cart') {

                    $cartItemIds =
                        session(
                            'checkout.cart_item_ids',
                            []
                        );


                    if (
                        empty(
                            $cartItemIds
                        )
                    ) {

                        throw ValidationException::withMessages([
                            'checkout' =>
                                'Không có sản phẩm nào được chọn để đặt hàng.',
                        ]);
                    }


                    /*
                     * Chỉ lấy CartItem thuộc User.
                     */
                    $cartItems =
                        CartItem::query()
                            ->whereIn(
                                'id',
                                $cartItemIds
                            )
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
                            ->get();


                    /*
                     * Không cho trường hợp
                     * có ID bị mất / không thuộc User.
                     */
                    $expectedCount =
                        count(
                            array_unique(
                                $cartItemIds
                            )
                        );


                    if (
                        $cartItems->count()
                        !== $expectedCount
                    ) {

                        throw ValidationException::withMessages([
                            'checkout' =>
                                'Giỏ hàng đã thay đổi. Vui lòng kiểm tra lại.',
                        ]);
                    }


                    foreach (
                        $cartItems as $cartItem
                    ) {

                        /*
                         * Lock Product để tránh
                         * hai đơn cùng trừ stock
                         * tại cùng thời điểm.
                         */
                        $product =
                            Product::query()
                                ->with('category')
                                ->whereKey(
                                    $cartItem
                                        ->product_id
                                )
                                ->lockForUpdate()
                                ->first();


                        $this->validateProduct(
                            $product,
                            $cartItem->quantity
                        );


                        /*
                         * Giá luôn lấy lại
                         * trực tiếp từ Database.
                         */
                        $unitPrice =
                            $this->getProductPrice(
                                $product
                            );


                        $lineSubtotal =
                            $unitPrice
                            * $cartItem->quantity;


                        $subtotal +=
                            $lineSubtotal;


                        $orderItems->push(
                            [
                                'cart_item_id' =>
                                    $cartItem->id,

                                'product' =>
                                    $product,

                                'quantity' =>
                                    $cartItem->quantity,

                                'unit_price' =>
                                    $unitPrice,

                                'subtotal' =>
                                    $lineSubtotal,
                            ]
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | 4B. MUA NGAY
                |--------------------------------------------------------------------------
                */

                elseif (
                    $mode === 'buy_now'
                ) {

                    $productId =
                        session(
                            'checkout.product_id'
                        );


                    $quantity =
                        (int)
                        session(
                            'checkout.quantity',
                            1
                        );


                    if (!$productId) {

                        throw ValidationException::withMessages([
                            'checkout' =>
                                'Không tìm thấy sản phẩm Mua ngay.',
                        ]);
                    }


                    $product =
                        Product::query()
                            ->with('category')
                            ->whereKey(
                                $productId
                            )
                            ->lockForUpdate()
                            ->first();


                    $this->validateProduct(
                        $product,
                        $quantity
                    );


                    $unitPrice =
                        $this->getProductPrice(
                            $product
                        );


                    $lineSubtotal =
                        $unitPrice
                        * $quantity;


                    $subtotal =
                        $lineSubtotal;


                    $orderItems->push(
                        [
                            'cart_item_id' =>
                                null,

                            'product' =>
                                $product,

                            'quantity' =>
                                $quantity,

                            'unit_price' =>
                                $unitPrice,

                            'subtotal' =>
                                $lineSubtotal,
                        ]
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | MODE KHÔNG HỢP LỆ
                |--------------------------------------------------------------------------
                */

                else {

                    throw ValidationException::withMessages([
                        'checkout' =>
                            'Phiên thanh toán không hợp lệ.',
                    ]);
                }


                if (
                    $orderItems->isEmpty()
                ) {

                    throw ValidationException::withMessages([
                        'checkout' =>
                            'Không có sản phẩm để đặt hàng.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | 5. VOUCHER
                |--------------------------------------------------------------------------
                */

                $voucher =
                    null;


                $discountAmount =
                    0;


                $voucherCode =
                    session(
                        'checkout.voucher_code'
                    );


                if ($voucherCode) {

                    /*
                     * Lock Voucher để tránh
                     * vượt usage_limit.
                     */
                    $voucher =
                        Voucher::query()
                            ->whereRaw(
                                'UPPER(code) = ?',
                                [
                                    strtoupper(
                                        trim(
                                            $voucherCode
                                        )
                                    ),
                                ]
                            )
                            ->lockForUpdate()
                            ->first();


                    if (!$voucher) {

                        throw ValidationException::withMessages([
                            'voucher' =>
                                'Voucher không còn tồn tại.',
                        ]);
                    }


                    $discountAmount =
                        $this->validateAndCalculateVoucher(
                            $user,
                            $voucher,
                            $subtotal
                        );
                }


                /*
|--------------------------------------------------------------------------
| 6. SHIPPING FEE - GHN
|--------------------------------------------------------------------------
|
| Tính lại phí vận chuyển từ GHN tại thời điểm
| tạo Order, không tin dữ liệu từ Browser.
|
*/

if (
    !$address->ghn_district_id ||
    !$address->ghn_ward_code
) {
    throw ValidationException::withMessages([
        'address' =>
            'Địa chỉ chưa được cấu hình mã GHN. Vui lòng cập nhật lại địa chỉ.',
    ]);
}

/*
 * Tính tổng trọng lượng đơn hàng.
 * GHN sử dụng đơn vị gram.
 */
$weight = 0;

foreach ($orderItems as $item) {

    $productWeight =
        (int) ($item['product']->weight ?? 500);

    $weight +=
        $productWeight
        * (int) $item['quantity'];
}

$weight = max(1, $weight);

/*
 * Thông tin kho/shop MiniGrove trên GHN.
 */
$shippingData = [
    'service_type_id' => 2,

    'from_district_id' => 3440,
    'from_ward_code' => '13005',

    'to_district_id' =>
        (int) $address->ghn_district_id,

    'to_ward_code' =>
        $address->ghn_ward_code,

    'weight' => $weight,
];

/*
 * Gọi GHN để lấy phí vận chuyển.
 */
$shippingResult =
    $this->ghnService
        ->calculateFee($shippingData);

$shippingFee =
    (float) ($shippingResult['total'] ?? 0);


                /*
                |--------------------------------------------------------------------------
                | 7. TOTAL
                |--------------------------------------------------------------------------
                */

                $totalAmount =
                    max(
                        0,
                        $subtotal
                        - $discountAmount
                        + $shippingFee
                    );


                /*
                |--------------------------------------------------------------------------
                | 8. SNAPSHOT ĐỊA CHỈ
                |--------------------------------------------------------------------------
                |
                | Sau này Customer thay Address,
                | Order cũ vẫn giữ thông tin cũ.
                |
                */

                $shippingAddress =
                    $address->address_detail
                    . ', '
                    . $address->ward
                    . ', '
                    . $address->district
                    . ', '
                    . $address->province;


                /*
                |--------------------------------------------------------------------------
                | 9. TẠO ORDER
                |--------------------------------------------------------------------------
                */

                $order =
                    Order::create(
                        [
                            'order_code' =>
                                $this
                                    ->generateOrderCode(),

                            'user_id' =>
                                $user->id,

                            'customer_name' =>
                                $address
                                    ->receiver_name,

                            'phone' =>
                                $address
                                    ->receiver_phone,

                            'email' =>
                                $user->email,

                            'shipping_address' =>
                                $shippingAddress,

                            'subtotal' =>
                                $subtotal,

                            'discount_amount' =>
                                $discountAmount,

                            'shipping_fee' =>
                                $shippingFee,

                            'total_amount' =>
                                $totalAmount,

                            'payment_method' =>
                                $paymentMethod,

                            'payment_status' =>
                                'pending',

                            'order_status' =>
                                'pending',

                            'note' =>
                                $note
                                    ? trim($note)
                                    : null,
                        ]
                    );


                /*
                |--------------------------------------------------------------------------
                | 10. ORDER DETAIL + TRỪ STOCK
                |--------------------------------------------------------------------------
                */

                foreach (
                    $orderItems as $item
                ) {

                    /** @var Product $product */
                    $product =
                        $item['product'];


                    $quantity =
                        (int)
                        $item['quantity'];


                    /*
                     * Snapshot tồn kho trước.
                     */
                    $quantityBefore =
                        (int)
                        $product
                            ->stock_quantity;


                    $quantityAfter =
                        $quantityBefore
                        - $quantity;


                    /*
                     * Tuy đã kiểm tra phía trên,
                     * vẫn bảo vệ không để Stock âm.
                     */
                    if (
                        $quantityAfter < 0
                    ) {

                        throw ValidationException::withMessages([
                            'stock' =>
                                'Sản phẩm '
                                . $product->name
                                . ' không còn đủ tồn kho.',
                        ]);
                    }


                    /*
                     * Tạo Order Detail.
                     */
                    OrderDetail::create(
                        [
                            'order_id' =>
                                $order->id,

                            'item_type' =>
                                'PRODUCT',

                            'product_id' =>
                                $product->id,

                            'custom_design_id' =>
                                null,

                            'item_name' =>
                                $product->name,

                            'item_code' =>
                                $product->sku
                                ?? (
                                    'PRODUCT-'
                                    . $product->id
                                ),

                            'unit_price' =>
                                $item[
                                    'unit_price'
                                ],

                            'quantity' =>
                                $quantity,

                            'subtotal' =>
                                $item[
                                    'subtotal'
                                ],

                            /*
                             * Snapshot tại
                             * thời điểm mua.
                             */
                            'item_snapshot' => [
                                'product_id' =>
                                    $product->id,

                                'sku' =>
                                    $product->sku,

                                'name' =>
                                    $product->name,

                                'category' =>
                                    $product
                                        ->category
                                        ?->name,

                                'list_price' =>
                                    (float)
                                    $product
                                        ->list_price,

                                'sale_price' =>
                                    $product
                                        ->sale_price
                                        !== null
                                            ? (float)
                                            $product
                                                ->sale_price
                                            : null,

                                'unit_price' =>
                                    $item[
                                        'unit_price'
                                    ],
                            ],
                        ]
                    );


                    /*
                     * Trừ Stock.
                     */
                    $product
                        ->stock_quantity =
                            $quantityAfter;


                    $product->save();


                    /*
                     * Ghi Stock History.
                     *
                     * transaction_type = ORDER
                     * là quy ước MiniGrove
                     * cho việc xuất kho do Order.
                     */
                    StockHistory::create(
                        [
                            'stock_type' =>
                                'PRODUCT',

                            'product_id' =>
    $product->id,

'transaction_type' =>
    'ORDER',

                            'quantity_before' =>
                                $quantityBefore,

                            'quantity_change' =>
                                -$quantity,

                            'quantity_after' =>
                                $quantityAfter,

                            'order_id' =>
                                $order->id,

                            /*
                             * Hệ thống tự động
                             * thực hiện trừ kho.
                             */
                            'updated_by' =>
                                null,

                            'note' =>
                                'Trừ tồn kho khi tạo đơn '
                                . $order
                                    ->order_code,
                        ]
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | 11. ORDER STATUS HISTORY
                |--------------------------------------------------------------------------
                */

                OrderStatusHistory::create(
                    [
                        'order_id' =>
                            $order->id,

                        'status' =>
                            'pending',

                        'description' =>
                            'Đơn hàng được tạo thành công.',

                        /*
                         * Trạng thái khởi tạo
                         * do hệ thống tạo.
                         */
                        'updated_by' =>
                            null,
                    ]
                );




                /*
|--------------------------------------------------------------------------
| 12. PAYMENT
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| 12. PAYMENT
|--------------------------------------------------------------------------
|
| Thông tin phương thức thanh toán được lấy
| từ payment_settings tại thời điểm tạo Order.
|
| Sau khi Payment được tạo, thông tin ngân hàng
| được lưu Snapshot vào payments.
|
| Vì vậy nếu Admin đổi tài khoản sau này,
| Payment cũ vẫn giữ đúng dữ liệu lúc đặt hàng.
|
*/


/*
 * Lấy cấu hình phương thức thanh toán.
 */
$paymentSetting =
    PaymentSetting::query()
        ->where(
            'payment_method',
            $paymentMethod
        )
        ->first();


/*
 * Bảo vệ nghiệp vụ ở Service.
 *
 * Dù OrderService được gọi từ nơi khác,
 * phương thức Inactive vẫn không được dùng.
 */
if (
    !$paymentSetting
    ||
    !$paymentSetting->is_enabled
) {

    throw ValidationException::withMessages([
        'payment_method' =>
            'Phương thức thanh toán này hiện không khả dụng. Vui lòng chọn phương thức khác.',
    ]);
}


/*
|--------------------------------------------------------------------------
| SNAPSHOT THÔNG TIN QR
|--------------------------------------------------------------------------
*/

$bankName = null;
$accountName = null;
$accountNumber = null;
$transferContent = null;


if ($paymentMethod === 'QR') {

    /*
     * QR bắt buộc phải có đủ
     * thông tin tài khoản ngân hàng.
     */
    if (
        !$paymentSetting->bank_name
        ||
        !$paymentSetting->account_name
        ||
        !$paymentSetting->account_number
    ) {

        throw ValidationException::withMessages([
            'payment_method' =>
                'Thông tin thanh toán QR hiện chưa được cấu hình đầy đủ.',
        ]);
    }


    /*
     * Snapshot cấu hình QR hiện tại.
     */
    $bankName =
        $paymentSetting->bank_name;

    $accountName =
        $paymentSetting->account_name;

    $accountNumber =
        $paymentSetting->account_number;


    /*
     * Nội dung chuyển khoản
     * gắn với từng Order.
     */
    $transferContent =
        'MINIGROVE '
        . $order->order_code;
}


/*
|--------------------------------------------------------------------------
| TẠO PAYMENT
|--------------------------------------------------------------------------
*/

Payment::create(
    [
        'order_id' =>
            $order->id,

        'payment_method' =>
            $paymentMethod,

        'amount' =>
            $totalAmount,

        /*
         * COD và QR đều bắt đầu Pending.
         */
        'status' =>
            'pending',

        'transaction_code' =>
            null,


        /*
         * COD:
         * các trường ngân hàng = NULL.
         *
         * QR:
         * lưu Snapshot từ payment_settings.
         */
        'bank_name' =>
            $bankName,

        'account_name' =>
            $accountName,

        'account_number' =>
            $accountNumber,

        'transfer_content' =>
            $transferContent,


        /*
         * QR đang mô phỏng,
         * chưa cần lưu file QR vật lý.
         */
        'qr_code_path' =>
            null,

        'confirmed_by' =>
            null,

        'paid_at' =>
            null,

        'refunded_at' =>
            null,

        'note' =>
            $paymentMethod === 'QR'
                ? 'Thanh toán QR chờ Staff/Admin xác nhận.'
                : null,
    ]
);

                /*
                |--------------------------------------------------------------------------
                | 13. VOUCHER USAGE
                |--------------------------------------------------------------------------
                */

                if ($voucher) {

                    VoucherUsage::create(
                        [
                            'voucher_id' =>
                                $voucher->id,

                            'user_id' =>
                                $user->id,

                            'order_id' =>
                                $order->id,

                            'discount_amount' =>
                                $discountAmount,

                            'status' =>
                                'used',

                            'used_at' =>
                                now(),

                            'released_at' =>
                                null,
                        ]
                    );


                    /*
                     * Tăng số lượt đã dùng.
                     */
                    $voucher
                        ->usage_count =
                            $voucher
                                ->usage_count
                            + 1;


                    $voucher->save();
                }


                /*
                |--------------------------------------------------------------------------
                | 14. XÓA CART ITEM ĐÃ MUA
                |--------------------------------------------------------------------------
                |
                | Chỉ Checkout mode = cart.
                |
                | Những món KHÔNG tick
                | vẫn giữ nguyên trong Cart.
                |
                */

                if (
                    $mode === 'cart'
                ) {

                    $idsToDelete =
                        $orderItems
                            ->pluck(
                                'cart_item_id'
                            )
                            ->filter()
                            ->values()
                            ->all();


                    CartItem::query()
                        ->whereIn(
                            'id',
                            $idsToDelete
                        )
                        ->whereHas(
                            'cart',
                            function ($query) use ($user) {

                                $query->where(
                                    'user_id',
                                    $user->id
                                );
                            }
                        )
                        ->delete();
                }


                /*
                |--------------------------------------------------------------------------
                | 15. RETURN ORDER
                |--------------------------------------------------------------------------
                */

                return $order
                    ->load([
                        'details',
                        'payments',
                        'statusHistories',
                        'voucherUsage',
                    ]);
            }
        );
    }


    /**
     * Kiểm tra Product trước khi đặt hàng.
     */
    private function validateProduct(
        ?Product $product,
        int $quantity
    ): void {

        if (!$product) {

            throw ValidationException::withMessages([
                'product' =>
                    'Có sản phẩm không còn tồn tại.',
            ]);
        }


        if (!$product->is_active) {

            throw ValidationException::withMessages([
                'product' =>
                    'Sản phẩm '
                    . $product->name
                    . ' hiện không còn được bán.',
            ]);
        }


        if ($quantity < 1) {

            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng sản phẩm không hợp lệ.',
            ]);
        }


        if (
            $quantity >
            $product->stock_quantity
        ) {

            throw ValidationException::withMessages([
                'stock' =>
                    'Sản phẩm '
                    . $product->name
                    . ' chỉ còn '
                    . $product->stock_quantity
                    . ' sản phẩm.',
            ]);
        }
    }


    /**
     * Giá hiện tại của Product.
     */
    private function getProductPrice(
        Product $product
    ): float {

        return $product->sale_price !== null
            ? (float)
                $product->sale_price
            : (float)
                $product->list_price;
    }


    /**
     * Validate Voucher lần cuối
     * bên trong Transaction.
     */
    private function validateAndCalculateVoucher(
        User $user,
        Voucher $voucher,
        float $subtotal
    ): float {

        if (!$voucher->is_active) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher không còn hoạt động.',
            ]);
        }


        $now = now();


        if (
            $voucher->start_at->gt(
                $now
            )
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher chưa đến thời gian sử dụng.',
            ]);
        }


        if (
            $voucher->end_at->lt(
                $now
            )
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher đã hết hạn.',
            ]);
        }


        /*
         * Kiểm tra tổng lượt.
         */
        if (
            $voucher->usage_limit
                !== null
            &&
            $voucher->usage_count
                >=
            $voucher->usage_limit
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher đã hết lượt sử dụng.',
            ]);
        }


        /*
         * Giá trị đơn tối thiểu.
         */
        if (
            $subtotal <
            (float)
            $voucher
                ->min_order_amount
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Đơn hàng không còn đủ điều kiện sử dụng Voucher.',
            ]);
        }


        /*
         * Giới hạn theo Customer.
         */
        if (
            $voucher
                ->per_user_limit
            !== null
        ) {

            $usedCount =
                VoucherUsage::query()
                    ->where(
                        'voucher_id',
                        $voucher->id
                    )
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->where(
                        'status',
                        'used'
                    )
                    ->count();


            if (
                $usedCount >=
                $voucher
                    ->per_user_limit
            ) {

                throw ValidationException::withMessages([
                    'voucher' =>
                        'Bạn đã sử dụng hết lượt của Voucher này.',
                ]);
            }
        }


        /*
         * Tính Discount.
         */
        if (
            $voucher
                ->discount_type
            === 'percentage'
        ) {

            $discount =
                $subtotal
                * (
                    (float)
                    $voucher
                        ->discount_value
                    / 100
                );
        }

        elseif (
            $voucher
                ->discount_type
            === 'fixed'
        ) {

            $discount =
                (float)
                $voucher
                    ->discount_value;
        }

        else {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher có cấu hình không hợp lệ.',
            ]);
        }


        /*
         * Giảm tối đa.
         */
        if (
            $voucher
                ->max_discount_amount
            !== null
        ) {

            $discount =
                min(
                    $discount,
                    (float)
                    $voucher
                        ->max_discount_amount
                );
        }


        /*
         * Không để tổng tiền âm.
         */
        $discount =
            min(
                $discount,
                $subtotal
            );


        return max(
            0,
            $discount
        );
    }


    /**
     * Sinh mã Order.
     *
     * Ví dụ:
     * MG-20260831-A8K29X
     */
    private function generateOrderCode(): string
    {
        do {

            $code =
                'MG-'
                . now()->format(
                    'Ymd'
                )
                . '-'
                . Str::upper(
                    Str::random(6)
                );

        } while (
            Order::query()
                ->where(
                    'order_code',
                    $code
                )
                ->exists()
        );


        return $code;
    }
}