<?php

namespace App\Http\Controllers;


use App\Models\Product;

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

        $products = Product::with([

            'images' => function ($query) {

                $query
                    ->orderByDesc('is_primary')
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

        ->orderBy(
            'ecosystem_type'
        )

        ->orderBy(
            'name'
        )

        ->get();





        $containers = $products->where(
            'ecosystem_type',
            'container'
        );


        $plants = $products->where(
            'ecosystem_type',
            'plant'
        );


        $mosses = $products->where(
            'ecosystem_type',
            'moss'
        );


        $substrates = $products->where(
            'ecosystem_type',
            'substrate'
        );


        $stones = $products->where(
            'ecosystem_type',
            'stone'
        );


        $accessories = $products->where(
            'ecosystem_type',
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
     */
    public function preview(Request $request)
    {

        $validated = $request->validate(

            [

                'container_id' => [

                    'required',

                    'integer',

                    'exists:products,id',

                ],



                'plant_id' => [

                    'nullable',

                    'integer',

                    'exists:products,id',

                ],



                'moss_id' => [

                    'nullable',

                    'integer',

                    'exists:products,id',

                ],



                'substrate_id' => [

                    'required',

                    'integer',

                    'exists:products,id',

                ],



                'stone_id' => [

                    'nullable',

                    'integer',

                    'exists:products,id',

                ],



                'accessory_id' => [

                    'nullable',

                    'integer',

                    'exists:products,id',

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
        |--------------------------------------------------------------------------
        | Phải chọn ít nhất Cây hoặc Rêu
        |--------------------------------------------------------------------------
        */

        if (

            empty($validated['plant_id'])

            &&

            empty($validated['moss_id'])

        ) {


            return back()

                ->withErrors([

                    'products' =>

                        'Bạn phải chọn ít nhất một Cây hoặc Rêu.',

                ])

                ->withInput();


        }







        /*
        |--------------------------------------------------------------------------
        | Gom các Product đã chọn
        |--------------------------------------------------------------------------
        */


        $productIds = [

            $validated['container_id'],

            $validated['substrate_id'],

        ];





        foreach (

            [

                'plant_id',

                'moss_id',

                'stone_id',

                'accessory_id',

            ]

            as $field

        ) {


            if (

                !empty($validated[$field])

            ) {


                $productIds[] =

                    $validated[$field];


            }

        }







        /*
        |--------------------------------------------------------------------------
        | Kiểm tra tương thích
        |--------------------------------------------------------------------------
        */


        $compatibility =

            $this->compatibilityService

                ->validate($productIds);







        /*
        |--------------------------------------------------------------------------
        | Chuẩn bị dữ liệu tính giá
        |--------------------------------------------------------------------------
        */


        $pricingItems = [];





        foreach ($productIds as $productId) {


            $pricingItems[] = [

                'product_id' =>

                    $productId,


                'quantity' =>

                    1,

            ];

        }







        /*
        |--------------------------------------------------------------------------
        | Tính giá
        |--------------------------------------------------------------------------
        */


        $pricing =

            $this->pricingService

                ->calculate($pricingItems);







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