@extends('layouts.customer')


@section(
    'title',
    'MiniGrove - Nhỏ một khu rừng, lớn một thế giới'
)


@section('content')


{{-- HERO --}}
<section class="hero">

    <div class="customer-container">

        <div class="hero-box hero-banner-box">

            {{-- Real Photographic Banner Visual --}}
            <div class="hero-visual hero-banner-visual">
                <img
                    src="{{ asset('images/banner/banner.png') }}"
                    alt="MiniGrove Terrarium Banner"
                    class="hero-banner-img"
                    width="2149"
                    height="732"
                    loading="eager"
                    fetchpriority="high"
                >
                <div class="hero-banner-overlay" aria-hidden="true"></div>
            </div>

            {{-- Editorial Text Overlay Content --}}
            <div class="hero-content hero-banner-content">

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

        </div>

    </div>

</section>


{{-- CATEGORY --}}
<section
    class="home-section reveal-on-scroll"
    id="categories"
>

    <div class="customer-container">


        <div class="section-heading category-section-heading">

            <div class="category-heading-left">

                <div class="section-eyebrow">
                    <span class="category-eyebrow-accent" aria-hidden="true"></span>
                    <span>KHÁM PHÁ THEO CHỦ ĐỀ</span>
                </div>

                <h2>
                    Danh mục MiniGrove
                </h2>

            </div>

            <div class="category-heading-right">

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
                            : 'no-image' }}
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
                            loading="lazy"
                        >

                        <div class="category-overlay" aria-hidden="true"></div>

                    @else

                        <div class="category-fallback" aria-hidden="true">
                            <svg class="category-fallback-motif" width="120" height="120" viewBox="0 0 120 120" fill="none">
                                <path d="M60 20C40 40 30 65 30 90C55 90 80 80 100 60C100 35 85 20 60 20Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M60 20V90" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-dasharray="3 3"/>
                                <path d="M45 55C55 58 60 65 60 65" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                                <path d="M75 45C65 48 60 55 60 55" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                            </svg>
                        </div>

                        <div class="category-overlay" aria-hidden="true"></div>

                    @endif


                    <div class="category-content">

                        <div class="category-name">
                            {{ $category->name }}
                        </div>

                        <div class="category-count">
                            <span class="category-count-accent" aria-hidden="true"></span>
                            <span>{{ $category->active_products_count }} sản phẩm</span>
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
    class="home-section reveal-on-scroll"
    id="products"
>

    <div class="customer-container">


        <div class="section-heading featured-section-heading">

            <div class="featured-heading-left">

                <div class="section-eyebrow">
                    <span class="featured-eyebrow-accent" aria-hidden="true"></span>
                    <span>ĐƯỢC YÊU THÍCH</span>
                </div>

                <h2>
                    Sản phẩm nổi bật
                </h2>

            </div>

            <div class="featured-heading-right">

                <p>
                    Những hệ sinh thái MiniGrove được lựa chọn dành cho bạn.
                </p>

            </div>

        </div>


        <div class="featured-products-grid product-grid">

            @forelse (
                $featuredProducts
                as $product
            )

                <div class="featured-product-slot {{ $loop->first ? 'featured-product-slot--focal' : '' }}">

                    @include(
                        'partials.product-card',
                        [
                            'product' => $product
                        ]
                    )

                </div>

            @empty

                <div class="home-empty">

                    Chưa có sản phẩm nổi bật.

                </div>

            @endforelse

        </div>

    </div>

</section>


{{-- STUDIO --}}
<section class="studio-promo reveal-on-scroll" id="studio">
    <div class="customer-container">
        <div class="studio-panel">
            {{-- Atmospheric organic blobs --}}
            <div class="studio-bg-aura" aria-hidden="true"></div>
            <div class="studio-leaf-motif" aria-hidden="true">
                <svg width="280" height="280" viewBox="0 0 280 280" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M40 240C40 240 70 120 180 80C140 130 130 190 40 240Z" stroke="rgba(201, 166, 107, 0.14)" stroke-width="1.5" stroke-dasharray="4 4" />
                    <path d="M180 80C220 60 250 40 250 40C250 40 240 90 200 130C160 170 110 200 40 240" stroke="rgba(111, 138, 110, 0.16)" stroke-width="1.5" />
                    <circle cx="180" cy="80" r="3" fill="rgba(201, 166, 107, 0.3)" />
                </svg>
            </div>

            <div class="studio-grid">
                {{-- Left: Workshop Narrative & Process --}}
                <div class="studio-info">
                    <div class="studio-eyebrow">
                        <span class="studio-eyebrow-badge">STUDIO MINIGROVE</span>
                        <span class="studio-eyebrow-divider" aria-hidden="true"></span>
                        <span class="studio-eyebrow-tag">TRẢI NGHIỆM THIẾT KẾ</span>
                    </div>

                    <h2 class="studio-heading">
                        Tạo khu rừng<br>của riêng bạn
                    </h2>

                    <p class="studio-desc">
                        Tự tay lựa chọn từ phôi bình kính, lớp nền sinh học đến từng nhánh rêu, phiến đá và tán cây nhiệt đới. Studio tự động kiểm tra tương thích sinh thái và tính giá trực tiếp cho thiết kế của bạn.
                    </p>

                    {{-- 6-step real process indicators --}}
                    <div class="studio-process" aria-label="Quy trình 6 bước thiết kế terrarium">
                        <div class="studio-process-label">
                            <span class="studio-process-num" aria-hidden="true">01—06</span>
                            <span>Quy trình sáng tạo 6 bước</span>
                        </div>
                        <div class="studio-steps-grid">
                            <div class="studio-step-chip">
                                <span class="step-idx">01</span>
                                <span class="step-name">Bình kính</span>
                            </div>
                            <div class="studio-step-chip">
                                <span class="step-idx">02</span>
                                <span class="step-name">Cây &amp; Rêu</span>
                            </div>
                            <div class="studio-step-chip">
                                <span class="step-idx">03</span>
                                <span class="step-name">Đất nền</span>
                            </div>
                            <div class="studio-step-chip">
                                <span class="step-idx">04</span>
                                <span class="step-name">Lũa &amp; Đá</span>
                            </div>
                            <div class="studio-step-chip">
                                <span class="step-idx">05</span>
                                <span class="step-name">Phụ kiện</span>
                            </div>
                            <div class="studio-step-chip">
                                <span class="step-idx">06</span>
                                <span class="step-name">Phong cách</span>
                            </div>
                        </div>
                    </div>

                    <div class="studio-action">
                        <a
                            href="{{ route('studio.index') }}"
                            class="studio-button"
                        >
                            <span class="studio-btn-text">Bắt đầu thiết kế</span>
                            <span class="studio-btn-arrow" aria-hidden="true">→</span>
                        </a>
                        <span class="studio-note">Khám phá xưởng terrarium trực tuyến</span>
                    </div>
                </div>

                {{-- Right: Signature Terrarium Visual --}}
                <div class="studio-visual-area">
                    <div class="studio-terrarium-display">
                        {{-- Ambient Aura behind vessel --}}
                        <div class="studio-vessel-glow" aria-hidden="true"></div>

                        {{-- Bespoke Crafted Inline SVG Terrarium Vessel --}}
                        <div class="studio-terrarium-art" aria-hidden="true">
                            <svg class="studio-terrarium-svg" viewBox="0 0 460 520" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="stGlassGrad" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.22" />
                                        <stop offset="35%" stop-color="#E8F5E9" stop-opacity="0.08" />
                                        <stop offset="70%" stop-color="#81C784" stop-opacity="0.04" />
                                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0.16" />
                                    </linearGradient>

                                    <linearGradient id="stDrainageGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#A1887F" />
                                        <stop offset="100%" stop-color="#5D4037" />
                                    </linearGradient>

                                    <linearGradient id="stCarbonGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#3E2723" />
                                        <stop offset="100%" stop-color="#212121" />
                                    </linearGradient>

                                    <linearGradient id="stSoilGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#556B2F" />
                                        <stop offset="30%" stop-color="#3D3028" />
                                        <stop offset="100%" stop-color="#271C16" />
                                    </linearGradient>

                                    <linearGradient id="stMossGrad" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#8FBC8F" />
                                        <stop offset="50%" stop-color="#4F7942" />
                                        <stop offset="100%" stop-color="#2D5A27" />
                                    </linearGradient>

                                    <linearGradient id="stFoliageGrad1" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#A9DFBF" />
                                        <stop offset="100%" stop-color="#1E8449" />
                                    </linearGradient>

                                    <linearGradient id="stFoliageGrad2" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#7DCEA0" />
                                        <stop offset="100%" stop-color="#145A32" />
                                    </linearGradient>

                                    <linearGradient id="stStoneGrad" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#8C92AC" />
                                        <stop offset="50%" stop-color="#5A6275" />
                                        <stop offset="100%" stop-color="#373B47" />
                                    </linearGradient>

                                    <linearGradient id="stWoodGrad" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#A0522D" />
                                        <stop offset="100%" stop-color="#5C2914" />
                                    </linearGradient>

                                    <linearGradient id="stSpecularGrad" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.45" />
                                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0.0" />
                                    </linearGradient>
                                </defs>

                                {{-- Pedestal Base shadow --}}
                                <ellipse cx="230" cy="486" rx="140" ry="18" fill="rgba(6, 20, 16, 0.45)" />

                                {{-- Cork / Wooden Lid --}}
                                <path d="M190 48 C190 44 200 40 230 40 C260 40 270 44 270 48 L266 64 C266 68 256 72 230 72 C204 72 194 68 194 64 Z" fill="#B38B6D" />
                                <ellipse cx="230" cy="46" rx="36" ry="7" fill="#C9A683" />
                                <ellipse cx="230" cy="46" rx="10" ry="3" fill="#8D684B" />

                                {{-- Main Glass Jar Body Outline --}}
                                <path d="M194 64 C194 76 140 108 114 168 C86 232 84 370 108 424 C128 468 174 480 230 480 C286 480 332 468 352 424 C376 370 374 232 346 168 C320 108 266 76 266 64 Z"
                                      fill="url(#stGlassGrad)"
                                      stroke="rgba(255, 255, 255, 0.35)"
                                      stroke-width="2.5" />

                                {{-- Inner Atmospheric Microclimate --}}
                                <ellipse cx="230" cy="280" rx="120" ry="150" fill="rgba(111, 138, 110, 0.06)" />

                                {{-- LAYER 03: SUBSTRATE STRATA --}}
                                <g opacity="0.95">
                                    {{-- Drainage Gravel Base --}}
                                    <path d="M115 435 C135 464 176 476 230 476 C284 476 325 464 345 435 C320 448 275 454 230 454 C185 454 140 448 115 435 Z" fill="url(#stDrainageGrad)" />
                                    {{-- Pebbles texture --}}
                                    <circle cx="150" cy="452" r="5" fill="#D7CCC8" opacity="0.7" />
                                    <circle cx="175" cy="458" r="6" fill="#8D6E63" opacity="0.8" />
                                    <circle cx="210" cy="462" r="5.5" fill="#BCAAA4" opacity="0.7" />
                                    <circle cx="245" cy="463" r="7" fill="#6D4C41" opacity="0.9" />
                                    <circle cx="280" cy="459" r="6" fill="#A1887F" opacity="0.8" />
                                    <circle cx="310" cy="453" r="5" fill="#D7CCC8" opacity="0.7" />

                                    {{-- Activated Charcoal Layer --}}
                                    <path d="M110 415 C132 432 178 440 230 440 C282 440 328 432 350 415 C345 435 325 448 230 454 C140 448 115 435 110 415 Z" fill="url(#stCarbonGrad)" />

                                    {{-- Botanical Soil Layer --}}
                                    <path d="M102 368 C120 376 160 388 210 382 C265 376 310 392 358 372 C362 395 354 416 350 415 C328 432 282 440 230 440 C178 440 132 432 110 415 C106 395 101 380 102 368 Z" fill="url(#stSoilGrad)" />
                                </g>

                                {{-- LAYER 04: ROCK & DRIFTWOOD --}}
                                <path d="M142 374 L165 298 L188 320 L196 376 Z" fill="url(#stStoneGrad)" stroke="rgba(0,0,0,0.15)" stroke-width="1" />
                                <path d="M165 298 L180 326 L175 374 L162 374 Z" fill="rgba(255,255,255,0.12)" />
                                <path d="M188 335 L208 305 L224 340 L216 375 Z" fill="#454D5A" />

                                {{-- Natural Driftwood --}}
                                <path d="M210 372 C225 340 248 305 272 268 C278 260 286 262 284 274 C262 312 245 352 238 375 Z" fill="url(#stWoodGrad)" />
                                <path d="M255 300 C270 285 290 280 306 278 C302 284 286 295 268 312 Z" fill="#795548" />

                                {{-- LAYER 02: BOTANICAL FOLIAGE & MOSS --}}
                                <path d="M118 368 C135 348 165 348 185 365 C205 352 235 354 252 368 C270 355 305 358 330 366 C348 362 358 368 358 372 C310 392 265 376 210 382 C160 388 120 376 102 368 C108 365 112 366 118 368 Z" fill="url(#stMossGrad)" />
                                <circle cx="145" cy="358" r="2.5" fill="#A2D149" opacity="0.6" />
                                <circle cx="170" cy="356" r="3" fill="#C5E1A5" opacity="0.5" />
                                <circle cx="225" cy="360" r="2.5" fill="#A2D149" opacity="0.7" />
                                <circle cx="290" cy="362" r="3.5" fill="#AED581" opacity="0.6" />

                                {{-- Tropical Ferns & Fittonia --}}
                                <path d="M150 330 C130 290 120 250 135 220 C145 245 155 275 168 310 Z" fill="url(#stFoliageGrad1)" />
                                <path d="M135 220 C125 210 115 225 128 238 C115 240 110 252 122 260 C110 268 112 280 124 284 Z" fill="#2E7D32" opacity="0.8" />

                                <ellipse cx="230" cy="295" rx="24" ry="14" transform="rotate(-18 230 295)" fill="url(#stFoliageGrad2)" />
                                <path d="M210 300 Q230 295 250 290" stroke="#E8F8F5" stroke-width="1.2" stroke-linecap="round" opacity="0.85" />
                                <path d="M220 294 Q224 290 228 296" stroke="#E8F8F5" stroke-width="0.8" opacity="0.7" />
                                <path d="M234 292 Q238 288 242 294" stroke="#E8F8F5" stroke-width="0.8" opacity="0.7" />

                                <ellipse cx="270" cy="275" rx="20" ry="12" transform="rotate(22 270 275)" fill="url(#stFoliageGrad1)" />
                                <path d="M252 270 Q270 275 288 280" stroke="#D4EFDF" stroke-width="1" stroke-linecap="round" opacity="0.75" />

                                {{-- LAYER 05: MINIATURE LANTERN ACCESSORY --}}
                                <g transform="translate(294, 335)">
                                    <path d="M8 18 L16 18 L18 24 L6 24 Z" fill="#ECEFF1" opacity="0.85" />
                                    <rect x="9" y="10" width="6" height="8" rx="1" fill="#FFECB3" />
                                    <circle cx="12" cy="14" r="2.5" fill="#FFA000" opacity="0.9" />
                                    <path d="M4 10 Q12 6 20 10 L18 8 Q12 5 6 8 Z" fill="#CFD8DC" />
                                    <rect x="11" y="24" width="2" height="6" fill="#90A4AE" />
                                </g>

                                {{-- LAYER 01: GLASS HIGHLIGHTS --}}
                                <path d="M125 150 C108 200 104 310 120 380 C114 320 118 210 134 160 Z" fill="url(#stSpecularGrad)" opacity="0.75" />
                                <path d="M338 150 C352 195 356 280 348 340 C352 270 348 190 330 155 Z" fill="#FFFFFF" opacity="0.25" />
                                <ellipse cx="230" cy="74" rx="34" ry="6" stroke="#FFFFFF" stroke-width="1" opacity="0.4" fill="none" />
                            </svg>
                        </div>

                        {{-- Floating Botanical Material Indicators (Editorial Chips) --}}
                        <div class="studio-chip chip-glass" aria-hidden="true">
                            <span class="chip-dot"></span>
                            <span class="chip-label">Bình kính thủ công</span>
                        </div>

                        <div class="studio-chip chip-plants" aria-hidden="true">
                            <span class="chip-dot"></span>
                            <span class="chip-label">Rêu đệm &amp; Cây nhiệt đới</span>
                        </div>

                        <div class="studio-chip chip-strata" aria-hidden="true">
                            <span class="chip-dot"></span>
                            <span class="chip-label">Đất nền sinh thái &amp; Than</span>
                        </div>

                        <div class="studio-chip chip-stone" aria-hidden="true">
                            <span class="chip-dot"></span>
                            <span class="chip-label">Đá tự nhiên &amp; Lũa rừng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- NEW PRODUCTS --}}
<section
    class="home-section reveal-on-scroll"
    id="new-products"
>

    <div class="customer-container">


        <div class="section-heading new-products-heading">

            <div class="new-heading-left">

                <div class="section-eyebrow">
                    <span class="new-eyebrow-accent" aria-hidden="true"></span>
                    <span>VỪA CẬP NHẬT</span>
                </div>

                <h2>
                    Sản phẩm mới
                </h2>

            </div>

            <div class="new-heading-right">

                <p>
                    Khám phá những MiniGrove mới nhất tại cửa hàng.
                </p>

            </div>

        </div>


        <div class="new-products-grid product-grid">

            @forelse (
                $newProducts
                as $product
            )

                <div class="new-product-slot {{ $loop->iteration % 2 === 0 ? 'new-product-slot--offset' : 'new-product-slot--anchor' }}">

                    @include(
                        'partials.product-card',
                        [
                            'product' => $product
                        ]
                    )

                </div>

            @empty

                <div class="home-empty">
                    Chưa có sản phẩm.
                </div>

            @endforelse

        </div>

    </div>

</section>


@push('scripts')
<script>
(function () {
    'use strict';
    if (typeof window === 'undefined' || !('IntersectionObserver' in window)) return;

    // Fail-safe: activate reveal CSS only when IntersectionObserver is supported & active
    document.documentElement.classList.add('js-motion-ready');

    var revealElements = document.querySelectorAll('.reveal-on-scroll');
    if (!revealElements.length) return;

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.08,
        rootMargin: '0px 0px -40px 0px'
    });

    revealElements.forEach(function (el) {
        observer.observe(el);
    });
})();
</script>
@endpush

@endsection