@php

    $primaryImage =
        $product->images
            ->firstWhere(
                'is_primary',
                true
            )
        ?? $product->images->first();

@endphp


<article class="product-card">


    <div class="product-image-box">

        @if ($primaryImage)

            <img
                class="product-image"
                src="{{
                    asset(
                        'storage/'
                        . $primaryImage->image_path
                    )
                }}"
                alt="{{ $product->name }}"
            >

        @else

            <div class="product-no-image">
                🌱
            </div>

        @endif


        @if ($product->is_featured)

            <div class="product-badge">
                NỔI BẬT
            </div>

        @endif

    </div>


    <div class="product-body">


        <div class="product-category">

            {{
                $product->category?->name
                ?? 'MiniGrove'
            }}

        </div>


        <div class="product-name">
            {{ $product->name }}
        </div>


        <div class="product-meta">

            <span>

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

            </span>

            <span>•</span>

            <span>

                @switch(
                    $product->care_level
                )

                    @case('easy')
                        Dễ chăm
                        @break

                    @case('medium')
                        Chăm sóc vừa
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

            </span>

        </div>


        <div class="product-price-row">

            @if (
                $product->sale_price !== null
            )

                <div class="product-sale-price">

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


                <div class="product-old-price">

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

                <div class="product-normal-price">

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


        <div class="product-card-footer">

            <span class="product-stock">

                Còn
                {{ $product->stock_quantity }}
                sản phẩm

            </span>


            <a
    href="{{
        route(
            'products.show',
            $product
        )
    }}"
    class="product-view"
>
    Xem chi tiết →
</a>

        </div>

    </div>

</article>