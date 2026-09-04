@extends('layouts.customer')


@section(
    'title',
    'Sản phẩm - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/products.css') }}"
>

@endpush


@section('content')

<section class="shop-page">

    <div class="customer-container">


        <div class="shop-heading">

            <h1>
                Sản phẩm MiniGrove
            </h1>

            <p>
                Khám phá các hệ sinh thái mini
                dành cho không gian của bạn.
            </p>

        </div>


        {{-- FILTER --}}
        <form
            method="GET"
            action="{{ route('products.index') }}"
            class="shop-filter"
        >

            <input
                type="text"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="Tên sản phẩm, SKU..."
            >


            <select name="category">

                <option value="">
                    Tất cả danh mục
                </option>

                @foreach ($categories as $item)

                    <option
                        value="{{ $item->slug }}"
                        @selected(
                            $category === $item->slug
                        )
                    >
                        {{ $item->name }}
                    </option>

                @endforeach

            </select>


            <select name="ecosystem">

                <option value="">
                    Tất cả hệ sinh thái
                </option>

                <option
                    value="closed"
                    @selected(
                        $ecosystem === 'closed'
                    )
                >
                    Hệ kín
                </option>

                <option
                    value="open"
                    @selected(
                        $ecosystem === 'open'
                    )
                >
                    Hệ mở
                </option>

                <option
                    value="dry"
                    @selected(
                        $ecosystem === 'dry'
                    )
                >
                    Hệ khô
                </option>

            </select>


            <select name="care">

                <option value="">
                    Mức chăm sóc
                </option>

                <option
                    value="easy"
                    @selected($care === 'easy')
                >
                    Dễ chăm
                </option>

                <option
                    value="medium"
                    @selected($care === 'medium')
                >
                    Trung bình
                </option>

                <option
                    value="high"
                    @selected($care === 'high')
                >
                    Cần chăm kỹ
                </option>

            </select>


            <select name="sort">

                <option
                    value="newest"
                    @selected($sort === 'newest')
                >
                    Mới nhất
                </option>

                <option
                    value="featured"
                    @selected($sort === 'featured')
                >
                    Nổi bật
                </option>

                <option
                    value="price_asc"
                    @selected($sort === 'price_asc')
                >
                    Giá thấp → cao
                </option>

                <option
                    value="price_desc"
                    @selected($sort === 'price_desc')
                >
                    Giá cao → thấp
                </option>

            </select>


            <button
                type="submit"
                class="shop-filter-button"
            >
                Tìm / Lọc
            </button>


            @if (
                $keyword ||
                $category ||
                $ecosystem ||
                $care ||
                $sort !== 'newest'
            )

                <a
                    href="{{ route('products.index') }}"
                    class="shop-filter-reset"
                >
                    Xóa lọc
                </a>

            @endif

        </form>


        <div class="shop-result-info">

            <span>
                Tìm thấy
                <strong>
                    {{ $products->total() }}
                </strong>
                sản phẩm
            </span>

        </div>


        <div class="product-grid">

            @forelse ($products as $product)

                @include(
                    'partials.product-card',
                    [
                        'product' => $product
                    ]
                )

            @empty

                <div class="home-empty">

                    Không tìm thấy sản phẩm phù hợp.

                </div>

            @endforelse

        </div>


        {{-- PAGINATION --}}
        @if ($products->hasPages())

            <div class="shop-pagination">

                <a
                    href="{{
                        $products->previousPageUrl()
                            ?? '#'
                    }}"
                    class="
                        page-item
                        {{
                            $products->onFirstPage()
                                ? 'disabled'
                                : ''
                        }}
                    "
                >
                    ←
                </a>


                @for (
                    $page = 1;
                    $page <= $products->lastPage();
                    $page++
                )

                    <a
                        href="{{
                            $products->url($page)
                        }}"
                        class="
                            page-item
                            {{
                                $page
                                === $products->currentPage()
                                    ? 'active'
                                    : ''
                            }}
                        "
                    >
                        {{ $page }}
                    </a>

                @endfor


                <a
                    href="{{
                        $products->nextPageUrl()
                            ?? '#'
                    }}"
                    class="
                        page-item
                        {{
                            !$products->hasMorePages()
                                ? 'disabled'
                                : ''
                        }}
                    "
                >
                    →
                </a>

            </div>

        @endif

    </div>

</section>

@endsection