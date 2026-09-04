@extends('layouts.admin')

@section('title', 'Quản lý thanh toán')


@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/admin-payments.css') }}"
    >

@endpush


@section('content')


<div class="payment-page">


    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="payment-header">

        <div>

            <h1>
                Thanh toán
            </h1>

            <p>
                Theo dõi giao dịch và cấu hình
                phương thức thanh toán của MiniGrove.
            </p>

        </div>

    </div>


    {{-- =========================================================
         THỐNG KÊ
    ========================================================== --}}
    <div class="payment-stats">


        {{-- TỔNG GIAO DỊCH --}}
        <div class="payment-stat-card">

            <div class="payment-stat-title">
                Tổng giao dịch
            </div>

            <div class="payment-stat-number">
                {{ $totalCount }}
            </div>

        </div>


        {{-- CHỜ THANH TOÁN --}}
        <div class="payment-stat-card">

            <div class="payment-stat-title">
                Chờ thanh toán
            </div>

            <div class="payment-stat-number">
                {{ $pendingCount }}
            </div>

        </div>


        {{-- ĐÃ THANH TOÁN --}}
        <div class="payment-stat-card">

            <div class="payment-stat-title">
                Đã thanh toán
            </div>

            <div class="payment-stat-number">
                {{ $paidCount }}
            </div>

        </div>


        {{-- THẤT BẠI --}}
        <div class="payment-stat-card">

            <div class="payment-stat-title">
                Thất bại
            </div>

            <div class="payment-stat-number">
                {{ $failedCount }}
            </div>

        </div>


        {{-- ĐÃ HOÀN TIỀN --}}
        <div class="payment-stat-card">

            <div class="payment-stat-title">
                Đã hoàn tiền
            </div>

            <div class="payment-stat-number">
                {{ $refundedCount }}
            </div>

        </div>


    </div>


    {{-- =========================================================
         CẤU HÌNH PHƯƠNG THỨC THANH TOÁN
    ========================================================== --}}
    <div class="payment-section">


        <div class="payment-section-heading">

            <div>

                <h2>
                    Cấu hình phương thức thanh toán
                </h2>

                <p>
                    Bật hoặc tắt COD và QR.
                    Thông tin QR được dùng khi Customer
                    chọn chuyển khoản.
                </p>

            </div>

        </div>


        <div class="payment-settings-grid">


            {{-- =================================================
                 COD
            ================================================== --}}
            @php

                $codSetting =
                    $paymentSettings->get('COD');

            @endphp


            @if ($codSetting)


                <form
                    method="POST"
                    action="{{
                        route(
                            'admin.payments.settings.update',
                            $codSetting
                        )
                    }}"
                    class="payment-setting-card"
                >

                    @csrf
                    @method('PATCH')


                    {{-- TOP --}}
                    <div class="payment-setting-top">


                        <div>

                            <span class="payment-method-code">
                                COD
                            </span>

                            <h3>
                                Thanh toán khi nhận hàng
                            </h3>

                        </div>


                        @if ($codSetting->is_enabled)

                            <span
                                class="
                                    payment-status
                                    payment-status-active
                                "
                            >
                                Active
                            </span>

                        @else

                            <span
                                class="
                                    payment-status
                                    payment-status-inactive
                                "
                            >
                                Inactive
                            </span>

                        @endif


                    </div>


                    {{-- TÊN HIỂN THỊ --}}
                    <div class="payment-field">

                        <label>
                            Tên hiển thị
                        </label>

                        <input
                            type="text"
                            name="display_name"
                            maxlength="100"
                            value="{{
                                old(
                                    'display_name',
                                    $codSetting->display_name
                                )
                            }}"
                            required
                        >

                    </div>


                    {{-- ACTIVE / INACTIVE --}}
                    <div class="payment-toggle-row">


                        <div>

                            <strong>
                                Cho phép sử dụng COD
                            </strong>

                            <small>
                                Customer có thể chọn COD
                                tại Checkout.
                            </small>

                        </div>


                        <label class="payment-switch">


                            <input
                                type="hidden"
                                name="is_enabled"
                                value="0"
                            >


                            <input
                                type="checkbox"
                                name="is_enabled"
                                value="1"
                                {{
                                    $codSetting->is_enabled
                                        ? 'checked'
                                        : ''
                                }}
                            >


                            <span
                                class="payment-switch-slider"
                            ></span>


                        </label>


                    </div>


                    {{-- BUTTON --}}
                    <button
                        type="submit"
                        class="
                            payment-btn
                            payment-btn-primary
                        "
                    >
                        Cập nhật COD
                    </button>


                </form>


            @endif



            {{-- =================================================
                 QR
            ================================================== --}}
            @php

                $qrSetting =
                    $paymentSettings->get('QR');

            @endphp


            @if ($qrSetting)


                <form
                    method="POST"
                    action="{{
                        route(
                            'admin.payments.settings.update',
                            $qrSetting
                        )
                    }}"
                    class="payment-setting-card"
                >

                    @csrf
                    @method('PATCH')


                    {{-- TOP --}}
                    <div class="payment-setting-top">


                        <div>

                            <span class="payment-method-code">
                                QR
                            </span>

                            <h3>
                                Chuyển khoản QR
                            </h3>

                        </div>


                        @if ($qrSetting->is_enabled)

                            <span
                                class="
                                    payment-status
                                    payment-status-active
                                "
                            >
                                Active
                            </span>

                        @else

                            <span
                                class="
                                    payment-status
                                    payment-status-inactive
                                "
                            >
                                Inactive
                            </span>

                        @endif


                    </div>


                    {{-- TÊN HIỂN THỊ --}}
                    <div class="payment-field">

                        <label>
                            Tên hiển thị
                        </label>

                        <input
                            type="text"
                            name="display_name"
                            maxlength="100"
                            value="{{
                                old(
                                    'display_name',
                                    $qrSetting->display_name
                                )
                            }}"
                            required
                        >

                    </div>


                    {{-- THÔNG TIN NGÂN HÀNG --}}
                    <div class="payment-qr-grid">


                        {{-- NGÂN HÀNG --}}
                        <div class="payment-field">

                            <label>
                                Ngân hàng
                            </label>

                            <input
                                type="text"
                                name="bank_name"
                                maxlength="100"
                                value="{{
                                    old(
                                        'bank_name',
                                        $qrSetting->bank_name
                                    )
                                }}"
                                required
                            >

                        </div>


                        {{-- CHỦ TÀI KHOẢN --}}
                        <div class="payment-field">

                            <label>
                                Chủ tài khoản
                            </label>

                            <input
                                type="text"
                                name="account_name"
                                maxlength="150"
                                value="{{
                                    old(
                                        'account_name',
                                        $qrSetting->account_name
                                    )
                                }}"
                                required
                            >

                        </div>


                        {{-- SỐ TÀI KHOẢN --}}
                        <div
                            class="
                                payment-field
                                payment-field-full
                            "
                        >

                            <label>
                                Số tài khoản
                            </label>

                            <input
                                type="text"
                                name="account_number"
                                maxlength="50"
                                value="{{
                                    old(
                                        'account_number',
                                        $qrSetting->account_number
                                    )
                                }}"
                                required
                            >

                        </div>


                    </div>


                    {{-- ACTIVE / INACTIVE --}}
                    <div class="payment-toggle-row">


                        <div>

                            <strong>
                                Cho phép thanh toán QR
                            </strong>

                            <small>
                                Customer có thể chọn QR
                                tại Checkout.
                            </small>

                        </div>


                        <label class="payment-switch">


                            <input
                                type="hidden"
                                name="is_enabled"
                                value="0"
                            >


                            <input
                                type="checkbox"
                                name="is_enabled"
                                value="1"
                                {{
                                    $qrSetting->is_enabled
                                        ? 'checked'
                                        : ''
                                }}
                            >


                            <span
                                class="payment-switch-slider"
                            ></span>


                        </label>


                    </div>


                    {{-- BUTTON --}}
                    <button
                        type="submit"
                        class="
                            payment-btn
                            payment-btn-primary
                        "
                    >
                        Cập nhật QR
                    </button>


                </form>


            @endif


        </div>


    </div>



    {{-- =========================================================
         LỊCH SỬ GIAO DỊCH
    ========================================================== --}}
    <div class="payment-section">


        <div class="payment-section-heading">

            <div>

                <h2>
                    Lịch sử giao dịch
                </h2>

                <p>
                    Theo dõi toàn bộ giao dịch phát sinh
                    từ các đơn hàng.
                </p>

            </div>

        </div>


        {{-- =====================================================
             FILTER
        ====================================================== --}}
        <form
            method="GET"
            action="{{ route('admin.payments.index') }}"
            class="payment-filter"
        >


            {{-- SEARCH --}}
            <input
                type="text"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="Mã đơn, tên hoặc email Customer..."
            >


            {{-- METHOD --}}
            <select name="method">

                <option value="">
                    Tất cả phương thức
                </option>

                <option
                    value="COD"
                    {{
                        $method === 'COD'
                            ? 'selected'
                            : ''
                    }}
                >
                    COD
                </option>

                <option
                    value="QR"
                    {{
                        $method === 'QR'
                            ? 'selected'
                            : ''
                    }}
                >
                    QR
                </option>

            </select>


            {{-- STATUS --}}
            <select name="status">

                <option value="">
                    Tất cả trạng thái
                </option>

                <option
                    value="pending"
                    {{
                        $status === 'pending'
                            ? 'selected'
                            : ''
                    }}
                >
                    Chờ thanh toán
                </option>

                <option
                    value="paid"
                    {{
                        $status === 'paid'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đã thanh toán
                </option>

                <option
                    value="failed"
                    {{
                        $status === 'failed'
                            ? 'selected'
                            : ''
                    }}
                >
                    Thất bại
                </option>

                <option
                    value="refunded"
                    {{
                        $status === 'refunded'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đã hoàn tiền
                </option>

            </select>


            {{-- FILTER BUTTON --}}
            <button
                type="submit"
                class="
                    payment-btn
                    payment-btn-primary
                "
            >
                Lọc
            </button>


            {{-- RESET --}}
            <a
                href="{{ route('admin.payments.index') }}"
                class="
                    payment-btn
                    payment-btn-secondary
                "
            >
                Đặt lại
            </a>


        </form>



        {{-- =====================================================
             TABLE
        ====================================================== --}}
        <div class="payment-table-wrapper">


            <table class="payment-table">


                <thead>

                    <tr>

                        <th>
                            Mã đơn
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Phương thức
                        </th>

                        <th>
                            Số tiền
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Người xác nhận
                        </th>

                        <th>
                            Thời gian
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>


                    @forelse ($payments as $payment)


                        <tr>


                            {{-- =================================
                                 ORDER
                            ================================== --}}
                            <td>


                                @if ($payment->order)


                                    <a
                                        href="{{
                                            route(
                                                'admin.orders.show',
                                                $payment->order
                                            )
                                        }}"
                                        class="payment-order-link"
                                    >

                                        {{
                                            $payment
                                                ->order
                                                ->order_code
                                        }}

                                    </a>


                                @else

                                    —

                                @endif


                            </td>



                            {{-- =================================
                                 CUSTOMER
                            ================================== --}}
                            <td>


                                @if ($payment->order)


                                    <strong>

                                        {{
                                            $payment
                                                ->order
                                                ->customer_name
                                        }}

                                    </strong>


                                    <small
                                        class="payment-table-sub"
                                    >

                                        {{
                                            $payment
                                                ->order
                                                ->email
                                        }}

                                    </small>


                                @else

                                    —

                                @endif


                            </td>



                            {{-- =================================
                                 PAYMENT METHOD
                            ================================== --}}
                            <td>

                                <span
                                    class="payment-method-badge"
                                >
                                    {{
                                        $payment
                                            ->payment_method
                                    }}
                                </span>

                            </td>



                            {{-- =================================
                                 AMOUNT
                            ================================== --}}
                            <td class="payment-money">

                                {{
                                    number_format(
                                        (float) $payment->amount,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}đ

                            </td>



                            {{-- =================================
                                 STATUS
                            ================================== --}}
                            <td>


                                @switch($payment->status)


                                    @case('pending')

                                        <span
                                            class="
                                                payment-status
                                                payment-status-pending
                                            "
                                        >
                                            Chờ thanh toán
                                        </span>

                                        @break



                                    @case('paid')

                                        <span
                                            class="
                                                payment-status
                                                payment-status-paid
                                            "
                                        >
                                            Đã thanh toán
                                        </span>

                                        @break



                                    @case('failed')

                                        <span
                                            class="
                                                payment-status
                                                payment-status-failed
                                            "
                                        >
                                            Thất bại
                                        </span>

                                        @break



                                    @case('refunded')

                                        <span
                                            class="
                                                payment-status
                                                payment-status-refunded
                                            "
                                        >
                                            Đã hoàn tiền
                                        </span>

                                        @break



                                    @default

                                        <span class="payment-status">

                                            {{
                                                $payment
                                                    ->status
                                            }}

                                        </span>


                                @endswitch


                            </td>



                            {{-- =================================
                                 CONFIRMER
                            ================================== --}}
                            <td>


                                @if ($payment->confirmer)

                                    {{
                                        $payment
                                            ->confirmer
                                            ->name
                                    }}

                                @else

                                    —

                                @endif


                            </td>



                            {{-- =================================
                                 TIME
                            ================================== --}}
                            <td>


                                {{
                                    optional(
                                        $payment->created_at
                                    )->format(
                                        'd/m/Y H:i'
                                    )
                                }}


                                @if ($payment->paid_at)


                                    <small
                                        class="payment-table-sub"
                                    >

                                        Đã thanh toán:

                                        {{
                                            $payment
                                                ->paid_at
                                                ->format(
                                                    'd/m/Y H:i'
                                                )
                                        }}

                                    </small>


                                @endif


                            </td>



                            {{-- =================================
                                 ACTION
                            ================================== --}}
                            <td>


                                @if ($payment->order)


                                    <a
                                        href="{{
                                            route(
                                                'admin.orders.show',
                                                $payment->order
                                            )
                                        }}"
                                        class="
                                            payment-btn
                                            payment-btn-small
                                        "
                                    >
                                        Xem đơn
                                    </a>


                                @else

                                    —

                                @endif


                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td
                                colspan="8"
                                class="payment-empty"
                            >
                                Chưa có giao dịch phù hợp.
                            </td>

                        </tr>


                    @endforelse


                </tbody>


            </table>


        </div>



        {{-- =====================================================
             PAGINATION
        ====================================================== --}}
        @if ($payments->hasPages())


            <div class="payment-pagination">

                {{ $payments->links() }}

            </div>


        @endif


    </div>


</div>


@endsection