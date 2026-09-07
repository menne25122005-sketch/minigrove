<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Validation\ValidationException;


class StudioPricingService
{


    /**
     * Kiểm tra Product + tồn kho
     * và tính giá từ Database.
     *
     * Dữ liệu:
     *
     * [
     *     [
     *         'product_id' => 1,
     *         'quantity' => 1,
     *     ]
     * ]
     */
    public function calculate(array $selectedItems): array
    {


        if (empty($selectedItems)) {

            throw ValidationException::withMessages([
                'products' =>
                    'Thiết kế phải có ít nhất một sản phẩm.',
            ]);

        }



        $resultItems = [];

        $estimatedPrice = 0;


        $usedProductIds = [];



        foreach ($selectedItems as $item) {


            $productId =
                (int) ($item['product_id'] ?? 0);


            $quantity =
                (int) ($item['quantity'] ?? 0);




            if ($productId <= 0) {

                throw ValidationException::withMessages([
                    'products' =>
                        'Có sản phẩm không hợp lệ.',
                ]);

            }



            if ($quantity < 1) {

                throw ValidationException::withMessages([
                    'products' =>
                        'Số lượng sản phẩm phải lớn hơn hoặc bằng 1.',
                ]);

            }




            $product = Product::find($productId);



            if (!$product) {

                throw ValidationException::withMessages([
                    'products' =>
                        'Có sản phẩm không còn tồn tại.',
                ]);

            }




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




            if ($quantity > $product->stock_quantity) {

                throw ValidationException::withMessages([
                    'products' =>
                        "Sản phẩm \"{$product->name}\" chỉ còn {$product->stock_quantity}.",
                ]);

            }




            if (
                in_array(
                    $product->id,
                    $usedProductIds,
                    true
                )
            ) {

                throw ValidationException::withMessages([
                    'products' =>
                        "Sản phẩm \"{$product->name}\" bị chọn trùng.",
                ]);

            }



            $usedProductIds[] =
                $product->id;




            $unitPrice =
                (float) (
                    $product->sale_price
                    ?? $product->list_price
                );



            $subtotal =
                $unitPrice * $quantity;



            $estimatedPrice +=
                $subtotal;




            $resultItems[] = [

                'product_id' =>
                    $product->id,


                'product_name' =>
                    $product->name,


                'ecosystem_type' =>
                    $product->ecosystem_type,


                'unit_price' =>
                    $unitPrice,


                'quantity' =>
                    $quantity,


                'subtotal' =>
                    $subtotal,


                'sku' =>
                    $product->sku,

            ];

        }




        return [

            'items' =>
                $resultItems,


            'estimated_price' =>
                $estimatedPrice,

        ];

    }

}