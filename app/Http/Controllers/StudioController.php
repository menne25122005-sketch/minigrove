<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Services\StudioCompatibilityService;
use App\Services\StudioPricingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudioController extends Controller
{
    public function __construct(
        private StudioPricingService $pricingService,
        private StudioCompatibilityService $compatibilityService
    ) {
    }


    /**
     * Màn hình MiniGrove Studio.
     */
    public function index()
    {
        $components = Component::with([
        'images' => function ($query) {
            $query->orderByDesc('is_primary')
                ->orderBy('sort_order');
        },
    ])
    ->where(
        'is_active',
        true
    )
            ->where(
                'stock_quantity',
                '>',
                0
            )
            ->orderBy('component_type')
            ->orderBy('name')
            ->get();


        $containers = $components->where(
            'component_type',
            'container'
        );

        $plants = $components->where(
            'component_type',
            'plant'
        );

        $mosses = $components->where(
            'component_type',
            'moss'
        );

        $substrates = $components->where(
            'component_type',
            'substrate'
        );

        $stones = $components->where(
            'component_type',
            'stone'
        );

        $accessories = $components->where(
            'component_type',
            'accessory'
        );


        return view(
            'studio.index',
            compact(
                'containers',
                'plants',
                'mosses',
                'substrates',
                'stones',
                'accessories'
            )
        );
    }


    /**
     * Kiểm tra cấu hình và tạo Preview.
     *
     * Chưa lưu CustomDesign vì Auth Customer
     * sẽ được tích hợp ở bước sau.
     */
    public function preview(Request $request)
    {
        $validated = $request->validate(
            [
                'container_id' => [
                    'required',
                    'integer',
                    'exists:components,id',
                ],

                'plant_id' => [
                    'nullable',
                    'integer',
                    'exists:components,id',
                ],

                'moss_id' => [
                    'nullable',
                    'integer',
                    'exists:components,id',
                ],

                'substrate_id' => [
                    'required',
                    'integer',
                    'exists:components,id',
                ],

                'stone_id' => [
                    'nullable',
                    'integer',
                    'exists:components,id',
                ],

                'accessory_id' => [
                    'nullable',
                    'integer',
                    'exists:components,id',
                ],

                'style' => [
                    'required',

                    Rule::in([
                        'natural',
                        'forest',
                        'zen',
                        'fairy',
                        'tropical',
                        'minimal',
                    ]),
                ],
            ],
            [
                'container_id.required' =>
                    'Bạn phải chọn bình.',

                'substrate_id.required' =>
                    'Bạn phải chọn nền / giá thể.',

                'style.required' =>
                    'Bạn phải chọn phong cách.',

                'style.in' =>
                    'Phong cách không hợp lệ.',
            ]
        );


        /*
         * Phải chọn ít nhất Cây hoặc Rêu.
         */
        if (
            empty($validated['plant_id']) &&
            empty($validated['moss_id'])
        ) {
            return back()
                ->withErrors([
                    'components' =>
                        'Bạn phải chọn ít nhất một Cây hoặc Rêu.',
                ])
                ->withInput();
        }


        /*
         * ---------------------------------------------------------------
         * Gom các Component đã chọn
         * ---------------------------------------------------------------
         */

        $componentIds = [
            $validated['container_id'],
            $validated['substrate_id'],
        ];


        foreach ([
            'plant_id',
            'moss_id',
            'stone_id',
            'accessory_id',
        ] as $field) {

            if (!empty($validated[$field])) {
                $componentIds[] =
                    $validated[$field];
            }
        }


        /*
         * ---------------------------------------------------------------
         * Kiểm tra tương thích
         * ---------------------------------------------------------------
         */
        $compatibility =
            $this->compatibilityService
                ->validate($componentIds);


        /*
         * ---------------------------------------------------------------
         * Chuẩn bị dữ liệu tính giá.
         *
         * Phiên bản đầu: mỗi Component Quantity = 1.
         * ---------------------------------------------------------------
         */
        $pricingItems = [];

        foreach ($componentIds as $componentId) {
            $pricingItems[] = [
                'component_id' =>
                    $componentId,

                'quantity' => 1,
            ];
        }


        /*
         * ---------------------------------------------------------------
         * Tính giá từ Database
         * ---------------------------------------------------------------
         */
        $pricing =
            $this->pricingService
                ->calculate($pricingItems);


        /*
         * ---------------------------------------------------------------
         * Preview
         * ---------------------------------------------------------------
         */
        return view(
            'studio.preview',
            [
                'style' =>
                    $validated['style'],

                'items' =>
                    $pricing['items'],

                'estimatedPrice' =>
                    $pricing['estimated_price'],

                'compatibility' =>
                    $compatibility,
            ]
        );
    }
}