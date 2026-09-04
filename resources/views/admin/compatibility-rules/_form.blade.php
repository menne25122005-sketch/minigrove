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

    {{-- Thành phần nguồn --}}
    <div class="form-group">

        <label for="source_component_id">
            Thành phần nguồn *
        </label>

        <select
            id="source_component_id"
            name="source_component_id"
        >
            <option value="">
                -- Chọn thành phần nguồn --
            </option>

            @foreach ($components as $component)

                <option
                    value="{{ $component->id }}"
                    @selected(
                        old(
                            'source_component_id',
                            $editingRule?->source_component_id
                        ) == $component->id
                    )
                >
                    [{{ $typeLabels[$component->component_type] ?? $component->component_type }}]
                    {{ $component->name }}
                    - {{ $component->sku }}
                </option>

            @endforeach

        </select>

        @error('source_component_id')
            <div class="error">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Thành phần đích --}}
    <div class="form-group">

        <label for="target_component_id">
            Thành phần đích *
        </label>

        <select
            id="target_component_id"
            name="target_component_id"
        >
            <option value="">
                -- Chọn thành phần đích --
            </option>

            @foreach ($components as $component)

                <option
                    value="{{ $component->id }}"
                    @selected(
                        old(
                            'target_component_id',
                            $editingRule?->target_component_id
                        ) == $component->id
                    )
                >
                    [{{ $typeLabels[$component->component_type] ?? $component->component_type }}]
                    {{ $component->name }}
                    - {{ $component->sku }}
                </option>

            @endforeach

        </select>

        @error('target_component_id')
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
                        isset($editingRule)
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
                        isset($editingRule)
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
        Nếu chọn “Không tương thích” thì bắt buộc nhập lý do.
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