<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'MiniGrove')
    </title>

    <link
        rel="stylesheet"
        href="{{
            asset('css/customer.css')
            . '?v='
            . filemtime(
                public_path(
                    'css/customer.css'
                )
            )
        }}"
    >

    @stack('styles')

</head>

<body>

<div class="customer-sticky-shell">

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


        <div class="topbar-links"></div>

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

        {{-- MAIN NAVIGATION --}}
        <nav class="customer-main-nav">

            <a
                href="{{ route('home') }}"
                class="nav-link"
            >
                Trang chủ
            </a>

            <a
                href="{{ route('home') }}#categories"
                class="nav-link"
            >
                Danh mục
            </a>

            <a
                href="{{ route('products.index') }}"
                class="nav-link"
            >
                Sản phẩm
            </a>

            @auth

                @if (
                    auth()->user()->role?->name
                    === 'customer'
                )

                    <a
                        href="{{ route('designs.index') }}"
                        class="nav-link"
                    >
                        Thiết kế của tôi
                    </a>

                    <a
                        href="{{ route('chat.index') }}"
                        class="nav-link"
                    >
                        Chat
                    </a>

                    <a
                        href="{{ route('profile.vouchers') }}"
                        class="nav-link"
                    >
                        Voucher
                    </a>

                @endif

            @endauth

        </nav>


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
                placeholder="Tìm sản phẩm, SKU..."
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
            === 'customer'
        )

            @php

                $headerCartQuantity = 0;

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

            @endphp


            {{-- YÊU THÍCH --}}
            <a
                href="{{ Route::has('wishlist.index')
                    ? route('wishlist.index')
                    : '#'
                }}"
                class="
                    header-action
                    header-wishlist-link
                "
                title="Yêu thích"
            >
                <span class="header-action-icon">
                    ♡
                </span>

                <span>
                    Yêu thích
                </span>
            </a>


            {{-- GIỎ HÀNG --}}
            <a
                href="{{ route('cart.index') }}"
                class="
                    header-action
                    header-cart-link
                "
                title="Giỏ hàng"
            >

                <span class="header-action-icon">
                    🛒
                </span>

                <span>
                    Giỏ hàng
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


            {{-- TÀI KHOẢN --}}
            <details class="header-account">

                <summary class="header-account-trigger">

                    @php

    $headerUser = auth()->user();

    $headerAvatarUrl = null;

    if ($headerUser->avatar) {

        if (
            str_starts_with(
                $headerUser->avatar,
                'http://'
            )
            ||
            str_starts_with(
                $headerUser->avatar,
                'https://'
            )
        ) {

            /*
             * Avatar Google.
             */
            $headerAvatarUrl =
                $headerUser->avatar;

        } else {

            /*
             * Avatar upload local.
             */
            $headerAvatarUrl =
                asset(
                    'storage/' .
                    ltrim(
                        $headerUser->avatar,
                        '/'
                    )
                );
        }
    }

@endphp


<span class="header-account-avatar">

    @if ($headerAvatarUrl)

        <img
            src="{{ $headerAvatarUrl }}"
            alt="{{ $headerUser->name }}"
        >

    @else

        {{
            strtoupper(
                mb_substr(
                    $headerUser->name,
                    0,
                    1
                )
            )
        }}

    @endif

</span>

                    <span>
                        Tài khoản của tôi
                    </span>

                    <span class="header-account-arrow">
                        ▾
                    </span>

                </summary>


                <div class="header-account-menu">

                    <a
                        href="{{ route('profile.edit') }}"
                        class="header-account-menu-item"
                    >
                        Tài khoản của tôi
                    </a>


                    <a
                        href="{{ route('orders.index') }}"
                        class="header-account-menu-item"
                    >
                        Đơn mua
                    </a>


                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        class="header-account-logout"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="header-account-menu-item"
                        >
                            Đăng xuất
                        </button>

                    </form>

                </div>

            </details>

        @elseif (
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

    @endguest

</div>

    </div>

</header>

</div>


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
                href="{{ route('about') }}"
                class="footer-link"
            >
                Về chúng tôi
            </a>

            @auth

                @if (
                    auth()->user()->role?->name
                    === 'customer'
                )

                    <a
                        href="{{ route('chat.index') }}"
                        class="footer-link"
                    >
                        Chat hỗ trợ
                    </a>

                @endif

            @endauth

        </div>


        <div>

            <div class="footer-title">
                Liên hệ
            </div>

            <div class="footer-contact-item">
                <span class="footer-contact-icon">⌖</span>

                <span>
                    Số 41A phường Phú Diễn,
                    quận Bắc Từ Liêm, Hà Nội
                </span>
            </div>

            <a
                href="tel:035791347"
                class="footer-link footer-contact-link"
            >
                <span class="footer-contact-icon">☎</span>
                035791347
            </a>

            <a
                href="mailto:gioinhatlop@gmail.com"
                class="footer-link footer-contact-link"
            >
                <span class="footer-contact-icon">✉</span>
                gioinhatlop@gmail.com
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

        MiniGrove được thực hiện bởi nhóm 13:
        Nguyễn Thị Mến, Nguyễn Minh Hà,
        Nguyễn Tùng Dương.

    </div>

</footer>


@vite(['resources/js/app.js'])

@stack('scripts')

</body>

</html>
