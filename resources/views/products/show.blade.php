@extends('layouts.customer')


@section(
    'title',
    $product->name . ' - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/products.css') }}"
>

@endpush


@section('content')

@php

    $primaryImage =
        $product->images
            ->firstWhere(
                'is_primary',
                true
            )
        ?? $product->images->first();

@endphp


<section class="product-detail-page">

    <div class="customer-container">

    <a
    href="{{ route('products.index') }}"
    class="product-back-link"
>
    ← Quay lại sản phẩm
</a>
        <div class="product-detail-grid">


            {{-- LEFT: IMAGE --}}
            <div>

                <div class="product-gallery-main">

                    @if ($primaryImage)

                        <img
                            src="{{
                                asset(
                                    'storage/'
                                    . $primaryImage->image_path
                                )
                            }}"
                            alt="{{ $product->name }}"
                        >

                    @else

                        <div class="product-gallery-empty">
                            🌱
                        </div>

                    @endif

                </div>


                @if ($product->images->count() > 1)

                    <div class="product-thumbnails">

                        @foreach (
                            $product->images
                            as $image
                        )

                            <div class="product-thumbnail">

                                <img
                                    src="{{
                                        asset(
                                            'storage/'
                                            . $image->image_path
                                        )
                                    }}"
                                    alt="{{ $product->name }}"
                                >

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>


            {{-- RIGHT: INFO --}}
            <div>

                <div class="product-detail-category">

                    {{
                        $product->category?->name
                        ?? 'MiniGrove'
                    }}

                </div>


                <h1 class="product-detail-title">
                    {{ $product->name }}
                </h1>


                <div class="product-detail-sku">
                    SKU: {{ $product->sku }}
                </div>


                <div class="product-detail-price">

                    @if (
                        $product->sale_price !== null
                    )

                        <div class="detail-sale-price">

                            {{
                                number_format(
                                    (float)
                                    $product->sale_price,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </div>

                        <div class="detail-old-price">

                            {{
                                number_format(
                                    (float)
                                    $product->list_price,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </div>

                    @else

                        <div class="detail-normal-price">

                            {{
                                number_format(
                                    (float)
                                    $product->list_price,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </div>

                    @endif

                </div>


                <div class="product-detail-info">


                    <div class="detail-info-item">

                        <div class="detail-info-label">
                            Hệ sinh thái
                        </div>

                        <div class="detail-info-value">

                            @switch(
                                $product->ecosystem_type
                            )

                                @case('closed')
                                    Hệ kín
                                    @break

                                @case('open')
                                    Hệ mở
                                    @break

                                @case('dry')
                                    Hệ khô
                                    @break

                                @default
                                    {{
                                        $product
                                            ->ecosystem_type
                                    }}

                            @endswitch

                        </div>

                    </div>


                    <div class="detail-info-item">

                        <div class="detail-info-label">
                            Mức chăm sóc
                        </div>

                        <div class="detail-info-value">

                            @switch(
                                $product->care_level
                            )

                                @case('easy')
                                    Dễ chăm
                                    @break

                                @case('medium')
                                    Trung bình
                                    @break

                                @case('high')
                                    Cần chăm sóc kỹ
                                    @break

                                @default
                                    {{
                                        $product
                                            ->care_level
                                    }}

                            @endswitch

                        </div>

                    </div>


                    <div class="detail-info-item">

                        <div class="detail-info-label">
                            Kích thước
                        </div>

                        <div class="detail-info-value">

                            {{
                                $product->size
                                ?: 'Đang cập nhật'
                            }}

                        </div>

                    </div>


                    <div class="detail-info-item">

                        <div class="detail-info-label">
                            Chu kỳ chăm sóc
                        </div>

                        <div class="detail-info-value">

                            @if (
                                $product->care_cycle_days
                            )

                                {{
                                    $product
                                        ->care_cycle_days
                                }}
                                ngày

                            @else

                                Theo hướng dẫn

                            @endif

                        </div>

                    </div>

                </div>


                @if ($product->description)

                    <div class="product-description">

                        {{
                            $product->description
                        }}

                    </div>

                @endif


                @if ($product->stock_quantity > 0)

                    <div class="product-stock-status">

                        Còn
                        {{ $product->stock_quantity }}
                        sản phẩm

                    </div>

                @else

                    <div
                        class="
                            product-stock-status
                            out
                        "
                    >

                        Sản phẩm hiện đã hết hàng.

                    </div>

                @endif

<div class="product-buy-area">


    @guest

        <a
            href="{{
                route(
                    'login',
                    [
                        'redirect'
                            => request()->getRequestUri()
                    ]
                )
            }}"
            class="
                product-buy-button
                secondary
            "
        >
            🛒 Thêm vào giỏ hàng
        </a>


        <a
            href="{{
                route(
                    'login',
                    [
                        'redirect'
                            => request()->getRequestUri()
                    ]
                )
            }}"
            class="product-buy-button"
        >
            Mua ngay
        </a>


    @else


        @if (
            auth()->user()->role?->name
            === 'customer'
        )

            @if ($product->stock_quantity > 0)

                <form
                    method="POST"
                    action="{{
                        route(
                            'cart.add',
                            $product
                        )
                    }}"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="quantity"
                        value="1"
                    >

                    <button
                        type="submit"
                        class="
                            product-buy-button
                            secondary
                        "
                    >
                        🛒 Thêm vào giỏ hàng
                    </button>

                </form>


                <form
    method="POST"
    action="{{
        route(
            'checkout.buy-now',
            $product
        )
    }}"
>

    @csrf

    <input
        type="hidden"
        name="quantity"
        value="1"
    >

    <button
        type="submit"
        class="product-buy-button"
    >
        Mua ngay
    </button>

</form>

            @endif


        @else

            <div class="product-stock-status">

                Chức năng mua hàng dành cho
                tài khoản Customer.

            </div>

        @endif


    @endguest



</div>

            </div>

        </div>


        {{-- EXTRA --}}
        <div class="product-extra">


            @if ($product->concept_story)

                <div class="product-extra-card">

                    <h3>
                        Câu chuyện MiniGrove
                    </h3>

                    <p>
                        {{ $product->concept_story }}
                    </p>

                </div>

            @endif


            @if ($product->care_instructions)

                <div class="product-extra-card">

                    <h3>
                        Hướng dẫn chăm sóc
                    </h3>

                    <p>
                        {{
                            $product
                                ->care_instructions
                        }}
                    </p>

                </div>

            @endif

        </div>


        {{-- RELATED --}}
        @if ($relatedProducts->isNotEmpty())

            <div class="related-products">

                <div class="section-heading">

                    <div>

                        <div class="section-eyebrow">
                            CÓ THỂ BẠN SẼ THÍCH
                        </div>

                        <h2>
                            Sản phẩm liên quan
                        </h2>

                    </div>

                </div>


                <div class="product-grid">

                    @foreach (
                        $relatedProducts
                        as $relatedProduct
                    )

                        @include(
                            'partials.product-card',
                            [
                                'product'
                                    => $relatedProduct
                            ]
                        )

                    @endforeach

                </div>

            </div>

        @endif

    </div>

</section>

@endsection