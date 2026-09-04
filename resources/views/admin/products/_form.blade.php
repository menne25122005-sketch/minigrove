@php
    $editingProduct = $product ?? null;
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


    {{-- Đường kính --}}
    <div class="form-group">
        <label for="container_diameter_cm">
            Đường kính bình (cm)
        </label>

        <input
            type="number"
            id="container_diameter_cm"
            name="container_diameter_cm"
            min="0"
            step="0.1"
            value="{{ old(
                'container_diameter_cm',
                $editingProduct?->container_diameter_cm
            ) }}"
        >

        @error('container_diameter_cm')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>


    {{-- Chiều cao --}}
    <div class="form-group">
        <label for="container_height_cm">
            Chiều cao bình (cm)
        </label>

        <input
            type="number"
            id="container_height_cm"
            name="container_height_cm"
            min="0"
            step="0.1"
            value="{{ old(
                'container_height_cm',
                $editingProduct?->container_height_cm
            ) }}"
        >

        @error('container_height_cm')
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
            Hình ảnh sản phẩm
        </label>

        <input
            type="file"
            id="images"
            name="images[]"
            multiple
            accept=".jpg,.jpeg,.png,.webp"
        >

        <small class="help-text">
            Tối đa 8 ảnh/lần, mỗi ảnh tối đa 4MB.
        </small>

        @error('images')
            <div class="error">{{ $message }}</div>
        @enderror

        @error('images.*')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

</div>


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


    <div class="checkbox-row">
        <input
            type="checkbox"
            id="is_warranty_eligible"
            name="is_warranty_eligible"
            value="1"
            @checked(
                old(
                    'is_warranty_eligible',
                    $editingProduct?->is_warranty_eligible ?? false
                )
            )
        >

        <label for="is_warranty_eligible">
            Có bảo hành
        </label>
    </div>

</div>


<div
    id="warranty-box"
    class="warranty-box"
>

    <h3>
        Thông tin bảo hành
    </h3>

    <div class="product-form-grid">

        <div class="form-group">

            <label for="warranty_days">
                Thời hạn bảo hành (ngày)
            </label>

            <input
                type="number"
                id="warranty_days"
                name="warranty_days"
                min="1"
                value="{{ old(
                    'warranty_days',
                    $editingProduct?->warranty_days
                ) }}"
                placeholder="Ví dụ: 30"
            >

            @error('warranty_days')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>


    <div class="form-group">

        <label for="warranty_policy">
            Chính sách bảo hành
        </label>

        <textarea
            id="warranty_policy"
            name="warranty_policy"
            placeholder="Nội dung, điều kiện và phạm vi bảo hành..."
        >{{ old('warranty_policy', $editingProduct?->warranty_policy) }}</textarea>

        @error('warranty_policy')
            <div class="error">
                {{ $message }}
            </div>
        @enderror

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

    .warranty-box {
        padding: 20px;
        margin: 20px 0;
        background: #faf8f1;
        border: 1px solid #e8dfc7;
        border-radius: 10px;
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

    @media (max-width: 750px) {
        .product-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const checkbox =
            document.getElementById(
                'is_warranty_eligible'
            );

        const warrantyBox =
            document.getElementById(
                'warranty-box'
            );

        function updateWarrantyBox() {

            warrantyBox.style.display =
                checkbox.checked
                    ? 'block'
                    : 'none';
        }

        checkbox.addEventListener(
            'change',
            updateWarrantyBox
        );

        updateWarrantyBox();
    });
</script>
@endpush