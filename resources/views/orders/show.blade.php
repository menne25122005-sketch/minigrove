@extends('layouts.customer')


@section(
    'title',
    'Chi tiết đơn hàng - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/orders.css') }}"
>

@endpush


@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | ORDER STATUS
    |--------------------------------------------------------------------------
    */

    $orderStatusLabels = [

        'pending' =>
            'Chờ xác nhận',

        'confirmed' =>
            'Đã xác nhận',

        'preparing' =>
            'Đang chuẩn bị',

        'packed' =>
            'Đã đóng gói',

        'shipping' =>
            'Đang giao',

        'completed' =>
            'Hoàn thành',

        'cancelled' =>
            'Đã hủy',
    ];


    /*
    |--------------------------------------------------------------------------
    | PAYMENT STATUS
    |--------------------------------------------------------------------------
    */

    $paymentStatusLabels = [

        'pending' =>
            'Chờ thanh toán',

        'paid' =>
            'Đã thanh toán',

        'failed' =>
            'Thanh toán thất bại',

        'refunded' =>
            'Đã hoàn tiền',
    ];


    /*
    |--------------------------------------------------------------------------
    | TIMELINE LABEL
    |--------------------------------------------------------------------------
    */

    $timelineLabels = [

        'pending' =>
            'Đặt hàng thành công',

        'confirmed' =>
            'MiniGrove đã xác nhận',

        'preparing' =>
            'Đang chuẩn bị Terrarium',

        'packed' =>
            'Đã đóng gói',

        'shipping' =>
            'Đang giao hàng',

        'completed' =>
            'Giao hàng thành công',

        'cancelled' =>
            'Đơn hàng đã hủy',
    ];


    /*
    |--------------------------------------------------------------------------
    | RETURN STATUS
    |--------------------------------------------------------------------------
    */

    $returnStatusLabels = [

        'pending' =>
            'Chờ xử lý',

        'approved' =>
            'Đã chấp nhận',

        'rejected' =>
            'Đã từ chối',

        'returning' =>
            'Đang trả hàng',

        'received' =>
            'Đã nhận hàng trả',

        'refunded' =>
            'Đã hoàn tiền',
    ];


    /*
    |--------------------------------------------------------------------------
    | PAYMENT
    |--------------------------------------------------------------------------
    */

    $payment =
        $order
            ->payments
            ->first();


    /*
    |--------------------------------------------------------------------------
    | VOUCHER
    |--------------------------------------------------------------------------
    */

    $voucherUsage =
        $order
            ->voucherUsage;


    $voucher =
        $voucherUsage
            ?->voucher;

@endphp


<section class="orders-page">

    <div class="customer-container">


        {{-- =====================================================
             BACK
        ====================================================== --}}

        <a
            href="{{ route('orders.index') }}"
            class="orders-back-button"
        >
            ← Quay lại đơn hàng
        </a>


        {{-- =====================================================
             FLASH MESSAGE
        ====================================================== --}}

        @if (session('success'))

            <div
                class="
                    orders-alert
                    orders-alert-success
                "
            >
                {{ session('success') }}
            </div>

        @endif


        @if (session('error'))

            <div
                class="
                    orders-alert
                    orders-alert-error
                "
            >
                {{ session('error') }}
            </div>

        @endif


        @if ($errors->any())

            <div
                class="
                    orders-alert
                    orders-alert-error
                "
            >

                {{ $errors->first() }}

            </div>

        @endif


        {{-- =====================================================
             ORDER HEADING
        ====================================================== --}}

        <div class="order-detail-heading">

            <div>

                <span class="order-detail-label">
                    Mã đơn hàng
                </span>


                <h1>
                    {{ $order->order_code }}
                </h1>


                <p>

                    Đặt lúc

                    {{
                        $order
                            ->created_at
                            ->format(
                                'd/m/Y H:i'
                            )
                    }}

                </p>

            </div>


            <span
                class="
                    order-status
                    order-status-{{
                        $order->order_status
                    }}
                "
            >

                {{
                    $orderStatusLabels[
                        $order->order_status
                    ]
                    ?? $order->order_status
                }}

            </span>

        </div>


        {{-- =====================================================
             MAIN GRID
        ====================================================== --}}

        <div class="order-detail-layout">


            {{-- =================================================
                 LEFT
            ================================================== --}}

            <div class="order-detail-main">


                {{-- =============================================
                     TIMELINE
                ============================================== --}}

                <section class="order-detail-card">

                    <div class="order-detail-title">

                        <div>

                            <strong>
                                Tiến trình đơn hàng
                            </strong>

                            <span>
                                Theo dõi quá trình xử lý đơn hàng.
                            </span>

                        </div>

                    </div>


                    <div class="order-timeline">


                        @forelse (
                            $order->statusHistories
                            as $history
                        )

                            <div
                                class="
                                    order-timeline-item
                                "
                            >


                                <div
                                    class="
                                        order-timeline-marker
                                    "
                                >

                                    <span
                                        class="
                                            order-timeline-dot
                                        "
                                    >
                                    </span>

                                </div>


                                <div
                                    class="
                                        order-timeline-content
                                    "
                                >

                                    <strong>

                                        {{
                                            $timelineLabels[
                                                $history->status
                                            ]
                                            ?? $history->status
                                        }}

                                    </strong>


                                    @if ($history->description)

                                        <p>
                                            {{
                                                $history
                                                    ->description
                                            }}
                                        </p>

                                    @endif


                                    <small>

                                        {{
                                            $history
                                                ->created_at
                                                ->format(
                                                    'd/m/Y H:i'
                                                )
                                        }}

                                    </small>

                                </div>

                            </div>


                        @empty


                            <div
                                class="
                                    order-detail-empty-text
                                "
                            >

                                Chưa có lịch sử trạng thái.

                            </div>

                        @endforelse

                    </div>

                </section>


                {{-- =============================================
                     SHIPPING
                ============================================== --}}

                <section class="order-detail-card">

                    <div class="order-detail-title">

                        <div>

                            <strong>
                                Thông tin nhận hàng
                            </strong>

                            <span>
                                Thông tin được lưu tại thời điểm đặt hàng.
                            </span>

                        </div>

                    </div>


                    <div class="order-shipping-info">


                        <div class="order-shipping-name">

                            {{
                                $order
                                    ->customer_name
                            }}

                        </div>


                        <div class="order-shipping-phone">

                            {{
                                $order
                                    ->phone
                            }}

                        </div>


                        <p>

                            {{
                                $order
                                    ->shipping_address
                            }}

                        </p>


                        @if ($order->email)

                            <span
                                class="
                                    order-shipping-email
                                "
                            >

                                {{
                                    $order
                                        ->email
                                }}

                            </span>

                        @endif

                    </div>

                </section>


                {{-- =============================================
                     PRODUCTS
                ============================================== --}}

                <section class="order-detail-card">

                    <div class="order-detail-title">

                        <div>

                            <strong>
                                Sản phẩm
                            </strong>

                            <span>
                                Các sản phẩm trong đơn hàng.
                            </span>

                        </div>

                    </div>


                    <div class="order-detail-products">


                        @foreach (
                            $order->details
                            as $detail
                        )


                            @php

                                $product =
                                    $detail
                                        ->product;


                                $primaryImage =
                                    $product
                                        ?->images
                                        ?->firstWhere(
                                            'is_primary',
                                            true
                                        )
                                    ??
                                    $product
                                        ?->images
                                        ?->first();

                            @endphp


                            <div
                                class="
                                    order-detail-product
                                "
                            >


                                <div
                                    class="
                                        order-product-image
                                    "
                                >

                                    @if ($primaryImage)

                                        <img
                                            src="{{
                                                asset(
                                                    'storage/'
                                                    . $primaryImage
                                                        ->image_path
                                                )
                                            }}"
                                            alt="{{
                                                $detail
                                                    ->item_name
                                            }}"
                                        >

                                    @else

                                        <span>
                                            🌱
                                        </span>

                                    @endif

                                </div>


                                <div
                                    class="
                                        order-product-info
                                    "
                                >


                                    <strong>

                                        {{
                                            $detail
                                                ->item_name
                                        }}

                                    </strong>


                                    <span>

                                        Mã:

                                        {{
                                            $detail
                                                ->item_code
                                            ?? '—'
                                        }}

                                    </span>


                                    <span>

                                        @if ((float) $order->discount_amount > 0)

    -{{
        number_format(
            $order->discount_amount,
            0,
            ',',
            '.'
        )
    }}đ

@else

    0đ

@endif

                                        ×

                                        {{
                                            $detail
                                                ->quantity
                                        }}

                                    </span>

                                </div>


                                <div
                                    class="
                                        order-product-subtotal
                                    "
                                >

                                    {{
                                        number_format(
                                            $detail
                                                ->subtotal,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}đ

                                </div>

                            </div>

                        @endforeach

                    </div>

                </section>


                {{-- =============================================
                     PAYMENT
                ============================================== --}}

                <section class="order-detail-card">

                    <div class="order-detail-title">

                        <div>

                            <strong>
                                Thanh toán
                            </strong>

                            <span>
                                Thông tin thanh toán của đơn hàng.
                            </span>

                        </div>

                    </div>


                    <div class="order-payment-grid">


                        <div>

                            <span>
                                Phương thức
                            </span>

                            <strong>
                                {{
                                    $order
                                        ->payment_method
                                }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Trạng thái
                            </span>

                            <strong>

                                {{
                                    $paymentStatusLabels[
                                        $order
                                            ->payment_status
                                    ]
                                    ?? $order
                                        ->payment_status
                                }}

                            </strong>

                        </div>


                        @if (
                            $payment
                            && $payment->payment_method
                                === 'QR'
                        )

                            <div>

                                <span>
                                    Ngân hàng
                                </span>

                                <strong>

                                    {{
                                        $payment
                                            ->bank_name
                                        ?? '—'
                                    }}

                                </strong>

                            </div>


                            <div>

                                <span>
                                    Nội dung chuyển khoản
                                </span>

                                <strong>

                                    {{
                                        $payment
                                            ->transfer_content
                                        ?? '—'
                                    }}

                                </strong>

                            </div>

                        @endif

                    </div>


                    @if (
                        $order->payment_method
                            === 'QR'
                        &&
                        $order->payment_status
                            === 'pending'
                    )

                        <div
                            class="
                                order-payment-action
                            "
                        >

                            <a
                                href="{{
                                    route(
                                        'checkout.qr',
                                        $order
                                    )
                                }}"
                                class="
                                    orders-secondary-button
                                "
                            >
                                Xem lại thông tin QR
                            </a>

                        </div>

                    @endif

                </section>


                {{-- =============================================
                     NOTE
                ============================================== --}}

                @if ($order->note)

                    <section
                        class="
                            order-detail-card
                        "
                    >

                        <div class="order-detail-title">

                            <div>

                                <strong>
                                    Ghi chú đơn hàng
                                </strong>

                            </div>

                        </div>


                        <div class="order-note">

                            {{
                                $order
                                    ->note
                            }}

                        </div>

                    </section>

                @endif


                {{-- =============================================
                     RETURN REQUESTS
                ============================================== --}}

                @if (
                    $order
                        ->returnRequests
                        ->isNotEmpty()
                )

                    <section
                        class="
                            order-detail-card
                        "
                    >

                        <div class="order-detail-title">

                            <div>

                                <strong>
                                    Trả hàng / Hoàn tiền
                                </strong>

                                <span>
                                    Các yêu cầu đã gửi cho đơn hàng này.
                                </span>

                            </div>

                        </div>


                        <div
                            class="
                                order-detail-returns
                            "
                        >


                            @foreach (
                                $order->returnRequests
                                as $returnRequest
                            )

                                <div
                                    class="
                                        order-detail-return
                                    "
                                >


                                    <div>

                                        <strong>

                                            {{
                                                $returnRequest
                                                    ->return_code
                                            }}

                                        </strong>


                                        <span>

                                            Gửi lúc

                                            {{
                                                $returnRequest
                                                    ->created_at
                                                    ->format(
                                                        'd/m/Y H:i'
                                                    )
                                            }}

                                        </span>

                                    </div>


                                    <div
                                        class="
                                            order-detail-return-actions
                                        "
                                    >

                                        <span
                                            class="
                                                return-status
                                                return-status-{{
                                                    $returnRequest
                                                        ->status
                                                }}
                                            "
                                        >

                                            {{
                                                $returnStatusLabels[
                                                    $returnRequest
                                                        ->status
                                                ]
                                                ?? $returnRequest
                                                    ->status
                                            }}

                                        </span>


                                        <a
                                            href="{{
                                                route(
                                                    'returns.show',
                                                    $returnRequest
                                                )
                                            }}"
                                            class="
                                                orders-secondary-button
                                            "
                                        >
                                            Xem yêu cầu
                                        </a>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </section>

                @endif

            </div>


            {{-- =================================================
                 RIGHT
            ================================================== --}}

            <aside class="order-detail-sidebar">


                {{-- =============================================
                     SUMMARY
                ============================================== --}}

                <section
                    class="
                        order-summary-card
                    "
                >

                    <h2>
                        Thanh toán đơn hàng
                    </h2>


                    <div class="order-summary-row">

                        <span>
                            Tạm tính
                        </span>

                        <strong>

                            {{
                                number_format(
                                    $order
                                        ->subtotal,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </strong>

                    </div>


                    <div class="order-summary-row">

                        <span>
                            Voucher
                        </span>

                        <strong>

                            -{{
                                number_format(
                                    $order
                                        ->discount_amount,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </strong>

                    </div>


                    @if ($voucher)

                        <div
                            class="
                                order-summary-voucher
                            "
                        >

                            Voucher:

                            <strong>
                                {{
                                    $voucher
                                        ->code
                                }}
                            </strong>

                        </div>

                    @endif


                    <div class="order-summary-row">

                        <span>
                            Phí vận chuyển
                        </span>

                        <strong>

                            {{
                                number_format(
                                    $order
                                        ->shipping_fee,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </strong>

                    </div>


                    <div
                        class="
                            order-summary-row
                            order-summary-total
                        "
                    >

                        <span>
                            Tổng thanh toán
                        </span>

                        <strong>

                            {{
                                number_format(
                                    $order
                                        ->total_amount,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </strong>

                    </div>

                </section>


                {{-- =============================================
                     ACTIONS
                ============================================== --}}

                <section
                    class="
                        order-action-card
                    "
                >

                    <h2>
                        Thao tác
                    </h2>


                    {{-- QR --}}

                    @if (
                        $order->payment_method
                            === 'QR'
                        &&
                        $order->payment_status
                            === 'pending'
                    )

                        <a
                            href="{{
                                route(
                                    'checkout.qr',
                                    $order
                                )
                            }}"
                            class="
                                orders-main-button
                                order-action-full
                            "
                        >
                            Thanh toán bằng QR
                        </a>

                    @endif


                    {{-- RETURN --}}

                    @if ($canRequestReturn)

                        <a
                            href="{{
                                route(
                                    'returns.create',
                                    $order
                                )
                            }}"
                            class="
                                orders-secondary-button
                                order-action-full
                            "
                        >
                            Yêu cầu trả hàng / hoàn tiền
                        </a>

                    @endif


                    {{-- CANCEL --}}

                    @if ($canCancel)

                        <details
                            class="
                                order-cancel-box
                            "
                        >

                            <summary>
                                Hủy đơn hàng
                            </summary>


                            <div
                                class="
                                    order-cancel-content
                                "
                            >

                                <p>
                                    Đơn hàng chỉ có thể
                                    hủy khi đang chờ xác nhận.
                                    Sau khi hủy, thao tác
                                    không thể hoàn tác.
                                </p>


                                <form
                                    action="{{
                                        route(
                                            'orders.cancel',
                                            $order
                                        )
                                    }}"
                                    method="POST"
                                >

                                    @csrf


                                    <label
                                        for="
                                            cancel_reason
                                        "
                                    >
                                        Lý do hủy
                                        <span>
                                            (không bắt buộc)
                                        </span>
                                    </label>


                                    <textarea
                                        id="
                                            cancel_reason
                                        "
                                        name="
                                            cancel_reason
                                        "
                                        rows="4"
                                        maxlength="500"
                                        placeholder="Nhập lý do hủy đơn..."
                                    >{{ old('cancel_reason') }}</textarea>


                                    <button
                                        type="submit"
                                        class="
                                            order-cancel-button
                                        "
                                        onclick="
                                            return confirm(
                                                'Bạn chắc chắn muốn hủy đơn hàng này?'
                                            );
                                        "
                                    >
                                        Xác nhận hủy đơn
                                    </button>

                                </form>

                            </div>

                        </details>

                    @endif


                    @if (
                        !$canCancel
                        &&
                        !$canRequestReturn
                        &&
                        !(
                            $order->payment_method
                                === 'QR'
                            &&
                            $order->payment_status
                                === 'pending'
                        )
                    )

                        <div
                            class="
                                order-no-action
                            "
                        >

                            Hiện chưa có thao tác
                            nào dành cho đơn hàng này.

                        </div>

                    @endif

                </section>

            </aside>

        </div>

    </div>

</section>

@endsection