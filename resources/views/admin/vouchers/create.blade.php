@extends('layouts.admin')

@section('title', 'Thêm Voucher - MiniGrove')

@section('page-title', 'Mã ưu đãi')

@section('content')
<div class="admin-form-container">

    {{-- PAGE HEADER --}}
    <div class="admin-form-header">
        <div class="admin-form-heading">
            <h1 class="admin-form-title">Thêm Voucher mới</h1>
            <p class="admin-form-subtitle">Tạo chương trình ưu đãi, mã giảm giá khuyến mãi cho khách hàng.</p>
        </div>

        <div>
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Quay lại</span>
            </a>
        </div>
    </div>

    {{-- ERROR SUMMARY --}}
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="alert-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="alert-content">
                <strong>Không thể lưu Voucher. Vui lòng kiểm tra lại:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- FORM CARD --}}
    <div class="admin-form-card">
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