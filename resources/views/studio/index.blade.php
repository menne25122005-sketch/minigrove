<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        MiniGrove Studio
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f7f5;

            color: #27332c;

        }



        .studio-header {

            background: white;

            border-bottom: 1px solid #e2e8e3;

            padding: 18px 30px;

        }



        .studio-header-inner {

            max-width: 1250px;

            margin: auto;

            display: flex;

            justify-content: space-between;

            align-items: center;

        }



        .brand {

            font-size: 24px;

            font-weight: bold;

            color: #28633d;

        }



        .brand span {

            color: #777;

            font-size: 14px;

            font-weight: normal;

            margin-left: 8px;

        }



        .studio-container {

            max-width: 1250px;

            margin: 30px auto;

            padding: 0 20px 50px;

        }



        .studio-title {

            text-align: center;

            margin-bottom: 30px;

        }



        .studio-title h1 {

            margin-bottom: 8px;

            color: #245c39;

        }



        .studio-title p {

            color: #6b756e;

        }



        .alert-error {

            background: #fdecec;

            color: #9e3434;

            border: 1px solid #efcaca;

            padding: 15px;

            border-radius: 9px;

            margin-bottom: 20px;

        }



        .studio-section {

            background: white;

            border-radius: 13px;

            padding: 22px;

            margin-bottom: 22px;

            box-shadow:
                0 3px 14px rgba(0,0,0,.05);

        }



        .studio-section h2 {

            margin-top: 0;

            margin-bottom: 6px;

            font-size: 20px;

        }



        .section-description {

            color: #788079;

            margin-top: 0;

            margin-bottom: 18px;

        }



        /* PRODUCT GRID */

        .product-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(190px,1fr)
                );

            gap: 15px;

        }



        .product-option {

            position: relative;

        }



        .product-option input {

            position: absolute;

            opacity: 0;

        }



        .product-card {

            display: block;

            height: 100%;

            border: 2px solid #e0e6e2;

            border-radius: 11px;

            overflow: hidden;

            background: white;

            cursor: pointer;

            transition: .2s;

        }



        .product-card:hover {

            border-color: #86ac91;

            transform: translateY(-2px);

        }



        .product-option input:checked
        + .product-card {

            border-color: #38714b;

            box-shadow:
                0 0 0 2px rgba(56,113,75,.12);

        }



        .product-image {

            width:100%;

            height:125px;

            object-fit:cover;

            background:#edf1ee;

        }



        .no-image {

            height:125px;

            display:flex;

            align-items:center;

            justify-content:center;

            background:#edf1ee;

            color:#818981;

        }



        .product-info {

            padding:13px;

        }



        .product-name {

            font-weight:bold;

            margin-bottom:6px;

        }



        .product-sku {

            color:#888;

            font-size:12px;

            margin-bottom:9px;

        }



        .product-bottom {

            display:flex;

            justify-content:space-between;

            gap:8px;

            align-items:center;

        }



        .price {

            color:#b34e38;

            font-weight:bold;

        }



        .stock {

            color:#69746c;

            font-size:12px;

        }


    </style>


</head>


<body>



<header class="studio-header">

    <div class="studio-header-inner">


        <div class="brand">

            MiniGrove

            <span>
                Nhỏ một khu rừng,
                lớn một thế giới.
            </span>

        </div>


        <div>
            MiniGrove Studio
        </div>


    </div>

</header>




<main class="studio-container">


<div class="studio-title">

    <h1>
        MiniGrove Studio
    </h1>


    <p>
        Tự tạo một hệ sinh thái nhỏ
        theo phong cách của riêng bạn.
    </p>


</div>




@if ($errors->any())

<div class="alert-error">

<strong>
    Cấu hình chưa hợp lệ:
</strong>


<div>

    {{ $errors->first() }}

</div>


</div>

@endif




<form
    action="{{ route('studio.preview') }}"
    method="POST"
>

@csrf



{{-- ================================================= --}}
{{-- BƯỚC 1: BÌNH --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>
    1. Chọn bình
    <span class="required">*</span>
</h2>


<p class="section-description">

    Bình quyết định môi trường chính
    của Terrarium.

</p>




<div class="product-grid">


@foreach ($containers as $product)



@php

$image =
    $product->images
        ->firstWhere(
            'is_primary',
            true
        )
    ??
    $product->images->first();

@endphp




<div class="product-option">



<input

type="radio"

name="container_id"

id="container_{{ $product->id }}"

value="{{ $product->id }}"

@checked(
    old('container_id')
    ==
    $product->id
)

>




<label

class="product-card"

for="container_{{ $product->id }}"

>



@if($image)


<img

class="product-image"

src="{{ asset(
    'storage/'.$image->image_path
) }}"

alt="{{ $product->name }}"

>


@else


<div class="no-image">

Không ảnh

</div>


@endif




<div class="product-info">


<div class="product-name">

{{ $product->name }}

</div>



<div class="product-sku">

{{ $product->sku }}

</div>




<div class="product-bottom">


<span class="price">

{{ number_format(
    (float)(
        $product->sale_price
        ??
        $product->list_price
    ),
    0,
    ',',
    '.'
) }}đ


</span>



<span class="stock">

Còn

{{ $product->stock_quantity }}

</span>



</div>



</div>




</label>


</div>



@endforeach


</div>



</section>
{{-- ================================================= --}}
{{-- BƯỚC 2: CÂY --}}
{{-- ================================================= --}}


<section class="studio-section">


<h2>
    2. Chọn cây
</h2>


<p class="section-description">

    Có thể bỏ qua nếu bạn chọn rêu.

</p>




<div class="optional-none">

<label>


<input

type="radio"

name="plant_id"

value=""

@checked(!old('plant_id'))

>


Không chọn cây


</label>


</div>





<div class="product-grid">


@foreach ($plants as $product)



@php

$image =
    $product->images
        ->firstWhere(
            'is_primary',
            true
        )
    ??
    $product->images->first();

@endphp




<div class="product-option">



<input

type="radio"

name="plant_id"

id="plant_{{ $product->id }}"

value="{{ $product->id }}"

@checked(
    old('plant_id')
    ==
    $product->id
)

>



<label

class="product-card"

for="plant_{{ $product->id }}"

>



@if($image)


<img

class="product-image"

src="{{ asset(
    'storage/'.$image->image_path
) }}"

alt="{{ $product->name }}"

>


@else


<div class="no-image">

Không ảnh

</div>


@endif




<div class="product-info">


<div class="product-name">

{{ $product->name }}

</div>



<div class="product-sku">

{{ $product->sku }}

</div>




<div class="product-bottom">


<span class="price">


{{ number_format(

    (float)(
        $product->sale_price
        ??
        $product->list_price
    ),

    0,

    ',',

    '.'

) }}đ



</span>



<span class="stock">

Còn

{{ $product->stock_quantity }}

</span>



</div>



</div>



</label>



</div>



@endforeach


</div>



</section>







{{-- ================================================= --}}
{{-- BƯỚC 3: RÊU --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>

3. Chọn rêu

</h2>



<div class="optional-none">


<label>


<input

type="radio"

name="moss_id"

value=""

@checked(!old('moss_id'))

>


Không chọn rêu


</label>


</div>





<div class="product-grid">



@foreach ($mosses as $product)


@include(

'studio.partials.product-radio',

[

'product' => $product,

'field' => 'moss_id',

'prefix' => 'moss'

]

)



@endforeach



</div>



</section>









{{-- ================================================= --}}
{{-- BƯỚC 4: NỀN --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>

4. Chọn nền / giá thể

<span class="required">*</span>

</h2>




<div class="product-grid">



@foreach ($substrates as $product)



@include(

'studio.partials.product-radio',

[

'product' => $product,

'field' => 'substrate_id',

'prefix' => 'substrate'

]

)



@endforeach



</div>



</section>









{{-- ================================================= --}}
{{-- BƯỚC 5: ĐÁ --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>

5. Chọn đá

</h2>




<div class="optional-none">


<label>


<input

type="radio"

name="stone_id"

value=""

@checked(!old('stone_id'))

>


Không chọn đá


</label>


</div>





<div class="product-grid">



@foreach ($stones as $product)



@include(

'studio.partials.product-radio',

[

'product' => $product,

'field' => 'stone_id',

'prefix' => 'stone'

]

)



@endforeach



</div>



</section>









{{-- ================================================= --}}
{{-- BƯỚC 6: PHỤ KIỆN --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>

6. Chọn phụ kiện

</h2>




<div class="optional-none">


<label>


<input

type="radio"

name="accessory_id"

value=""

@checked(!old('accessory_id'))

>


Không chọn phụ kiện


</label>


</div>





<div class="product-grid">



@foreach ($accessories as $product)



@include(

'studio.partials.product-radio',

[

'product' => $product,

'field' => 'accessory_id',

'prefix' => 'accessory'

]

)



@endforeach



</div>



</section>








{{-- ================================================= --}}
{{-- PHONG CÁCH --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>

7. Chọn phong cách

<span class="required">*</span>

</h2>




@php

$styles = [

'natural' => 'Tự nhiên',

'forest' => 'Rừng xanh',

'zen' => 'Zen',

'fairy' => 'Cổ tích',

'tropical' => 'Nhiệt đới',

'minimal' => 'Tối giản',

];


@endphp





<div class="style-grid">



@foreach ($styles as $value => $label)



<div class="style-option">


<input

type="radio"

name="style"

id="style_{{ $value }}"

value="{{ $value }}"

@checked(
    old('style') === $value
)

>



<label

class="style-card"

for="style_{{ $value }}"

>


{{ $label }}


</label>


</div>



@endforeach



</div>



</section>







<div class="studio-submit">


<button

type="submit"

class="preview-button"

>


Kiểm tra & Xem Preview


</button>



</div>





</form>



</main>



</body>


</html>