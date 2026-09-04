<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\ComponentCompatibilityRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComponentCompatibilityRuleController extends Controller
{
    /**
     * Danh sách quy tắc tương thích.
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->keyword);
        $compatibility = $request->compatibility;
        $status = $request->status;

        $rules = ComponentCompatibilityRule::with([
                'sourceComponent',
                'targetComponent',
            ])
            ->when(
                $keyword,
                function ($query) use ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->whereHas(
                            'sourceComponent',
                            function ($componentQuery) use ($keyword) {
                                $componentQuery->where(
                                    'name',
                                    'like',
                                    '%' . $keyword . '%'
                                )->orWhere(
                                    'sku',
                                    'like',
                                    '%' . $keyword . '%'
                                );
                            }
                        )
                        ->orWhereHas(
                            'targetComponent',
                            function ($componentQuery) use ($keyword) {
                                $componentQuery->where(
                                    'name',
                                    'like',
                                    '%' . $keyword . '%'
                                )->orWhere(
                                    'sku',
                                    'like',
                                    '%' . $keyword . '%'
                                );
                            }
                        )
                        ->orWhere(
                            'reason',
                            'like',
                            '%' . $keyword . '%'
                        );
                    });
                }
            )
            ->when(
                $compatibility !== null &&
                $compatibility !== '',
                function ($query) use ($compatibility) {
                    $query->where(
                        'is_compatible',
                        (int) $compatibility
                    );
                }
            )
            ->when(
                $status !== null &&
                $status !== '',
                function ($query) use ($status) {
                    $query->where(
                        'is_active',
                        (int) $status
                    );
                }
            )
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.compatibility-rules.index',
            compact(
                'rules',
                'keyword',
                'compatibility',
                'status'
            )
        );
    }

    /**
     * Form thêm quy tắc.
     */
    public function create()
    {
        $components = Component::orderBy('component_type')
            ->orderBy('name')
            ->get();

        return view(
            'admin.compatibility-rules.create',
            compact('components')
        );
    }

    /**
     * Thêm quy tắc mới.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRule($request);

        /*
         * Không cho trùng cùng cặp:
         * Source + Target.
         */
        $exists = ComponentCompatibilityRule::where(
                'source_component_id',
                $validated['source_component_id']
            )
            ->where(
                'target_component_id',
                $validated['target_component_id']
            )
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'target_component_id' =>
                        'Quy tắc giữa hai thành phần này đã tồn tại.',
                ])
                ->withInput();
        }

        ComponentCompatibilityRule::create([
            'source_component_id' =>
                $validated['source_component_id'],

            'target_component_id' =>
                $validated['target_component_id'],

            'is_compatible' =>
                (bool) $validated['is_compatible'],

            'reason' =>
                $validated['reason'] ?? null,

            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.compatibility-rules.index')
            ->with(
                'success',
                'Thêm quy tắc tương thích thành công.'
            );
    }

    /**
     * Form cập nhật.
     */
    public function edit(
        ComponentCompatibilityRule $compatibilityRule
    ) {
        $components = Component::orderBy('component_type')
            ->orderBy('name')
            ->get();

        return view(
            'admin.compatibility-rules.edit',
            compact(
                'compatibilityRule',
                'components'
            )
        );
    }

    /**
     * Cập nhật quy tắc.
     */
    public function update(
        Request $request,
        ComponentCompatibilityRule $compatibilityRule
    ) {
        $validated = $this->validateRule($request);

        $exists = ComponentCompatibilityRule::where(
                'source_component_id',
                $validated['source_component_id']
            )
            ->where(
                'target_component_id',
                $validated['target_component_id']
            )
            ->where(
                'id',
                '!=',
                $compatibilityRule->id
            )
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'target_component_id' =>
                        'Quy tắc giữa hai thành phần này đã tồn tại.',
                ])
                ->withInput();
        }

        $compatibilityRule->update([
            'source_component_id' =>
                $validated['source_component_id'],

            'target_component_id' =>
                $validated['target_component_id'],

            'is_compatible' =>
                (bool) $validated['is_compatible'],

            'reason' =>
                $validated['reason'] ?? null,

            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.compatibility-rules.index')
            ->with(
                'success',
                'Cập nhật quy tắc tương thích thành công.'
            );
    }

    /**
     * Active / Inactive.
     */
    public function toggleStatus(
        ComponentCompatibilityRule $compatibilityRule
    ) {
        $compatibilityRule->update([
            'is_active' =>
                !$compatibilityRule->is_active,
        ]);

        return redirect()
            ->route('admin.compatibility-rules.index')
            ->with(
                'success',
                $compatibilityRule->is_active
                    ? 'Đã chuyển quy tắc sang Active.'
                    : 'Đã chuyển quy tắc sang Inactive.'
            );
    }

    /**
     * Xóa quy tắc.
     */
    public function destroy(
        ComponentCompatibilityRule $compatibilityRule
    ) {
        $compatibilityRule->delete();

        return redirect()
            ->route('admin.compatibility-rules.index')
            ->with(
                'success',
                'Xóa quy tắc tương thích thành công.'
            );
    }

    /**
     * Validation dùng chung.
     */
    private function validateRule(Request $request): array
    {
        return $request->validate(
            [
                'source_component_id' => [
                    'required',
                    'integer',
                    'exists:components,id',
                ],

                'target_component_id' => [
                    'required',
                    'integer',
                    'exists:components,id',
                    'different:source_component_id',
                ],

                'is_compatible' => [
                    'required',
                    Rule::in([
                        '0',
                        '1',
                        0,
                        1,
                    ]),
                ],

                /*
                 * Nếu không tương thích,
                 * nên bắt buộc giải thích lý do.
                 */
                'reason' => [
                    'nullable',
                    'string',
                    'max:1000',
                    Rule::requiredIf(
                        fn () =>
                            (string) $request
                                ->input('is_compatible') === '0'
                    ),
                ],
            ],
            [
                'source_component_id.required' =>
                    'Bạn phải chọn thành phần nguồn.',

                'source_component_id.exists' =>
                    'Thành phần nguồn không tồn tại.',

                'target_component_id.required' =>
                    'Bạn phải chọn thành phần đích.',

                'target_component_id.exists' =>
                    'Thành phần đích không tồn tại.',

                'target_component_id.different' =>
                    'Hai thành phần phải khác nhau.',

                'is_compatible.required' =>
                    'Bạn phải chọn kết quả tương thích.',

                'is_compatible.in' =>
                    'Giá trị tương thích không hợp lệ.',

                'reason.required' =>
                    'Quy tắc không tương thích phải có lý do.',

                'reason.max' =>
                    'Lý do không được vượt quá 1000 ký tự.',
            ]
        );
    }
}