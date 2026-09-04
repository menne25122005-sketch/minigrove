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

            <div class="hero-content">

                <div class="hero-eyebrow">
                    WELCOME TO MINIGROVE
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
    "
>
    Khám phá sản phẩm
</a>

                    <a
                        href="{{ route('studio.index') }}"
                        class="
                            hero-button
                            hero-button-outline
                        "
                    >
                        Tự thiết kế Studio
                    </a>

                </div>

            </div>


            <div class="hero-visual">

                <div class="hero-orb">
                    🌱
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

                <div
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

                </div>

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
                    MiniGrove Studio sẽ kiểm tra
                    khả năng tương thích và tính
                    giá trực tiếp cho thiết kế.
                </p>

            </div>


            <a
                href="{{ route('studio.index') }}"
                class="studio-button"
            >
                Khám phá Studio →
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