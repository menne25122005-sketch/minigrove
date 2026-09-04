<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ComponentController extends Controller
{
    /**
     * Danh sách Component.
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->keyword);
        $componentType = $request->component_type;
        $ecosystemType = $request->ecosystem_type;
        $status = $request->status;

        $components = Component::with([
            'images' => function ($query) {
                $query->orderByDesc('is_primary')
                    ->orderBy('sort_order');
            },
        ])
            ->when(
                $keyword,
                function ($query) use ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where(
                            'name',
                            'like',
                            '%' . $keyword . '%'
                        )
                            ->orWhere(
                                'sku',
                                'like',
                                '%' . $keyword . '%'
                            );
                    });
                }
            )
            ->when(
                $componentType,
                function ($query) use ($componentType) {
                    $query->where(
                        'component_type',
                        $componentType
                    );
                }
            )
            ->when(
                $ecosystemType,
                function ($query) use ($ecosystemType) {
                    $query->where(
                        'compatible_ecosystem_type',
                        $ecosystemType
                    );
                }
            )
            ->when(
                $status !== null && $status !== '',
                function ($query) use ($status) {
                    $query->where(
                        'is_active',
                        (int) $status
                    );
                }
            )

            /*
             * Hiển thị ID từ nhỏ đến lớn
             * để đồng nhất với các màn hình quản lý.
             */
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.components.index',
            compact(
                'components',
                'keyword',
                'componentType',
                'ecosystemType',
                'status'
            )
        );
    }

    /**
     * Form thêm Component.
     */
    public function create()
    {
        return view('admin.components.create');
    }

    /**
     * Thêm Component mới.
     */
    public function store(Request $request)
    {
        $validated = $this->validateComponent($request);

        DB::transaction(
            function () use ($request, $validated) {

                $component = Component::create([
                    'name' =>
                        $validated['name'],

                    'sku' =>
                        strtoupper($validated['sku']),

                    'component_type' =>
                        $validated['component_type'],

                    'price' =>
                        $validated['price'],

                    'description' =>
                        $validated['description'] ?? null,

                    'stock_quantity' =>
                        $validated['stock_quantity'],

                    'unit' =>
                        $validated['unit'],

                    'size_group' =>
                        $validated['size_group'] ?? null,

                    'compatible_ecosystem_type' =>
                        $validated[
                            'compatible_ecosystem_type'
                        ],

                    'compatibility_note' =>
                        $validated[
                            'compatibility_note'
                        ] ?? null,

                    'is_active' =>
                        $request->boolean('is_active'),
                ]);

                /*
                 * Upload nhiều ảnh.
                 * Ảnh đầu tiên là ảnh chính.
                 */
                if ($request->hasFile('images')) {

                    foreach (
                        $request->file('images')
                        as $index => $image
                    ) {
                        $path = $image->store(
                            'components',
                            'public'
                        );

                        $component->images()->create([
                            'image_path' => $path,
                            'is_primary' => $index === 0,
                            'sort_order' => $index,
                        ]);
                    }
                }
            }
        );

        return redirect()
            ->route('admin.components.index')
            ->with(
                'success',
                'Thêm thành phần thành công.'
            );
    }

    /**
     * Form cập nhật Component.
     */
    public function edit(Component $component)
    {
        $component->load([
            'images' => function ($query) {
                $query->orderByDesc('is_primary')
                    ->orderBy('sort_order');
            },
        ]);

        return view(
            'admin.components.edit',
            compact('component')
        );
    }

    /**
     * Cập nhật Component.
     */
    public function update(
        Request $request,
        Component $component
    ) {
        $validated = $this->validateComponent(
            $request,
            $component
        );

        DB::transaction(
            function () use (
                $request,
                $validated,
                $component
            ) {

                $component->update([
                    'name' =>
                        $validated['name'],

                    'sku' =>
                        strtoupper($validated['sku']),

                    'component_type' =>
                        $validated['component_type'],

                    'price' =>
                        $validated['price'],

                    'description' =>
                        $validated['description'] ?? null,

                    'stock_quantity' =>
                        $validated['stock_quantity'],

                    'unit' =>
                        $validated['unit'],

                    'size_group' =>
                        $validated['size_group'] ?? null,

                    'compatible_ecosystem_type' =>
                        $validated[
                            'compatible_ecosystem_type'
                        ],

                    'compatibility_note' =>
                        $validated[
                            'compatibility_note'
                        ] ?? null,

                    'is_active' =>
                        $request->boolean('is_active'),
                ]);

                /*
                 * Upload thêm ảnh mới.
                 */
                if ($request->hasFile('images')) {

                    $hasPrimaryImage =
                        $component->images()
                            ->where(
                                'is_primary',
                                true
                            )
                            ->exists();

                    $currentMaxOrder =
                        $component->images()
                            ->max('sort_order') ?? -1;

                    foreach (
                        $request->file('images')
                        as $index => $image
                    ) {
                        $path = $image->store(
                            'components',
                            'public'
                        );

                        $component->images()->create([
                            'image_path' => $path,

                            'is_primary' =>
                                !$hasPrimaryImage &&
                                $index === 0,

                            'sort_order' =>
                                $currentMaxOrder
                                + $index
                                + 1,
                        ]);
                    }
                }
            }
        );

        return redirect()
            ->route('admin.components.index')
            ->with(
                'success',
                'Cập nhật thành phần thành công.'
            );
    }

    /**
     * Active / Inactive.
     */
    public function toggleStatus(Component $component)
    {
        $component->update([
            'is_active' => !$component->is_active,
        ]);

        return redirect()
            ->route('admin.components.index')
            ->with(
                'success',
                $component->is_active
                    ? 'Đã chuyển thành phần sang Active.'
                    : 'Đã chuyển thành phần sang Inactive.'
            );
    }

    /**
     * Đặt ảnh chính.
     */
    public function setPrimaryImage(
        Component $component,
        int $image
    ) {
        $componentImage = $component->images()
            ->findOrFail($image);

        DB::transaction(
            function () use (
                $component,
                $componentImage
            ) {

                $component->images()->update([
                    'is_primary' => false,
                ]);

                $componentImage->update([
                    'is_primary' => true,
                ]);
            }
        );

        return back()->with(
            'success',
            'Đã đặt ảnh đại diện thành phần.'
        );
    }

    /**
     * Xóa một ảnh Component.
     */
    public function deleteImage(
        Component $component,
        int $image
    ) {
        $componentImage = $component->images()
            ->findOrFail($image);

        $wasPrimary = $componentImage->is_primary;

        DB::transaction(
            function () use (
                $component,
                $componentImage,
                $wasPrimary
            ) {

                if (
                    Storage::disk('public')
                        ->exists(
                            $componentImage->image_path
                        )
                ) {
                    Storage::disk('public')
                        ->delete(
                            $componentImage->image_path
                        );
                }

                $componentImage->delete();

                /*
                 * Nếu xóa ảnh chính,
                 * chọn ảnh tiếp theo làm ảnh chính.
                 */
                if ($wasPrimary) {

                    $nextImage = $component->images()
                        ->orderBy('sort_order')
                        ->first();

                    if ($nextImage) {
                        $nextImage->update([
                            'is_primary' => true,
                        ]);
                    }
                }
            }
        );

        return back()->with(
            'success',
            'Đã xóa ảnh thành phần.'
        );
    }

    /**
     * Xóa Component.
     */
    public function destroy(Component $component)
    {
        /*
         * Component đã được sử dụng trong Product
         * thì không xóa cứng.
         */
        if ($component->products()->exists()) {

            $component->update([
                'is_active' => false,
            ]);

            return redirect()
                ->route('admin.components.index')
                ->with(
                    'error',
                    'Thành phần đã được sử dụng trong sản phẩm nên không thể xóa. Thành phần đã được chuyển sang Inactive.'
                );
        }

        /*
         * Component đang có dữ liệu nghiệp vụ
         * liên quan thì giữ lại và chuyển Inactive.
         */
        if (
            $component->customDesignItems()->exists() ||
            $component->careRules()->exists() ||
            $component->rewards()->exists() ||
            $component
                ->outgoingCompatibilityRules()
                ->exists() ||
            $component
                ->incomingCompatibilityRules()
                ->exists()
        ) {
            $component->update([
                'is_active' => false,
            ]);

            return redirect()
                ->route('admin.components.index')
                ->with(
                    'error',
                    'Thành phần đang có dữ liệu liên quan nên không thể xóa cứng. Thành phần đã được chuyển sang Inactive.'
                );
        }

        /*
         * Không có dữ liệu liên quan:
         * có thể xóa thật.
         */
        DB::transaction(
            function () use ($component) {

                foreach ($component->images as $image) {

                    if (
                        Storage::disk('public')
                            ->exists($image->image_path)
                    ) {
                        Storage::disk('public')
                            ->delete($image->image_path);
                    }
                }

                $component->delete();
            }
        );

        return redirect()
            ->route('admin.components.index')
            ->with(
                'success',
                'Xóa thành phần thành công.'
            );
    }

    /**
     * Validation dùng chung
     * cho Thêm + Cập nhật.
     */
    private function validateComponent(
        Request $request,
        ?Component $component = null
    ): array {
        return $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:150',
                ],

                'sku' => [
                    'required',
                    'string',
                    'max:50',
                    'regex:/^[A-Za-z0-9-]+$/',

                    Rule::unique(
                        'components',
                        'sku'
                    )->ignore($component?->id),
                ],

                'component_type' => [
                    'required',

                    Rule::in([
                        'container',
                        'plant',
                        'moss',
                        'substrate',
                        'stone',
                        'accessory',
                    ]),
                ],

                'price' => [
                    'required',
                    'numeric',
                    'gt:0',
                ],

                'description' => [
                    'nullable',
                    'string',
                ],

                'stock_quantity' => [
                    'required',
                    'integer',
                    'min:0',
                ],

                'unit' => [
                    'required',
                    'string',
                    'max:30',
                ],

                'size_group' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'compatible_ecosystem_type' => [
                    'required',

                    Rule::in([
                        'all',
                        'closed',
                        'open',
                        'dry',
                    ]),
                ],

                'compatibility_note' => [
                    'nullable',
                    'string',
                ],

                'images' => [
                    'nullable',
                    'array',
                    'max:5',
                ],

                'images.*' => [
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:4096',
                ],
            ],
            [
                'name.required' =>
                    'Tên thành phần không được để trống.',

                'name.min' =>
                    'Tên thành phần phải có ít nhất 3 ký tự.',

                'name.max' =>
                    'Tên thành phần không được vượt quá 150 ký tự.',

                'sku.required' =>
                    'SKU không được để trống.',

                'sku.unique' =>
                    'SKU đã tồn tại.',

                'sku.regex' =>
                    'SKU chỉ được chứa chữ, số và dấu gạch ngang (-).',

                'component_type.required' =>
                    'Bạn phải chọn loại thành phần.',

                'component_type.in' =>
                    'Loại thành phần không hợp lệ.',

                'price.required' =>
                    'Giá không được để trống.',

                'price.numeric' =>
                    'Giá phải là số.',

                'price.gt' =>
                    'Giá phải lớn hơn 0.',

                'stock_quantity.required' =>
                    'Tồn kho không được để trống.',

                'stock_quantity.integer' =>
                    'Tồn kho phải là số nguyên.',

                'stock_quantity.min' =>
                    'Tồn kho không được âm.',

                'unit.required' =>
                    'Đơn vị tồn kho không được để trống.',

                'unit.max' =>
                    'Đơn vị tồn kho không được vượt quá 30 ký tự.',

                'compatible_ecosystem_type.required' =>
                    'Bạn phải chọn hệ sinh thái tương thích.',

                'compatible_ecosystem_type.in' =>
                    'Hệ sinh thái tương thích không hợp lệ.',

                'images.max' =>
                    'Mỗi lần chỉ được tải tối đa 5 ảnh.',

                'images.*.image' =>
                    'File tải lên phải là hình ảnh.',

                'images.*.mimes' =>
                    'Ảnh chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',

                'images.*.max' =>
                    'Mỗi ảnh không được vượt quá 4MB.',
            ]
        );
    }
}