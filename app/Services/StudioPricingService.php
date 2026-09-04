<?php

namespace App\Services;

use App\Models\Component;
use Illuminate\Validation\ValidationException;

class StudioPricingService
{
    /**
     * Kiểm tra Component + tồn kho
     * và tính giá hoàn toàn từ Database.
     *
     * $selectedItems dạng:
     *
     * [
     *     [
     *         'component_id' => 1,
     *         'quantity' => 1,
     *     ],
     *     ...
     * ]
     */
    public function calculate(array $selectedItems): array
    {
        if (empty($selectedItems)) {
            throw ValidationException::withMessages([
                'components' =>
                    'Thiết kế phải có ít nhất một thành phần.',
            ]);
        }

        $resultItems = [];

        $estimatedPrice = 0;

        /*
         * Không cho cùng một Component
         * xuất hiện nhiều dòng.
         */
        $usedComponentIds = [];

        foreach ($selectedItems as $item) {

            $componentId =
                (int) ($item['component_id'] ?? 0);

            $quantity =
                (int) ($item['quantity'] ?? 0);


            /*
             * Component ID phải hợp lệ.
             */
            if ($componentId <= 0) {
                throw ValidationException::withMessages([
                    'components' =>
                        'Có thành phần không hợp lệ.',
                ]);
            }


            /*
             * Quantity phải >= 1.
             */
            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'components' =>
                        'Số lượng thành phần phải lớn hơn hoặc bằng 1.',
                ]);
            }


            /*
             * Không tin dữ liệu tên/giá từ Client.
             * Lấy lại Component từ Database.
             */
            $component = Component::find($componentId);

            if (!$component) {
                throw ValidationException::withMessages([
                    'components' =>
                        'Có thành phần không còn tồn tại.',
                ]);
            }


            /*
             * Component phải Active.
             */
            if (!$component->is_active) {
                throw ValidationException::withMessages([
                    'components' =>
                        "Thành phần \"{$component->name}\" đang Inactive.",
                ]);
            }


            /*
             * Không được chọn Component hết hàng.
             */
            if ($component->stock_quantity <= 0) {
                throw ValidationException::withMessages([
                    'components' =>
                        "Thành phần \"{$component->name}\" đã hết hàng.",
                ]);
            }


            /*
             * Số lượng chọn không vượt Stock.
             */
            if ($quantity > $component->stock_quantity) {
                throw ValidationException::withMessages([
                    'components' =>
                        "Thành phần \"{$component->name}\" chỉ còn {$component->stock_quantity} {$component->unit}.",
                ]);
            }


            /*
             * Không cho cùng một Component
             * xuất hiện hai lần.
             */
            if (in_array(
                $component->id,
                $usedComponentIds,
                true
            )) {
                throw ValidationException::withMessages([
                    'components' =>
                        "Thành phần \"{$component->name}\" bị chọn trùng.",
                ]);
            }

            $usedComponentIds[] = $component->id;


            /*
             * Giá luôn lấy từ Database.
             */
            $unitPrice = (float) $component->price;

            $subtotal = $unitPrice * $quantity;

            $estimatedPrice += $subtotal;


            /*
             * Dữ liệu chuẩn để lưu
             * CustomDesignItem sau này.
             */
            $resultItems[] = [
                'component_id' =>
                    $component->id,

                'component_type' =>
                    $component->component_type,

                'component_name' =>
                    $component->name,

                'unit_price' =>
                    $unitPrice,

                'quantity' =>
                    $quantity,

                'subtotal' =>
                    $subtotal,

                'sku' =>
                    $component->sku,

                'unit' =>
                    $component->unit,

                'compatible_ecosystem_type' =>
                    $component
                        ->compatible_ecosystem_type,
            ];
        }


        return [
            'items' => $resultItems,

            'estimated_price' =>
                $estimatedPrice,
        ];
    }
}