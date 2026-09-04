@extends('layouts.admin')

@section(
    'title',
    'Cập nhật quy tắc tương thích - MiniGrove'
)

@section('content')

<div class="card">

    <div class="page-header">

        <h1>
            Cập nhật quy tắc tương thích
        </h1>

        <a
            href="{{ route('admin.compatibility-rules.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại
        </a>

    </div>


    <form
        action="{{ route(
            'admin.compatibility-rules.update',
            $compatibilityRule
        ) }}"
        method="POST"
    >

        @csrf
        @method('PUT')

        @include('admin.compatibility-rules._form')


        <button
            type="submit"
            class="btn btn-primary"
        >
            Cập nhật quy tắc
        </button>

    </form>

</div>

@endsection