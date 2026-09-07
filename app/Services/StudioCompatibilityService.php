<?php

namespace App\Services;


use App\Models\Product;
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
                        "Sản phẩm \"{$product->name}\" không phù hợp.",
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


        ];

    }

}