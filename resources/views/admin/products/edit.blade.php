@extends('layouts.admin')

@section(
    'title',
    'Sửa sản phẩm - MiniGrove'
)

@section('content')

<div class="card">

    <div class="page-header">

        <h1>
            Sửa sản phẩm
        </h1>

        <a
            href="{{ route('admin.products.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại
        </a>

    </div>


    <form
        action="{{ route(
            'admin.products.update',
            $product
        ) }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')

        @include('admin.products._form')

        <button
            type="submit"
            class="btn btn-primary"
        >
            Cập nhật sản phẩm
        </button>

    </form>


    <hr class="product-divider">

    <div class="product-image-heading">
        <div>
            <h2>
                Quản lý hình ảnh
            </h2>

            <p>
                Thêm, thay thế, xóa hoặc chọn ảnh chính
                cho sản phẩm. Ảnh chính được ưu tiên
                hiển thị trên trang khách hàng.
            </p>
        </div>

        <span class="image-count-badge">
            {{ $product->images->count() }}/8 ảnh
        </span>
    </div>

    @if ($product->images->count())

        <div class="product-image-list">

            @foreach ($product->images as $image)

                <div class="product-image-card">

                    <div class="product-image-status">
                        <span>
                            Ảnh {{ $loop->iteration }}
                        </span>

                        @if ($image->is_primary)
                            <span class="primary-label">
                                ★ Ảnh chính
                            </span>
                        @endif
                    </div>

                    <img
                        src="{{ asset(
                            'storage/' .
                            $image->image_path
                        ) }}"
                        alt="{{ $product->name }}"
                    >

                    @unless ($image->is_primary)

                        <form
                            action="{{ route(
                                'admin.products.images.primary',
                                [
                                    $product,
                                    $image->id
                                ]
                            ) }}"
                            method="POST"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-secondary"
                            >
                                ★ Đặt làm ảnh chính
                            </button>
                        </form>

                    @endunless

                    <form
                        action="{{ route(
                            'admin.products.images.update',
                            [
                                $product,
                                $image->id
                            ]
                        ) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="replace-image-form"
                    >
                        @csrf
                        @method('PATCH')

                        <label
                            for="replace-image-{{ $image->id }}"
                            class="replace-image-label"
                        >
                            Thay ảnh này
                        </label>

                        <input
                            id="replace-image-{{ $image->id }}"
                            type="file"
                            name="image"
                            accept=".jpg,.jpeg,.png,.webp"
                            required
                        >

                        <button
                            type="submit"
                            class="btn btn-secondary"
                        >
                            Thay ảnh
                        </button>
                    </form>


                    <form
                        action="{{ route(
                            'admin.products.images.destroy',
                            [
                                $product,
                                $image->id
                            ]
                        ) }}"
                        method="POST"
                        onsubmit="
                            return confirm(
                                'Bạn chắc chắn muốn xóa ảnh này?'
                            );
                        "
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            Xóa ảnh
                        </button>
                    </form>

                </div>

            @endforeach

        </div>

    @else
        <div class="empty-image-state">
            Sản phẩm chưa có hình ảnh.
            Hãy chọn ảnh ở mục “Thêm hình ảnh sản phẩm”
            phía trên rồi cập nhật sản phẩm.
        </div>
    @endif

</div>

@endsection


@push('styles')
<style>
    .product-divider {
        border: 0;
        border-top: 1px solid #e5e9e6;
        margin: 32px 0;
    }

    .product-image-list {
        display: grid;
        grid-template-columns:
            repeat(auto-fill, minmax(220px, 1fr));
        gap: 18px;
        margin-top: 20px;
    }

    .product-image-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
    }

    .product-image-heading h2 {
        margin: 0 0 5px;
    }

    .product-image-heading p {
        margin: 0;
        color: #69736d;
        line-height: 1.55;
    }

    .image-count-badge {
        flex: 0 0 auto;
        padding: 7px 11px;
        border-radius: 999px;
        background: #edf5f0;
        color: #174d39;
        font-size: 13px;
        font-weight: 700;
    }

    .product-image-card {
        border: 1px solid #e0e6e2;
        border-radius: 10px;
        padding: 12px;
        background: #fafbfa;
    }

    .product-image-status {
        min-height: 28px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        color: #69736d;
        font-size: 12px;
    }

    .product-image-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .product-image-card form {
        margin-top: 8px;
    }

    .product-image-card .btn {
        width: 100%;
        text-align: center;
    }

    .replace-image-form {
        padding-top: 8px;
        border-top: 1px solid #e5e9e6;
    }

    .replace-image-label {
        display: block;
        margin-bottom: 6px;
        color: #174d39;
        font-size: 13px;
        font-weight: 700;
    }

    .replace-image-form input[type="file"] {
        width: 100%;
        margin-bottom: 8px;
        font-size: 12px;
    }

    .primary-label {
        display: inline-block;
        padding: 6px 10px;
        background: #dff2e5;
        color: #22643a;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
    }

    .empty-image-state {
        margin-top: 18px;
        padding: 20px;
        border: 1px dashed #cdd8d1;
        border-radius: 10px;
        background: #f7faf8;
        color: #68736c;
    }
</style>
@endpush