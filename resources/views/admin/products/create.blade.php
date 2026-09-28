@extends('layouts.admin')

@section('title', 'Thêm sản phẩm - MiniGrove')

@section('page-title', 'Sản phẩm')

@section('content')
<div class="admin-form-container admin-form-container-wide">

    {{-- PAGE HEADER --}}
    <div class="admin-form-header">
        <div class="admin-form-heading">
            <h1 class="admin-form-title">Thêm sản phẩm mới</h1>
            <p class="admin-form-subtitle">Tạo sản phẩm Terrarium, rêu cảnh hoặc phụ kiện sinh thái cho cửa hàng.</p>
        </div>

        <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
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
                <strong>Không thể lưu sản phẩm. Vui lòng kiểm tra lại:</strong>
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
            action="{{ route('admin.products.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            @include('admin.products._form')

            <div class="admin-form-actions">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Thêm sản phẩm</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection