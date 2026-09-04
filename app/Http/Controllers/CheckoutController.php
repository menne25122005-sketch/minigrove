<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\PaymentSetting;

use App\Services\CheckoutService;
use App\Services\VoucherService;
use App\Services\OrderService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;


class CheckoutController extends Controller
{
    /**
     * Nhận các CartItem được tick
     * từ trang giỏ hàng.
     */
    public function prepareCart(
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
                    'Vui lòng chọn ít nhất một sản phẩm để mua.',

                'selected_items.min' =>
                    'Vui lòng chọn ít nhất một sản phẩm để mua.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | CHỈ LẤY CART ITEM THUỘC CUSTOMER HIỆN TẠI
        |--------------------------------------------------------------------------
        */

        $items = CartItem::query()
            ->whereIn(
                'id',
                $validated['selected_items']
            )
            ->whereHas(
                'cart',
                function ($query) use ($request) {

                    $query->where(
                        'user_id',
                        $request->user()->id
                    );
                }
            )
            ->where(
                'item_type',
                'PRODUCT'
            )
            ->with('product')
            ->get();


        if ($items->isEmpty()) {

            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Không tìm thấy sản phẩm hợp lệ để mua.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA PRODUCT + STOCK
        |--------------------------------------------------------------------------
        */

        foreach ($items as $item) {

            $product = $item->product;


            if (
                !$product ||
                !$product->is_active
            ) {

                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Có sản phẩm không còn được bán.'
                    );
            }


            if (
                $item->quantity < 1 ||
                $item->quantity >
                $product->stock_quantity
            ) {

                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Có sản phẩm không còn đủ số lượng tồn kho.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DỌN DỮ LIỆU BUY NOW CŨ
        |--------------------------------------------------------------------------
        */

        session()->forget([
            'checkout.product_id',
            'checkout.quantity',
        ]);


        /*
        |--------------------------------------------------------------------------
        | LƯU CHECKOUT CART VÀO SESSION
        |--------------------------------------------------------------------------
        |
        | Không lưu trạng thái checkbox vào Database.
        |
        */

        session([
            'checkout.mode' =>
                'cart',

            'checkout.cart_item_ids' =>
                $items
                    ->pluck('id')
                    ->values()
                    ->all(),
        ]);


        return redirect()
            ->route('checkout.index');
    }


    /**
     * Mua ngay một Product.
     *
     * Không thêm Product vào Cart
     * và không ảnh hưởng Cart hiện tại.
     */
    public function prepareBuyNow(
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
            (int) (
                $validated['quantity']
                ?? 1
            );


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA PRODUCT
        |--------------------------------------------------------------------------
        */

        if (!$product->is_active) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Sản phẩm hiện không còn được bán.'
                );
        }


        if (
            $product->stock_quantity <= 0
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Sản phẩm hiện đã hết hàng.'
                );
        }


        if (
            $quantity >
            $product->stock_quantity
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Số lượng mua vượt quá tồn kho hiện tại.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | DỌN CHECKOUT CART CŨ
        |--------------------------------------------------------------------------
        */

        session()->forget(
            'checkout.cart_item_ids'
        );


        /*
        |--------------------------------------------------------------------------
        | LƯU BUY NOW VÀO SESSION
        |--------------------------------------------------------------------------
        */

        session([
            'checkout.mode' =>
                'buy_now',

            'checkout.product_id' =>
                $product->id,

            'checkout.quantity' =>
                $quantity,
        ]);


        return redirect()
            ->route('checkout.index');
    }


    /**
     * Hiển thị trang Checkout.
     */
    public function index(
        Request $request,
        CheckoutService $checkoutService,
        VoucherService $voucherService
    ): View|RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | LẤY SẢN PHẨM CHECKOUT
        |--------------------------------------------------------------------------
        */

        try {

            $checkoutData =
                $checkoutService
                    ->getCheckoutData(
                        $request->user()
                    );

        } catch (
            ValidationException $e
        ) {

            /*
             * Nếu Checkout từ Cart lỗi,
             * quay về Cart.
             *
             * Nếu Buy Now bị lỗi thì hiện tại
             * cũng quay về Cart để Customer
             * kiểm tra lại.
             */
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    $e->validator
                        ->errors()
                        ->first()
                );
        }


        $mode =
            $checkoutData['mode'];


        $checkoutItems =
            $checkoutData[
                'checkoutItems'
            ];


        $subtotal =
            $checkoutData[
                'subtotal'
            ];


        /*
        |--------------------------------------------------------------------------
        | ĐỊA CHỈ CỦA CUSTOMER
        |--------------------------------------------------------------------------
        */

        $addresses =
            $request->user()
                ->addresses()
                ->orderByDesc(
                    'is_default'
                )
                ->orderByDesc(
                    'id'
                )
                ->get();


        $selectedAddress =
            null;


        $selectedAddressId =
            session(
                'checkout.address_id'
            );


        /*
         * Nếu Session đang có Address ID,
         * chỉ lấy trong danh sách Address
         * thuộc Customer hiện tại.
         */
        if ($selectedAddressId) {

            $selectedAddress =
                $addresses->firstWhere(
                    'id',
                    (int)
                    $selectedAddressId
                );
        }


        /*
         * Nếu chưa chọn Address:
         *
         * 1. Ưu tiên Address mặc định.
         * 2. Nếu chưa có mặc định thì lấy
         *    Address đầu tiên.
         */
        if (!$selectedAddress) {

            $selectedAddress =
                $addresses
                    ->firstWhere(
                        'is_default',
                        true
                    )
                ?? $addresses->first();


            if ($selectedAddress) {

                session([
                    'checkout.address_id'
                        => $selectedAddress->id,
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | VOUCHER
        |--------------------------------------------------------------------------
        */

        $appliedVoucher =
            null;


        $discountAmount =
            0;


        $voucherError =
            null;


        $voucherCode =
            session(
                'checkout.voucher_code'
            );


        /*
         * Nếu Customer đã áp Voucher trước đó,
         * mỗi lần mở Checkout vẫn phải
         * kiểm tra Voucher lại từ Database.
         */
        if ($voucherCode) {

            try {

                $result =
                    $voucherService
                        ->validateAndCalculate(
                            $request->user(),
                            $voucherCode,
                            $subtotal
                        );


                $appliedVoucher =
                    $result['voucher'];


                $discountAmount =
                    $result[
                        'discount_amount'
                    ];

            } catch (
                ValidationException $e
            ) {

                /*
                 * Voucher không còn hợp lệ:
                 * xóa khỏi Checkout Session.
                 */
                session()->forget(
                    'checkout.voucher_code'
                );


                $voucherError =
                    $e->validator
                        ->errors()
                        ->first();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PHÍ VẬN CHUYỂN
        |--------------------------------------------------------------------------
        */

        $shippingFee =
            $checkoutService
                ->calculateShippingFee(
                    $selectedAddress
                );


        /*
        |--------------------------------------------------------------------------
        | TỔNG THANH TOÁN
        |--------------------------------------------------------------------------
        |
        | total =
        | subtotal
        | - discount
        | + shipping
        |
        */

        $total =
            max(
                0,
                $subtotal
                - $discountAmount
                + $shippingFee
            );
/*
|--------------------------------------------------------------------------
| PHƯƠNG THỨC THANH TOÁN ĐANG HOẠT ĐỘNG
|--------------------------------------------------------------------------
|
| Chỉ truyền các phương thức đang Active
| sang giao diện Checkout.
|
*/

$paymentSettings =
    PaymentSetting::query()
        ->whereIn(
            'payment_method',
            [
                'COD',
                'QR',
            ]
        )
        ->where(
            'is_enabled',
            true
        )
        ->orderByRaw(
            "
            CASE payment_method
                WHEN 'COD' THEN 1
                WHEN 'QR' THEN 2
                ELSE 3
            END
            "
        )
        ->get()
        ->keyBy(
            'payment_method'
        );

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'checkout.index',
            compact(
                'mode',
    'checkoutItems',
    'subtotal',
    'addresses',
    'selectedAddress',
    'appliedVoucher',
    'discountAmount',
    'shippingFee',
    'total',
    'voucherError',
    'paymentSettings'
            )
        );
    }


    /**
     * Áp dụng Voucher cho Checkout hiện tại.
     */
    public function applyVoucher(
        Request $request,
        CheckoutService $checkoutService,
        VoucherService $voucherService
    ): RedirectResponse {

        $validated =
            $request->validate(
                [
                    'voucher_code' => [
                        'required',
                        'string',
                        'max:50',
                    ],
                ],
                [
                    'voucher_code.required' =>
                        'Vui lòng nhập mã Voucher.',

                    'voucher_code.max' =>
                        'Mã Voucher không hợp lệ.',
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | LẤY LẠI CHECKOUT
        |--------------------------------------------------------------------------
        |
        | Không dùng giá từ Browser.
        |
        */

        $checkoutData =
            $checkoutService
                ->getCheckoutData(
                    $request->user()
                );


        /*
        |--------------------------------------------------------------------------
        | VALIDATE VOUCHER
        |--------------------------------------------------------------------------
        |
        | Kiểm tra:
        | - tồn tại
        | - Active
        | - còn hạn
        | - còn lượt
        | - min order
        | - per user limit
        | - discount
        |
        */

        $voucherService
            ->validateAndCalculate(
                $request->user(),
                $validated[
                    'voucher_code'
                ],
                $checkoutData[
                    'subtotal'
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | LƯU VOUCHER SESSION
        |--------------------------------------------------------------------------
        */

        session([
            'checkout.voucher_code'
                => strtoupper(
                    trim(
                        $validated[
                            'voucher_code'
                        ]
                    )
                ),
        ]);


        return redirect()
            ->route('checkout.index')
            ->with(
                'success',
                'Áp dụng Voucher thành công.'
            );
    }


    /**
     * Bỏ Voucher đang áp dụng.
     */
    public function removeVoucher(): RedirectResponse
    {
        session()->forget(
            'checkout.voucher_code'
        );


        return redirect()
            ->route('checkout.index')
            ->with(
                'success',
                'Đã bỏ Voucher.'
            );
    }
/**
 * Đặt hàng thật.
 */
public function placeOrder(
    Request $request,
    OrderService $orderService
): RedirectResponse {

    $validated = $request->validate(
        [
            'address_id' => [
                'required',
                'integer',
            ],

            'payment_method' => [
                'required',
                'in:COD,QR',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ],
        [
            'address_id.required' =>
                'Vui lòng chọn địa chỉ nhận hàng.',

            'payment_method.required' =>
                'Vui lòng chọn phương thức thanh toán.',

            'payment_method.in' =>
                'Phương thức thanh toán không hợp lệ.',

            'note.max' =>
                'Ghi chú không được vượt quá 1000 ký tự.',
        ]
    );

    /*
|--------------------------------------------------------------------------
| KIỂM TRA PHƯƠNG THỨC THANH TOÁN CÓ ĐANG ACTIVE
|--------------------------------------------------------------------------
|
| Không tin dữ liệu từ giao diện.
| Dù Customer sửa request thủ công,
| phương thức Inactive vẫn bị từ chối.
|
*/

if (
    !PaymentSetting::isEnabled(
        $validated['payment_method']
    )
) {

    return redirect()
        ->route('checkout.index')
        ->withInput()
        ->with(
            'error',
            'Phương thức thanh toán này hiện không khả dụng. Vui lòng chọn phương thức khác.'
        );
}

    try {

        /*
         * Tạo Order bằng Transaction.
         */
        $order =
            $orderService->placeOrder(
                $request->user(),
                (int) $validated['address_id'],
                $validated['payment_method'],
                $validated['note'] ?? null
            );

    } catch (
        ValidationException $e
    ) {

        return redirect()
            ->route('checkout.index')
            ->withInput()
            ->with(
                'error',
                $e->validator
                    ->errors()
                    ->first()
            );

    } catch (\Throwable $e) {

        /*
         * Ghi lỗi vào storage/logs/laravel.log
         */
        report($e);


        return redirect()
            ->route('checkout.index')
            ->withInput()
            ->with(
                'error',
                'Không thể tạo đơn hàng. Vui lòng thử lại.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ĐẶT HÀNG THÀNH CÔNG
    |--------------------------------------------------------------------------
    |
    | Lúc này Order đã được commit.
    | Có thể xóa Checkout Session.
    |
    */

    session()->forget([
        'checkout.mode',
        'checkout.cart_item_ids',
        'checkout.product_id',
        'checkout.quantity',
        'checkout.address_id',
        'checkout.voucher_code',
    ]);


    /*
     * QR:
     * chuyển tới trang thanh toán QR.
     */
    if (
        $order->payment_method
        === 'QR'
    ) {

        return redirect()
            ->route(
                'checkout.qr',
                $order
            );
    }


    /*
     * COD:
     * chuyển thẳng tới trang thành công.
     */
    return redirect()
        ->route(
            'checkout.success',
            $order
        );
}


/**
 * Trang đặt hàng thành công.
 */
public function success(
    Request $request,
    Order $order
): View|RedirectResponse {

    /*
     * Customer chỉ được xem Order
     * của chính mình.
     */
    abort_unless(
        $order->user_id
            === $request->user()->id,
        403
    );


    /*
     * Với QR:
     * chỉ được vào trang thành công
     * sau khi Casso đã xác nhận Paid.
     */
    if (
        $order->payment_method === 'QR'
        &&
        $order->payment_status !== 'paid'
    ) {

        return redirect()
            ->route(
                'checkout.qr',
                $order
            );
    }


    $order->load([
        'details',
        'payments',
        'statusHistories',
        'voucherUsage',
    ]);


    return view(
        'checkout.success',
        compact('order')
    );
}
/**
 * Trang thanh toán QR mô phỏng.
 */
public function qr(
    Request $request,
    Order $order
): View {

    /*
     * Không được xem Order của người khác.
     */
    abort_unless(
        $order->user_id
            === $request->user()->id,
        403
    );


    /*
     * QR chỉ dùng cho Order QR.
     */
    abort_unless(
        $order->payment_method
            === 'QR',
        404
    );


    $payment =
        $order->payments()
            ->latest('id')
            ->firstOrFail();


    return view(
        'checkout.qr',
        compact(
            'order',
            'payment'
        )
    );
}
/**
 * Kiểm tra trạng thái thanh toán QR.
 *
 * Được trang QR gọi định kỳ để biết
 * Casso Webhook đã xác nhận thanh toán hay chưa.
 */
public function qrStatus(
    Request $request,
    Order $order
): JsonResponse {

    /*
     * Customer chỉ được kiểm tra
     * Order của chính mình.
     */
    abort_unless(
        $order->user_id
            === $request->user()->id,
        403
    );


    /*
     * Chỉ áp dụng cho QR.
     */
    abort_unless(
        $order->payment_method === 'QR',
        404
    );


    /*
     * Chỉ trả dữ liệu tối thiểu.
     */
    return response()->json([
        'success' => true,

        'payment_status' =>
            $order->payment_status,

        'paid' =>
            $order->payment_status === 'paid',

        'success_url' =>
            route(
                'checkout.success',
                $order
            ),
    ]);
}

}