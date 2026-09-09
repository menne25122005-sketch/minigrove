<?php

namespace App\Services;


use App\Models\Product;
use App\Models\ProductCompatibilityRule;
use Illuminate\Validation\ValidationException;



class StudioCompatibilityService
{


    /**
     * Kiểm tra các Product
     * trong một cấu hình MiniGrove Studio.
     */
    public function validate(array $productIds): array
    {


        $productIds = array_values(

            array_unique(

                array_map(
                    'intval',
                    $productIds
                )

            )

        );



        if (empty($productIds)) {

            throw ValidationException::withMessages([
                'products' =>
                    'Thiết kế chưa có sản phẩm.',
            ]);

        }




        /*
        |--------------------------------------------------------------------------
        | Lấy Product
        |--------------------------------------------------------------------------
        */

        $products = Product::whereIn(
            'id',
            $productIds
        )->get();




        if (
            $products->count()
            !==
            count($productIds)
        ) {

            throw ValidationException::withMessages([
                'products' =>
                    'Có sản phẩm không tồn tại.',
            ]);

        }




        /*
        |--------------------------------------------------------------------------
        | Kiểm tra trạng thái Product
        |--------------------------------------------------------------------------
        */

        foreach ($products as $product) {


            if (!$product->is_active) {

                throw ValidationException::withMessages([
                    'products' =>
                        "Sản phẩm \"{$product->name}\" đang Inactive.",
                ]);

            }




            if ($product->stock_quantity <= 0) {

                throw ValidationException::withMessages([
                    'products' =>
                        "Sản phẩm \"{$product->name}\" đã hết hàng.",
                ]);

            }


        }





        /*
        |--------------------------------------------------------------------------
        | Kiểm tra Product Compatibility Rule
        |--------------------------------------------------------------------------
        |
        | Kiểm tra cả 2 chiều:
        |
        | A -> B
        | B -> A
        |
        |--------------------------------------------------------------------------
        */


        $rules = ProductCompatibilityRule::query()

            ->where(
                'is_active',
                true
            )

            ->where(
                'is_compatible',
                false
            )

            ->where(function ($query) use ($productIds) {


                $query->whereIn(
                    'source_product_id',
                    $productIds
                )

                ->whereIn(
                    'target_product_id',
                    $productIds
                );


            })

            ->get();




        if ($rules->isNotEmpty()) {


            $rule = $rules->first();



            $source = Product::find(
                $rule->source_product_id
            );


            $target = Product::find(
                $rule->target_product_id
            );



            $message =
                "Sản phẩm \"{$source?->name}\" không tương thích với \"{$target?->name}\".";



            if (!empty($rule->reason)) {

                $message .=
                    ' ' . $rule->reason;

            }



            throw ValidationException::withMessages([
                'products' =>
                    $message,
            ]);


        }





        /*
        |--------------------------------------------------------------------------
        | Kiểm tra hệ sinh thái
        |--------------------------------------------------------------------------
        */


        $container = $products->firstWhere(
            'ecosystem_type',
            'container'
        );




        if (!$container) {


            throw ValidationException::withMessages([
                'products' =>
                    'Thiết kế phải có sản phẩm loại bình/chậu.',
            ]);


        }




        $ecosystemType =
            $container->ecosystem_type;





        foreach ($products as $product) {



            if (
                $product->id
                ===
                $container->id
            ) {

                continue;

            }




            if (

                $product->ecosystem_type
                !==
                'all'

                &&

                $product->ecosystem_type
                !==
                $ecosystemType

            ) {


                throw ValidationException::withMessages([
                    'products' =>
                        "Sản phẩm \"{$product->name}\" không phù hợp với hệ sinh thái.",
                ]);


            }


        }






        return [

            'compatible' =>
                true,


            'ecosystem_type' =>
                $ecosystemType,



            'container' => [

                'id' =>
                    $container->id,


                'name' =>
                    $container->name,

            ],



            'checked_rules' =>
                $rules->count(),


        ];

    }

}