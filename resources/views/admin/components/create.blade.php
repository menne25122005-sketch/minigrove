@extends('layouts.admin')

@section(
    'title',
    'Thêm thành phần - MiniGrove'
)

@section('content')

<div class="card">

    <div class="page-header">

        <h1>
            Thêm thành phần
        </h1>

        <a
            href="{{ route('admin.components.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại
        </a>

    </div>


    <form
        action="{{ route('admin.components.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf

        @include('admin.components._form')


        <button
            type="submit"
            class="btn btn-primary"
        >
            Thêm thành phần
        </button>

    </form>

</div>

@endsection