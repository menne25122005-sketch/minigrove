@extends('layouts.admin')


@section(
    'title',
    'Chi tiết đơn hàng - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/admin-orders.css') }}"
>

@endpush


@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | PAYMENT GẦN NHẤT
    |--------------------------------------------------------------------------
    */

    $payment =
        $order
            ->payments
            ->first();


    /*
    |--------------------------------------------------------------------------
    | ORDER STATUS LABEL
    |--------------------------------------------------------------------------
    */

    $statusLabels = [

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

    $paymentLabels = [

        'pending' =>
            'Chờ thanh toán',

        'paid' =>
            'Đã thanh toán',

        'failed' =>
            'Thất bại',

        'refunded' =>
            'Đã hoàn tiền',
    ];


    /*
    |--------------------------------------------------------------------------
    | BUTTON CHUYỂN TRẠNG THÁI
    |--------------------------------------------------------------------------
    */

    $nextStatusLabels = [

        'confirmed' =>
            'Xác nhận đơn',

        'preparing' =>
            'Bắt đầu chuẩn bị',

        'packed' =>
            'Đã đóng gói',

        'shipping' =>
            'Bắt đầu giao hàng',

        'completed' =>
            'Xác nhận giao thành công',
    ];

@endphp


<div class="admin-order-detail-page">


    {{-- =====================================================
         BACK
    ====================================================== --}}

    <div class="admin-order-detail-top">

        <a
            href="{{ route('admin.orders.index') }}"
            class="btn btn-secondary"
        >
            ← Danh sách đơn hàng
        </a>

    </div>


    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <section class="card admin-order-detail-header">

        <div class="admin-order-detail-heading">

            <div>

                <span class="admin-detail-label">
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


            <div class="admin-order-header-status">

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
                        $statusLabels[
                            $order
                                ->order_status
                        ]
                        ??
                        $order
                            ->order_status
                    }}
                </span>

            </div>

        </div>

    </section>


    <div class="admin-order-detail-layout">


        {{-- =================================================
             LEFT
        ================================================== --}}

        <div class="admin-order-detail-main">


            {{-- =============================================
     CUSTOMER ID
============================================== --}}

<section class="card admin-detail-card">

    <div class="admin-detail-card-header">

        <div>

            <h2>
                Định danh Customer
            </h2>

            <p>
                Khu vực Admin chỉ sử dụng mã định danh Customer.
            </p>

        </div>

    </div>


    <div class="admin-detail-info-grid">


        <div>

            <span>
                Customer ID
            </span>

            <strong>
                #{{ $order->user_id }}
            </strong>

        </div>


        <div>

            <span>
                Quyền riêng tư
            </span>

            <strong>
                Thông tin cá nhân được bảo vệ
            </strong>

        </div>


        <div class="admin-detail-full">

            <p>
                Họ tên, Email, số điện thoại,
                thông tin người nhận, địa chỉ giao hàng
                và ghi chú của Customer không được hiển thị
                trong khu vực quản trị.
            </p>

        </div>


    </div>

</section>


            {{-- =============================================
                 PRODUCTS
            ============================================== --}}

            <section class="card admin-detail-card">

                <div class="admin-detail-card-header">

                    <div>

                        <h2>
                            Sản phẩm trong đơn
                        </h2>

                        <p>
                            Snapshot sản phẩm tại thời điểm đặt hàng.
                        </p>

                    </div>

                </div>


                <div class="admin-order-products">

                    @foreach (
                        $order->details
                        as $detail
                    )

                        @php

                            $product =
                                $detail->product;


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


                        <div class="admin-order-product-row">


                            <div class="admin-order-product-image">

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


                            <div class="admin-order-product-info">

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

                                    Loại:

                                    {{
                                        $detail
                                            ->item_type
                                    }}

                                </span>

                            </div>


                            <div class="admin-order-product-price">

                                <span>
                                    Đơn giá
                                </span>

                                <strong>

                                    {{
                                        number_format(
                                            (float)
                                            $detail
                                                ->unit_price,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}đ

                                </strong>

                            </div>


                            <div class="admin-order-product-quantity">

                                <span>
                                    Số lượng
                                </span>

                                <strong>
                                    × {{
                                        $detail
                                            ->quantity
                                    }}
                                </strong>

                            </div>


                            <div class="admin-order-product-subtotal">

                                <span>
                                    Thành tiền
                                </span>

                                <strong>

                                    {{
                                        number_format(
                                            (float)
                                            $detail
                                                ->subtotal,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}đ

                                </strong>

                            </div>

                        </div>

                    @endforeach

                </div>

            </section>


            {{-- =============================================
                 PAYMENT
            ============================================== --}}

            <section class="card admin-detail-card">

                <div class="admin-detail-card-header">

                    <div>

                        <h2>
                            Thanh toán
                        </h2>

                        <p>
                            Thông tin Payment của đơn hàng.
                        </p>

                    </div>

                </div>


                <div class="admin-payment-detail-grid">


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

                        <strong
                            class="
                                payment-status
                                payment-status-{{
                                    $order
                                        ->payment_status
                                }}
                            "
                        >

                            {{
                                $paymentLabels[
                                    $order
                                        ->payment_status
                                ]
                                ??
                                $order
                                    ->payment_status
                            }}

                        </strong>

                    </div>


                    <div>

                        <span>
                            Số tiền
                        </span>

                        <strong>

                            {{
                                number_format(
                                    (float)
                                    (
                                        $payment
                                            ?->amount
                                        ??
                                        $order
                                            ->total_amount
                                    ),
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </strong>

                    </div>


                    <div>

                        <span>
                            Thanh toán lúc
                        </span>

                        <strong>

                            {{
                                $payment
                                    ?->paid_at
                                    ?->format(
                                        'd/m/Y H:i'
                                    )
                                ?? '—'
                            }}

                        </strong>

                    </div>


                    @if (
                        $order
                            ->payment_method
                        === 'QR'
                    )

                        <div>

                            <span>
                                Ngân hàng
                            </span>

                            <strong>
                                {{
                                    $payment
                                        ?->bank_name
                                    ?? '—'
                                }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Chủ tài khoản
                            </span>

                            <strong>
                                {{
                                    $payment
                                        ?->account_name
                                    ?? '—'
                                }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Số tài khoản
                            </span>

                            <strong>
                                {{
                                    $payment
                                        ?->account_number
                                    ?? '—'
                                }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Mã giao dịch
                            </span>

                            <strong>
                                {{
                                    $payment
                                        ?->transaction_code
                                    ?? '—'
                                }}
                            </strong>

                        </div>


                        <div class="admin-detail-full">

                            <span>
                                Nội dung chuyển khoản
                            </span>

                            <strong>
                                {{
                                    $payment
                                        ?->transfer_content
                                    ?? '—'
                                }}
                            </strong>

                        </div>

                    @endif


                    @if (
                        $payment
                        &&
                        $payment->note
                    )

                        <div class="admin-detail-full">

                            <span>
                                Ghi chú Payment
                            </span>

                            <p>
                                {{
                                    $payment
                                        ->note
                                }}
                            </p>

                        </div>

                    @endif

                </div>

            </section>


            {{-- =============================================
                 TIMELINE
            ============================================== --}}

            <section class="card admin-detail-card">

                <div class="admin-detail-card-header">

                    <div>

                        <h2>
                            Lịch sử trạng thái
                        </h2>

                        <p>
                            Các bước xử lý đơn hàng.
                        </p>

                    </div>

                </div>


                <div class="admin-order-timeline">

                    @forelse (
                        $order->statusHistories
                        as $history
                    )

                        <div class="admin-timeline-item">


                            <div class="admin-timeline-marker">

                                <span></span>

                            </div>


                            <div class="admin-timeline-content">

                                <div class="admin-timeline-top">

                                    <strong>

                                        {{
                                            $statusLabels[
                                                $history
                                                    ->status
                                            ]
                                            ??
                                            $history
                                                ->status
                                        }}

                                    </strong>


                                    <time>

                                        {{
                                            $history
                                                ->created_at
                                                ->format(
                                                    'd/m/Y H:i'
                                                )
                                        }}

                                    </time>

                                </div>


                                <p>

                                    {{
                                        $history
                                            ->description
                                        ?: 'Trạng thái đơn hàng đã được cập nhật.'
                                    }}

                                </p>


                                @if (
                                    $history
                                        ->updated_by
                                )

                                    <small>

                                        Người cập nhật:
                                        #{{ $history->updated_by }}

                                    </small>

                                @endif

                            </div>

                        </div>

                    @empty

                        <div class="admin-detail-empty">
                            Chưa có lịch sử trạng thái.
                        </div>

                    @endforelse

                </div>

            </section>


            {{-- =============================================
                 RETURN REQUESTS
            ============================================== --}}

            @if (
                $order
                    ->returnRequests
                    ->isNotEmpty()
            )

                <section class="card admin-detail-card">

                    <div class="admin-detail-card-header">

                        <div>

                            <h2>
                                Yêu cầu trả hàng / hoàn tiền
                            </h2>

                            <p>
                                Đơn hàng đang có yêu cầu sau bán hàng.
                            </p>

                        </div>

                    </div>


                    <div class="admin-return-summary-list">

                        @foreach (
                            $order->returnRequests
                            as $returnRequest
                        )

                            <div class="admin-return-summary">

                                <div>

                                    <strong>
                                        {{
                                            $returnRequest
                                                ->return_code
                                        }}
                                    </strong>

                                    <span>

                                        {{
                                            $returnRequest
                                                ->created_at
                                                ->format(
                                                    'd/m/Y H:i'
                                                )
                                        }}

                                    </span>

                                </div>


                                <div>

                                    <span>
                                        Trạng thái:
                                    </span>

                                    <strong>
                                        {{
                                            $returnRequest
                                                ->status
                                        }}
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        Dự kiến hoàn:
                                    </span>

                                    <strong>

                                        {{
                                            number_format(
                                                (float)
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

                        @endforeach

                    </div>

                </section>

            @endif

        </div>


        {{-- =================================================
             RIGHT SIDEBAR
        ================================================== --}}

        <aside class="admin-order-detail-sidebar">


            {{-- =============================================
                 SUMMARY
            ============================================== --}}

            <section class="card admin-order-summary-card">

                <h2>
                    Tổng thanh toán
                </h2>


                <div class="admin-summary-row">

                    <span>
                        Tạm tính
                    </span>

                    <strong>

                        {{
                            number_format(
                                (float)
                                $order
                                    ->subtotal,
                                0,
                                ',',
                                '.'
                            )
                        }}đ

                    </strong>

                </div>


                <div class="admin-summary-row">

                    <span>
                        Voucher
                    </span>

                    <strong>

                        @if (
                            (float)
                            $order
                                ->discount_amount
                            > 0
                        )

                            -{{
                                number_format(
                                    (float)
                                    $order
                                        ->discount_amount,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        @else

                            0đ

                        @endif

                    </strong>

                </div>


                <div class="admin-summary-row">

                    <span>
                        Phí vận chuyển
                    </span>

                    <strong>

                        {{
                            number_format(
                                (float)
                                $order
                                    ->shipping_fee,
                                0,
                                ',',
                                '.'
                            )
                        }}đ

                    </strong>

                </div>


                <div class="
                    admin-summary-row
                    admin-summary-total
                ">

                    <span>
                        Tổng cộng
                    </span>

                    <strong>

                        {{
                            number_format(
                                (float)
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
                 QR PAYMENT STATUS
            ============================================== --}}

            @if (
                $order
                    ->payment_method
                    === 'QR'
            )

                <section class="card admin-order-action-card">

                    <div class="admin-action-heading">

                        <h2>
                            Thanh toán QR tự động
                        </h2>

                        @if (
                            $order
                                ->payment_status
                            === 'paid'
                        )

                            <p>
                                Giao dịch đã được hệ thống xác nhận
                                tự động qua PayOS Webhook.
                                Admin chỉ theo dõi trạng thái thanh toán,
                                không cần xác nhận thủ công.
                            </p>

                        @elseif (
                            $order
                                ->order_status
                            === 'cancelled'
                        )

                            <p>
                                Đơn hàng đã bị hủy.
                                Admin không thực hiện xác nhận
                                thanh toán QR thủ công.
                            </p>

                        @else

                            <p>
                                Đang chờ PayOS Webhook xác nhận giao dịch.
                                Admin không cần và không thể xác nhận
                                thanh toán thủ công.
                            </p>

                        @endif

                    </div>

                </section>

            @endif


            {{-- =============================================
                 ORDER WORKFLOW ACTION
            ============================================== --}}

            @if ($nextStatus)

                @php

                    /*
                     * QR chưa Paid thì nút xử lý đơn
                     * sẽ bị vô hiệu hóa.
                     */

                    $qrNeedsPayment =
                        $order
                            ->payment_method
                        === 'QR'
                        &&
                        $order
                            ->order_status
                        === 'pending'
                        &&
                        $order
                            ->payment_status
                        !== 'paid';

                @endphp


                <section class="card admin-order-action-card">

                    <div class="admin-action-heading">

                        <h2>
                            Xử lý đơn hàng
                        </h2>

                        <p>
                            Trạng thái tiếp theo:
                            <strong>
                                {{
                                    $statusLabels[
                                        $nextStatus
                                    ]
                                    ??
                                    $nextStatus
                                }}
                            </strong>
                        </p>

                    </div>


                    @if ($qrNeedsPayment)

                        <div class="admin-action-warning">

                            Đơn QR đang chờ PayOS Webhook
                            xác nhận thanh toán trước khi xử lý đơn.

                        </div>

                    @endif


                    <form
                        action="{{
                            route(
                                'admin.orders.status.update',
                                $order
                            )
                        }}"
                        method="POST"
                        onsubmit="
                            return confirm(
                                'Bạn chắc chắn muốn cập nhật trạng thái đơn hàng?'
                            );
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <input
                            type="hidden"
                            name="status"
                            value="{{ $nextStatus }}"
                        >


                        <div class="admin-action-field">

                            <label for="description">
                                Ghi chú xử lý
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                maxlength="500"
                                placeholder="Không bắt buộc"
                            ></textarea>

                        </div>


                        <button
                            type="submit"
                            class="
                                btn
                                btn-primary
                                admin-action-full-button
                            "
                            @disabled($qrNeedsPayment)
                        >

                            {{
                                $nextStatusLabels[
                                    $nextStatus
                                ]
                                ??
                                'Cập nhật trạng thái'
                            }}

                        </button>

                    </form>

                </section>

            @else

                <section class="card admin-order-action-card">

                    <div class="admin-action-heading">

                        <h2>
                            Trạng thái đơn
                        </h2>

                    </div>


                    <div class="admin-order-final-state">

                        @if (
                            $order
                                ->order_status
                            === 'completed'
                        )

                            ✅ Đơn hàng đã hoàn thành.

                        @elseif (
                            $order
                                ->order_status
                            === 'cancelled'
                        )

                            ⛔ Đơn hàng đã bị hủy.

                        @else

                            Không có thao tác tiếp theo.

                        @endif

                    </div>

                </section>

            @endif

        </aside>

    </div>

</div>

@endsection