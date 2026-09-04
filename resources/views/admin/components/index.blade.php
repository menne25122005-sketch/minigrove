@extends('layouts.admin')

@section(
    'title',
    'Quản lý thành phần - MiniGrove'
)

@section('content')

<div class="card">

    <div class="page-header">

        <h1>
            Quản lý thành phần
        </h1>

        <a
            href="{{ route('admin.components.create') }}"
            class="btn btn-primary"
        >
            + Thêm thành phần
        </a>

    </div>


    <form
        action="{{ route('admin.components.index') }}"
        method="GET"
        class="component-filter"
    >

        {{-- Tìm kiếm --}}
        <input
            type="text"
            name="keyword"
            value="{{ $keyword }}"
            placeholder="Tên hoặc SKU..."
        >


        {{-- Loại --}}
        <select name="component_type">

            <option value="">
                Tất cả loại
            </option>

            <option
                value="container"
                @selected(
                    $componentType === 'container'
                )
            >
                Bình
            </option>

            <option
                value="plant"
                @selected(
                    $componentType === 'plant'
                )
            >
                Cây
            </option>

            <option
                value="moss"
                @selected(
                    $componentType === 'moss'
                )
            >
                Rêu
            </option>

            <option
                value="substrate"
                @selected(
                    $componentType === 'substrate'
                )
            >
                Nền / Giá thể
            </option>

            <option
                value="stone"
                @selected(
                    $componentType === 'stone'
                )
            >
                Đá
            </option>

            <option
                value="accessory"
                @selected(
                    $componentType === 'accessory'
                )
            >
                Phụ kiện
            </option>

        </select>


        {{-- Hệ sinh thái --}}
        <select name="ecosystem_type">

            <option value="">
                Tất cả hệ sinh thái
            </option>

            <option
                value="all"
                @selected(
                    $ecosystemType === 'all'
                )
            >
                Tất cả
            </option>

            <option
                value="closed"
                @selected(
                    $ecosystemType === 'closed'
                )
            >
                Kín
            </option>

            <option
                value="open"
                @selected(
                    $ecosystemType === 'open'
                )
            >
                Mở
            </option>

            <option
                value="dry"
                @selected(
                    $ecosystemType === 'dry'
                )
            >
                Khô
            </option>

        </select>


        {{-- Trạng thái --}}
        <select name="status">

            <option value="">
                Tất cả trạng thái
            </option>

            <option
                value="1"
                @selected(
                    (string) $status === '1'
                )
            >
                Active
            </option>

            <option
                value="0"
                @selected(
                    (string) $status === '0'
                )
            >
                Inactive
            </option>

        </select>


        <button
            type="submit"
            class="btn btn-primary"
        >
            Lọc
        </button>


        @if (
            $keyword ||
            $componentType ||
            $ecosystemType ||
            (
                $status !== null &&
                $status !== ''
            )
        )

            <a
                href="{{ route(
                    'admin.components.index'
                ) }}"
                class="btn btn-secondary"
            >
                Xóa lọc
            </a>

        @endif

    </form>


    <div class="table-wrapper">

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Thành phần</th>
                    <th>SKU</th>
                    <th>Loại</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Đơn vị</th>
                    <th>Hệ sinh thái</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>

            </thead>


            <tbody>

                @forelse ($components as $component)

                    @php
                        $primaryImage =
                            $component->images
                                ->firstWhere(
                                    'is_primary',
                                    true
                                )
                            ?? $component->images->first();
                    @endphp


                    <tr>

                        <td>
                            {{ $component->id }}
                        </td>


                        <td>

                            @if ($primaryImage)

                                <img
                                    class="component-thumbnail"
                                    src="{{ asset(
                                        'storage/' .
                                        $primaryImage->image_path
                                    ) }}"
                                    alt="{{ $component->name }}"
                                >

                            @else

                                <div class="no-component-image">
                                    Không ảnh
                                </div>

                            @endif

                        </td>


                        <td>

                            <strong>
                                {{ $component->name }}
                            </strong>

                        </td>


                        <td>
                            {{ $component->sku }}
                        </td>


                        <td>

                            @switch($component->component_type)

                                @case('container')
                                    Bình
                                    @break

                                @case('plant')
                                    Cây
                                    @break

                                @case('moss')
                                    Rêu
                                    @break

                                @case('substrate')
                                    Nền / Giá thể
                                    @break

                                @case('stone')
                                    Đá
                                    @break

                                @case('accessory')
                                    Phụ kiện
                                    @break

                                @default
                                    {{ $component->component_type }}

                            @endswitch

                        </td>


                        <td>

                            <strong>
                                {{ number_format(
                                    (float) $component->price,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ
                            </strong>

                        </td>


                        <td>

                            @if ($component->stock_quantity > 0)

                                {{ $component->stock_quantity }}

                            @else

                                <span class="out-of-stock">
                                    Hết hàng
                                </span>

                            @endif

                        </td>


                        <td>
                            {{ $component->unit }}
                        </td>


                        <td>

                            @switch(
                                $component
                                    ->compatible_ecosystem_type
                            )

                                @case('all')
                                    Tất cả
                                    @break

                                @case('closed')
                                    Kín
                                    @break

                                @case('open')
                                    Mở
                                    @break

                                @case('dry')
                                    Khô
                                    @break

                                @default
                                    {{
                                        $component
                                            ->compatible_ecosystem_type
                                    }}

                            @endswitch

                        </td>


                        <td>

                            @if ($component->is_active)

                                <span
                                    class="
                                        status
                                        status-active
                                    "
                                >
                                    Active
                                </span>

                            @else

                                <span
                                    class="
                                        status
                                        status-inactive
                                    "
                                >
                                    Inactive
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="actions">

                                <a
                                    href="{{ route(
                                        'admin.components.edit',
                                        $component
                                    ) }}"
                                    class="btn btn-warning"
                                >
                                    Sửa
                                </a>


                                <form
                                    action="{{ route(
                                        'admin.components.toggle-status',
                                        $component
                                    ) }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-status"
                                    >
                                        {{ $component->is_active
                                            ? 'Inactive'
                                            : 'Active'
                                        }}
                                    </button>

                                </form>


                                <form
                                    action="{{ route(
                                        'admin.components.destroy',
                                        $component
                                    ) }}"
                                    method="POST"
                                    onsubmit="
                                        return confirm(
                                            'Bạn chắc chắn muốn xóa thành phần này?'
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
                            colspan="11"
                            class="empty"
                        >
                            Không có thành phần phù hợp.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="pagination">
        {{ $components->links() }}
    </div>

</div>

@endsection


@push('styles')

<style>

    .component-filter {
        display: grid;

        grid-template-columns:
            minmax(180px, 2fr)
            minmax(150px, 1fr)
            minmax(160px, 1fr)
            minmax(130px, 1fr)
            auto
            auto;

        gap: 9px;

        margin-bottom: 20px;
    }


    .component-filter select {
        border: 1px solid #ccd5cf;

        border-radius: 7px;

        padding: 10px;

        background: white;
    }


    .component-thumbnail,
    .no-component-image {
        width: 70px;
        height: 58px;

        border-radius: 7px;
    }


    .component-thumbnail {
        object-fit: cover;
    }


    .no-component-image {
        background: #edf0ed;

        display: flex;

        justify-content: center;
        align-items: center;

        color: #777;

        font-size: 12px;
    }


    .out-of-stock {
        color: #b43f3f;

        font-weight: bold;
    }


    @media (max-width: 1000px) {

        .component-filter {
            grid-template-columns: 1fr;
        }

    }

</style>

@endpush