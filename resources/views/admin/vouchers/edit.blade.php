@extends('layouts.admin')

@section('title', 'Sửa Voucher - MiniGrove')

@section('page-title', 'Sửa Voucher')

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
                Sửa Voucher
            </h1>

            <p>
                Cập nhật Voucher
                <strong>{{ $voucher->code }}</strong>.
            </p>

        </div>


        <form
            action="{{
                route(
                    'admin.vouchers.update',
                    $voucher
                )
            }}"
            method="POST"
        >

            @csrf
            @method('PUT')

            @include('admin.vouchers._form')

        </form>

    </div>

</div>

@endsection