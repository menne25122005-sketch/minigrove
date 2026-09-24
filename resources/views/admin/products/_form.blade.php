@php
    $editingProduct = $product ?? null;
    $currentImageCount =
        $editingProduct?->images?->count() ?? 0;
    $remainingImageSlots =
        max(0, 8 - $currentImageCount);
    $hasPrimaryImage =
        $editingProduct?->images?->contains(
            'is_primary',
            true
        ) ?? false;
@endphp

<div class="product-form-grid">

    {{-- Tên sản phẩm --}}
    <div class="form-group">
        <label for="name">
            Tên sản phẩm *
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $editingProduct?->name) }}"
            placeholder="Ví dụ: Forest Calm"
        >

        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>


    {{-- SKU --}}
    <div class="form-group">
        <label for="sku">
            SKU *
        </label>

        <input
            type="text"
            id="sku"
            name="sku"
            value="{{ old('sku', $editingProduct?->sku) }}"
            placeholder="Ví dụ: MG-FOREST-001"
        >

        @error('sku')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>


    {{-- Category --}}
    <div class="form-group">
        <label for="category_id">
            Danh mục *
        </label>

        <select
            id="category_id"
            name="category_id"
        >
            <option value="">
                -- Chọn danh mục --
            </option>

            @foreach ($categories as $category)
                <option
                    value="{{ $category->id }}"
                    @selected(
                        old(
                            'category_id',
                            $editingProduct?->category_id
                        ) == $category->id
                    )
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        @error('category_id')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>


    {{-- Loại hệ sinh thái --}}
    <div class="form-group">
        <label for="ecosystem_type">
            Loại hệ sinh thái *
        </label>

        <select
            id="ecosystem_type"
            name="ecosystem_type"
        >
            <option value="">
                -- Chọn loại --
            </option>

            <option
                value="closed"
                @selected(
                    old(
                        'ecosystem_type',
                        $editingProduct?->ecosystem_type
                    ) === 'closed'
                )
            >
                Kín
            </option>

            <option
                value="open"
                @selected(
                    old(
                        'ecosystem_type',
                        $editingProduct?->ecosystem_type
                    ) === 'open'
                )
            >
                Mở
            </option>

            <option
                value="dry"
                @selected(
                    old(
                        'ecosystem_type',
                        $editingProduct?->ecosystem_type
                    ) === 'dry'
                )
            >
                Khô
            </option>
        </select>

        @error('ecosystem_type')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>


    {{-- Giá niêm yết --}}
    <div class="form-group">
    <label for="list_price">
        Giá niêm yết *
    </label>

    <input
        type="number"
        id="list_price"
        name="list_price"
        min="1000"
        step="1000"
        value="{{ old(
            'list_price',
            $editingProduct?->list_price
        ) }}"
        placeholder="Ví dụ: 650000"
    >

    @error('list_price')
        <div class="error">{{ $message }}</div>
    @enderror
</div>


    {{-- Giá khuyến mãi --}}
    <div class="form-group">
        <label for="sale_price">
            Giá khuyến mãi
        </label>

        <input
            type="number"
            id="sale_price"
            name="sale_price"
            min="0"
            step="1000"
            value="{{ old(
                'sale_price',
                $editingProduct?->sale_price
            ) }}"
            placeholder="Để trống nếu không giảm giá"
        >

        @error('sale_price')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>


    {{-- Size --}}
    <div class="form-group">
        <label for="size">
            Nhóm kích thước
        </label>

        <select
            id="size"
            name="size"
        >
            <option value="">
                -- Không xác định --
            </option>

            <option
                value="mini"
                @selected(
                    old('size', $editingProduct?->size) === 'mini'
                )
            >
                Mini
            </option>

            <option
                value="medium"
                @selected(
                    old('size', $editingProduct?->size) === 'medium'
                )
            >
                Medium
            </option>

            <option
                value="large"
                @selected(
                    old('size', $editingProduct?->size) === 'large'
                )
            >
                Large
            </option>
        </select>

        @error('size')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>


    {{-- Mức chăm sóc --}}
    <div class="form-group">
        <label for="care_level">
            Mức độ chăm sóc *
        </label>

        <select
            id="care_level"
            name="care_level"
        >
            <option value="">
                -- Chọn mức độ --
            </option>

            <option
                value="easy"
                @selected(
                    old(
                        'care_level',
                        $editingProduct?->care_level
                    ) === 'easy'
                )
            >
                Dễ
            </option>

            <option
                value="medium"
                @selected(
                    old(
                        'care_level',
                        $editingProduct?->care_level
                    ) === 'medium'
                )
            >
                Trung bình
            </option>

            <option
                value="high"
                @selected(
                    old(
                        'care_level',
                        $editingProduct?->care_level
                    ) === 'high'
                )
            >
                Cao
            </option>
        </select>

        @error('care_level')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>


    {{-- Ánh sáng --}}
    <div class="form-group">
        <label for="light_requirement">
            Nhu cầu ánh sáng
        </label>

        <input
            type="text"
            id="light_requirement"
            name="light_requirement"
            value="{{ old(
                'light_requirement',
                $editingProduct?->light_requirement
            ) }}"
            placeholder="Ví dụ: Ánh sáng gián tiếp"
        >

        @error('light_requirement')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>


    {{-- Chu kỳ chăm sóc --}}
    <div class="form-group">
        <label for="care_cycle_days">
            Chu kỳ chăm sóc (ngày)
        </label>

        <input
            type="number"
            id="care_cycle_days"
            name="care_cycle_days"
            min="1"
            value="{{ old(
                'care_cycle_days',
                $editingProduct?->care_cycle_days
            ) }}"
            placeholder="Ví dụ: 7"
        >

        @error('care_cycle_days')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>


    {{-- Tồn kho --}}
    <div class="form-group">
        <label for="stock_quantity">
            Số lượng tồn kho *
        </label>

        <input
            type="number"
            id="stock_quantity"
            name="stock_quantity"
            min="0"
            value="{{ old(
                'stock_quantity',
                $editingProduct?->stock_quantity ?? 0
            ) }}"
        >

        @error('stock_quantity')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>


    {{-- Upload ảnh --}}
    <div class="form-group">
        <label for="images">
            {{
                $editingProduct
                    ? 'Thêm hình ảnh sản phẩm'
                    : 'Hình ảnh sản phẩm'
            }}
        </label>

        <input
            type="file"
            id="images"
            name="images[]"
            multiple
            accept=".jpg,.jpeg,.png,.webp"
            data-max-files="{{
                $editingProduct
                    ? $remainingImageSlots
                    : 8
            }}"
            data-auto-primary="{{
                !$editingProduct || !$hasPrimaryImage
                    ? '1'
                    : '0'
            }}"
            @disabled(
                $editingProduct &&
                $remainingImageSlots === 0
            )
        >

        <small class="help-text">
            @if ($editingProduct)
                Hiện có
                <strong>{{ $currentImageCount }}/8</strong>
                ảnh.

                @if ($remainingImageSlots > 0)
                    Bạn có thể thêm tối đa
                    {{ $remainingImageSlots }} ảnh nữa.
                @else
                    Đã đủ 8 ảnh. Hãy thay thế hoặc xóa
                    ảnh hiện có nếu muốn đổi ảnh.
                @endif
            @else
                Tối đa 8 ảnh, mỗi ảnh tối đa 4MB.
            @endif
        </small>

        <div
            id="new-image-preview"
            class="new-image-preview"
        ></div>

        @error('images')
            <div class="error">{{ $message }}</div>
        @enderror

        @error('images.*')
            <div class="error">{{ $message }}</div>
        @enderror

        @error('primary_image_index')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

</div>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const input = document.getElementById(
            'images'
        );

        const preview = document.getElementById(
            'new-image-preview'
        );

        if (!input || !preview) {
            return;
        }

        input.addEventListener(
            'change',
            function () {
                preview.innerHTML = '';

                const files = Array.from(
                    input.files || []
                );

                const maxFiles = Number(
                    input.dataset.maxFiles || 8
                );

                if (files.length > maxFiles) {
                    window.alert(
                        'Bạn chỉ có thể chọn tối đa '
                        + maxFiles
                        + ' ảnh ở lần này.'
                    );

                    input.value = '';
                    return;
                }

                files.forEach(
                    function (file, index) {
                        const card =
                            document.createElement(
                                'div'
                            );

                        card.className =
                            'new-image-card';

                        const image =
                            document.createElement(
                                'img'
                            );

                        image.src =
                            URL.createObjectURL(file);

                        image.alt =
                            'Ảnh mới '
                            + (index + 1);

                        image.onload = function () {
                            URL.revokeObjectURL(
                                image.src
                            );
                        };

                        const name =
                            document.createElement(
                                'div'
                            );

                        name.className =
                            'new-image-name';

                        name.textContent = file.name;

                        const primaryLabel =
                            document.createElement(
                                'label'
                            );

                        primaryLabel.className =
                            'new-image-primary';

                        const radio =
                            document.createElement(
                                'input'
                            );

                        radio.type = 'radio';
                        radio.name =
                            'primary_image_index';
                        radio.value = String(index);

                        if (
                            index === 0 &&
                            input.dataset
                                .autoPrimary === '1'
                        ) {
                            radio.checked = true;
                        }

                        const labelText =
                            document.createElement(
                                'span'
                            );

                        labelText.textContent =
                            'Đặt làm ảnh chính';

                        primaryLabel.append(
                            radio,
                            labelText
                        );

                        card.append(
                            image,
                            name,
                            primaryLabel
                        );

                        preview.appendChild(card);
                    }
                );
            }
        );
    }
);
</script>


{{-- Mô tả --}}
<div class="form-group">
    <label for="description">
        Mô tả sản phẩm
    </label>

    <textarea
        id="description"
        name="description"
        placeholder="Mô tả Terrarium..."
    >{{ old('description', $editingProduct?->description) }}</textarea>

    @error('description')
        <div class="error">{{ $message }}</div>
    @enderror
</div>


{{-- Concept Story --}}
<div class="form-group">
    <label for="concept_story">
        Câu chuyện Concept
    </label>

    <textarea
        id="concept_story"
        name="concept_story"
        placeholder="Câu chuyện hoặc cảm hứng của sản phẩm..."
    >{{ old('concept_story', $editingProduct?->concept_story) }}</textarea>

    @error('concept_story')
        <div class="error">{{ $message }}</div>
    @enderror
</div>


{{-- Hướng dẫn chăm sóc --}}
<div class="form-group">
    <label for="care_instructions">
        Hướng dẫn chăm sóc
    </label>

    <textarea
        id="care_instructions"
        name="care_instructions"
        placeholder="Hướng dẫn tưới nước, ánh sáng, vệ sinh..."
    >{{ old('care_instructions', $editingProduct?->care_instructions) }}</textarea>

    @error('care_instructions')
        <div class="error">{{ $message }}</div>
    @enderror
</div>


<div class="product-options">

    <div class="checkbox-row">
        <input
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            @checked(
                old(
                    'is_active',
                    $editingProduct
                        ? $editingProduct->is_active
                        : true
                )
            )
        >

        <label for="is_active">
    Active
</label>
    </div>


    <div class="checkbox-row">
        <input
            type="checkbox"
            id="is_featured"
            name="is_featured"
            value="1"
            @checked(
                old(
                    'is_featured',
                    $editingProduct?->is_featured ?? false
                )
            )
        >

        <label for="is_featured">
            Sản phẩm nổi bật
        </label>
    </div>


    <div class="checkbox-row">
        <input
            type="checkbox"
            id="is_grove_eligible"
            name="is_grove_eligible"
            value="1"
            @checked(
                old(
                    'is_grove_eligible',
                    $editingProduct
                        ? $editingProduct->is_grove_eligible
                        : true
                )
            )
        >

        <label for="is_grove_eligible">
            Cho phép tạo My Grove
        </label>
    </div>


</div>


@push('styles')
<style>
    .product-form-grid {
        display: grid;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
        gap: 0 22px;
    }

    .product-options {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 18px;
        margin: 20px 0;
        background: #f4f7f5;
        border-radius: 9px;
    }

    select {
        width: 100%;
        border: 1px solid #ccd5cf;
        border-radius: 7px;
        padding: 11px;
        font-size: 14px;
        background: white;
    }

    .help-text {
        display: block;
        color: #6b746e;
        margin-top: 6px;
    }

    .new-image-preview {
        display: grid;
        grid-template-columns:
            repeat(auto-fill, minmax(130px, 1fr));
        gap: 10px;
        margin-top: 12px;
    }

    .new-image-card {
        overflow: hidden;
        border: 1px solid #dbe3de;
        border-radius: 9px;
        background: #f8faf8;
        padding: 8px;
    }

    .new-image-card img {
        width: 100%;
        height: 100px;
        display: block;
        object-fit: cover;
        border-radius: 7px;
        background: #edf2ee;
    }

    .new-image-name {
        overflow: hidden;
        margin: 7px 0;
        color: #5e6861;
        font-size: 12px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .new-image-primary {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #174d39;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .new-image-primary input {
        width: auto;
    }

    @media (max-width: 750px) {
        .product-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
