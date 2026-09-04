@extends('layouts.admin')

@section('title', 'Thêm danh mục - MiniGrove')

@section('content')

<div class="card">

    <div class="page-header">

        <h1>
            Thêm danh mục
        </h1>

        <a
            href="{{ route('admin.categories.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại
        </a>

    </div>


    <form
        action="{{ route('admin.categories.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf

        @include('admin.categories._form')

        <button
            type="submit"
            class="btn btn-primary"
        >
            Lưu danh mục
        </button>

    </form>

</div>

@endsection