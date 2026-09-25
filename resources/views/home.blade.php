@extends('layouts.customer')


@section(
    'title',
    'MiniGrove - Nhỏ một khu rừng, lớn một thế giới'
)


@section('content')


{{-- HERO --}}
<section class="hero">

    <div class="customer-container">

        <div class="hero-box">

            {{-- Background decorative botanical aura & line art --}}
            <div class="hero-bg-accent" aria-hidden="true"></div>
            <div class="hero-bg-lines" aria-hidden="true">
                <svg width="420" height="420" viewBox="0 0 420 420" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M40 380C140 340 220 220 240 60" stroke="#C8A96B" stroke-width="1.2" stroke-opacity="0.18" stroke-dasharray="4 6"/>
                    <path d="M90 410C170 340 280 200 360 40" stroke="#C8A96B" stroke-width="1.2" stroke-opacity="0.12"/>
                    <circle cx="240" cy="60" r="3" fill="#C56B45" fill-opacity="0.5"/>
                    <circle cx="360" cy="40" r="2.5" fill="#C8A96B" fill-opacity="0.6"/>
                </svg>
            </div>

            {{-- LEFT COLUMN (~52%): Content & Editorial Typography --}}
            <div class="hero-content">

                <div class="hero-eyebrow">
                    <span class="hero-eyebrow-accent" aria-hidden="true"></span>
                    <span>WELCOME TO MINIGROVE</span>
                </div>

                <h1 class="hero-title">
                    Nhỏ một khu rừng,<br>
                    lớn một thế giới.
                </h1>

                <p class="hero-text">
                    Khám phá những hệ sinh thái mini
                    được chăm chút từng chi tiết,
                    hoặc tự tay tạo nên khu rừng
                    mang dấu ấn riêng của bạn.
                </p>

                <div class="hero-actions">

                    <a
                        href="{{ route('products.index') }}"
                        class="
                            hero-button
                            hero-button-light
                            hero-button-primary
                        "
                    >
                        <span>Khám phá sản phẩm</span>
                        <svg class="hero-button-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>

                    <a
                        href="{{ route('studio.index') }}"
                        class="
                            hero-button
                            hero-button-outline
                        "
                    >
                        <span>Tự thiết kế Studio</span>
                    </a>

                </div>

            </div>


            {{-- RIGHT COLUMN (~48%): Asymmetric Botanical Visual Composition --}}
            <div class="hero-visual">

                <div class="hero-visual-composition">

                    <div class="hero-visual-backdrop" aria-hidden="true"></div>

                    {{-- Organic Terrarium Portal Frame --}}
                    <div class="hero-showcase-frame">

                        <svg class="hero-terrarium-artwork" viewBox="0 0 360 420" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Minh họa hệ sinh thái Terrarium MiniGrove">
                            <defs>
                                <linearGradient id="domeGrad" x1="0" y1="0" x2="360" y2="420" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#1E4D40"/>
                                    <stop offset="60%" stop-color="#123B31"/>
                                    <stop offset="100%" stop-color="#0B2620"/>
                                </linearGradient>
                                <radialGradient id="lightBeam" cx="50%" cy="15%" r="60%">
                                    <stop offset="0%" stop-color="#FBF8F2" stop-opacity="0.22"/>
                                    <stop offset="45%" stop-color="#C8A96B" stop-opacity="0.10"/>
                                    <stop offset="100%" stop-color="#0B2620" stop-opacity="0"/>
                                </radialGradient>
                                <linearGradient id="mossGrad1" x1="60" y1="280" x2="280" y2="380" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#8EAB80"/>
                                    <stop offset="50%" stop-color="#557C55"/>
                                    <stop offset="100%" stop-color="#2D4B39"/>
                                </linearGradient>
                                <linearGradient id="mossGrad2" x1="120" y1="240" x2="320" y2="360" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#A9C496"/>
                                    <stop offset="60%" stop-color="#466E4E"/>
                                    <stop offset="100%" stop-color="#1B3B2B"/>
                                </linearGradient>
                                <linearGradient id="stoneGrad" x1="100" y1="260" x2="220" y2="340" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#8A8275"/>
                                    <stop offset="50%" stop-color="#544E45"/>
                                    <stop offset="100%" stop-color="#2E2A24"/>
                                </linearGradient>
                                <linearGradient id="soilLayer" x1="40" y1="360" x2="320" y2="410" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#2B2118"/>
                                    <stop offset="100%" stop-color="#17120C"/>
                                </linearGradient>
                                <linearGradient id="glassReflection" x1="0" y1="0" x2="180" y2="360" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.28"/>
                                    <stop offset="25%" stop-color="#FFFFFF" stop-opacity="0.08"/>
                                    <stop offset="70%" stop-color="#FFFFFF" stop-opacity="0"/>
                                </linearGradient>
                            </defs>

                            {{-- Dome Inner Chamber --}}
                            <path d="M70 140C70 70 119 20 180 20C241 20 290 70 290 140V340C290 375 255 395 180 395C105 395 70 375 70 340V140Z" fill="url(#domeGrad)"/>
                            <path d="M70 140C70 70 119 20 180 20C241 20 290 70 290 140V340C290 375 255 395 180 395C105 395 70 375 70 340V140Z" fill="url(#lightBeam)"/>

                            {{-- Stratified Substrate (Soil, Activated Charcoal & Drainage) --}}
                            <path d="M75 350C100 348 140 353 180 351C220 349 260 354 285 350V370C270 388 230 392 180 392C130 392 90 388 75 370V350Z" fill="url(#soilLayer)"/>
                            <ellipse cx="180" cy="378" rx="80" ry="6" fill="#C56B45" fill-opacity="0.35"/>

                            {{-- Sculpted Aquascape Stone / Driftwood Cliff --}}
                            <path d="M120 352L145 270C148 260 162 258 168 266L195 305L215 285C220 280 228 282 230 289L248 351C205 355 160 354 120 352Z" fill="url(#stoneGrad)"/>

                            {{-- Moss Mounds & Botanical Cushions --}}
                            <path d="M80 348C90 315 130 300 160 325C185 305 235 300 255 330C270 320 285 332 285 350C240 354 120 354 80 348Z" fill="url(#mossGrad1)"/>
                            <path d="M110 345C125 320 155 315 175 332C195 318 230 316 245 342C210 346 150 347 110 345Z" fill="url(#mossGrad2)"/>

                            {{-- Delicate Fern Foliage & Botanical Fronds --}}
                            <g stroke="#A3D4B0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M180 265C178 220 165 195 145 175"/>
                                <path d="M156 190C166 186 172 188 174 194"/>
                                <path d="M165 212C175 210 180 213 180 220"/>
                                <path d="M150 205C142 201 138 204 138 210"/>
                                <path d="M158 228C150 225 146 228 147 234"/>
                            </g>
                            <g stroke="#C8A96B" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M185 270C192 230 208 205 228 185"/>
                                <path d="M214 200C205 196 200 198 198 204"/>
                                <path d="M222 222C212 220 208 223 207 230"/>
                                <path d="M205 215C213 211 217 214 217 220"/>
                            </g>

                            {{-- Terracotta / Rose Veined Fittonia Leaf Accent --}}
                            <path d="M135 295C120 285 118 265 130 255C145 258 152 278 145 292C142 297 137 297 135 295Z" fill="#C56B45" fill-opacity="0.85"/>
                            <path d="M125 274C135 273 142 277 145 290" stroke="#FBF8F2" stroke-width="1.1" stroke-linecap="round" stroke-opacity="0.8"/>

                            {{-- Glass Dome Outer Contour & Specular Glare --}}
                            <path d="M70 140C70 70 119 20 180 20C241 20 290 70 290 140V340C290 375 255 395 180 395C105 395 70 375 70 340V140Z" stroke="rgba(243, 235, 221, 0.35)" stroke-width="2"/>
                            <path d="M85 145C85 85 125 40 175 32C130 42 98 88 98 145V325C98 332 94 336 88 335C86 333 85 329 85 324V145Z" fill="url(#glassReflection)"/>

                            {{-- Dome Finial Brass Knob --}}
                            <ellipse cx="180" cy="18" rx="14" ry="7" fill="#C8A96B"/>
                            <circle cx="180" cy="12" r="6" fill="#E5D1A6"/>
                            <circle cx="180" cy="12" r="2" fill="#8C6F34"/>

                            {{-- Boutique Brand Seal --}}
                            <text x="180" y="325" text-anchor="middle" font-family="'Be Vietnam Pro', sans-serif" font-size="7.5" font-weight="600" letter-spacing="2.4" fill="#C8A96B" fill-opacity="0.9">MINIGROVE BOTANICAL</text>
                            <line x1="120" y1="332" x2="240" y2="332" stroke="#C8A96B" stroke-width="0.8" stroke-opacity="0.4"/>
                        </svg>

                    </div>

                    {{-- Single Floating Detail Card --}}
                    <div class="hero-floating-card">
                        <span class="hero-floating-pill">Studio Craft</span>
                        <div class="hero-floating-title">
                            Thiết kế theo phong cách của bạn
                        </div>
                        <div class="hero-floating-desc">
                            Tự do phối bình, cây & lũa đá
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- CATEGORY --}}
<section
    class="home-section"
    id="categories"
>

    <div class="customer-container">


        <div class="section-heading">

            <div>

                <div class="section-eyebrow">
                    KHÁM PHÁ THEO CHỦ ĐỀ
                </div>

                <h2>
                    Danh mục MiniGrove
                </h2>

                <p>
                    Mỗi thế giới nhỏ mang một câu chuyện riêng.
                </p>

            </div>

        </div>


        <div class="category-grid">

            @forelse ($categories as $category)

                <a
    href="{{
        route(
            'products.index',
            [
                'category' => $category->slug
            ]
        )
    }}"
    class="
        category-card
        {{ $category->image
            ? 'has-image'
            : '' }}
    "
>

                    @if ($category->image)

                        <img
                            class="category-image"
                            src="{{
                                asset(
                                    'storage/'
                                    . $category->image
                                )
                            }}"
                            alt="{{ $category->name }}"
                        >

                        <div class="category-overlay"></div>

                    @endif


                    <div class="category-content">

                        <div class="category-name">
                            {{ $category->name }}
                        </div>

                        <div class="category-count">

                            {{
                                $category
                                    ->active_products_count
                            }}
                            sản phẩm

                        </div>

                    </div>

                </a>

            @empty

                <div class="home-empty">
                    Chưa có danh mục đang hoạt động.
                </div>

            @endforelse

        </div>

    </div>

</section>


{{-- FEATURED PRODUCTS --}}
<section
    class="home-section"
    id="products"
>

    <div class="customer-container">


        <div class="section-heading">

            <div>

                <div class="section-eyebrow">
                    ĐƯỢC YÊU THÍCH
                </div>

                <h2>
                    Sản phẩm nổi bật
                </h2>

                <p>
                    Những hệ sinh thái MiniGrove
                    được lựa chọn dành cho bạn.
                </p>

            </div>

        </div>


        <div class="product-grid">

            @forelse (
                $featuredProducts
                as $product
            )

                @include(
                    'partials.product-card',
                    [
                        'product' => $product
                    ]
                )

            @empty

                <div class="home-empty">

                    Chưa có sản phẩm nổi bật.

                </div>

            @endforelse

        </div>

    </div>

</section>


{{-- STUDIO --}}
<section class="studio-promo">

    <div class="customer-container">

        <div class="studio-promo-box">

            <div>

                <h2>
                    Tạo khu rừng của riêng bạn
                </h2>

                <p>
                    Chọn bình, cây, rêu, nền,
                    đá và phụ kiện.
                    Công cụ thiết kế sẽ kiểm tra
                    khả năng tương thích và tính
                    giá trực tiếp cho thiết kế.
                </p>

            </div>


            <a
                href="{{ route('studio.index') }}"
                class="studio-button"
            >
                Bắt đầu thiết kế →
            </a>

        </div>

    </div>

</section>


{{-- NEW PRODUCTS --}}
<section class="home-section">

    <div class="customer-container">


        <div class="section-heading">

            <div>

                <div class="section-eyebrow">
                    VỪA CẬP NHẬT
                </div>

                <h2>
                    Sản phẩm mới
                </h2>

                <p>
                    Khám phá những MiniGrove
                    mới nhất tại cửa hàng.
                </p>

            </div>

        </div>


        <div class="product-grid">

            @forelse (
                $newProducts
                as $product
            )

                @include(
                    'partials.product-card',
                    [
                        'product' => $product
                    ]
                )

            @empty

                <div class="home-empty">
                    Chưa có sản phẩm.
                </div>

            @endforelse

        </div>

    </div>

</section>


@endsection