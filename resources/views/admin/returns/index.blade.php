@extends('layouts.admin')


@section(
    'title',
    'Trả hàng / Hoàn tiền - MiniGrove'
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
    | STATUS LABELS
    |--------------------------------------------------------------------------
    */

    $statusLabels = [

        'pending' =>
            'Chờ xử lý',

        'approved' =>
            'Đã chấp nhận',

        'returning' =>
            'Đang trả hàng',

        'received' =>
            'Đã nhận hàng',

        'refunded' =>
            'Đã hoàn tiền',

        'rejected' =>
            'Đã từ chối',
    ];


    /*
    |--------------------------------------------------------------------------
    | REASON LABELS
    |--------------------------------------------------------------------------
    */

    $reasonLabels = [

        'damaged' =>
            'Sản phẩm bị hư hỏng',

        'wrong_item' =>
            'Giao sai sản phẩm',

        'not_as_described' =>
            'Không đúng mô tả',

        'quality_issue' =>
            'Vấn đề chất lượng',

        'other' =>
            'Lý do khác',
    ];

@endphp


<div class="card admin-returns-card">


    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="page-header">

        <div>

            <h1>
                Trả hàng / Hoàn tiền
            </h1>

            <p class="admin-returns-subtitle">
                Theo dõi và xử lý các yêu cầu trả hàng của Customer.
            </p>

        </div>


        <a
            href="{{ route('admin.orders.index') }}"
            class="btn btn-secondary"
        >
            Đơn hàng
        </a>

    </div>


    {{-- =====================================================
         STATUS TABS
    ====================================================== --}}

    <div class="return-status-tabs">


        {{-- ALL --}}

        <a
            href="{{
                route(
                    'admin.returns.index',
                    array_filter([
                        'keyword' =>
                            $keyword,
                    ])
                )
            }}"
            class="
                return-status-tab
                {{
                    !$status
                    ? 'active'
                    : ''
                }}
            "
        >
            Tất cả
        </a>


        {{-- EACH STATUS --}}

        @foreach (
            $statusLabels
            as $value => $label
        )

            <a
                href="{{
                    route(
                        'admin.returns.index',
                        array_filter([
                            'status' =>
                                $value,

                            'keyword' =>
                                $keyword,
                        ])
                    )
                }}"
                class="
                    return-status-tab
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
        action="{{ route('admin.returns.index') }}"
        method="GET"
        class="admin-return-filter"
    >


        <input
            type="text"
            name="keyword"
            value="{{ $keyword }}"
            placeholder="Mã yêu cầu, mã đơn, tên khách, SĐT..."
        >


        <select name="status">

            <option value="">
                Tất cả trạng thái
            </option>


            @foreach (
                $statusLabels
                as $value => $label
            )

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
        )

            <a
                href="{{ route('admin.returns.index') }}"
                class="btn btn-secondary"
            >
                Xóa lọc
            </a>

        @endif

    </form>


    {{-- =====================================================
         RESULT
    ====================================================== --}}

    <div class="admin-return-result">

        <span>
            Tổng kết quả:
        </span>

        <strong>
            {{ $returnRequests->total() }}
        </strong>

        <span>
            yêu cầu
        </span>

    </div>


    {{-- =====================================================
         TABLE
    ====================================================== --}}

    <div class="table-wrapper">

        <table class="admin-return-table">

            <thead>

                <tr>

                    <th>
                        Mã yêu cầu
                    </th>

                    <th>
                        Đơn hàng
                    </th>

                    <th>
                        Customer
                    </th>

                    <th>
                        Sản phẩm trả
                    </th>

                    <th>
                        Lý do
                    </th>

                    <th>
                        Dự kiến hoàn
                    </th>

                    <th>
                        Trạng thái
                    </th>

                    <th>
                        Ngày gửi
                    </th>

                    <th>
                        Thao tác
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse (
                    $returnRequests
                    as $returnRequest
                )

                    <tr>


                        {{-- RETURN CODE --}}

                        <td>

                            <a
                                href="{{
                                    route(
                                        'admin.returns.show',
                                        $returnRequest
                                    )
                                }}"
                                class="admin-return-code"
                            >
                                {{
                                    $returnRequest
                                        ->return_code
                                }}
                            </a>


                            <span class="admin-return-id">
                                #{{ $returnRequest->id }}
                            </span>

                        </td>


                        {{-- ORDER --}}

                        <td>

                            @if (
                                $returnRequest->order
                            )

                                <a
                                    href="{{
                                        route(
                                            'admin.orders.show',
                                            $returnRequest
                                                ->order
                                        )
                                    }}"
                                    class="admin-return-order-code"
                                >
                                    {{
                                        $returnRequest
                                            ->order
                                            ->order_code
                                    }}
                                </a>


                                <span class="admin-return-order-status">

                                    {{
                                        $returnRequest
                                            ->order
                                            ->order_status
                                    }}

                                </span>

                            @else

                                <span>
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- CUSTOMER --}}

                        <td>

                            <strong class="admin-return-customer">

                                {{
                                    $returnRequest
                                        ->order
                                        ?->customer_name
                                    ??
                                    $returnRequest
                                        ->user
                                        ?->name
                                    ??
                                    '—'
                                }}

                            </strong>


                            @if (
                                $returnRequest
                                    ->order
                                    ?->phone
                            )

                                <span class="admin-return-customer-info">

                                    {{
                                        $returnRequest
                                            ->order
                                            ->phone
                                    }}

                                </span>

                            @endif

                        </td>


                        {{-- ITEM COUNT --}}

                        <td>

                            <span class="admin-return-item-count">

                                {{
                                    $returnRequest
                                        ->items_count
                                }}

                                dòng sản phẩm

                            </span>

                        </td>


                        {{-- REASON --}}

                        <td>

                            <span class="admin-return-reason">

                                {{
                                    $reasonLabels[
                                        $returnRequest
                                            ->reason
                                    ]
                                    ??
                                    $returnRequest
                                        ->reason
                                }}

                            </span>

                        </td>


                        {{-- REFUND --}}

                        <td>

                            <strong class="admin-return-refund">

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

                        </td>


                        {{-- STATUS --}}

                        <td>

                            <span
                                class="
                                    admin-return-status
                                    admin-return-status-{{
                                        $returnRequest
                                            ->status
                                    }}
                                "
                            >

                                {{
                                    $statusLabels[
                                        $returnRequest
                                            ->status
                                    ]
                                    ??
                                    $returnRequest
                                        ->status
                                }}

                            </span>

                        </td>


                        {{-- CREATED AT --}}

                        <td>

                            <span class="admin-return-date">

                                {{
                                    $returnRequest
                                        ->created_at
                                        ->format(
                                            'd/m/Y'
                                        )
                                }}

                            </span>


                            <span class="admin-return-time">

                                {{
                                    $returnRequest
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
                                            'admin.returns.show',
                                            $returnRequest
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
                            colspan="9"
                            class="
                                empty
                                admin-returns-empty
                            "
                        >

                            <div class="admin-returns-empty-icon">
                                ↩
                            </div>


                            <strong>
                                Không có yêu cầu phù hợp.
                            </strong>


                            <p>
                                Hiện chưa có yêu cầu trả hàng /
                                hoàn tiền theo điều kiện đã chọn.
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
        $returnRequests
            ->hasPages()
    )

        <div class="pagination">

            {{
                $returnRequests
                    ->links()
            }}

        </div>

    @endif

</div>

@endsection