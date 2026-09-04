@extends('layouts.admin')

@section(
    'title',
    'Quản lý sản phẩm - MiniGrove'
)

@section('content')

<div class="card">

    <div class="page-header">

        <h1>
            Quản lý sản phẩm
        </h1>

        <a
            href="{{ route('admin.products.create') }}"
            class="btn btn-primary"
        >
            + Thêm sản phẩm
        </a>

    </div>


    <form
        action="{{ route('admin.products.index') }}"
        method="GET"
        class="product-filter"
    >

        <input
            type="text"
            name="keyword"
            value="{{ $keyword }}"
            placeholder="Tên hoặc SKU..."
        >


        <select name="category_id">

            <option value="">
                Tất cả danh mục
            </option>

            @foreach ($categories as $category)

                <option
                    value="{{ $category->id }}"
                    @selected(
                        $categoryId == $category->id
                    )
                >
                    {{ $category->name }}
                </option>

            @endforeach

        </select>


        <select name="status">

            <option value="">
                Tất cả trạng thái
            </option>

            <option
                value="1"
                @selected((string) $status === '1')
            >
                Active
            </option>

            <option
                value="0"
                @selected((string) $status === '0')
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
            $categoryId ||
            (
                $status !== null &&
                $status !== ''
            )
        )

            <a
                href="{{ route('admin.products.index') }}"
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
                    <th>Sản phẩm</th>
                    <th>SKU</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Tồn</th>
                    <th>Loại</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>

            </thead>


            <tbody>

                @forelse ($products as $product)

                    @php
                        $primaryImage =
                            $product->images
                                ->firstWhere(
                                    'is_primary',
                                    true
                                )
                            ?? $product->images->first();
                    @endphp


                    <tr>

                        <td>
                            {{ $product->id }}
                        </td>


                        <td>

                            @if ($primaryImage)

                                <img
                                    class="product-thumbnail"
                                    src="{{ asset(
                                        'storage/' .
                                        $primaryImage->image_path
                                    ) }}"
                                    alt="{{ $product->name }}"
                                >

                            @else

                                <div class="no-product-image">
                                    Không ảnh
                                </div>

                            @endif

                        </td>


                        <td>

                            <strong>
                                {{ $product->name }}
                            </strong>

                            @if ($product->is_featured)

                                <div class="featured-label">
                                    ★ Nổi bật
                                </div>

                            @endif

                        </td>


                        <td>
                            {{ $product->sku }}
                        </td>


                        <td>
                            {{ $product->category?->name }}
                        </td>


                        <td>

                            @if ($product->sale_price !== null)

                                <div>

                                    <strong class="sale-price">

                                        {{ number_format(
                                            (float) $product->sale_price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                                    </strong>

                                </div>


                                <div class="old-price">

                                    {{ number_format(
                                        (float) $product->list_price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </div>

                            @else

                                <strong>

                                    {{ number_format(
                                        (float) $product->list_price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            @endif

                        </td>


                        <td>

                            @if ($product->stock_quantity > 0)

                                {{ $product->stock_quantity }}

                            @else

                                <span class="out-of-stock">
                                    Hết hàng
                                </span>

                            @endif

                        </td>


                        <td>

                            @switch($product->ecosystem_type)

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
                                    {{ $product->ecosystem_type }}

                            @endswitch

                        </td>


                        <td>

                            @if ($product->is_active)

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
                                        'admin.products.edit',
                                        $product
                                    ) }}"
                                    class="btn btn-warning"
                                >
                                    Sửa
                                </a>


                                <form
                                    action="{{ route(
                                        'admin.products.toggle-status',
                                        $product
                                    ) }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-status"
                                    >
                                        {{ $product->is_active
                                            ? 'Inactive'
                                            : 'Active'
                                        }}
                                    </button>

                                </form>


                                <form
                                    action="{{ route(
                                        'admin.products.destroy',
                                        $product
                                    ) }}"
                                    method="POST"
                                    onsubmit="
                                        return confirm(
                                            'Bạn chắc chắn muốn xóa sản phẩm này?'
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
                            colspan="10"
                            class="empty"
                        >
                            Không có sản phẩm phù hợp.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="pagination">
        {{ $products->links() }}
    </div>

</div>

@endsection


@push('styles')

<style>

    .product-filter {
        display: grid;

        grid-template-columns:
            minmax(200px, 2fr)
            minmax(180px, 1fr)
            minmax(160px, 1fr)
            auto
            auto;

        gap: 9px;

        margin-bottom: 20px;
    }


    .product-filter select {
        border: 1px solid #ccd5cf;
        border-radius: 7px;

        padding: 10px;

        background: white;
    }


    .product-thumbnail,
    .no-product-image {
        width: 75px;
        height: 60px;

        border-radius: 7px;
    }


    .product-thumbnail {
        object-fit: cover;
    }


    .no-product-image {
        background: #edf0ed;

        display: flex;

        justify-content: center;
        align-items: center;

        color: #777;

        font-size: 12px;
    }


    .sale-price {
        color: #b44343;
    }


    .old-price {
        color: #888;

        text-decoration: line-through;

        margin-top: 3px;

        font-size: 13px;
    }


    .featured-label {
        color: #a27416;

        font-size: 12px;

        margin-top: 5px;
    }


    .out-of-stock {
        color: #b43f3f;

        font-weight: bold;
    }


    @media (max-width: 900px) {

        .product-filter {
            grid-template-columns: 1fr;
        }

    }

</style>

@endpush