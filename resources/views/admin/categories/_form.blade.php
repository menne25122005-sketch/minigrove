<div class="form-group">

    <label for="name">
        Tên danh mục *
    </label>

    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $category->name ?? '') }}"
        placeholder="Ví dụ: Rừng nhiệt đới mini"
    >

    @error('name')
        <div class="error">
            {{ $message }}
        </div>
    @enderror

</div>


<div class="form-group">

    <label for="description">
        Mô tả
    </label>

    <textarea
        id="description"
        name="description"
        placeholder="Mô tả ngắn về danh mục..."
    >{{ old('description', $category->description ?? '') }}</textarea>

    @error('description')
        <div class="error">
            {{ $message }}
        </div>
    @enderror

</div>


<div class="form-group">

    <label for="image">
        Ảnh danh mục
    </label>

    <input
        type="file"
        id="image"
        name="image"
        accept=".jpg,.jpeg,.png,.webp"
    >

    @error('image')
        <div class="error">
            {{ $message }}
        </div>
    @enderror


    @if (!empty($category?->image))

        <div class="current-image">

            <p>
                Ảnh hiện tại:
            </p>

            <img
                src="{{ asset('storage/' . $category->image) }}"
                alt="{{ $category->name }}"
            >

        </div>

    @endif

</div>


<div class="form-group">

    <label for="display_order">
        Thứ tự hiển thị
    </label>

    <input
        type="number"
        id="display_order"
        name="display_order"
        min="0"
        value="{{ old(
            'display_order',
            $category->display_order ?? 0
        ) }}"
    >

    @error('display_order')
        <div class="error">
            {{ $message }}
        </div>
    @enderror

</div>


<div class="form-group">

    <div class="checkbox-row">

        <input
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"

            @checked(
                old(
                    'is_active',
                    isset($category)
                        ? $category->is_active
                        : true
                )
            )
        >

        <label for="is_active">
    Active
</label>

    </div>

</div>