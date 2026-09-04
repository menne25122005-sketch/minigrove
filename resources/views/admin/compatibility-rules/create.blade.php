@extends('layouts.admin')

@section(
    'title',
    'Thêm quy tắc tương thích - MiniGrove'
)

@section('content')

<div class="card">

    <div class="page-header">

        <h1>
            Thêm quy tắc tương thích
        </h1>

        <a
            href="{{ route('admin.compatibility-rules.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại
        </a>

    </div>


    <form
        action="{{ route('admin.compatibility-rules.store') }}"
        method="POST"
    >

        @csrf

        @include('admin.compatibility-rules._form')


        <button
            type="submit"
            class="btn btn-primary"
        >
            Thêm quy tắc
        </button>

    </form>

</div>

@endsection