@extends('layouts.admin')

@section('title', 'Quản lý danh mục - MiniGrove')

@section('content')

<div class="card">

    <div class="page-header">

        <div>
            <h1>
                Quản lý danh mục
            </h1>
        </div>

        <a
            href="{{ route('admin.categories.create') }}"
            class="btn btn-primary"
        >
            + Thêm danh mục
        </a>

    </div>


    <form
        action="{{ route('admin.categories.index') }}"
        method="GET"
        class="search-form"
    >

        <input
            type="text"
            name="keyword"
            value="{{ $keyword }}"
            placeholder="Tìm tên hoặc slug..."
        >

        <button
            type="submit"
            class="btn btn-primary"
        >
            Tìm kiếm
        </button>

        @if ($keyword)

            <a
                href="{{ route('admin.categories.index') }}"
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
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Sản phẩm</th>
                    <th>Thứ tự</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>

            </thead>


            <tbody>

                @forelse ($categories as $category)

                    <tr>

                        <td>
                            {{ $category->id }}
                        </td>


                        <td>

                            @if ($category->image)

                                <img
                                    class="category-image"
                                    src="{{ asset(
                                        'storage/' .
                                        $category->image
                                    ) }}"
                                    alt="{{ $category->name }}"
                                >

                            @else

                                <div
                                    class="category-image"
                                    style="
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        font-size:12px;
                                        color:#777;
                                    "
                                >
                                    Không ảnh
                                </div>

                            @endif

                        </td>


                        <td>
                            <strong>
                                {{ $category->name }}
                            </strong>
                        </td>


                        <td>
                            {{ $category->slug }}
                        </td>


                        <td>
                            {{ $category->products_count }}
                        </td>


                        <td>
                            {{ $category->display_order }}
                        </td>


                        <td>

                            @if ($category->is_active)

    <span
        class="status status-active"
    >
        Active
    </span>

@else

    <span
        class="status status-inactive"
    >
        Inactive
    </span>

@endif

                        </td>


                        <td>

                            <div class="actions">

                                <a
                                    href="{{ route(
                                        'admin.categories.edit',
                                        $category
                                    ) }}"
                                    class="btn btn-warning"
                                >
                                    Sửa
                                </a>


                                <form
                                    action="{{ route(
                                        'admin.categories.toggle-status',
                                        $category
                                    ) }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-status"
                                    >
                                        {{ $category->is_active
                                            ? 'Inactive'
                                            : 'Active'
                                        }}
                                    </button>

                                </form>


                                <form
                                    action="{{ route(
                                        'admin.categories.destroy',
                                        $category
                                    ) }}"
                                    method="POST"
                                    onsubmit="
                                        return confirm(
                                            'Bạn chắc chắn muốn xóa danh mục này?'
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
                            colspan="8"
                            class="empty"
                        >
                            Không có danh mục phù hợp.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="pagination">
        {{ $categories->links() }}
    </div>

</div>

@endsection