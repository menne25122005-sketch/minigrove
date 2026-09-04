@extends('layouts.admin')

@section('title', 'Thêm Voucher - MiniGrove')

@section('page-title', 'Thêm Voucher')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/admin-vouchers.css') }}"
    >
@endpush

@section('content')

<div class="voucher-page">

    <div class="voucher-form-page-header">

        <a
            href="{{ route('admin.vouchers.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại danh sách
        </a>

    </div>


    <div class="card voucher-form-card">

        <div class="voucher-form-title">

            <h1>
                Thêm Voucher
            </h1>

            <p>
                Tạo chương trình giảm giá mới cho Customer.
            </p>

        </div>


        <form
            action="{{ route('admin.vouchers.store') }}"
            method="POST"
        >

            @csrf

            @include('admin.vouchers._form')

        </form>

    </div>

</div>

@endsection