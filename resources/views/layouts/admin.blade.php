<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'MiniGrove Admin')
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/admin.css') }}"
    >

    @stack('styles')

</head>


<body>

<div class="admin-layout">


    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}
    <aside class="sidebar">


        {{-- BRAND --}}
        <div class="sidebar-brand">

            <div class="sidebar-brand-name">
                MiniGrove
            </div>

            <div class="sidebar-brand-subtitle">
                ADMIN PANEL
            </div>

        </div>


        {{-- =====================================================
             MENU
        ====================================================== --}}
        <nav class="sidebar-menu">


            {{-- BẢNG ĐIỀU KHIỂN --}}
            <a
                href="{{ route('admin.dashboard') }}"
                class="sidebar-link
                    {{
                        request()->routeIs('admin.dashboard')
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">⌂</span>

                Dashboard

            </a>


            {{-- DANH MỤC --}}
            <a
                href="{{ route('admin.categories.index') }}"
                class="sidebar-link
                    {{
                        request()->routeIs('admin.categories.*')
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">▦</span>

                Danh mục

            </a>


            {{-- SẢN PHẨM --}}
            <a
                href="{{ route('admin.products.index') }}"
                class="sidebar-link
                    {{
                        request()->routeIs('admin.products.*')
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">□</span>

                Sản phẩm

            </a>



            {{-- QUY TẮC TƯƠNG THÍCH --}}
            <a
                href="{{ route('admin.compatibility-rules.index') }}"
                class="sidebar-link
                    {{
                        request()->routeIs(
                            'admin.compatibility-rules.*'
                        )
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">⇄</span>

                Quy tắc tương thích

            </a>


            {{-- TỒN KHO --}}
            <a
                href="{{ route('admin.stocks.index') }}"
                class="sidebar-link
                    {{
                        request()->routeIs('admin.stocks.*')
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">▤</span>

                Tồn kho

            </a>


            {{-- VOUCHER --}}
            <a
                href="{{ route('admin.vouchers.index') }}"
                class="sidebar-link
                    {{
                        request()->routeIs('admin.vouchers.*')
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">%</span>

                Voucher

            </a>


            {{-- ĐƠN HÀNG --}}
            <a
                href="{{ route('admin.orders.index') }}"
                class="sidebar-link
                    {{
                        request()->routeIs('admin.orders.*')
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">▧</span>

                Đơn hàng

            </a>


            {{-- THANH TOÁN --}}
            <a
                href="{{ route('admin.payments.index') }}"
                class="sidebar-link
                    {{
                        request()->routeIs('admin.payments.*')
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">💳</span>

                Thanh toán

            </a>


            {{-- TRẢ HÀNG / HOÀN TIỀN --}}
            <a
                href="{{ route('admin.returns.index') }}"
                class="sidebar-link
                    {{
                        request()->routeIs('admin.returns.*')
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">↩</span>

                Trả hàng / Hoàn tiền

            </a>


            {{-- KHÁCH HÀNG --}}
            <a
                href="{{ route('admin.customers.index') }}"
                class="sidebar-link
                    {{
                        request()->routeIs('admin.customers.*')
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">○</span>

                Khách hàng

            </a>


            {{-- GROVE CỦA KHÁCH HÀNG --}}
            <a
                href="{{ route('admin.groves.index') }}"
                class="
                    sidebar-link
                    {{
                        request()->routeIs('admin.groves.*')
                            ? 'active'
                            : ''
                    }}
                "
            >

                <span class="sidebar-icon">
                    ♧
                </span>

                Grove của khách hàng

            </a>


            {{-- CHAT --}}
            <span class="sidebar-link sidebar-link-disabled">

                <span class="sidebar-icon">◌</span>

                Chat

            </span>


            {{-- ĐÁNH GIÁ SẢN PHẨM --}}
            <a
                href="{{ route('admin.reviews.index') }}"
                class="sidebar-link
                    {{
                        request()->routeIs('admin.reviews.*')
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">☆</span>

                Đánh giá sản phẩm

            </a>


                        {{-- BÁO CÁO & THỐNG KÊ --}}
            <a
                href="{{ route('admin.reports.index') }}"
                class="sidebar-link
                    {{
                        request()->routeIs('admin.reports.*')
                            ? 'active'
                            : ''
                    }}"
            >

                <span class="sidebar-icon">▥</span>

                Báo cáo & thống kê

            </a>


        {{-- =====================================================
             PHẦN CUỐI SIDEBAR
        ====================================================== --}}
        <div class="sidebar-bottom">


            {{-- VỀ TRANG CHỦ --}}
            <a
                href="{{ route('home') }}"
                class="sidebar-link"
            >

                <span class="sidebar-icon">🌿</span>

                Về trang chủ

            </a>


            {{-- TÀI KHOẢN --}}
            <div class="sidebar-user">

                <div class="sidebar-user-name">
                    {{ auth()->user()->name }}
                </div>

                <div class="sidebar-user-role">
                    Tài khoản Admin
                </div>

            </div>


            {{-- ĐĂNG XUẤT --}}
            <form
                method="POST"
                action="{{ route('logout') }}"
                class="sidebar-logout-form"
            >

                @csrf

                <button
                    type="submit"
                    class="sidebar-link sidebar-logout"
                >

                    <span class="sidebar-icon">↪</span>

                    Đăng xuất

                </button>

            </form>


        </div>


    </aside>


    {{-- =========================================================
         MAIN
    ========================================================== --}}
    <div class="admin-main">


        {{-- TOP BAR --}}
        <header class="admin-topbar">

            <div class="admin-topbar-title">

                @yield(
                    'page-title',
                    'Quản trị MiniGrove'
                )

            </div>

            <div class="admin-topbar-text">

                Nhỏ một khu rừng, lớn một thế giới.

            </div>

        </header>


        {{-- =====================================================
             CONTENT
        ====================================================== --}}
        <main class="container">


            {{-- =================================================
                 THÔNG BÁO THÀNH CÔNG
                 Dùng chung cho toàn bộ Admin
            ================================================== --}}
            @if (session('success'))

                <div class="alert alert-success">

                    {{ session('success') }}

                </div>

            @endif


            {{-- =================================================
                 THÔNG BÁO LỖI NGHIỆP VỤ
            ================================================== --}}
            @if (session('error'))

                <div class="alert alert-danger">

                    {{ session('error') }}

                </div>

            @endif


            {{-- =================================================
                 VALIDATION ERROR
            ================================================== --}}
            @if ($errors->any())

                <div class="alert alert-danger">

                    <strong>
                        Không thể thực hiện thao tác:
                    </strong>

                    <ul>

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- =================================================
                 NỘI DUNG TRANG
            ================================================== --}}
            @yield('content')


        </main>


    </div>


</div>


@stack('scripts')


</body>

</html>