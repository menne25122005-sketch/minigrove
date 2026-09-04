@extends('layouts.admin')

@section(
    'title',
    'Cập nhật thành phần - MiniGrove'
)

@section('content')

<div class="card">

    <div class="page-header">

        <h1>
            Cập nhật thành phần
        </h1>

        <a
            href="{{ route('admin.components.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại
        </a>

    </div>


    <form
        action="{{ route(
            'admin.components.update',
            $component
        ) }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')

        @include('admin.components._form')


        <button
            type="submit"
            class="btn btn-primary"
        >
            Cập nhật thành phần
        </button>

    </form>


    @if ($component->images->count())

        <hr class="component-divider">


        <h2>
            Hình ảnh hiện tại
        </h2>


        <div class="component-image-list">

            @foreach ($component->images as $image)

                <div class="component-image-card">


                    <img
                        src="{{ asset(
                            'storage/' .
                            $image->image_path
                        ) }}"
                        alt="{{ $component->name }}"
                    >


                    @if ($image->is_primary)

                        <span class="primary-label">
                            Ảnh chính
                        </span>

                    @else

                        <form
                            action="{{ route(
                                'admin.components.images.primary',
                                [
                                    $component,
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
                                Đặt làm ảnh chính
                            </button>

                        </form>

                    @endif


                    <form
                        action="{{ route(
                            'admin.components.images.destroy',
                            [
                                $component,
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

    @endif

</div>

@endsection


@push('styles')

<style>

    .component-divider {
        border: 0;

        border-top: 1px solid #e5e9e6;

        margin: 32px 0;
    }


    .component-image-list {
        display: grid;

        grid-template-columns:
            repeat(
                auto-fill,
                minmax(180px, 1fr)
            );

        gap: 18px;

        margin-top: 20px;
    }


    .component-image-card {
        border: 1px solid #e0e6e2;

        border-radius: 10px;

        padding: 12px;

        background: #fafbfa;
    }


    .component-image-card img {
        width: 100%;

        height: 150px;

        object-fit: cover;

        border-radius: 8px;

        margin-bottom: 10px;
    }


    .component-image-card form {
        margin-top: 8px;
    }


    .primary-label {
        display: inline-block;

        padding: 6px 10px;

        background: #dff2e5;

        color: #22643a;

        border-radius: 20px;

        font-size: 13px;
    }

</style>

@endpush