<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\PaymentSetting;

use App\Services\CheckoutService;
use App\Services\CustomDesignService;
use App\Services\VoucherService;
use App\Services\OrderService;
use App\Services\OnePayService;
use App\Services\PayOSService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedMail;


class CheckoutController extends Controller
{
    /**
     * Nhận các CartItem được tick
     * từ trang giỏ hàng.
     */
    public function prepareCart(
        Request $request,
        CustomDesignService $customDesignService
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
            ->with([
                'product',
                'customDesign.items.product',
            ])
            ->get();


        if (
            $items->isEmpty()
            || $items->count()
                !== count(array_unique($validated['selected_items']))
        ) {

            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Giỏ hàng đã thay đổi. Vui lòng chọn lại các mục cần thanh toán.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA PRODUCT / CUSTOM DESIGN + STOCK
        |--------------------------------------------------------------------------
        */

        foreach ($items as $item) {
            if ($item->item_type === 'PRODUCT') {
                $product = $item->product;

                if (
                    !$product
                    || !$product->is_active
                ) {
                    return redirect()
                        ->route('cart.index')
                        ->with(
                            'error',
                            'Có sản phẩm không còn được bán.'
                        );
                }

                if (
                    $item->quantity < 1
                    || $item->quantity > $product->stock_quantity
                ) {
                    return redirect()
                        ->route('cart.index')
                        ->with(
                            'error',
                            'Có sản phẩm không còn đủ số lượng tồn kho.'
                        );
                }

                continue;
            }

            if (
                $item->item_type === 'CUSTOM_DESIGN'
                && $item->customDesign
                && $item->customDesign->user_id === $request->user()->id
            ) {
                try {
                    $customDesignService->recalculate(
                        $item->customDesign
                    );
                } catch (ValidationException $exception) {
                    return redirect()
                        ->route('cart.index')
                        ->with(
                            'error',
                            $exception->validator
                                ->errors()
                                ->first()
                        );
                }

                continue;
            }

            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Có mục trong giỏ hàng không hợp lệ.'
                );
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
|
| Chỉ sử dụng địa chỉ có đầy đủ mã GHN.
|
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


/*
|--------------------------------------------------------------------------
| ĐỊA CHỈ ĐANG ĐƯỢC CHỌN
|--------------------------------------------------------------------------
*/

$selectedAddress = null;

$selectedAddressId =
    session(
        'checkout.address_id'
    );


/*
|--------------------------------------------------------------------------
| 1. ƯU TIÊN ADDRESS ĐANG CÓ TRONG SESSION
|--------------------------------------------------------------------------
|
| Nhưng phải kiểm tra Address đó có mã GHN.
|
*/

if ($selectedAddressId) {

    $candidate =
        $addresses->firstWhere(
            'id',
            (int) $selectedAddressId
        );

    if (
        $candidate
        &&
        $candidate->ghn_district_id
        &&
        $candidate->ghn_ward_code
    ) {

        $selectedAddress =
            $candidate;
    }
}


/*
|--------------------------------------------------------------------------
| 2. NẾU SESSION CHỨA ADDRESS KHÔNG HỢP LỆ
|--------------------------------------------------------------------------
|
| Tìm địa chỉ mặc định có mã GHN hợp lệ.
|
*/

if (!$selectedAddress) {

    $selectedAddress =
        $addresses->first(
            function ($address) {

                return
                    !empty(
                        $address->ghn_district_id
                    )
                    &&
                    !empty(
                        $address->ghn_ward_code
                    )
                    &&
                    $address->is_default;
            }
        );
}


/*
|--------------------------------------------------------------------------
| 3. NẾU KHÔNG CÓ ADDRESS MẶC ĐỊNH HỢP LỆ
|--------------------------------------------------------------------------
|
| Lấy địa chỉ đầu tiên có mã GHN hợp lệ.
|
*/

if (!$selectedAddress) {

    $selectedAddress =
        $addresses->first(
            function ($address) {

                return
                    !empty(
                        $address->ghn_district_id
                    )
                    &&
                    !empty(
                        $address->ghn_ward_code
                    );
            }
        );
}


/*
|--------------------------------------------------------------------------
| 4. CẬP NHẬT SESSION
|--------------------------------------------------------------------------
|
| Đảm bảo Checkout luôn sử dụng Address
| mà GHN có thể tính phí.
|
*/

if ($selectedAddress) {

    session([
        'checkout.address_id'
            => $selectedAddress->id,
    ]);
} else {

    /*
     * Không có địa chỉ GHN hợp lệ.
     * Xóa Address ID cũ để tránh gọi GHN
     * với mã NULL/0.
     */
    session()->forget(
        'checkout.address_id'
    );
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
            $selectedAddress,
            $checkoutItems
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
                'ONEPAY',
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
        | VOUCHER ĐÃ LƯU CỦA CUSTOMER
        |--------------------------------------------------------------------------
        |
        | Hiển thị trong dropdown ở ô Voucher.
        | Mỗi Voucher vẫn được kiểm tra lại theo
        | subtotal hiện tại trước khi cho chọn.
        |
        */

        $savedVouchers = $request->user()
            ->savedVouchers()
            ->orderByPivot(
                'created_at',
                'desc'
            )
            ->get();

        $savedVouchers->each(
            function ($voucher) use (
                $request,
                $voucherService,
                $subtotal
            ) {
                try {
                    $result = $voucherService
                        ->validateAndCalculate(
                            $request->user(),
                            $voucher->code,
                            $subtotal
                        );

                    $voucher->setAttribute(
                        'checkout_eligible',
                        true
                    );

                    $voucher->setAttribute(
                        'checkout_discount_amount',
                        $result['discount_amount']
                    );

                    $voucher->setAttribute(
                        'checkout_error',
                        null
                    );
                } catch (
                    ValidationException $exception
                ) {
                    $voucher->setAttribute(
                        'checkout_eligible',
                        false
                    );

                    $voucher->setAttribute(
                        'checkout_discount_amount',
                        0
                    );

                    $voucher->setAttribute(
                        'checkout_error',
                        $exception->validator
                            ->errors()
                            ->first()
                    );
                }
            }
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
    'paymentSettings',
    'savedVouchers'
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
    OrderService $orderService,
    OnePayService $onePayService,
    PayOSService $payOSService
): RedirectResponse {

    $validated = $request->validate(
        [
            'address_id' => [
                'required',
                'integer',
            ],

            'payment_method' => [
    'required',
    'in:COD,QR,ONEPAY',
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

if (
    $validated['payment_method'] === 'QR'
    && !$payOSService->configured()
) {
    return redirect()
        ->route('checkout.index')
        ->withInput()
        ->with(
            'error',
            'PayOS chưa được cấu hình. Vui lòng liên hệ Admin hoặc chọn phương thức thanh toán khác.'
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
            /*
|--------------------------------------------------------------------------
| GỬI EMAIL XÁC NHẬN ĐƠN HÀNG
|--------------------------------------------------------------------------
|
| Order đã được tạo thành công và transaction đã commit.
|
*/

try {

    Mail::to($request->user()->email)
        ->send(
            new OrderPlacedMail(
                $order
            )
        );

} catch (\Throwable $e) {

    /*
     * Email lỗi không được làm mất đơn hàng.
     */
    report($e);
}

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
| XỬ LÝ THANH TOÁN SAU KHI TẠO ORDER
|--------------------------------------------------------------------------
|
| Order và Payment đã được tạo thành công.
|
*/


/*
|--------------------------------------------------------------------------
| ONEPAY
|--------------------------------------------------------------------------
|
| Payment vẫn đang ở trạng thái pending.
| Chuyển khách hàng sang OnePay TEST.
|
*/

if (
    $order->payment_method === 'ONEPAY'
) {

    $onePayUrl = $onePayService->createPaymentUrl(
        $order,
        route('checkout.onepay.return')
    );

    return redirect()->away($onePayUrl);
}


/*
|--------------------------------------------------------------------------
| XÓA CHECKOUT SESSION
|--------------------------------------------------------------------------
|
| Chỉ xóa sau khi xử lý OnePay.
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
|--------------------------------------------------------------------------
| QR
|--------------------------------------------------------------------------
*/

if (
    $order->payment_method === 'QR'
) {
    try {
        $checkoutUrl =
            $payOSService->createCheckoutUrl(
                $order
            );

        return redirect()->away(
            $checkoutUrl
        );
    } catch (\Throwable $e) {
        report($e);

        return redirect()
            ->route(
                'checkout.qr',
                $order
            )
            ->with(
                'error',
                'Chưa thể mở PayOS. Vui lòng thử lại từ trang thanh toán.'
            );
    }
}


/*
|--------------------------------------------------------------------------
| COD
|--------------------------------------------------------------------------
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
     * sau khi PayOS đã xác nhận Paid.
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
    Order $order,
    PayOSService $payOSService
): View|RedirectResponse {

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

    /*
     * Nếu PayOS đã xác nhận trước lúc Customer
     * mở/làm mới trang QR thì chuyển thẳng
     * sang trang thanh toán thành công.
     */
    if ($order->payment_status === 'paid') {
        return redirect()->route(
            'checkout.success',
            $order
        );
    }


    $payment =
        $order->payments()
            ->latest('id')
            ->firstOrFail();

    /*
     * Nếu Order cũ/chưa tạo được link PayOS,
     * thử tạo lại khi Customer mở trang dự phòng.
     */
    if (
        $payment->status !== 'paid'
        && blank(
            $payment->provider_checkout_url
        )
        && $payOSService->configured()
    ) {
        try {
            $payOSService->createCheckoutUrl(
                $order
            );

            $payment->refresh();
        } catch (\Throwable $e) {
            report($e);
        }
    }


    return view(
        'checkout.qr',
        compact(
            'order',
            'payment'
        )
    );
}

/**
 * PayOS chuyển trình duyệt Customer về đây
 * sau khi hoàn tất thanh toán.
 *
 * Query string từ trình duyệt không được dùng
 * để tự đánh dấu Paid. MiniGrove luôn hỏi lại
 * API PayOS trước khi xác nhận.
 */
public function payOSReturn(
    Request $request,
    Order $order,
    PayOSService $payOSService
): RedirectResponse {
    abort_unless(
        $order->user_id
            === $request->user()->id,
        403
    );

    abort_unless(
        $order->payment_method === 'QR',
        404
    );

    try {
        $payOSService->syncOrderStatus(
            $order
        );
    } catch (\Throwable $e) {
        report($e);
    }

    $order->refresh();

    if ($order->payment_status === 'paid') {
        return redirect()->route(
            'checkout.success',
            $order
        );
    }

    return redirect()
        ->route('checkout.qr', $order)
        ->with(
            'error',
            'PayOS chưa xác nhận thanh toán. Hệ thống sẽ tiếp tục kiểm tra tự động.'
        );
}

/**
 * Customer hủy/thoát PayOS.
 * Order vẫn giữ Pending để có thể thanh toán lại.
 */
public function payOSCancel(
    Request $request,
    Order $order
): RedirectResponse {
    abort_unless(
        $order->user_id
            === $request->user()->id,
        403
    );

    abort_unless(
        $order->payment_method === 'QR',
        404
    );

    return redirect()
        ->route('checkout.qr', $order)
        ->with(
            'error',
            'Bạn đã rời PayOS. Đơn hàng vẫn đang chờ thanh toán.'
        );
}

/**
 * Kiểm tra trạng thái thanh toán QR.
 *
 * Được trang QR gọi định kỳ để biết
 * PayOS Webhook đã xác nhận thanh toán hay chưa.
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
     * Luôn đọc lại trạng thái mới nhất từ DB.
     * Webhook PayOS có thể cập nhật Order
     * giữa hai lần polling của trình duyệt.
     */
    $order->refresh();


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