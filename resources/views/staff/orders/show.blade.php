@extends('layouts.staff')

@section('title', 'Chi tiết đơn hàng - Staff')


@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/staff-orders.css') }}"
    >

@endpush


@section('content')

@php

    $statusLabels = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'preparing' => 'Đang chuẩn bị',
        'packed' => 'Đã đóng gói',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];


    $paymentLabels = [
        'pending' => 'Chờ thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thất bại',
        'refunded' => 'Đã hoàn tiền',
    ];


    $nextStatusLabels = [
        'confirmed' => 'Xác nhận đơn',
        'preparing' => 'Chuyển sang Đang chuẩn bị',
        'packed' => 'Chuyển sang Đã đóng gói',
        'shipping' => 'Chuyển sang Đang giao',
        'completed' => 'Hoàn thành đơn',
    ];


    $latestPayment =
        $order->payments->first();


    $qrWaiting =
        $order->order_status === 'pending'
        &&
        $order->payment_method === 'QR'
        &&
        $order->payment_status !== 'paid';

@endphp


<div class="staff-order-detail-page">


    {{-- =========================================================
         BACK
    ========================================================== --}}
    <div>

        <a
            href="{{ route('staff.orders.index') }}"
            class="
                staff-order-btn
                staff-order-btn-secondary
            "
        >
            ← Quay lại Đơn hàng
        </a>

    </div>



    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="staff-order-detail-header">

        <div>

            <h1>
                {{ $order->order_code }}
            </h1>

            <p>
                Chi tiết nghiệp vụ đơn hàng #{{ $order->id }}
            </p>

        </div>


        <span
            class="
                staff-order-status
                staff-order-status-{{ $order->order_status }}
            "
        >
            {{
                $statusLabels[
                    $order->order_status
                ]
                ?? $order->order_status
            }}
        </span>

    </div>



    {{-- =========================================================
         PRIVACY
    ========================================================== --}}
    <div class="staff-orders-privacy">

        Staff chỉ nhận diện khách hàng bằng

        <strong>
            Customer #{{ $order->user_id }}
        </strong>.

        Thông tin cá nhân và thông tin nhận hàng
        của Customer không được hiển thị.

    </div>



    {{-- =========================================================
         ORDER INFO
    ========================================================== --}}
    <div class="staff-order-detail-grid">


        <div class="staff-order-detail-card">

            <h2>
                Thông tin đơn hàng
            </h2>


            <div class="staff-order-info-row">

                <span>
                    Mã đơn
                </span>

                <strong>
                    {{ $order->order_code }}
                </strong>

            </div>


            <div class="staff-order-info-row">

                <span>
                    Customer
                </span>

                <strong>
                    Customer #{{ $order->user_id }}
                </strong>

            </div>


            <div class="staff-order-info-row">

                <span>
                    Ngày đặt
                </span>

                <strong>
                    {{
                        optional(
                            $order->created_at
                        )->format(
                            'd/m/Y H:i'
                        )
                    }}
                </strong>

            </div>


            <div class="staff-order-info-row">

                <span>
                    Trạng thái
                </span>

                <strong>
                    {{
                        $statusLabels[
                            $order->order_status
                        ]
                        ?? $order->order_status
                    }}
                </strong>

            </div>

        </div>



        {{-- =====================================================
             PAYMENT
        ====================================================== --}}
        <div class="staff-order-detail-card">

            <h2>
                Thanh toán
            </h2>


            <div class="staff-order-info-row">

                <span>
                    Phương thức
                </span>

                <strong>
                    {{ $order->payment_method }}
                </strong>

            </div>


            <div class="staff-order-info-row">

                <span>
                    Trạng thái
                </span>

                <strong>
                    {{
                        $paymentLabels[
                            $order->payment_status
                        ]
                        ?? $order->payment_status
                    }}
                </strong>

            </div>


            @if ($latestPayment)

                <div class="staff-order-info-row">

                    <span>
                        Giá trị Payment
                    </span>

                    <strong>
                        {{
                            number_format(
                                (float) $latestPayment->amount,
                                0,
                                ',',
                                '.'
                            )
                        }}đ
                    </strong>

                </div>


                @if ($latestPayment->paid_at)

                    <div class="staff-order-info-row">

                        <span>
                            Thời gian thanh toán
                        </span>

                        <strong>
                            {{
                                $latestPayment
                                    ->paid_at
                                    ->format(
                                        'd/m/Y H:i'
                                    )
                            }}
                        </strong>

                    </div>

                @endif

            @endif


            @if ($order->payment_method === 'QR')

                <div class="staff-order-payment-note">

                    Staff không có quyền xác nhận
                    thanh toán QR.

                    Việc xác nhận QR do Admin thực hiện.

                </div>

            @endif

        </div>


    </div>



    {{-- =========================================================
         MONEY
    ========================================================== --}}
    <div class="staff-order-detail-card">

        <h2>
            Giá trị đơn hàng
        </h2>


        <div class="staff-order-money-grid">


            <div>

                <span>
                    Tạm tính
                </span>

                <strong>
                    {{
                        number_format(
                            (float) $order->subtotal,
                            0,
                            ',',
                            '.'
                        )
                    }}đ
                </strong>

            </div>


            <div>

                <span>
                    Giảm giá
                </span>

                <strong>
                    -{{
                        number_format(
                            (float) $order->discount_amount,
                            0,
                            ',',
                            '.'
                        )
                    }}đ
                </strong>

            </div>


            <div>

                <span>
                    Phí vận chuyển
                </span>

                <strong>
                    {{
                        number_format(
                            (float) $order->shipping_fee,
                            0,
                            ',',
                            '.'
                        )
                    }}đ
                </strong>

            </div>


            <div class="staff-order-total">

                <span>
                    Tổng tiền
                </span>

                <strong>
                    {{
                        number_format(
                            (float) $order->total_amount,
                            0,
                            ',',
                            '.'
                        )
                    }}đ
                </strong>

            </div>


        </div>

    </div>



    {{-- =========================================================
         PRODUCTS
    ========================================================== --}}
    <div class="staff-order-detail-card">

        <div class="staff-order-card-heading">

            <h2>
                Sản phẩm trong đơn
            </h2>

            <span>
                {{ $order->details->count() }} dòng sản phẩm
            </span>

        </div>


        <div class="staff-order-products-wrapper">

            <table class="staff-order-products-table">

                <thead>

                    <tr>

                        <th>
                            Sản phẩm
                        </th>

                        <th>
                            Mã
                        </th>

                        <th>
                            Đơn giá
                        </th>

                        <th>
                            Số lượng
                        </th>

                        <th>
                            Thành tiền
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach ($order->details as $detail)

                        <tr>

                            <td>

                                <strong>
                                    {{ $detail->item_name }}
                                </strong>

                                <small>
                                    {{ $detail->item_type }}
                                </small>

                            </td>


                            <td>
                                {{ $detail->item_code }}
                            </td>


                            <td>
                                {{
                                    number_format(
                                        (float) $detail->unit_price,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}đ
                            </td>


                            <td>
                                {{ $detail->quantity }}
                            </td>


                            <td>

                                <strong>
                                    {{
                                        number_format(
                                            (float) $detail->subtotal,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}đ
                                </strong>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>



    {{-- =========================================================
         VOUCHER
    ========================================================== --}}
    @if ($order->voucherUsage)

        <div class="staff-order-detail-card">

            <h2>
                Voucher
            </h2>


            <div class="staff-order-info-row">

                <span>
                    Mã Voucher
                </span>

                <strong>
                    {{
                        $order
                            ->voucherUsage
                            ->voucher
                            ->code
                        ?? 'Không xác định'
                    }}
                </strong>

            </div>


            <div class="staff-order-info-row">

                <span>
                    Số tiền giảm
                </span>

                <strong>
                    {{
                        number_format(
                            (float)
                            $order
                                ->voucherUsage
                                ->discount_amount,
                            0,
                            ',',
                            '.'
                        )
                    }}đ
                </strong>

            </div>


            <div class="staff-order-info-row">

                <span>
                    Trạng thái Voucher
                </span>

                <strong>
                    {{
                        $order
                            ->voucherUsage
                            ->status
                    }}
                </strong>

            </div>

        </div>

    @endif



    {{-- =========================================================
         RETURN REQUESTS
    ========================================================== --}}
    @if ($order->returnRequests->isNotEmpty())

        <div class="staff-order-detail-card">

            <h2>
                Trả hàng / Hoàn tiền
            </h2>


            @foreach ($order->returnRequests as $returnRequest)

                <div class="staff-order-return-row">

                    <div>

                        <strong>
                            {{ $returnRequest->return_code }}
                        </strong>

                        <small>
                            {{
                                optional(
                                    $returnRequest->created_at
                                )->format(
                                    'd/m/Y H:i'
                                )
                            }}
                        </small>

                    </div>


                    <div>

                        <span>
                            {{ $returnRequest->status }}
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

    @endif



    {{-- =========================================================
         STATUS HISTORY
    ========================================================== --}}
    <div class="staff-order-detail-card">

        <h2>
            Lịch sử trạng thái
        </h2>


        <div class="staff-order-timeline">

            @forelse ($order->statusHistories as $history)

                <div class="staff-order-timeline-item">

                    <div class="staff-order-timeline-dot"></div>


                    <div>

                        <strong>
                            {{
                                $statusLabels[
                                    $history->status
                                ]
                                ?? $history->status
                            }}
                        </strong>

                        <small>
                            {{
                                optional(
                                    $history->created_at
                                )->format(
                                    'd/m/Y H:i'
                                )
                            }}
                        </small>

                    </div>

                </div>

            @empty

                <div class="staff-order-muted">
                    Chưa có lịch sử trạng thái.
                </div>

            @endforelse

        </div>

    </div>



    {{-- =========================================================
         UPDATE STATUS
    ========================================================== --}}
    <div class="staff-order-detail-card">

        <h2>
            Xử lý đơn hàng
        </h2>


        @if ($nextStatus)


            @if ($qrWaiting)

                <div class="staff-order-warning">

                    Đơn QR chưa được Admin xác nhận thanh toán.

                    Staff chưa thể chuyển đơn từ
                    <strong>Chờ xác nhận</strong>
                    sang
                    <strong>Đã xác nhận</strong>.

                </div>

            @else

                <form
                    method="POST"
                    action="{{
                        route(
                            'staff.orders.status.update',
                            $order->id
                        )
                    }}"
                    class="staff-order-status-form"
                >

                    @csrf
                    @method('PATCH')


                    <input
                        type="hidden"
                        name="status"
                        value="{{ $nextStatus }}"
                    >


                    <div class="staff-order-form-group">

                        <label for="description">
                            Ghi chú nghiệp vụ
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            maxlength="500"
                            placeholder="Có thể để trống..."
                        >{{ old('description') }}</textarea>

                        <small>
                            Không nhập thông tin cá nhân
                            hoặc thông tin nhận hàng của Customer.
                        </small>

                    </div>


                    <button
                        type="submit"
                        class="
                            staff-order-btn
                            staff-order-btn-primary
                        "
                        onclick="
                            return confirm(
                                'Bạn có chắc muốn cập nhật trạng thái đơn hàng?'
                            );
                        "
                    >
                        {{
                            $nextStatusLabels[
                                $nextStatus
                            ]
                            ?? 'Cập nhật trạng thái'
                        }}
                    </button>

                </form>

            @endif


        @else

            <div class="staff-order-finished">

                Đơn hàng hiện không còn trạng thái
                tiếp theo để Staff xử lý.

            </div>

        @endif


    </div>


</div>

@endsection