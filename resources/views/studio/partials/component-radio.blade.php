@php
    $image =
        $component->images
            ->firstWhere(
                'is_primary',
                true
            )
        ?? $component->images->first();

    $inputId =
        $prefix . '_' . $component->id;
@endphp


<div class="component-option">

    <input
        type="radio"
        name="{{ $field }}"
        id="{{ $inputId }}"
        value="{{ $component->id }}"
        @checked(
            old($field) == $component->id
        )
    >

    <label
        class="component-card"
        for="{{ $inputId }}"
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