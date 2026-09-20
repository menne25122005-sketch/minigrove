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
<style>
    .order-review-box {
        margin-top: 18px;
        padding: 18px;
        border: 1px solid #dce8df;
        border-radius: 14px;
        background: #f8fbf9;
    }
    .order-review-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }
    .order-review-heading strong {
        color: #174d39;
        font-size: 16px;
    }
    .order-review-status {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 999px;
        background: #e5f4eb;
        color: #17613f;
        font-size: 12px;
        font-weight: 700;
    }
    .order-review-stars {
        color: #d79b00;
        letter-spacing: 2px;
        font-size: 18px;
        line-height: 1.2;
    }
    .order-review-comment {
        margin: 8px 0 0;
        color: #40534a;
        white-space: pre-line;
    }
    .order-review-date {
        display: block;
        margin-top: 8px;
        color: #7a8a82;
        font-size: 12px;
    }
    .order-review-form {
        display: grid;
        gap: 12px;
        margin-top: 10px;
    }
    .order-review-form label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #244238;
    }
    .order-review-form select,
    .order-review-form textarea {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #ccdcd2;
        border-radius: 10px;
        background: #fff;
        padding: 10px 12px;
        color: #24352e;
    }
    .order-review-form textarea {
        min-height: 90px;
        resize: vertical;
    }
    .order-review-form input[type="file"] {
        width: 100%;
        box-sizing: border-box;
    }
    .order-review-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }
    .order-review-primary,
    .order-review-secondary,
    .order-review-danger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 8px 14px;
        border-radius: 9px;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
    }
    .order-review-primary {
        background: #17613f;
        color: #fff;
    }
    .order-review-secondary {
        background: #fff;
        border-color: #b8cec0;
        color: #17613f;
    }
    .order-review-danger {
        background: #fff;
        border-color: #e4b8b8;
        color: #a33333;
    }
    .order-review-help {
        margin: 0;
        color: #6d7d75;
        font-size: 13px;
    }
</style>


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



                                @if (
                                    $order->order_status === 'completed'
                                    && $detail->product_id
                                )

                                    @php
                                        $review = $detail->review;
                                    @endphp

                                    <div class="order-review-box">

                                        <div class="order-review-heading">
                                            <strong>
                                                Đánh giá sản phẩm
                                            </strong>

                                            @if ($review)
                                                <span class="order-review-status">
                                                    Đã đánh giá
                                                </span>
                                            @endif
                                        </div>

                                        @if ($review)

                                            <div class="order-review-stars">
                                                @for ($star = 1; $star <= 5; $star++)
                                                    {{ $star <= $review->rating ? '★' : '☆' }}
                                                @endfor
                                            </div>

                                            @if ($review->comment)
                                                <p class="order-review-comment">
                                                    {{ $review->comment }}
                                                </p>
                                            @endif

                                            @if ($review->images->isNotEmpty())
                                                <div
                                                    class="order-review-images"
                                                    style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;"
                                                >
                                                    @foreach ($review->images as $reviewImage)
                                                        <img
                                                            src="{{ asset('storage/' . $reviewImage->image_path) }}"
                                                            alt="Ảnh đánh giá"
                                                            style="width:82px;height:82px;object-fit:cover;border-radius:10px;border:1px solid #dce8df;"
                                                        >
                                                    @endforeach
                                                </div>
                                            @endif

                                            <small class="order-review-date">
                                                Đánh giá lúc
                                                {{ $review->created_at?->format('d/m/Y H:i') }}
                                            </small>

                                            <details style="margin-top: 12px;">
                                                <summary
                                                    class="order-review-secondary"
                                                    style="display:inline-flex; cursor:pointer;"
                                                >
                                                    Sửa đánh giá
                                                </summary>

                                                <form
                                                    method="POST"
                                                    action="{{
                                                        route(
                                                            'customer.reviews.update',
                                                            [
                                                                'product' => $product,
                                                                'review' => $review,
                                                            ]
                                                        )
                                                    }}"
                                                    class="order-review-form"
                                                    enctype="multipart/form-data"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <div>
                                                        <label
                                                            for="rating-edit-{{ $detail->id }}"
                                                        >
                                                            Số sao
                                                        </label>

                                                        <select
                                                            id="rating-edit-{{ $detail->id }}"
                                                            name="rating"
                                                            required
                                                        >
                                                            @for ($star = 5; $star >= 1; $star--)
                                                                <option
                                                                    value="{{ $star }}"
                                                                    @selected((int) $review->rating === $star)
                                                                >
                                                                    {{ $star }} sao
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label
                                                            for="comment-edit-{{ $detail->id }}"
                                                        >
                                                            Nội dung
                                                        </label>

                                                        <textarea
                                                            id="comment-edit-{{ $detail->id }}"
                                                            name="comment"
                                                            maxlength="2000"
                                                            placeholder="Chia sẻ cảm nhận của bạn..."
                                                        >{{ old('comment', $review->comment) }}</textarea>
                                                    </div>


                                                    <div>
                                                        <label
                                                            for="images-edit-{{ $detail->id }}"
                                                        >
                                                            Thêm ảnh đánh giá (không bắt buộc, tối đa 5 ảnh)
                                                        </label>

                                                        <input
                                                            id="images-edit-{{ $detail->id }}"
                                                            type="file"
                                                            name="images[]"
                                                            accept=".jpg,.jpeg,.png,.webp"
                                                            multiple
                                                        >
                                                    </div>

                                                    <div class="order-review-actions">
                                                        <button
                                                            type="submit"
                                                            class="order-review-primary"
                                                        >
                                                            Lưu thay đổi
                                                        </button>
                                                    </div>
                                                </form>
                                            </details>

                                            <div class="order-review-actions">
                                                <form
                                                    method="POST"
                                                    action="{{
                                                        route(
                                                            'customer.reviews.destroy',
                                                            [
                                                                'product' => $product,
                                                                'review' => $review,
                                                            ]
                                                        )
                                                    }}"
                                                    onsubmit="return confirm('Bạn chắc chắn muốn xóa đánh giá này?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="order-review-danger"
                                                    >
                                                        Xóa đánh giá
                                                    </button>
                                                </form>
                                            </div>

                                        @else

                                            <p class="order-review-help">
                                                Đơn hàng đã hoàn thành. Hãy chia sẻ trải nghiệm của bạn về sản phẩm này.
                                            </p>

                                            <form
                                                method="POST"
                                                action="{{
                                                    route(
                                                        'customer.reviews.store',
                                                        $product
                                                    )
                                                }}"
                                                enctype="multipart/form-data"
                                                class="order-review-form"
                                            >
                                                @csrf

                                                <input
                                                    type="hidden"
                                                    name="order_detail_id"
                                                    value="{{ $detail->id }}"
                                                >

                                                <div>
                                                    <label
                                                        for="rating-{{ $detail->id }}"
                                                    >
                                                        Số sao
                                                    </label>

                                                    <select
                                                        id="rating-{{ $detail->id }}"
                                                        name="rating"
                                                        required
                                                    >
                                                        <option value="">
                                                            Chọn số sao
                                                        </option>

                                                        @for ($star = 5; $star >= 1; $star--)
                                                            <option value="{{ $star }}">
                                                                {{ $star }} sao
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>

                                                <div>
                                                    <label
                                                        for="comment-{{ $detail->id }}"
                                                    >
                                                        Nội dung đánh giá
                                                    </label>

                                                    <textarea
                                                        id="comment-{{ $detail->id }}"
                                                        name="comment"
                                                        maxlength="2000"
                                                        placeholder="Chia sẻ cảm nhận của bạn..."
                                                    >{{ old('comment') }}</textarea>
                                                </div>

                                                <div>
                                                    <label
                                                        for="images-{{ $detail->id }}"
                                                    >
                                                        Ảnh sản phẩm (không bắt buộc, tối đa 5 ảnh)
                                                    </label>

                                                    <input
                                                        id="images-{{ $detail->id }}"
                                                        type="file"
                                                        name="images[]"
                                                        accept=".jpg,.jpeg,.png,.webp"
                                                        multiple
                                                    >
                                                </div>

                                                <div class="order-review-actions">
                                                    <button
                                                        type="submit"
                                                        class="order-review-primary"
                                                    >
                                                        Gửi đánh giá
                                                    </button>
                                                </div>
                                            </form>

                                        @endif

                                    </div>

                                @endif

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