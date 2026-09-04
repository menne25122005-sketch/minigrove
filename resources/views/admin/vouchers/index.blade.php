@extends('layouts.admin')

@section('title', 'Quản lý Voucher - MiniGrove')

@section('page-title', 'Quản lý Voucher')


@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/admin-vouchers.css') }}"
    >
@endpush


@section('content')

<div class="voucher-page">


    {{-- =========================================================
         TIÊU ĐỀ
    ========================================================== --}}
    <div class="voucher-heading">

        <div>

            <h1>
                Quản lý Voucher
            </h1>

            <p>
                Tạo và quản lý các chương trình giảm giá
                áp dụng cho đơn hàng MiniGrove.
            </p>

        </div>


        <a
            href="{{ route('admin.vouchers.create') }}"
            class="btn btn-primary"
        >
            + Thêm Voucher
        </a>

    </div>


    {{-- =========================================================
         VALIDATION ERROR
    ========================================================== --}}
    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>
                Không thể thực hiện thao tác:
            </strong>

            <ul class="voucher-error-list">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
         THỐNG KÊ NHANH
    ========================================================== --}}
    <div class="voucher-stats">

        <div class="voucher-stat-card">

            <div class="voucher-stat-label">
                Tổng Voucher
            </div>

            <div class="voucher-stat-value">
                {{ $totalCount }}
            </div>

        </div>


        <div class="voucher-stat-card">

            <div class="voucher-stat-label">
                Active
            </div>

            <div class="voucher-stat-value">
                {{ $activeCount }}
            </div>

        </div>


        <div class="voucher-stat-card voucher-stat-running">

            <div class="voucher-stat-label">
                Đang áp dụng
            </div>

            <div class="voucher-stat-value">
                {{ $runningCount }}
            </div>

        </div>


        <div class="voucher-stat-card voucher-stat-expired">

            <div class="voucher-stat-label">
                Hết hạn
            </div>

            <div class="voucher-stat-value">
                {{ $expiredCount }}
            </div>

        </div>

    </div>


    {{-- =========================================================
         BỘ LỌC
    ========================================================== --}}
    <div class="card voucher-filter-card">

        <form
            action="{{ route('admin.vouchers.index') }}"
            method="GET"
            class="voucher-filter-form"
        >

            <div class="voucher-filter-field">

                <label for="keyword">
                    Tìm kiếm
                </label>

                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    placeholder="Nhập mã Voucher..."
                >

            </div>


            <div class="voucher-filter-field">

                <label for="active">
                    Trạng thái
                </label>

                <select
                    id="active"
                    name="active"
                >

                    <option value="">
                        Tất cả trạng thái
                    </option>

                    <option
                        value="1"
                        @selected($active === '1')
                    >
                        Active
                    </option>

                    <option
                        value="0"
                        @selected($active === '0')
                    >
                        Inactive
                    </option>

                </select>

            </div>


            <div class="voucher-filter-field">

                <label for="period">
                    Thời gian
                </label>

                <select
                    id="period"
                    name="period"
                >

                    <option value="">
                        Tất cả
                    </option>

                    <option
                        value="upcoming"
                        @selected($period === 'upcoming')
                    >
                        Sắp diễn ra
                    </option>

                    <option
                        value="running"
                        @selected($period === 'running')
                    >
                        Đang áp dụng
                    </option>

                    <option
                        value="expired"
                        @selected($period === 'expired')
                    >
                        Hết hạn
                    </option>

                </select>

            </div>


            <div class="voucher-filter-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Lọc
                </button>

                <a
                    href="{{ route('admin.vouchers.index') }}"
                    class="btn btn-secondary"
                >
                    Đặt lại
                </a>

            </div>

        </form>

    </div>


    {{-- =========================================================
         DANH SÁCH
    ========================================================== --}}
    <div class="card">

        <div class="voucher-table-header">

            <div>

                <h2>
                    Danh sách Voucher
                </h2>

                <p>
                    Tổng {{ $vouchers->total() }} Voucher
                </p>

            </div>

        </div>


        <div class="table-wrapper">

            <table class="voucher-table">

                <thead>

                <tr>
                    <th>Mã Voucher</th>
                    <th>Ưu đãi</th>
                    <th>Đơn tối thiểu</th>
                    <th>Thời gian</th>
                    <th>Lượt sử dụng</th>
                    <th>Mỗi Customer</th>
                    <th>Tình trạng</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>

                </thead>


                <tbody>

                @forelse ($vouchers as $voucher)

                    @php

                        $now = now();


                        if ($voucher->start_at->gt($now)) {

                            $periodLabel = 'Sắp diễn ra';
                            $periodClass = 'upcoming';

                        } elseif ($voucher->end_at->lt($now)) {

                            $periodLabel = 'Hết hạn';
                            $periodClass = 'expired';

                        } else {

                            $periodLabel = 'Đang áp dụng';
                            $periodClass = 'running';

                        }


                        if (
                            $voucher->usage_limit !== null
                            &&
                            $voucher->usage_count
                                >= $voucher->usage_limit
                        ) {

                            $periodLabel = 'Hết lượt';
                            $periodClass = 'expired';

                        }

                    @endphp


                    <tr>

                        {{-- MÃ --}}
                        <td>

                            <strong class="voucher-code">
                                {{ $voucher->code }}
                            </strong>

                        </td>


                        {{-- ƯU ĐÃI --}}
                        <td>

                            @if (
                                $voucher->discount_type
                                === 'percentage'
                            )

                                <strong>
                                    {{
                                        number_format(
                                            $voucher->discount_value,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}%
                                </strong>

                                @if (
                                    $voucher->max_discount_amount
                                    !== null
                                )

                                    <div class="voucher-subtext">

                                        Tối đa

                                        {{
                                            number_format(
                                                $voucher->max_discount_amount,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}đ

                                    </div>

                                @endif

                            @else

                                <strong>

                                    {{
                                        number_format(
                                            $voucher->discount_value,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}đ

                                </strong>

                            @endif

                        </td>


                        {{-- ĐƠN TỐI THIỂU --}}
                        <td>

                            {{
                                number_format(
                                    $voucher->min_order_amount,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </td>


                        {{-- THỜI GIAN --}}
                        <td class="voucher-date-cell">

                            <div>
                                {{
                                    $voucher->start_at
                                        ->format('d/m/Y H:i')
                                }}
                            </div>

                            <div class="voucher-date-arrow">
                                đến
                            </div>

                            <div>
                                {{
                                    $voucher->end_at
                                        ->format('d/m/Y H:i')
                                }}
                            </div>

                        </td>


                        {{-- LƯỢT SỬ DỤNG --}}
                        <td>

                            <strong>
                                {{ $voucher->usage_count }}
                            </strong>

                            <span class="voucher-subtext-inline">
                                /
                                {{
                                    $voucher->usage_limit
                                        ?? '∞'
                                }}
                            </span>

                        </td>


                        {{-- MỖI CUSTOMER --}}
                        <td>

                            {{
                                $voucher->per_user_limit
                                    ?? 'Không giới hạn'
                            }}

                        </td>


                        {{-- TÌNH TRẠNG --}}
                        <td>

                            <span
                                class="
                                    voucher-period
                                    voucher-period-{{ $periodClass }}
                                "
                            >
                                {{ $periodLabel }}
                            </span>

                        </td>


                        {{-- ACTIVE --}}
                        <td>

                            @if ($voucher->is_active)

                                <span class="status status-active">
                                    Active
                                </span>

                            @else

                                <span class="status status-inactive">
                                    Inactive
                                </span>

                            @endif

                        </td>


                        {{-- THAO TÁC --}}
                        <td>

                            <div class="voucher-actions">


                                <a
                                    href="{{
                                        route(
                                            'admin.vouchers.edit',
                                            $voucher
                                        )
                                    }}"
                                    class="btn btn-warning"
                                >
                                    Sửa
                                </a>


                                <form
                                    action="{{
                                        route(
                                            'admin.vouchers.toggle-status',
                                            $voucher
                                        )
                                    }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('PATCH')


                                    <button
                                        type="submit"
                                        class="btn btn-status"
                                    >

                                        {{
                                            $voucher->is_active
                                                ? 'Inactive'
                                                : 'Active'
                                        }}

                                    </button>

                                </form>


                                <a
                                    href="{{
                                        route(
                                            'admin.vouchers.history',
                                            $voucher
                                        )
                                    }}"
                                    class="btn btn-secondary"
                                >
                                    Lịch sử
                                </a>


                                <form
                                    action="{{
                                        route(
                                            'admin.vouchers.destroy',
                                            $voucher
                                        )
                                    }}"
                                    method="POST"
                                    onsubmit="
                                        return confirm(
                                            'Bạn chắc chắn muốn xóa Voucher này?'
                                        );
                                    "
                                >

                                    @csrf
                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                    >
                                        Xóa
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="9"
                            class="empty"
                        >
                            Không tìm thấy Voucher phù hợp.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if ($vouchers->hasPages())

            <div class="voucher-pagination">

                <span>
                    Trang
                    <strong>
                        {{ $vouchers->currentPage() }}
                    </strong>
                    /
                    {{ $vouchers->lastPage() }}
                </span>


                <div>

                    @if (!$vouchers->onFirstPage())

                        <a
                            href="{{ $vouchers->previousPageUrl() }}"
                            class="btn btn-secondary"
                        >
                            Trước
                        </a>

                    @endif


                    @if ($vouchers->hasMorePages())

                        <a
                            href="{{ $vouchers->nextPageUrl() }}"
                            class="btn btn-secondary"
                        >
                            Sau
                        </a>

                    @endif

                </div>

            </div>

        @endif

    </div>

</div>

@endsection