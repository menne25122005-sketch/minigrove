<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'MiniGrove')
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/customer.css') }}"
    >

    @stack('styles')

</head>

<body>


{{-- TOP BAR --}}
<div class="customer-topbar">

    <div
        class="
            customer-container
            customer-topbar-inner
        "
    >

        <div>
            Nhỏ một khu rừng, lớn một thế giới.
        </div>


        <div class="topbar-links">

            <a
                href="{{ route('studio.index') }}"
                class="topbar-link"
            >
                MiniGrove Studio
            </a>

            @auth

                <a
                    href="{{ route('profile.edit') }}"
                    class="topbar-link"
                >
                    Hồ sơ
                </a>

            @endauth

        </div>

    </div>

</div>


{{-- HEADER --}}
<header class="customer-header">

    <div
        class="
            customer-container
            customer-header-inner
        "
    >

        {{-- Brand --}}
        <a
            href="{{ route('home') }}"
            class="customer-brand"
        >

            <span class="customer-brand-icon">
                🌿
            </span>

            <span>

                <span class="customer-brand-name">
                    MiniGrove
                </span>

                <span
                    class="customer-brand-slogan"
                    style="display:block"
                >
                    MINI ECOSYSTEM
                </span>

            </span>

        </a>


        {{-- Search --}}
        <form
            class="customer-search"
            action="{{ route('products.index') }}"
            method="GET"
        >
            <input
                type="text"
                name="keyword"
                value="{{ request('keyword') }}"
                placeholder="Tìm Terrarium, tên sản phẩm, SKU..."
            >

            <button
                type="submit"
                class="search-button"
                aria-label="Tìm kiếm"
            >
                ⌕
            </button>

        </form>


        {{-- Actions --}}
        <div class="header-actions">

            @guest

                <a
                    href="{{ route('login') }}"
                    class="header-action"
                >
                    Đăng nhập
                </a>

                <a
                    href="{{ route('register') }}"
                    class="
                        header-action
                        header-action-primary
                    "
                >
                    Đăng ký
                </a>

            @else

                @if (
                    auth()->user()->role?->name
                    === 'admin'
                )

                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="
                            header-action
                            header-action-primary
                        "
                    >
                        Khu quản trị
                    </a>

                @endif


                <a
                    href="{{ route('profile.edit') }}"
                    class="header-action"
                >
                    {{ auth()->user()->name }}
                </a>


                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="header-logout"
                >

                    @csrf

                    <button
                        type="submit"
                        class="header-action"
                    >
                        Đăng xuất
                    </button>

                </form>

            @endguest


            @guest

                <a
                    href="{{ route('login') }}"
                    class="header-action"
                    title="Đăng nhập để xem giỏ hàng"
                >
                    🛒
                </a>

            @else

                @if (
                    auth()->user()->role?->name
                    === 'customer'
                )

                    @php

                        $headerCartQuantity = 0;

                        if (
                            auth()->check()
                            && auth()->user()->role?->name
                                === 'customer'
                        ) {

                            $headerCartQuantity =
                                \App\Models\CartItem::query()
                                    ->whereHas(
                                        'cart',
                                        function ($query) {

                                            $query->where(
                                                'user_id',
                                                auth()->id()
                                            );
                                        }
                                    )
                                    ->sum('quantity');
                        }

                    @endphp


                    <a
                        href="{{ route('cart.index') }}"
                        class="
                            header-action
                            header-cart-link
                        "
                        title="Giỏ hàng"
                    >

                        <span>
                            🛒
                        </span>


                        @if ($headerCartQuantity > 0)

                            <span class="header-cart-badge">

                                {{
                                    $headerCartQuantity > 99
                                        ? '99+'
                                        : $headerCartQuantity
                                }}

                            </span>

                        @endif

                    </a>

                @endif

            @endguest

        </div>

    </div>

</header>


{{-- NAVIGATION --}}
<nav class="customer-nav">

    <div
        class="
            customer-container
            customer-nav-inner
        "
    >

        <a
            href="{{ route('home') }}"
            class="nav-link"
        >
            Trang chủ
        </a>

        <a
            href="#categories"
            class="nav-link"
        >
            Danh mục
        </a>

        <a
            href="#products"
            class="nav-link"
        >
            Sản phẩm
        </a>

        <a
            href="{{ route('studio.index') }}"
            class="nav-link"
        >
            MiniGrove Studio
        </a>


        @auth

            @if (
                auth()->user()->role?->name
                === 'customer'
            )

                <a
                    href="{{ route('groves.index') }}"
                    class="nav-link"
                >
                    My Grove
                </a>

            @endif

        @endauth


        <a
            href="#"
            class="nav-link"
        >
            GroveMatch
        </a>

        <a
            href="#"
            class="nav-link"
        >
            Kiến thức
        </a>

    </div>

</nav>


{{-- CONTENT --}}
<main>

    {{-- THÔNG BÁO THÀNH CÔNG --}}
    @if (session('success'))

        <div class="customer-container">

            <div class="customer-alert customer-alert-success">
                ✓ {{ session('success') }}
            </div>

        </div>

    @endif


    {{-- THÔNG BÁO LỖI --}}
    @if (session('error'))

        <div class="customer-container">

            <div class="customer-alert customer-alert-error">
                {{ session('error') }}
            </div>

        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if ($errors->any())

        <div class="customer-container">

            <div class="customer-alert customer-alert-error">

                {{ $errors->first() }}

            </div>

        </div>

    @endif


    @yield('content')

</main>


{{-- FOOTER --}}
<footer class="customer-footer">

    <div
        class="
            customer-container
            customer-footer-inner
        "
    >

        <div>

            <div class="footer-brand">
                🌿 MiniGrove
            </div>

            <div class="footer-description">
                Không chỉ bán một Terrarium,
                MiniGrove đồng hành cùng hệ sinh thái
                của bạn trong suốt hành trình chăm sóc
                và phát triển.
            </div>

        </div>


        <div>

            <div class="footer-title">
                Khám phá
            </div>

            <a
                href="{{ route('products.index') }}"
                class="footer-link"
            >
                Sản phẩm
            </a>

            <a
                href="{{ route('studio.index') }}"
                class="footer-link"
            >
                MiniGrove Studio
            </a>


            @auth

                @if (
                    auth()->user()->role?->name
                    === 'customer'
                )

                    <a
                        href="{{ route('groves.index') }}"
                        class="footer-link"
                    >
                        My Grove
                    </a>

                @endif

            @endauth


            <a
                href="#"
                class="footer-link"
            >
                GroveMatch
            </a>

        </div>


        <div>

            <div class="footer-title">
                Tài khoản
            </div>

            @guest

                <a
                    href="{{ route('login') }}"
                    class="footer-link"
                >
                    Đăng nhập
                </a>

                <a
                    href="{{ route('register') }}"
                    class="footer-link"
                >
                    Đăng ký
                </a>

            @else

                <a
                    href="{{ route('profile.edit') }}"
                    class="footer-link"
                >
                    Hồ sơ cá nhân
                </a>

            @endguest

        </div>

    </div>


    <div class="customer-footer-bottom">

        MiniGrove —
        Nhỏ một khu rừng, lớn một thế giới.

    </div>

</footer>


@stack('scripts')

</body>

</html>
