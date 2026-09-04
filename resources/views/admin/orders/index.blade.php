@extends('layouts.admin')


@section(
    'title',
    'Quản lý đơn hàng - MiniGrove'
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
    | LABEL TRẠNG THÁI ORDER
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
    | LABEL PAYMENT
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


<div class="card admin-orders-card">


    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="page-header">

        <div>

            <h1>
                Quản lý đơn hàng
            </h1>

            <p class="admin-orders-subtitle">
                Theo dõi và xử lý các đơn hàng của MiniGrove.
            </p>

        </div>

    </div>


    {{-- =====================================================
         STATUS QUICK FILTER
    ====================================================== --}}

    <div class="order-status-tabs">


        <a
            href="{{ route('admin.orders.index') }}"
            class="
                order-status-tab
                {{
                    !$status
                    ? 'active'
                    : ''
                }}
            "
        >
            Tất cả
        </a>


        @foreach ($statusLabels as $value => $label)

            <a
                href="{{
                    route(
                        'admin.orders.index',
                        array_filter([
                            'status' =>
                                $value,

                            'payment_method' =>
                                $paymentMethod,

                            'keyword' =>
                                $keyword,
                        ])
                    )
                }}"
                class="
                    order-status-tab
                    {{
                        $status === $value
                        ? 'active'
                        : ''
                    }}
                "
            >
                {{ $label }}
            </a>

        @endforeach

    </div>


    {{-- =====================================================
         FILTER
    ====================================================== --}}

    <form
        action="{{ route('admin.orders.index') }}"
        method="GET"
        class="admin-order-filter"
    >


        {{-- SEARCH --}}

        <input
            type="text"
            name="keyword"
            value="{{ $keyword }}"
            placeholder="Mã đơn hoặc Customer ID..."
        >


        {{-- STATUS --}}

        <select name="status">

            <option value="">
                Tất cả trạng thái
            </option>


            @foreach ($statusLabels as $value => $label)

                <option
                    value="{{ $value }}"
                    @selected(
                        $status === $value
                    )
                >
                    {{ $label }}
                </option>

            @endforeach

        </select>


        {{-- PAYMENT METHOD --}}

        <select name="payment_method">

            <option value="">
                Tất cả thanh toán
            </option>


            <option
                value="COD"
                @selected(
                    $paymentMethod === 'COD'
                )
            >
                COD
            </option>


            <option
                value="QR"
                @selected(
                    $paymentMethod === 'QR'
                )
            >
                QR
            </option>

        </select>


        <button
            type="submit"
            class="btn btn-primary"
        >
            Lọc
        </button>


        @if (
            $keyword !== ''
            ||
            $status
            ||
            $paymentMethod
        )

            <a
                href="{{ route('admin.orders.index') }}"
                class="btn btn-secondary"
            >
                Xóa lọc
            </a>

        @endif

    </form>


    {{-- =====================================================
         RESULT INFO
    ====================================================== --}}

    <div class="admin-order-result-info">

        <span>
            Tổng kết quả:
        </span>

        <strong>
            {{ $orders->total() }}
        </strong>

        <span>
            đơn hàng
        </span>

    </div>


    {{-- =====================================================
         TABLE
    ====================================================== --}}

    <div class="table-wrapper">

        <table class="admin-order-table">

            <thead>

                <tr>

                    <th>
                        Mã đơn
                    </th>

                    <th>
                        Customer ID
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

                    <tr>


                        {{-- ORDER CODE --}}

                        <td>

                            <a
                                href="{{
                                    route(
                                        'admin.orders.show',
                                        $order
                                    )
                                }}"
                                class="admin-order-code"
                            >
                                {{
                                    $order
                                        ->order_code
                                }}
                            </a>

                            <div class="admin-order-id">
                                #{{ $order->id }}
                            </div>

                        </td>


                        {{-- CUSTOMER ID --}}

<td>

    <strong class="admin-customer-name">

        Customer #{{ $order->user_id }}

    </strong>


    <span class="admin-customer-info">

        ID #{{ $order->user_id }}

    </span>

</td>


                        {{-- ITEMS --}}

                        <td>

                            <span class="order-item-count">

                                {{
                                    $order
                                        ->details_count
                                }}

                                sản phẩm

                            </span>

                        </td>


                        {{-- TOTAL --}}

                        <td>

                            <strong class="admin-order-total">

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

                        </td>


                        {{-- PAYMENT --}}

                        <td>

                            <div class="order-payment-method">

                                <span
                                    class="
                                        payment-method-badge
                                        payment-method-{{
                                            strtolower(
                                                $order
                                                    ->payment_method
                                            )
                                        }}
                                    "
                                >
                                    {{
                                        $order
                                            ->payment_method
                                    }}
                                </span>

                            </div>


                            <span
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

                            </span>

                        </td>


                        {{-- ORDER STATUS --}}

                        <td>

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

                        </td>


                        {{-- CREATED AT --}}

                        <td>

                            <span class="admin-order-date">

                                {{
                                    $order
                                        ->created_at
                                        ->format(
                                            'd/m/Y'
                                        )
                                }}

                            </span>


                            <span class="admin-order-time">

                                {{
                                    $order
                                        ->created_at
                                        ->format(
                                            'H:i'
                                        )
                                }}

                            </span>

                        </td>


                        {{-- ACTION --}}

                        <td>

                            <div class="actions">

                                <a
                                    href="{{
                                        route(
                                            'admin.orders.show',
                                            $order
                                        )
                                    }}"
                                    class="btn btn-primary"
                                >
                                    Xem
                                </a>

                            </div>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="empty admin-orders-empty"
                        >

                            <div class="admin-orders-empty-icon">
                                📦
                            </div>

                            <strong>
                                Không có đơn hàng phù hợp.
                            </strong>

                            <p>
                                Thử thay đổi điều kiện tìm kiếm hoặc bộ lọc.
                            </p>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- =====================================================
         PAGINATION
    ====================================================== --}}

    @if (
        $orders->hasPages()
    )

        <div class="pagination">
            {{ $orders->links() }}
        </div>

    @endif

</div>

@endsection