@extends('layouts.admin')

@section('title', 'Sửa danh mục - MiniGrove')

@section('content')

<div class="card">

    <div class="page-header">

        <h1>
            Sửa danh mục
        </h1>

        <a
            href="{{ route('admin.categories.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại
        </a>

    </div>


    <form
        action="{{ route(
            'admin.categories.update',
            $category
        ) }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')

        @include('admin.categories._form')

        <button
            type="submit"
            class="btn btn-primary"
        >
            Cập nhật danh mục
        </button>

    </form>

</div>

@endsection