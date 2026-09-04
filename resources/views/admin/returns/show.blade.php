@extends('layouts.admin')


@section(
    'title',
    'Chi tiết trả hàng / hoàn tiền - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/admin-returns.css') }}"
>

@endpush


@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | ORDER / PAYMENT
    |--------------------------------------------------------------------------
    */

    $order =
        $returnRequest->order;


    $payment =
        $order
            ?->payments
            ?->sortByDesc('id')
            ->first();


    /*
    |--------------------------------------------------------------------------
    | STATUS LABEL
    |--------------------------------------------------------------------------
    */

    $statusLabels = [

        'pending' =>
            'Chờ xử lý',

        'approved' =>
            'Đã chấp nhận',

        'rejected' =>
            'Đã từ chối',

        'returning' =>
            'Đang trả hàng',

        'received' =>
            'Đã nhận hàng',

        'refunded' =>
            'Đã hoàn tiền',
    ];


    /*
    |--------------------------------------------------------------------------
    | REASON
    |--------------------------------------------------------------------------
    */

    $reasonLabels = [

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


    /*
    |--------------------------------------------------------------------------
    | PAYMENT STATUS
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

@endphp


<div class="admin-return-detail-page">


    {{-- =====================================================
         BACK
    ====================================================== --}}

    <div class="admin-return-detail-top">

        <a
            href="{{ route('admin.returns.index') }}"
            class="btn btn-secondary"
        >
            ← Danh sách trả hàng
        </a>

    </div>


    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <section class="card admin-return-detail-header">

        <div class="admin-return-detail-heading">


            <div>

                <span class="admin-return-detail-label">
                    Mã yêu cầu
                </span>


                <h1>
                    {{ $returnRequest->return_code }}
                </h1>


                <p>

                    Gửi lúc

                    {{
                        $returnRequest
                            ->created_at
                            ->format(
                                'd/m/Y H:i'
                            )
                    }}

                </p>

            </div>


            <span
                class="
                    admin-return-status
                    admin-return-status-{{
                        $returnRequest->status
                    }}
                "
            >

                {{
                    $statusLabels[
                        $returnRequest->status
                    ]
                    ??
                    $returnRequest->status
                }}

            </span>

        </div>

    </section>


    <div class="admin-return-detail-layout">


        {{-- =================================================
             LEFT
        ================================================== --}}

        <main class="admin-return-detail-main">


            {{-- =============================================
                 ORDER
            ============================================== --}}

            <section class="card admin-return-detail-card">

                <div class="admin-return-card-header">

                    <div>

                        <h2>
                            Đơn hàng liên quan
                        </h2>

                        <p>
                            Thông tin Order phát sinh yêu cầu trả hàng.
                        </p>

                    </div>

                </div>


                <div class="admin-return-order-info">


                    <div>

                        <span>
                            Mã đơn hàng
                        </span>

                        <strong>
                            {{
                                $order
                                    ?->order_code
                                ?? '—'
                            }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Customer
                        </span>

                        <strong>
                            {{
                                $order
                                    ?->customer_name
                                ??
                                $returnRequest
                                    ->user
                                    ?->name
                                ??
                                '—'
                            }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Số điện thoại
                        </span>

                        <strong>
                            {{
                                $order
                                    ?->phone
                                ?? '—'
                            }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Trạng thái Order
                        </span>

                        <strong>
                            {{
                                $order
                                    ?->order_status
                                === 'completed'
                                    ? 'Hoàn thành'
                                    : (
                                        $order
                                            ?->order_status
                                        ?? '—'
                                    )
                            }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Thanh toán
                        </span>

                        <strong>
                            {{
                                $order
                                    ?->payment_method
                                ?? '—'
                            }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Trạng thái thanh toán
                        </span>

                        <strong>

                            {{
                                $paymentLabels[
                                    $order
                                        ?->payment_status
                                ]
                                ??
                                $order
                                    ?->payment_status
                                ??
                                '—'
                            }}

                        </strong>

                    </div>

                </div>


                @if ($order)

                    <div class="admin-return-order-link">

                        <a
                            href="{{
                                route(
                                    'admin.orders.show',
                                    $order
                                )
                            }}"
                            class="btn btn-secondary"
                        >
                            Xem chi tiết đơn hàng
                        </a>

                    </div>

                @endif

            </section>


            {{-- =============================================
                 ITEMS
            ============================================== --}}

            <section class="card admin-return-detail-card">

                <div class="admin-return-card-header">

                    <div>

                        <h2>
                            Sản phẩm Customer yêu cầu trả
                        </h2>

                        <p>
                            Số lượng và giá trị hoàn được lưu theo yêu cầu.
                        </p>

                    </div>

                </div>


                <div class="admin-return-products">

                    @foreach (
                        $returnRequest->items
                        as $item
                    )

                        @php

                            $detail =
                                $item
                                    ->orderDetail;


                            $product =
                                $detail
                                    ?->product;


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


                        <div class="admin-return-product-row">


                            <div class="admin-return-product-image">

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
                                                ?->item_name
                                            ?? 'Sản phẩm'
                                        }}"
                                    >

                                @else

                                    <span>
                                        🌱
                                    </span>

                                @endif

                            </div>


                            <div class="admin-return-product-info">

                                <strong>
                                    {{
                                        $detail
                                            ?->item_name
                                        ?? 'Sản phẩm'
                                    }}
                                </strong>


                                <span>

                                    Mã:

                                    {{
                                        $detail
                                            ?->item_code
                                        ?? '—'
                                    }}

                                </span>


                                <span>

                                    Loại:

                                    {{
                                        $detail
                                            ?->item_type
                                        ?? '—'
                                    }}

                                </span>

                            </div>


                            <div>

                                <span class="admin-return-product-label">
                                    Đơn giá
                                </span>

                                <strong class="admin-return-product-value">

                                    {{
                                        number_format(
                                            (float)
                                            $item
                                                ->unit_price,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}đ

                                </strong>

                            </div>


                            <div>

                                <span class="admin-return-product-label">
                                    Số lượng trả
                                </span>

                                <strong class="admin-return-product-value">

                                    ×
                                    {{
                                        $item
                                            ->quantity
                                    }}

                                </strong>

                            </div>


                            <div class="admin-return-product-refund">

                                <span class="admin-return-product-label">
                                    Dự kiến hoàn
                                </span>

                                <strong>

                                    {{
                                        number_format(
                                            (float)
                                            $item
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


            {{-- =============================================
                 CUSTOMER REQUEST
            ============================================== --}}

            <section class="card admin-return-detail-card">

                <div class="admin-return-card-header">

                    <div>

                        <h2>
                            Nội dung yêu cầu
                        </h2>

                    </div>

                </div>


                <div class="admin-return-request-content">


                    <div>

                        <span>
                            Lý do
                        </span>

                        <strong>

                            {{
                                $reasonLabels[
                                    $returnRequest
                                        ->reason
                                ]
                                ??
                                $returnRequest
                                    ->reason
                            }}

                        </strong>

                    </div>


                    <div>

                        <span>
                            Mô tả của Customer
                        </span>

                        <p>

                            {{
                                $returnRequest
                                    ->description
                                ?: 'Không có mô tả.'
                            }}

                        </p>

                    </div>

                </div>

            </section>


            {{-- =============================================
                 PROCESSING INFO
            ============================================== --}}

            @if (
                $returnRequest->processed_by
                ||
                $returnRequest->processing_note
            )

                <section class="card admin-return-detail-card">

                    <div class="admin-return-card-header">

                        <div>

                            <h2>
                                Thông tin xử lý
                            </h2>

                        </div>

                    </div>


                    <div class="admin-return-processing-info">


                        <div>

                            <span>
                                Người xử lý gần nhất
                            </span>

                            <strong>

                                {{
                                    $returnRequest
                                        ->processor
                                        ?->name
                                    ??
                                    (
                                        $returnRequest
                                            ->processed_by
                                        ? '#'
                                            . $returnRequest
                                                ->processed_by
                                        : '—'
                                    )
                                }}

                            </strong>

                        </div>


                        <div>

                            <span>
                                Thời gian xử lý gần nhất
                            </span>

                            <strong>

                                {{
                                    $returnRequest
                                        ->processed_at
                                        ?->format(
                                            'd/m/Y H:i'
                                        )
                                    ?? '—'
                                }}

                            </strong>

                        </div>


                        @if (
                            $returnRequest
                                ->processing_note
                        )

                            <div class="admin-return-full">

                                <span>
                                    Ghi chú xử lý
                                </span>

                                <p class="admin-return-note">

                                    {{
                                        $returnRequest
                                            ->processing_note
                                    }}

                                </p>

                            </div>

                        @endif

                    </div>

                </section>

            @endif

        </main>


        {{-- =================================================
             RIGHT
        ================================================== --}}

        <aside class="admin-return-detail-sidebar">


            {{-- =============================================
                 REFUND SUMMARY
            ============================================== --}}

            <section class="card admin-return-refund-card">

                <h2>
                    Hoàn tiền
                </h2>


                <div class="admin-return-summary-row">

                    <span>
                        Trạng thái
                    </span>

                    <strong>

                        {{
                            $statusLabels[
                                $returnRequest
                                    ->status
                            ]
                            ??
                            $returnRequest
                                ->status
                        }}

                    </strong>

                </div>


                <div class="admin-return-summary-row">

                    <span>
                        Payment
                    </span>

                    <strong>

                        {{
                            $paymentLabels[
                                $payment
                                    ?->status
                            ]
                            ??
                            $payment
                                ?->status
                            ??
                            '—'
                        }}

                    </strong>

                </div>


                <div
                    class="
                        admin-return-summary-row
                        admin-return-summary-total
                    "
                >

                    <span>
                        Số tiền yêu cầu hoàn
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

            </section>


            {{-- =============================================
                 PENDING
            ============================================== --}}

            @if (
                $returnRequest->status
                === 'pending'
            )

                <section class="card admin-return-action-card">

                    <div class="admin-return-action-heading">

                        <h2>
                            Xử lý yêu cầu
                        </h2>

                        <p>
                            Kiểm tra thông tin trước khi chấp nhận hoặc từ chối.
                        </p>

                    </div>


                    {{-- APPROVE --}}

                    <form
                        action="{{
                            route(
                                'admin.returns.approve',
                                $returnRequest
                            )
                        }}"
                        method="POST"
                        class="admin-return-action-form"
                        onsubmit="
                            return confirm(
                                'Chấp nhận yêu cầu trả hàng này?'
                            );
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <div class="admin-return-action-field">

                            <label for="approve_note">
                                Ghi chú khi chấp nhận
                            </label>

                            <textarea
                                id="approve_note"
                                name="processing_note"
                                rows="3"
                                maxlength="2000"
                                placeholder="Không bắt buộc"
                            ></textarea>

                        </div>


                        <button
                            type="submit"
                            class="
                                btn
                                btn-primary
                                admin-return-full-button
                            "
                        >
                            Chấp nhận yêu cầu
                        </button>

                    </form>


                    <div class="admin-return-action-divider">
                        hoặc
                    </div>


                    {{-- REJECT --}}

                    <form
                        action="{{
                            route(
                                'admin.returns.reject',
                                $returnRequest
                            )
                        }}"
                        method="POST"
                        class="admin-return-action-form"
                        onsubmit="
                            return confirm(
                                'Bạn chắc chắn muốn từ chối yêu cầu này?'
                            );
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <div class="admin-return-action-field">

                            <label for="reject_note">
                                Lý do từ chối
                                <span>*</span>
                            </label>

                            <textarea
                                id="reject_note"
                                name="processing_note"
                                rows="3"
                                maxlength="2000"
                                required
                                placeholder="Bắt buộc khi từ chối"
                            ></textarea>

                        </div>


                        <button
                            type="submit"
                            class="
                                btn
                                admin-return-reject-button
                                admin-return-full-button
                            "
                        >
                            Từ chối yêu cầu
                        </button>

                    </form>

                </section>

            @endif


            {{-- =============================================
                 APPROVED
            ============================================== --}}

            @if (
                $returnRequest->status
                === 'approved'
            )

                <section class="card admin-return-action-card">

                    <div class="admin-return-action-heading">

                        <h2>
                            Bắt đầu trả hàng
                        </h2>

                        <p>
                            Xác nhận Customer bắt đầu gửi sản phẩm về MiniGrove.
                        </p>

                    </div>


                    <form
                        action="{{
                            route(
                                'admin.returns.returning',
                                $returnRequest
                            )
                        }}"
                        method="POST"
                        onsubmit="
                            return confirm(
                                'Chuyển yêu cầu sang trạng thái Đang trả hàng?'
                            );
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <div class="admin-return-action-field">

                            <label for="returning_note">
                                Hướng dẫn / ghi chú
                            </label>

                            <textarea
                                id="returning_note"
                                name="processing_note"
                                rows="4"
                                maxlength="2000"
                                placeholder="Ví dụ: Vui lòng đóng gói sản phẩm và gửi về MiniGrove..."
                            ></textarea>

                        </div>


                        <button
                            type="submit"
                            class="
                                btn
                                btn-primary
                                admin-return-full-button
                            "
                        >
                            Bắt đầu quá trình trả hàng
                        </button>

                    </form>

                </section>

            @endif


            {{-- =============================================
                 RETURNING
            ============================================== --}}

            @if (
                $returnRequest->status
                === 'returning'
            )

                <section class="card admin-return-action-card">

                    <div class="admin-return-action-heading">

                        <h2>
                            Xác nhận nhận hàng
                        </h2>

                        <p>
                            Chỉ cộng lại Stock đối với sản phẩm còn đủ điều kiện bán.
                        </p>

                    </div>


                    <form
                        action="{{
                            route(
                                'admin.returns.received',
                                $returnRequest
                            )
                        }}"
                        method="POST"
                        onsubmit="
                            return confirm(
                                'Xác nhận MiniGrove đã nhận lại sản phẩm?'
                            );
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <div class="admin-return-restock-warning">

                            <strong>
                                Lưu ý về tồn kho
                            </strong>

                            <p>
                                Hàng hư hỏng, vỡ hoặc không còn đủ điều kiện bán
                                phải để số lượng nhập lại kho bằng 0.
                            </p>

                        </div>


                        @foreach (
                            $returnRequest->items
                            as $item
                        )

                            <div class="admin-return-restock-item">

                                <div>

                                    <strong>

                                        {{
                                            $item
                                                ->orderDetail
                                                ?->item_name
                                            ?? 'Sản phẩm'
                                        }}

                                    </strong>

                                    <span>

                                        Customer trả:
                                        {{
                                            $item
                                                ->quantity
                                        }}

                                    </span>

                                </div>


                                <div>

                                    <label
                                        for="restock_{{
                                            $item->id
                                        }}"
                                    >
                                        Nhập lại kho
                                    </label>


                                    <select
                                        id="restock_{{
                                            $item->id
                                        }}"
                                        name="restock_quantities[{{
                                            $item->id
                                        }}]"
                                    >

                                        @for (
                                            $quantity = 0;
                                            $quantity
                                                <=
                                            $item
                                                ->quantity;
                                            $quantity++
                                        )

                                            <option
                                                value="{{
                                                    $quantity
                                                }}"
                                            >
                                                {{ $quantity }}
                                            </option>

                                        @endfor

                                    </select>

                                </div>

                            </div>

                        @endforeach


                        <div class="admin-return-action-field">

                            <label for="received_note">
                                Ghi chú kiểm tra hàng
                            </label>

                            <textarea
                                id="received_note"
                                name="processing_note"
                                rows="4"
                                maxlength="2000"
                                placeholder="Ghi nhận tình trạng sản phẩm khi nhận lại..."
                            ></textarea>

                        </div>


                        <button
                            type="submit"
                            class="
                                btn
                                btn-primary
                                admin-return-full-button
                            "
                        >
                            Xác nhận đã nhận hàng
                        </button>

                    </form>

                </section>

            @endif


            {{-- =============================================
                 RECEIVED
            ============================================== --}}

            @if (
                $returnRequest->status
                === 'received'
            )

                <section class="card admin-return-action-card">

                    <div class="admin-return-action-heading">

                        <h2>
                            Hoàn tiền
                        </h2>

                        <p>
                            Xác nhận MiniGrove đã thực hiện hoàn tiền cho Customer.
                        </p>

                    </div>


                    <div class="admin-return-refund-confirm">

                        <span>
                            Số tiền hoàn
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


                    <form
                        action="{{
                            route(
                                'admin.returns.refund',
                                $returnRequest
                            )
                        }}"
                        method="POST"
                        onsubmit="
                            return confirm(
                                'Xác nhận đã hoàn tiền cho Customer?'
                            );
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <div class="admin-return-action-field">

                            <label for="refund_note">
                                Ghi chú hoàn tiền
                            </label>

                            <textarea
                                id="refund_note"
                                name="processing_note"
                                rows="4"
                                maxlength="2000"
                                placeholder="Ví dụ: Đã hoàn tiền theo yêu cầu trả hàng..."
                            ></textarea>

                        </div>


                        <button
                            type="submit"
                            class="
                                btn
                                btn-primary
                                admin-return-full-button
                            "
                        >
                            Xác nhận hoàn tiền
                        </button>

                    </form>

                </section>

            @endif


            {{-- =============================================
                 FINAL
            ============================================== --}}

            @if (
                in_array(
                    $returnRequest->status,
                    [
                        'refunded',
                        'rejected',
                    ],
                    true
                )
            )

                <section class="card admin-return-action-card">

                    <div class="admin-return-action-heading">

                        <h2>
                            Trạng thái yêu cầu
                        </h2>

                    </div>


                    <div class="admin-return-final-state">

                        @if (
                            $returnRequest->status
                            === 'refunded'
                        )

                            ✅ Yêu cầu đã hoàn tất hoàn tiền.

                        @else

                            ⛔ Yêu cầu đã bị từ chối.

                        @endif

                    </div>

                </section>

            @endif

        </aside>

    </div>

</div>

@endsection