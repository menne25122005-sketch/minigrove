@php
    $editingComponent = $component ?? null;
@endphp


<div class="component-form-grid">

    {{-- Tên thành phần --}}
    <div class="form-group">

        <label for="name">
            Tên thành phần *
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old(
                'name',
                $editingComponent?->name
            ) }}"
            placeholder="Ví dụ: Rêu Java"
        >

        @error('name')
            <div class="error">
                {{ $message }}
            </div>
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
            value="{{ old(
                'sku',
                $editingComponent?->sku
            ) }}"
            placeholder="Ví dụ: MG-MOSS-001"
        >

        @error('sku')
            <div class="error">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Loại Component --}}
    <div class="form-group">

        <label for="component_type">
            Loại thành phần *
        </label>

        <select
            id="component_type"
            name="component_type"
        >

            <option value="">
                -- Chọn loại --
            </option>

            <option
                value="container"
                @selected(
                    old(
                        'component_type',
                        $editingComponent?->component_type
                    ) === 'container'
                )
            >
                Bình
            </option>

            <option
                value="plant"
                @selected(
                    old(
                        'component_type',
                        $editingComponent?->component_type
                    ) === 'plant'
                )
            >
                Cây
            </option>

            <option
                value="moss"
                @selected(
                    old(
                        'component_type',
                        $editingComponent?->component_type
                    ) === 'moss'
                )
            >
                Rêu
            </option>

            <option
                value="substrate"
                @selected(
                    old(
                        'component_type',
                        $editingComponent?->component_type
                    ) === 'substrate'
                )
            >
                Nền / Giá thể
            </option>

            <option
                value="stone"
                @selected(
                    old(
                        'component_type',
                        $editingComponent?->component_type
                    ) === 'stone'
                )
            >
                Đá
            </option>

            <option
                value="accessory"
                @selected(
                    old(
                        'component_type',
                        $editingComponent?->component_type
                    ) === 'accessory'
                )
            >
                Phụ kiện
            </option>

        </select>

        @error('component_type')
            <div class="error">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Giá --}}
    <div class="form-group">

        <label for="price">
            Giá *
        </label>

        <input
            type="number"
            id="price"
            name="price"
            min="1"
            step="1"
            value="{{ old(
                'price',
                $editingComponent?->price
            ) }}"
            placeholder="Ví dụ: 35000"
        >

        @error('price')
            <div class="error">
                {{ $message }}
            </div>
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
            step="1"
            value="{{ old(
                'stock_quantity',
                $editingComponent?->stock_quantity ?? 0
            ) }}"
        >

        @error('stock_quantity')
            <div class="error">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Đơn vị --}}
    <div class="form-group">

        <label for="unit">
            Đơn vị tồn kho *
        </label>

        <input
            type="text"
            id="unit"
            name="unit"
            value="{{ old(
                'unit',
                $editingComponent?->unit ?? 'cái'
            ) }}"
            placeholder="cái / cây / gói..."
        >

        @error('unit')
            <div class="error">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Nhóm kích thước --}}
    <div class="form-group">

        <label for="size_group">
            Nhóm kích thước
        </label>

        <select
            id="size_group"
            name="size_group"
        >

            <option value="">
                -- Không xác định --
            </option>

            <option
                value="mini"
                @selected(
                    old(
                        'size_group',
                        $editingComponent?->size_group
                    ) === 'mini'
                )
            >
                Mini
            </option>

            <option
                value="medium"
                @selected(
                    old(
                        'size_group',
                        $editingComponent?->size_group
                    ) === 'medium'
                )
            >
                Medium
            </option>

            <option
                value="large"
                @selected(
                    old(
                        'size_group',
                        $editingComponent?->size_group
                    ) === 'large'
                )
            >
                Large
            </option>

        </select>

        @error('size_group')
            <div class="error">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Hệ sinh thái --}}
    <div class="form-group">

        <label for="compatible_ecosystem_type">
            Hệ sinh thái tương thích *
        </label>

        <select
            id="compatible_ecosystem_type"
            name="compatible_ecosystem_type"
        >

            <option
                value="all"
                @selected(
                    old(
                        'compatible_ecosystem_type',
                        $editingComponent
                            ? $editingComponent
                                ->compatible_ecosystem_type
                            : 'all'
                    ) === 'all'
                )
            >
                Tất cả
            </option>

            <option
                value="closed"
                @selected(
                    old(
                        'compatible_ecosystem_type',
                        $editingComponent
                            ? $editingComponent
                                ->compatible_ecosystem_type
                            : 'all'
                    ) === 'closed'
                )
            >
                Kín
            </option>

            <option
                value="open"
                @selected(
                    old(
                        'compatible_ecosystem_type',
                        $editingComponent
                            ? $editingComponent
                                ->compatible_ecosystem_type
                            : 'all'
                    ) === 'open'
                )
            >
                Mở
            </option>

            <option
                value="dry"
                @selected(
                    old(
                        'compatible_ecosystem_type',
                        $editingComponent
                            ? $editingComponent
                                ->compatible_ecosystem_type
                            : 'all'
                    ) === 'dry'
                )
            >
                Khô
            </option>

        </select>

        @error('compatible_ecosystem_type')
            <div class="error">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Hình ảnh --}}
    <div class="form-group">

        <label for="images">
            Hình ảnh thành phần
        </label>

        <input
            type="file"
            id="images"
            name="images[]"
            multiple
            accept=".jpg,.jpeg,.png,.webp"
        >

        <small class="help-text">
            Tối đa 5 ảnh/lần, mỗi ảnh tối đa 4MB.
        </small>

        @error('images')
            <div class="error">
                {{ $message }}
            </div>
        @enderror

        @error('images.*')
            <div class="error">
                {{ $message }}
            </div>
        @enderror

    </div>

</div>


{{-- Mô tả --}}
<div class="form-group">

    <label for="description">
        Mô tả
    </label>

    <textarea
        id="description"
        name="description"
        placeholder="Mô tả thành phần..."
    >{{ old(
        'description',
        $editingComponent?->description
    ) }}</textarea>

    @error('description')
        <div class="error">
            {{ $message }}
        </div>
    @enderror

</div>


{{-- Ghi chú tương thích --}}
<div class="form-group">

    <label for="compatibility_note">
        Ghi chú tương thích / chăm sóc
    </label>

    <textarea
        id="compatibility_note"
        name="compatibility_note"
        placeholder="Ví dụ: Phù hợp môi trường ẩm, tránh ánh sáng trực tiếp..."
    >{{ old(
        'compatibility_note',
        $editingComponent?->compatibility_note
    ) }}</textarea>

    @error('compatibility_note')
        <div class="error">
            {{ $message }}
        </div>
    @enderror

</div>


{{-- Trạng thái --}}
<div class="component-options">

    <div class="checkbox-row">

        <input
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            @checked(
                old(
                    'is_active',
                    $editingComponent
                        ? $editingComponent->is_active
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

    .component-form-grid {
        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 0 22px;
    }


    .component-options {
        display: flex;

        gap: 20px;

        padding: 18px;

        margin: 20px 0;

        background: #f4f7f5;

        border-radius: 9px;
    }


    .component-form-grid select {
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

        .component-form-grid {
            grid-template-columns: 1fr;
        }

    }

</style>

@endpush