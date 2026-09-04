@extends('layouts.admin')

@section('title', 'Quản lý tồn kho - MiniGrove')

@section('page-title', 'Quản lý tồn kho')


@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/admin-stocks.css') }}"
    >
@endpush


@section('content')

<div class="stock-page">


    {{-- =========================================================
         TIÊU ĐỀ
    ========================================================== --}}
    <div class="stock-heading">

        <div>
            <h1>Quản lý tồn kho</h1>

            <p>
                Theo dõi và điều chỉnh tồn kho sản phẩm,
                thành phần Terrarium.
            </p>
        </div>

    </div>


    {{-- =========================================================
         VALIDATION ERROR
    ========================================================== --}}
    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>
                Không thể thực hiện thao tác:
            </strong>

            <ul class="stock-error-list">

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
    <div class="stock-stats">

        <div class="stock-stat-card">

            <div class="stock-stat-label">
                Sản phẩm
            </div>

            <div class="stock-stat-value">
                {{ $productCount }}
            </div>

        </div>


        <div class="stock-stat-card">

            <div class="stock-stat-label">
                Thành phần
            </div>

            <div class="stock-stat-value">
                {{ $componentCount }}
            </div>

        </div>


        <div class="stock-stat-card stock-stat-warning">

            <div class="stock-stat-label">
                Sắp hết hàng
            </div>

            <div class="stock-stat-value">
                {{ $lowStockCount }}
            </div>

        </div>


        <div class="stock-stat-card stock-stat-danger">

            <div class="stock-stat-label">
                Hết hàng
            </div>

            <div class="stock-stat-value">
                {{ $outOfStockCount }}
            </div>

        </div>

    </div>


    {{-- =========================================================
         BỘ LỌC
    ========================================================== --}}
    <div class="card stock-filter-card">

        <form
            action="{{ route('admin.stocks.index') }}"
            method="GET"
            class="stock-filter-form"
        >

            <div class="stock-filter-field">

                <label for="keyword">
                    Tìm kiếm
                </label>

                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    placeholder="Tên hoặc SKU..."
                >

            </div>


            <div class="stock-filter-field">

                <label for="type">
                    Loại kho
                </label>

                <select
                    id="type"
                    name="type"
                >

                    <option
                        value="ALL"
                        @selected($type === 'ALL')
                    >
                        Tất cả
                    </option>

                    <option
                        value="PRODUCT"
                        @selected($type === 'PRODUCT')
                    >
                        Sản phẩm
                    </option>

                    <option
                        value="COMPONENT"
                        @selected($type === 'COMPONENT')
                    >
                        Thành phần
                    </option>

                </select>

            </div>


            <div class="stock-filter-field">

                <label for="status">
                    Trạng thái
                </label>

                <select
                    id="status"
                    name="status"
                >

                    <option
                        value=""
                        @selected($status === '')
                    >
                        Tất cả trạng thái
                    </option>

                    <option
                        value="in_stock"
                        @selected($status === 'in_stock')
                    >
                        Còn hàng
                    </option>

                    <option
                        value="low_stock"
                        @selected($status === 'low_stock')
                    >
                        Sắp hết hàng
                    </option>

                    <option
                        value="out_of_stock"
                        @selected($status === 'out_of_stock')
                    >
                        Hết hàng
                    </option>

                </select>

            </div>


            <div class="stock-filter-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Lọc
                </button>

                <a
                    href="{{ route('admin.stocks.index') }}"
                    class="btn btn-secondary"
                >
                    Đặt lại
                </a>

            </div>

        </form>

    </div>


    {{-- =========================================================
         DANH SÁCH TỒN KHO
    ========================================================== --}}
    <div class="card">

        <div class="stock-table-header">

            <div>

                <h2>
                    Danh sách tồn kho
                </h2>

                <p>
                    Tổng {{ $stocks->total() }} mục
                </p>

            </div>

        </div>


        <div class="table-wrapper">

            <table class="stock-table">

                <thead>

                <tr>
                    <th>SKU</th>
                    <th>Tên</th>
                    <th>Loại</th>
                    <th>Tồn hiện tại</th>
                    <th>Ngưỡng cảnh báo</th>
                    <th>Trạng thái</th>
                    <th>Hoạt động</th>
                    <th>Thao tác</th>
                </tr>

                </thead>


                <tbody>

                @forelse ($stocks as $stock)

                    @php

                        $quantity =
                            (int) $stock->stock_quantity;

                        $threshold =
                            (int) $stock->low_stock_threshold;


                        if ($quantity === 0) {

                            $stockStatus = 'out';

                            $stockStatusLabel =
                                'Hết hàng';

                        } elseif ($quantity <= $threshold) {

                            $stockStatus = 'low';

                            $stockStatusLabel =
                                'Sắp hết';

                        } else {

                            $stockStatus = 'normal';

                            $stockStatusLabel =
                                'Còn hàng';

                        }

                    @endphp


                    <tr>

                        {{-- SKU --}}
                        <td>
                            <strong>
                                {{ $stock->sku }}
                            </strong>
                        </td>


                        {{-- TÊN --}}
                        <td>

                            <div class="stock-item-name">
                                {{ $stock->name }}
                            </div>

                            <div class="stock-item-unit">
                                Đơn vị:
                                {{ $stock->unit ?: '—' }}
                            </div>

                        </td>


                        {{-- LOẠI --}}
                        <td>

                            <span class="stock-type-badge">

                                {{
                                    $stock->stock_type === 'PRODUCT'
                                        ? 'Sản phẩm'
                                        : 'Thành phần'
                                }}

                            </span>

                        </td>


                        {{-- TỒN --}}
                        <td>

                            <strong class="stock-quantity">
                                {{ number_format($quantity, 0, ',', '.') }}
                            </strong>

                        </td>


                        {{-- NGƯỠNG --}}
                        <td>

                            <form
                                action="{{
                                    route(
                                        'admin.stocks.threshold.update',
                                        [
                                            $stock->stock_type,
                                            $stock->item_id,
                                        ]
                                    )
                                }}"
                                method="POST"
                                class="stock-threshold-form"
                            >

                                @csrf
                                @method('PATCH')

                                <input
                                    type="number"
                                    name="low_stock_threshold"
                                    value="{{ $threshold }}"
                                    min="0"
                                    max="1000000"
                                    required
                                >

                                <button
                                    type="submit"
                                    class="stock-small-button"
                                    title="Lưu ngưỡng cảnh báo"
                                >
                                    Lưu
                                </button>

                            </form>

                        </td>


                        {{-- TRẠNG THÁI KHO --}}
                        <td>

                            <span
                                class="
                                    stock-status
                                    stock-status-{{ $stockStatus }}
                                "
                            >
                                {{ $stockStatusLabel }}
                            </span>

                        </td>


                        {{-- ACTIVE --}}
                        <td>

                            @if ($stock->is_active)

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

                            <div class="stock-row-actions">

                                <details class="stock-action-dropdown">

                                    <summary>
                                        Điều chỉnh kho
                                    </summary>


                                    <div class="stock-action-panel">

                                        <div class="stock-action-current">

                                            Tồn hiện tại:

                                            <strong>
                                                {{ $quantity }}
                                            </strong>

                                        </div>


                                        <form
                                            action="{{
                                                route(
                                                    'admin.stocks.change',
                                                    [
                                                        $stock->stock_type,
                                                        $stock->item_id,
                                                    ]
                                                )
                                            }}"
                                            method="POST"
                                        >

                                            @csrf
                                            @method('PATCH')


                                            <div class="stock-action-field">

                                                <label>
                                                    Thao tác
                                                </label>

                                                <select
                                                    name="operation"
                                                    required
                                                >

                                                    <option value="import">
                                                        Nhập kho
                                                    </option>

                                                    <option value="export">
                                                        Xuất kho
                                                    </option>

                                                    <option value="adjust">
                                                        Điều chỉnh tồn thực tế
                                                    </option>

                                                </select>

                                            </div>


                                            <div class="stock-action-field">

                                                <label>
                                                    Số lượng
                                                </label>

                                                <input
                                                    type="number"
                                                    name="quantity"
                                                    min="0"
                                                    max="1000000"
                                                    required
                                                >

                                                <small>
                                                    Với "Điều chỉnh",
                                                    nhập số tồn thực tế mới.
                                                </small>

                                            </div>


                                            <div class="stock-action-field">

                                                <label>
                                                    Ghi chú
                                                </label>

                                                <textarea
                                                    name="note"
                                                    rows="3"
                                                    maxlength="1000"
                                                    placeholder="Nhập lý do thay đổi kho..."
                                                    required
                                                ></textarea>

                                            </div>


                                            <button
                                                type="submit"
                                                class="btn btn-primary stock-submit-button"
                                                onclick="
                                                    return confirm(
                                                        'Bạn chắc chắn muốn thay đổi tồn kho?'
                                                    );
                                                "
                                            >
                                                Xác nhận
                                            </button>

                                        </form>

                                    </div>

                                </details>


                                <a
                                    href="{{
                                        route(
                                            'admin.stocks.history',
                                            [
                                                $stock->stock_type,
                                                $stock->item_id,
                                            ]
                                        )
                                    }}"
                                    class="btn btn-secondary stock-history-button"
                                >
                                    Lịch sử
                                </a>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="empty"
                        >
                            Không tìm thấy dữ liệu tồn kho phù hợp.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- =====================================================
             PHÂN TRANG
        ====================================================== --}}
        @if ($stocks->hasPages())

            <div class="stock-pagination">

                <div>
                    Trang
                    <strong>
                        {{ $stocks->currentPage() }}
                    </strong>
                    /
                    {{ $stocks->lastPage() }}
                </div>


                <div class="stock-pagination-actions">

                    @if ($stocks->onFirstPage())

                        <span class="btn btn-secondary stock-page-disabled">
                            Trước
                        </span>

                    @else

                        <a
                            href="{{ $stocks->previousPageUrl() }}"
                            class="btn btn-secondary"
                        >
                            Trước
                        </a>

                    @endif


                    @if ($stocks->hasMorePages())

                        <a
                            href="{{ $stocks->nextPageUrl() }}"
                            class="btn btn-secondary"
                        >
                            Sau
                        </a>

                    @else

                        <span class="btn btn-secondary stock-page-disabled">
                            Sau
                        </span>

                    @endif

                </div>

            </div>

        @endif

    </div>

</div>

@endsection