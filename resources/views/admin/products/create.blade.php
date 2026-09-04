@extends('layouts.admin')

@section(
    'title',
    'Thêm sản phẩm - MiniGrove'
)

@section('content')

<div class="card">

    <div class="page-header">

        <h1>
            Thêm sản phẩm
        </h1>

        <a
            href="{{ route('admin.products.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại
        </a>

    </div>


    <form
        action="{{ route('admin.products.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf

        @include('admin.products._form')

        <button
            type="submit"
            class="btn btn-primary"
        >
            Thêm sản phẩm
        </button>

    </form>

</div>

@endsection