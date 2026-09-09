@php

    $editingRule = $compatibilityRule ?? null;

    $typeLabels = [
        'container' => 'Bình',
        'plant' => 'Cây',
        'moss' => 'Rêu',
        'substrate' => 'Nền / Giá thể',
        'stone' => 'Đá',
        'accessory' => 'Phụ kiện',
    ];

@endphp


<div class="compatibility-form-grid">

    {{-- Sản phẩm nguồn --}}
    <div class="form-group">

        <label for="source_product_id">
            Sản phẩm nguồn *
        </label>

        <select
            id="source_product_id"
            name="source_product_id"
        >

            <option value="">
                -- Chọn sản phẩm nguồn --
            </option>

            @foreach ($products as $product)

                <option
                    value="{{ $product->id }}"
                    @selected(
                        old(
                            'source_product_id',
                            $editingRule?->source_product_id
                        ) == $product->id
                    )
                >

                    [
                    {{ $typeLabels[$product->ecosystem_type] ?? $product->ecosystem_type }}
                    ]

                    {{ $product->name }}

                    @if ($product->sku)
                        - {{ $product->sku }}
                    @endif

                </option>

            @endforeach

        </select>

        @error('source_product_id')

            <div class="error">
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- Sản phẩm đích --}}
    <div class="form-group">

        <label for="target_product_id">
            Sản phẩm đích *
        </label>

        <select
            id="target_product_id"
            name="target_product_id"
        >

            <option value="">
                -- Chọn sản phẩm đích --
            </option>

            @foreach ($products as $product)

                <option
                    value="{{ $product->id }}"
                    @selected(
                        old(
                            'target_product_id',
                            $editingRule?->target_product_id
                        ) == $product->id
                    )
                >

                    [
                    {{ $typeLabels[$product->ecosystem_type] ?? $product->ecosystem_type }}
                    ]

                    {{ $product->name }}

                    @if ($product->sku)
                        - {{ $product->sku }}
                    @endif

                </option>

            @endforeach

        </select>

        @error('target_product_id')

            <div class="error">
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- Kết quả tương thích --}}
    <div class="form-group">

        <label for="is_compatible">
            Kết quả tương thích *
        </label>

        <select
            id="is_compatible"
            name="is_compatible"
        >

            <option value="">
                -- Chọn kết quả --
            </option>

            <option
                value="1"
                @selected(
                    (string) old(
                        'is_compatible',
                        $editingRule
                            ? (int) $editingRule->is_compatible
                            : ''
                    ) === '1'
                )
            >
                Tương thích
            </option>

            <option
                value="0"
                @selected(
                    (string) old(
                        'is_compatible',
                        $editingRule
                            ? (int) $editingRule->is_compatible
                            : ''
                    ) === '0'
                )
            >
                Không tương thích
            </option>

        </select>

        @error('is_compatible')

            <div class="error">
                {{ $message }}
            </div>

        @enderror

    </div>

</div>


{{-- Lý do --}}
<div class="form-group">

    <label for="reason">
        Lý do / Ghi chú
    </label>

    <textarea
        id="reason"
        name="reason"
        placeholder="Giải thích lý do tương thích hoặc không tương thích..."
    >{{ old('reason', $editingRule?->reason) }}</textarea>

    <small class="help-text">
        Nếu chọn “Không tương thích” thì nên nhập lý do để giải thích.
    </small>

    @error('reason')

        <div class="error">
            {{ $message }}
        </div>

    @enderror

</div>


{{-- Active --}}
<div class="compatibility-options">

    <div class="checkbox-row">

        {{-- Giá trị mặc định khi checkbox không được tick --}}
        <input
            type="hidden"
            name="is_active"
            value="0"
        >

        <input
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            @checked(
                old(
                    'is_active',
                    $editingRule
                        ? $editingRule->is_active
                        : true
                )
            )
        >

        <label for="is_active">
            Active
        </label>

    </div>

</div>


@push('styles')

<style>

    .compatibility-form-grid {

        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 0 22px;

    }


    .compatibility-form-grid select {

        width: 100%;

        border: 1px solid #ccd5cf;

        border-radius: 7px;

        padding: 11px;

        font-size: 14px;

        background: white;

    }


    .compatibility-options {

        padding: 18px;

        margin: 20px 0;

        background: #f4f7f5;

        border-radius: 9px;

    }


    .help-text {

        display: block;

        margin-top: 6px;

        color: #6b746e;

    }


    @media (max-width: 750px) {

        .compatibility-form-grid {

            grid-template-columns: 1fr;

        }

    }

</style>

@endpush