@extends('layouts.admin')

@section('title', 'Quản lý khách hàng')


@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/admin-customers.css') }}"
    >

@endpush


@section('content')


<div class="customer-page">


    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="customer-header">

        <div>

            <h1>
                Khách hàng
            </h1>

            <p>
                Quản lý tài khoản Customer theo mã định danh,
                trạng thái và lịch sử giao dịch.
            </p>

        </div>

    </div>



    {{-- =========================================================
         THỐNG KÊ
    ========================================================== --}}
    <div class="customer-stats">


        <div class="customer-stat-card">

            <div class="customer-stat-title">
                Tổng Customer
            </div>

            <div class="customer-stat-number">
                {{ $totalCustomers }}
            </div>

        </div>


        <div class="customer-stat-card">

            <div class="customer-stat-title">
                Active
            </div>

            <div class="customer-stat-number">
                {{ $activeCustomers }}
            </div>

        </div>


        <div class="customer-stat-card">

            <div class="customer-stat-title">
                Inactive
            </div>

            <div class="customer-stat-number">
                {{ $inactiveCustomers }}
            </div>

        </div>


        <div class="customer-stat-card">

            <div class="customer-stat-title">
                Customer mới tháng này
            </div>

            <div class="customer-stat-number">
                {{ $newCustomersThisMonth }}
            </div>

        </div>


    </div>



    {{-- =========================================================
         DANH SÁCH CUSTOMER
    ========================================================== --}}
    <div class="customer-section">


        <div class="customer-section-heading">

            <div>

                <h2>
                    Danh sách khách hàng
                </h2>

                <p>
                    Customer được quản lý bằng mã định danh.
                    Thông tin cá nhân không hiển thị trong khu vực Admin.
                </p>

            </div>

        </div>



        {{-- =====================================================
             FILTER
        ====================================================== --}}
        <form
            method="GET"
            action="{{ route('admin.customers.index') }}"
            class="customer-filter"
        >


            <input
                type="text"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="Nhập Customer ID, ví dụ: 5"
                inputmode="numeric"
                autocomplete="off"
            >


            <select name="status">

                <option value="">
                    Tất cả trạng thái
                </option>


                <option
                    value="active"
                    {{
                        $status === 'active'
                            ? 'selected'
                            : ''
                    }}
                >
                    Active
                </option>


                <option
                    value="inactive"
                    {{
                        $status === 'inactive'
                            ? 'selected'
                            : ''
                    }}
                >
                    Inactive
                </option>

            </select>


            <button
                type="submit"
                class="
                    customer-btn
                    customer-btn-primary
                "
            >
                Lọc
            </button>


            <a
                href="{{ route('admin.customers.index') }}"
                class="
                    customer-btn
                    customer-btn-secondary
                "
            >
                Đặt lại
            </a>


        </form>



        {{-- =====================================================
             PRIVACY NOTE
        ====================================================== --}}
        <div class="customer-privacy-note customer-list-privacy-note">

            Khu vực quản trị chỉ sử dụng Customer ID.
            Họ tên, Email, số điện thoại, Avatar và thông tin nhận hàng
            của Customer không được hiển thị.

        </div>



        {{-- =====================================================
             TABLE
        ====================================================== --}}
        <div class="customer-table-wrapper">


            <table class="customer-table">


                <thead>

                    <tr>

                        <th>
                            Customer ID
                        </th>

                        <th>
                            Đơn hàng
                        </th>

                        <th>
                            Tổng chi tiêu
                        </th>

                        <th>
                            Ngày tham gia
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>


                    @forelse ($customers as $customer)


                        @php

                            $completedSpending =
                                (float) (
                                    $customer->completed_spending
                                    ?? 0
                                );

                            $refundedAmount =
                                (float) (
                                    $customer->refunded_amount
                                    ?? 0
                                );

                            $actualSpending =
                                max(
                                    0,
                                    $completedSpending
                                    -
                                    $refundedAmount
                                );

                        @endphp


                        <tr>


                            {{-- CUSTOMER ID --}}
                            <td>

                                <div class="customer-anonymous-info">


                                    <div class="customer-anonymous-icon">
                                        C
                                    </div>


                                    <div>

                                        <strong>
                                            Customer #{{ $customer->id }}
                                        </strong>

                                        <small>
                                            ID #{{ $customer->id }}
                                        </small>

                                    </div>


                                </div>

                            </td>



                            {{-- ORDER COUNT --}}
                            <td>

                                <strong>
                                    {{ $customer->orders_count }}
                                </strong>

                                <span class="customer-muted">
                                    đơn
                                </span>

                            </td>



                            {{-- TOTAL SPENDING --}}
                            <td class="customer-money">

                                {{
                                    number_format(
                                        $actualSpending,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}đ

                            </td>



                            {{-- CREATED AT --}}
                            <td>

                                {{
                                    optional(
                                        $customer->created_at
                                    )->format(
                                        'd/m/Y H:i'
                                    )
                                }}

                            </td>



                            {{-- STATUS --}}
                            <td>

                                @if ($customer->is_active)

                                    <span
                                        class="
                                            customer-status
                                            customer-status-active
                                        "
                                    >
                                        Active
                                    </span>

                                @else

                                    <span
                                        class="
                                            customer-status
                                            customer-status-inactive
                                        "
                                    >
                                        Inactive
                                    </span>

                                @endif

                            </td>



                            {{-- ACTION --}}
                            <td>

                                <div class="customer-actions">


                                    <a
                                        href="{{
                                            route(
                                                'admin.customers.show',
                                                $customer->id
                                            )
                                        }}"
                                        class="
                                            customer-btn
                                            customer-btn-small
                                        "
                                    >
                                        Xem chi tiết
                                    </a>


                                    <form
                                        method="POST"
                                        action="{{
                                            route(
                                                'admin.customers.toggle-status',
                                                $customer->id
                                            )
                                        }}"
                                    >

                                        @csrf
                                        @method('PATCH')


                                        <button
                                            type="submit"
                                            class="
                                                customer-btn
                                                {{
                                                    $customer->is_active
                                                        ? 'customer-btn-inactive'
                                                        : 'customer-btn-active'
                                                }}
                                            "
                                            onclick="
                                                return confirm(
                                                    '{{ $customer->is_active
                                                        ? 'Bạn có chắc muốn khóa Customer #' . $customer->id . '?'
                                                        : 'Bạn có chắc muốn kích hoạt Customer #' . $customer->id . '?'
                                                    }}'
                                                );
                                            "
                                        >

                                            {{
                                                $customer->is_active
                                                    ? 'Inactive'
                                                    : 'Active'
                                            }}

                                        </button>


                                    </form>


                                </div>

                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td
                                colspan="6"
                                class="customer-empty"
                            >
                                Không có Customer phù hợp.
                            </td>

                        </tr>


                    @endforelse


                </tbody>


            </table>


        </div>



        {{-- =====================================================
             PAGINATION
        ====================================================== --}}
        @if ($customers->hasPages())


            <div class="customer-pagination">

                {{ $customers->links() }}

            </div>


        @endif


    </div>


</div>


@endsection