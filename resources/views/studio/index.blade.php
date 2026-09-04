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
            box-shadow: 0 3px 14px
                rgba(0, 0, 0, 0.05);
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

        .component-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(190px, 1fr)
                );
            gap: 15px;
        }

        .component-option {
            position: relative;
        }

        .component-option input {
            position: absolute;
            opacity: 0;
        }

        .component-card {
            display: block;
            height: 100%;
            border: 2px solid #e0e6e2;
            border-radius: 11px;
            overflow: hidden;
            background: white;
            cursor: pointer;
            transition: 0.2s;
        }

        .component-card:hover {
            border-color: #86ac91;
            transform: translateY(-2px);
        }

        .component-option input:checked
            + .component-card {
            border-color: #38714b;
            box-shadow:
                0 0 0 2px
                rgba(56, 113, 75, 0.12);
        }

        .component-image {
            width: 100%;
            height: 125px;
            object-fit: cover;
            background: #edf1ee;
        }

        .no-image {
            height: 125px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #edf1ee;
            color: #818981;
        }

        .component-info {
            padding: 13px;
        }

        .component-name {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .component-sku {
            color: #888;
            font-size: 12px;
            margin-bottom: 9px;
        }

        .component-bottom {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: center;
        }

        .price {
            color: #b34e38;
            font-weight: bold;
        }

        .stock {
            color: #69746c;
            font-size: 12px;
        }

        .optional-none {
            border: 2px dashed #d4ddd6;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 15px;
        }

        .style-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(150px, 1fr)
                );
            gap: 12px;
        }

        .style-option {
            position: relative;
        }

        .style-option input {
            position: absolute;
            opacity: 0;
        }

        .style-card {
            display: block;
            padding: 17px;
            border: 2px solid #e0e6e2;
            border-radius: 9px;
            text-align: center;
            cursor: pointer;
            font-weight: bold;
        }

        .style-option input:checked
            + .style-card {
            border-color: #38714b;
            background: #edf6f0;
            color: #28633d;
        }

        .studio-submit {
            text-align: center;
            margin-top: 30px;
        }

        .preview-button {
            border: 0;
            background: #28633d;
            color: white;
            border-radius: 9px;
            padding: 14px 35px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .preview-button:hover {
            background: #1f5031;
        }

        .required {
            color: #c03c3c;
        }

        @media (max-width: 600px) {

            .studio-header-inner {
                flex-direction: column;
                gap: 5px;
            }

            .component-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

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


        {{-- ========================================================= --}}
        {{-- BƯỚC 1: BÌNH --}}
        {{-- ========================================================= --}}

        <section class="studio-section">

            <h2>
                1. Chọn bình
                <span class="required">*</span>
            </h2>

            <p class="section-description">
                Bình quyết định môi trường chính
                của Terrarium.
            </p>


            <div class="component-grid">

                @foreach ($containers as $component)

                    @php
                        $image =
                            $component->images
                                ->firstWhere(
                                    'is_primary',
                                    true
                                )
                            ?? $component->images->first();
                    @endphp


                    <div class="component-option">

                        <input
                            type="radio"
                            name="container_id"
                            id="container_{{ $component->id }}"
                            value="{{ $component->id }}"
                            @checked(
                                old('container_id')
                                == $component->id
                            )
                        >

                        <label
                            class="component-card"
                            for="container_{{ $component->id }}"
                        >

                            @if ($image)

                                <img
                                    class="component-image"
                                    src="{{ asset(
                                        'storage/' .
                                        $image->image_path
                                    ) }}"
                                    alt="{{ $component->name }}"
                                >

                            @else

                                <div class="no-image">
                                    Không ảnh
                                </div>

                            @endif


                            <div class="component-info">

                                <div class="component-name">
                                    {{ $component->name }}
                                </div>

                                <div class="component-sku">
                                    {{ $component->sku }}
                                </div>

                                <div class="component-bottom">

                                    <span class="price">
                                        {{ number_format(
                                            (float) $component->price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ
                                    </span>

                                    <span class="stock">
                                        Còn
                                        {{ $component->stock_quantity }}
                                    </span>

                                </div>

                            </div>

                        </label>

                    </div>

                @endforeach

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- BƯỚC 2: CÂY --}}
        {{-- ========================================================= --}}

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


            <div class="component-grid">

                @foreach ($plants as $component)

                    @php
                        $image =
                            $component->images
                                ->firstWhere(
                                    'is_primary',
                                    true
                                )
                            ?? $component->images->first();
                    @endphp

                    <div class="component-option">

                        <input
                            type="radio"
                            name="plant_id"
                            id="plant_{{ $component->id }}"
                            value="{{ $component->id }}"
                            @checked(
                                old('plant_id')
                                == $component->id
                            )
                        >

                        <label
                            class="component-card"
                            for="plant_{{ $component->id }}"
                        >

                            @if ($image)

                                <img
                                    class="component-image"
                                    src="{{ asset(
                                        'storage/' .
                                        $image->image_path
                                    ) }}"
                                    alt="{{ $component->name }}"
                                >

                            @else

                                <div class="no-image">
                                    Không ảnh
                                </div>

                            @endif


                            <div class="component-info">

                                <div class="component-name">
                                    {{ $component->name }}
                                </div>

                                <div class="component-sku">
                                    {{ $component->sku }}
                                </div>

                                <div class="component-bottom">

                                    <span class="price">
                                        {{ number_format(
                                            (float) $component->price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ
                                    </span>

                                    <span class="stock">
                                        Còn
                                        {{ $component->stock_quantity }}
                                    </span>

                                </div>

                            </div>

                        </label>

                    </div>

                @endforeach

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- BƯỚC 3: RÊU --}}
        {{-- ========================================================= --}}

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


            <div class="component-grid">

                @foreach ($mosses as $component)

                    @include(
                        'studio.partials.component-radio',
                        [
                            'component' => $component,
                            'field' => 'moss_id',
                            'prefix' => 'moss'
                        ]
                    )

                @endforeach

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- BƯỚC 4: NỀN --}}
        {{-- ========================================================= --}}

        <section class="studio-section">

            <h2>
                4. Chọn nền / giá thể
                <span class="required">*</span>
            </h2>

            <div class="component-grid">

                @foreach ($substrates as $component)

                    @include(
                        'studio.partials.component-radio',
                        [
                            'component' => $component,
                            'field' => 'substrate_id',
                            'prefix' => 'substrate'
                        ]
                    )

                @endforeach

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- BƯỚC 5: ĐÁ --}}
        {{-- ========================================================= --}}

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


            <div class="component-grid">

                @foreach ($stones as $component)

                    @include(
                        'studio.partials.component-radio',
                        [
                            'component' => $component,
                            'field' => 'stone_id',
                            'prefix' => 'stone'
                        ]
                    )

                @endforeach

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- BƯỚC 6: PHỤ KIỆN --}}
        {{-- ========================================================= --}}

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


            <div class="component-grid">

                @foreach ($accessories as $component)

                    @include(
                        'studio.partials.component-radio',
                        [
                            'component' => $component,
                            'field' => 'accessory_id',
                            'prefix' => 'accessory'
                        ]
                    )

                @endforeach

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- PHONG CÁCH --}}
        {{-- ========================================================= --}}

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