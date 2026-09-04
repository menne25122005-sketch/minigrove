@extends('layouts.admin')


@section('title', 'Dashboard - MiniGrove Admin')

@section('page-title', 'Dashboard')


@section('content')

<div class="dashboard-heading">

    <h1>
        Tổng quan MiniGrove
    </h1>

    <p>
        Theo dõi nhanh dữ liệu và hoạt động của hệ thống.
    </p>

</div>


<div class="stats-grid">

    <div class="stat-card">

        <div class="stat-label">
            Sản phẩm
        </div>

        <div class="stat-value">
            {{ $productCount }}
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-label">
            Danh mục
        </div>

        <div class="stat-value">
            {{ $categoryCount }}
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-label">
            Thành phần
        </div>

        <div class="stat-value">
            {{ $componentCount }}
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-label">
            Quy tắc tương thích
        </div>

        <div class="stat-value">
            {{ $compatibilityRuleCount }}
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-label">
            Khách hàng
        </div>

        <div class="stat-value">
            {{ $customerCount }}
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-label">
            Đơn hàng
        </div>

        <div class="stat-value">
            {{ $orderCount }}
        </div>

    </div>

</div>


<div class="card quick-section">

    <h2>
        Truy cập nhanh
    </h2>


    <div class="quick-links">

        <a
            href="{{ route('admin.categories.index') }}"
            class="quick-link"
        >
            Quản lý danh mục
        </a>


        <a
            href="{{ route('admin.products.index') }}"
            class="quick-link"
        >
            Quản lý sản phẩm
        </a>


        <a
            href="{{ route('admin.components.index') }}"
            class="quick-link"
        >
            Quản lý thành phần
        </a>


        <a
            href="{{
                route(
                    'admin.compatibility-rules.index'
                )
            }}"
            class="quick-link"
        >
            Quy tắc tương thích
        </a>

    </div>

</div>

@endsection