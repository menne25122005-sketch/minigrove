<?php

namespace App\Services;

use App\Models\Component;
use App\Models\ComponentCompatibilityRule;
use Illuminate\Validation\ValidationException;

class StudioCompatibilityService
{
    /**
     * Kiểm tra toàn bộ Component
     * trong một cấu hình MiniGrove Studio.
     */
    public function validate(array $componentIds): array
    {
        $componentIds = array_values(
            array_unique(
                array_map(
                    'intval',
                    $componentIds
                )
            )
        );

        if (empty($componentIds)) {
            throw ValidationException::withMessages([
                'components' =>
                    'Thiết kế chưa có thành phần.',
            ]);
        }


        /*
         * Lấy Component thật từ Database.
         */
        $components = Component::whereIn(
            'id',
            $componentIds
        )->get();


        /*
         * Nếu số lượng trả về khác số ID gửi vào
         * thì có Component không tồn tại.
         */
        if ($components->count() !== count($componentIds)) {
            throw ValidationException::withMessages([
                'components' =>
                    'Có thành phần không tồn tại.',
            ]);
        }


        /*
         * ---------------------------------------------------------------
         * 1. Kiểm tra Active + Stock
         * ---------------------------------------------------------------
         */
        foreach ($components as $component) {

            if (!$component->is_active) {
                throw ValidationException::withMessages([
                    'components' =>
                        "Thành phần \"{$component->name}\" đang Inactive.",
                ]);
            }

            if ($component->stock_quantity <= 0) {
                throw ValidationException::withMessages([
                    'components' =>
                        "Thành phần \"{$component->name}\" đã hết hàng.",
                ]);
            }
        }


        /*
         * ---------------------------------------------------------------
         * 2. Tìm bình của thiết kế
         * ---------------------------------------------------------------
         */
        $containers = $components->where(
            'component_type',
            'container'
        );


        /*
         * Một thiết kế chỉ được chọn đúng một bình.
         */
        if ($containers->count() !== 1) {
            throw ValidationException::withMessages([
                'components' =>
                    'Thiết kế phải chọn đúng một bình.',
            ]);
        }


        $container = $containers->first();

        $ecosystemType =
            $container->compatible_ecosystem_type;


        /*
         * ---------------------------------------------------------------
         * 3. Kiểm tra hệ sinh thái
         * ---------------------------------------------------------------
         *
         * Ví dụ:
         * Bình closed
         *
         * Component được phép:
         * - closed
         * - all
         *
         * Component dry sẽ bị từ chối.
         */
        foreach ($components as $component) {

            /*
             * Không cần so bình với chính nó.
             */
            if ($component->id === $container->id) {
                continue;
            }


            $componentEcosystem =
                $component->compatible_ecosystem_type;


            if (
                $componentEcosystem !== 'all' &&
                $componentEcosystem !== $ecosystemType
            ) {
                throw ValidationException::withMessages([
                    'components' =>
                        "Thành phần \"{$component->name}\" không phù hợp với hệ sinh thái của bình \"{$container->name}\".",
                ]);
            }
        }


        /*
         * ---------------------------------------------------------------
         * 4. Kiểm tra Compatibility Rule
         * ---------------------------------------------------------------
         */

        $rules = ComponentCompatibilityRule::where(
                'is_active',
                true
            )
            ->whereIn(
                'source_component_id',
                $componentIds
            )
            ->whereIn(
                'target_component_id',
                $componentIds
            )
            ->get();


        $incompatibleRules = [];


        foreach ($rules as $rule) {

            /*
             * Chỉ Rule is_compatible = false
             * mới chặn cấu hình.
             */
            if (!$rule->is_compatible) {

                $source = $components->firstWhere(
                    'id',
                    $rule->source_component_id
                );

                $target = $components->firstWhere(
                    'id',
                    $rule->target_component_id
                );


                $incompatibleRules[] = [
                    'source_component_id' =>
                        $rule->source_component_id,

                    'source_name' =>
                        $source?->name,

                    'target_component_id' =>
                        $rule->target_component_id,

                    'target_name' =>
                        $target?->name,

                    'reason' =>
                        $rule->reason,
                ];
            }
        }


        /*
         * Có Rule cấm
         * => không cho tiếp tục.
         */
        if (!empty($incompatibleRules)) {

            $firstRule = $incompatibleRules[0];


            $message =
                "\"{$firstRule['source_name']}\" không tương thích với \"{$firstRule['target_name']}\".";


            if (!empty($firstRule['reason'])) {
                $message .=
                    ' ' . $firstRule['reason'];
            }


            throw ValidationException::withMessages([
                'components' => $message,
            ]);
        }


        /*
         * ---------------------------------------------------------------
         * 5. Hợp lệ
         * ---------------------------------------------------------------
         */
        return [
            'compatible' => true,

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

            'incompatible_rules' => [],
        ];
    }
}