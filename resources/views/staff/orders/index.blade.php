@extends('layouts.staff')

@section('title', 'Đơn hàng - Staff')


@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/staff-orders.css') }}"
    >

@endpush


@section('content')


<div class="staff-orders-page">


    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="staff-orders-header">

        <div>

            <h1>
                Đơn hàng
            </h1>

            <p>
                Theo dõi và xử lý trạng thái đơn hàng MiniGrove.
            </p>

        </div>

    </div>



    {{-- =========================================================
         PRIVACY NOTE
    ========================================================== --}}
    <div class="staff-orders-privacy">

        Staff chỉ sử dụng
        <strong>Customer ID</strong>
        để nhận diện khách hàng.

        Thông tin cá nhân, số điện thoại,
        email và địa chỉ nhận hàng của Customer
        không được hiển thị.

    </div>



    {{-- =========================================================
         FILTER
    ========================================================== --}}
    <div class="staff-orders-section">


        <form
            method="GET"
            action="{{ route('staff.orders.index') }}"
            class="staff-orders-filter"
        >


            {{-- SEARCH --}}
            <input
                type="text"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="Mã đơn hoặc Customer ID..."
                autocomplete="off"
            >



            {{-- STATUS --}}
            <select name="status">

                <option value="">
                    Tất cả trạng thái
                </option>

                <option
                    value="pending"
                    {{ $status === 'pending' ? 'selected' : '' }}
                >
                    Chờ xác nhận
                </option>

                <option
                    value="confirmed"
                    {{ $status === 'confirmed' ? 'selected' : '' }}
                >
                    Đã xác nhận
                </option>

                <option
                    value="preparing"
                    {{ $status === 'preparing' ? 'selected' : '' }}
                >
                    Đang chuẩn bị
                </option>

                <option
                    value="packed"
                    {{ $status === 'packed' ? 'selected' : '' }}
                >
                    Đã đóng gói
                </option>

                <option
                    value="shipping"
                    {{ $status === 'shipping' ? 'selected' : '' }}
                >
                    Đang giao
                </option>

                <option
                    value="completed"
                    {{ $status === 'completed' ? 'selected' : '' }}
                >
                    Hoàn thành
                </option>

                <option
                    value="cancelled"
                    {{ $status === 'cancelled' ? 'selected' : '' }}
                >
                    Đã hủy
                </option>

            </select>



            {{-- PAYMENT METHOD --}}
            <select name="payment_method">

                <option value="">
                    Tất cả thanh toán
                </option>

                <option
                    value="COD"
                    {{ $paymentMethod === 'COD' ? 'selected' : '' }}
                >
                    COD
                </option>

                <option
                    value="QR"
                    {{ $paymentMethod === 'QR' ? 'selected' : '' }}
                >
                    QR
                </option>

            </select>



            <button
                type="submit"
                class="
                    staff-order-btn
                    staff-order-btn-primary
                "
            >
                Lọc
            </button>



            <a
                href="{{ route('staff.orders.index') }}"
                class="
                    staff-order-btn
                    staff-order-btn-secondary
                "
            >
                Đặt lại
            </a>


        </form>


    </div>



    {{-- =========================================================
         TABLE
    ========================================================== --}}
    <div class="staff-orders-section">


        <div class="staff-orders-section-heading">

            <div>

                <h2>
                    Danh sách đơn hàng
                </h2>

                <p>
                    Chỉ hiển thị dữ liệu nghiệp vụ cần thiết.
                </p>

            </div>

        </div>



        <div class="staff-orders-table-wrapper">


            <table class="staff-orders-table">


                <thead>

                    <tr>

                        <th>
                            Mã đơn
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Sản phẩm
                        </th>

                        <th>
                            Tổng tiền
                        </th>

                        <th>
                            Thanh toán
                        </th>

                        <th>
                            Trạng thái đơn
                        </th>

                        <th>
                            Ngày đặt
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>



                <tbody>


                    @forelse ($orders as $order)


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

                        @endphp



                        <tr>


                            {{-- ORDER CODE --}}
                            <td>

                                <strong class="staff-order-code">

                                    {{ $order->order_code }}

                                </strong>

                                <small class="staff-order-muted">

                                    ID #{{ $order->id }}

                                </small>

                            </td>



                            {{-- CUSTOMER --}}
                            <td>

                                <div class="staff-order-customer">

                                    <div class="staff-order-customer-icon">
                                        C
                                    </div>

                                    <div>

                                        <strong>
                                            Customer #{{ $order->user_id }}
                                        </strong>

                                        <small>
                                            ID #{{ $order->user_id }}
                                        </small>

                                    </div>

                                </div>

                            </td>



                            {{-- ITEMS --}}
                            <td>

                                <strong>
                                    {{ $order->details_count }}
                                </strong>

                                <span class="staff-order-muted">
                                    sản phẩm
                                </span>

                            </td>



                            {{-- TOTAL --}}
                            <td class="staff-order-money">

                                {{
                                    number_format(
                                        (float) $order->total_amount,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}đ

                            </td>



                            {{-- PAYMENT --}}
                            <td>

                                <div class="staff-order-payment">

                                    <strong>
                                        {{ $order->payment_method }}
                                    </strong>

                                    <small>

                                        {{
                                            $paymentLabels[
                                                $order->payment_status
                                            ]
                                            ?? $order->payment_status
                                        }}

                                    </small>

                                </div>

                            </td>



                            {{-- ORDER STATUS --}}
                            <td>

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

                            </td>



                            {{-- DATE --}}
                            <td>

                                {{
                                    optional(
                                        $order->created_at
                                    )->format(
                                        'd/m/Y H:i'
                                    )
                                }}

                            </td>



                            {{-- ACTION --}}
                            <td>

                                <a
                                    href="{{
                                        route(
                                            'staff.orders.show',
                                            $order->id
                                        )
                                    }}"
                                    class="
                                        staff-order-btn
                                        staff-order-btn-small
                                        staff-order-btn-secondary
                                    "
                                >
                                    Xem
                                </a>

                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td
                                colspan="8"
                                class="staff-orders-empty"
                            >
                                Không có đơn hàng phù hợp.
                            </td>

                        </tr>


                    @endforelse


                </tbody>


            </table>


        </div>



        {{-- PAGINATION --}}
        @if ($orders->hasPages())

            <div class="staff-orders-pagination">

                {{ $orders->links() }}

            </div>

        @endif


    </div>


</div>


@endsection