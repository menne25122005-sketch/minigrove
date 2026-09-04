@extends('layouts.customer')


@section(
    'title',
    'Đơn hàng của tôi - MiniGrove'
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
    | ORDER STATUS LABEL
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
    | PAYMENT STATUS LABEL
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
    | RETURN STATUS LABEL
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
    | RETURN REASON LABEL
    |--------------------------------------------------------------------------
    */

    $returnReasonLabels = [

        'damaged' =>
            'Sản phẩm bị hư hỏng',

        'wrong_item' =>
            'Giao sai sản phẩm',

        'not_as_described' =>
            'Sản phẩm không đúng mô tả',

        'quality_issue' =>
            'Sản phẩm có vấn đề chất lượng',

        'other' =>
            'Lý do khác',
    ];

@endphp


<section class="orders-page">

    <div class="customer-container">


        {{-- =====================================================
             HEADING
        ====================================================== --}}

        <div class="orders-heading">

            <div>

                <h1>
                    Đơn hàng của tôi
                </h1>

                <p>
                    Theo dõi đơn hàng,
                    thanh toán và các yêu cầu
                    trả hàng của bạn.
                </p>

            </div>

        </div>


        {{-- =====================================================
             FLASH MESSAGE
        ====================================================== --}}

        @if (session('success'))

            <div class="orders-alert orders-alert-success">

                {{ session('success') }}

            </div>

        @endif


        @if (session('error'))

            <div class="orders-alert orders-alert-error">

                {{ session('error') }}

            </div>

        @endif


        {{-- =====================================================
             7 TABS
        ====================================================== --}}

        <div class="orders-tabs">

            @foreach (
                $tabs
                as $tabKey => $tabLabel
            )

                <a
                    href="{{
                        route(
                            'orders.index',
                            [
                                'tab' =>
                                    $tabKey,
                            ]
                        )
                    }}"
                    class="
                        orders-tab

                        {{
                            $tab === $tabKey
                                ? 'active'
                                : ''
                        }}
                    "
                >

                    {{ $tabLabel }}

                </a>

            @endforeach

        </div>


        {{-- =====================================================
             TAB: TRẢ HÀNG / HOÀN TIỀN
        ====================================================== --}}

        @if ($tab === 'returns')


            @if (
                !$returnRequests
                || $returnRequests->isEmpty()
            )

                <div class="orders-empty">

                    <div class="orders-empty-icon">
                        ↩
                    </div>


                    <h2>
                        Chưa có yêu cầu trả hàng
                    </h2>


                    <p>
                        Các yêu cầu trả hàng /
                        hoàn tiền của bạn sẽ
                        xuất hiện tại đây.
                    </p>


                    <a
                        href="{{
                            route(
                                'orders.index',
                                [
                                    'tab' =>
                                        'completed',
                                ]
                            )
                        }}"
                        class="orders-main-button"
                    >
                        Xem đơn đã hoàn thành
                    </a>

                </div>


            @else


                <div class="return-request-list">

                    @foreach (
                        $returnRequests
                        as $returnRequest
                    )

                        <article
                            class="
                                return-request-card
                            "
                        >


                            {{-- HEADER --}}

                            <div
                                class="
                                    return-card-header
                                "
                            >

                                <div>

                                    <div
                                        class="
                                            return-card-code
                                        "
                                    >

                                        {{
                                            $returnRequest
                                                ->return_code
                                        }}

                                    </div>


                                    <div
                                        class="
                                            return-card-order
                                        "
                                    >

                                        Đơn hàng:

                                        <strong>

                                            {{
                                                $returnRequest
                                                    ->order
                                                    ?->order_code
                                                ?? 'Không xác định'
                                            }}

                                        </strong>

                                    </div>

                                </div>


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

                            </div>


                            {{-- BODY --}}

                            <div
                                class="
                                    return-card-body
                                "
                            >

                                <div
                                    class="
                                        return-card-row
                                    "
                                >

                                    <span>
                                        Ngày yêu cầu
                                    </span>

                                    <strong>

                                        {{
                                            $returnRequest
                                                ->created_at
                                                ->format(
                                                    'd/m/Y H:i'
                                                )
                                        }}

                                    </strong>

                                </div>


                                <div
                                    class="
                                        return-card-row
                                    "
                                >

                                    <span>
                                        Lý do
                                    </span>

                                    <strong>

                                        {{
                                            $returnReasonLabels[
                                                $returnRequest
                                                    ->reason
                                            ]
                                            ?? $returnRequest
                                                ->reason
                                        }}

                                    </strong>

                                </div>


                                <div
                                    class="
                                        return-card-row
                                    "
                                >

                                    <span>
                                        Sản phẩm yêu cầu trả
                                    </span>

                                    <strong>

                                        {{
                                            $returnRequest
                                                ->items
                                                ->sum(
                                                    'quantity'
                                                )
                                        }}

                                        sản phẩm

                                    </strong>

                                </div>


                                <div
                                    class="
                                        return-card-row
                                        return-card-refund
                                    "
                                >

                                    <span>
                                        Số tiền dự kiến hoàn
                                    </span>

                                    <strong>

                                        {{
                                            number_format(
                                                $returnRequest
                                                    ->refund_amount,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}đ

                                    </strong>

                                </div>

                            </div>


                            {{-- ITEMS --}}

                            <div
                                class="
                                    return-card-items
                                "
                            >

                                @foreach (
                                    $returnRequest->items
                                        ->take(2)
                                    as $returnItem
                                )

                                    <div
                                        class="
                                            return-card-item
                                        "
                                    >

                                        <span>

                                            {{
                                                $returnItem
                                                    ->orderDetail
                                                    ?->item_name
                                                ?? 'Sản phẩm'
                                            }}

                                        </span>


                                        <strong>

                                            ×

                                            {{
                                                $returnItem
                                                    ->quantity
                                            }}

                                        </strong>

                                    </div>

                                @endforeach


                                @if (
                                    $returnRequest
                                        ->items
                                        ->count()
                                    > 2
                                )

                                    <div
                                        class="
                                            return-more
                                        "
                                    >

                                        Và còn

                                        {{
                                            $returnRequest
                                                ->items
                                                ->count()
                                            - 2
                                        }}

                                        sản phẩm khác

                                    </div>

                                @endif

                            </div>


                            {{-- FOOTER --}}

                            <div
                                class="
                                    return-card-footer
                                "
                            >

                                <a
                                    href="{{
                                        route(
                                            'orders.show',
                                            $returnRequest
                                                ->order_id
                                        )
                                    }}"
                                    class="
                                        orders-secondary-button
                                    "
                                >
                                    Xem đơn hàng
                                </a>


                                <a
                                    href="{{
                                        route(
                                            'returns.show',
                                            $returnRequest
                                        )
                                    }}"
                                    class="
                                        orders-main-button
                                    "
                                >
                                    Xem chi tiết
                                </a>

                            </div>

                        </article>

                    @endforeach

                </div>


                <div class="orders-pagination">

                    {{
                        $returnRequests
                            ->links()
                    }}

                </div>

            @endif


        {{-- =====================================================
             CÁC TAB ORDER
        ====================================================== --}}

        @else


            @if (
                !$orders
                || $orders->isEmpty()
            )

                <div class="orders-empty">

                    <div class="orders-empty-icon">
                        📦
                    </div>


                    <h2>
                        Chưa có đơn hàng
                    </h2>


                    <p>
                        Không có đơn hàng nào
                        trong trạng thái này.
                    </p>


                    <a
                        href="{{
                            route(
                                'products.index'
                            )
                        }}"
                        class="orders-main-button"
                    >
                        Khám phá sản phẩm
                    </a>

                </div>


            @else


                <div class="orders-list">


                    @foreach (
                        $orders as $order
                    )


                        <article
                            class="order-card"
                        >


                            {{-- =================================
                                 HEADER
                            ================================== --}}

                            <div
                                class="
                                    order-card-header
                                "
                            >

                                <div>

                                    <div
                                        class="
                                            order-card-code
                                        "
                                    >

                                        {{
                                            $order
                                                ->order_code
                                        }}

                                    </div>


                                    <div
                                        class="
                                            order-card-date
                                        "
                                    >

                                        Đặt lúc

                                        {{
                                            $order
                                                ->created_at
                                                ->format(
                                                    'd/m/Y H:i'
                                                )
                                        }}

                                    </div>

                                </div>


                                <span
                                    class="
                                        order-status
                                        order-status-{{
                                            $order
                                                ->order_status
                                        }}
                                    "
                                >

                                    {{
                                        $orderStatusLabels[
                                            $order
                                                ->order_status
                                        ]
                                        ?? $order
                                            ->order_status
                                    }}

                                </span>

                            </div>


                            {{-- =================================
                                 PRODUCTS
                            ================================== --}}

                            <div
                                class="
                                    order-card-products
                                "
                            >


                                @foreach (
                                    $order->details
                                        ->take(2)
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
                                            order-card-product
                                        "
                                    >


                                        <div
                                            class="
                                                order-product-image
                                            "
                                        >

                                            @if (
                                                $primaryImage
                                            )

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

                                                {{
                                                    number_format(
                                                        $detail
                                                            ->unit_price,
                                                        0,
                                                        ',',
                                                        '.'
                                                    )
                                                }}đ

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


                                @if (
                                    $order
                                        ->details_count
                                    > 2
                                )

                                    <div
                                        class="
                                            order-more-products
                                        "
                                    >

                                        Và còn

                                        {{
                                            $order
                                                ->details_count
                                            - 2
                                        }}

                                        sản phẩm khác

                                    </div>

                                @endif

                            </div>


                            {{-- =================================
                                 FOOTER
                            ================================== --}}

                            <div
                                class="
                                    order-card-footer
                                "
                            >


                                <div
                                    class="
                                        order-card-payment
                                    "
                                >

                                    <span
                                        class="
                                            payment-method
                                        "
                                    >

                                        {{
                                            $order
                                                ->payment_method
                                        }}

                                    </span>


                                    <span>
                                        •
                                    </span>


                                    <span>

                                        {{
                                            $paymentStatusLabels[
                                                $order
                                                    ->payment_status
                                            ]
                                            ?? $order
                                                ->payment_status
                                        }}

                                    </span>

                                </div>


                                <div
                                    class="
                                        order-card-actions
                                    "
                                >


                                    <div
                                        class="
                                            order-card-total
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


                                    <a
                                        href="{{
                                            route(
                                                'orders.show',
                                                $order
                                            )
                                        }}"
                                        class="
                                            orders-main-button
                                        "
                                    >
                                        Xem chi tiết
                                    </a>

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>


                {{-- PAGINATION --}}

                <div class="orders-pagination">

                    {{
                        $orders->links()
                    }}

                </div>

            @endif

        @endif

    </div>

</section>

@endsection