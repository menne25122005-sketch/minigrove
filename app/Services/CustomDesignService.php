<?php

namespace App\Services;

use App\Models\CustomDesign;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CustomDesignService
{
    public function __construct(
        private StudioPricingService $pricingService,
        private StudioCompatibilityService $compatibilityService
    ) {
    }


    /**
     * Tạo và lưu Custom Design cho một Customer.
     */
    public function create(
        User $user,
        string $name,
        string $style,
        array $selectedItems
    ): CustomDesign {
        /*
         * ---------------------------------------------------------------
         * 1. Tính giá lại từ Database
         * ---------------------------------------------------------------
         */
        $pricing = $this->pricingService
            ->calculate($selectedItems);


        /*
         * ---------------------------------------------------------------
         * 2. Kiểm tra tương thích
         * ---------------------------------------------------------------
         */
        $componentIds = collect(
            $pricing['items']
        )
            ->pluck('component_id')
            ->all();


        $compatibility = $this->compatibilityService
            ->validate($componentIds);


        /*
         * ---------------------------------------------------------------
         * 3. Lưu trong Transaction
         * ---------------------------------------------------------------
         */
        return DB::transaction(
            function () use (
                $user,
                $name,
                $style,
                $pricing,
                $compatibility
            ) {

                /*
                 * Sinh mã thiết kế duy nhất.
                 */
                $designCode = $this->generateDesignCode();


                /*
                 * Snapshot toàn cấu hình.
                 */
                $snapshot = [
                    'style' => $style,

                    'ecosystem_type' =>
                        $compatibility[
                            'ecosystem_type'
                        ],

                    'container' =>
                        $compatibility[
                            'container'
                        ],

                    'components' =>
                        $pricing['items'],

                    'estimated_price' =>
                        $pricing[
                            'estimated_price'
                        ],
                ];


                /*
                 * Tạo Custom Design.
                 */
                $design = CustomDesign::create([
                    'user_id' =>
                        $user->id,

                    'design_code' =>
                        $designCode,

                    'name' =>
                        $name,

                    'style' =>
                        $style,

                    'estimated_price' =>
                        $pricing[
                            'estimated_price'
                        ],

                    'configuration_snapshot' =>
                        $snapshot,

                    'status' =>
                        'saved',
                ]);


                /*
                 * Tạo từng CustomDesignItem.
                 */
                foreach (
                    $pricing['items']
                    as $item
                ) {
                    $design->items()->create([
                        'component_id' =>
                            $item[
                                'component_id'
                            ],

                        'component_type' =>
                            $item[
                                'component_type'
                            ],

                        'component_name' =>
                            $item[
                                'component_name'
                            ],

                        'unit_price' =>
                            $item[
                                'unit_price'
                            ],

                        'quantity' =>
                            $item[
                                'quantity'
                            ],

                        'subtotal' =>
                            $item[
                                'subtotal'
                            ],
                    ]);
                }


                return $design->load(
                    'items'
                );
            }
        );
    }


    /**
     * Tính lại giá hiện hành của
     * một thiết kế đã lưu.
     *
     * Dùng khi đưa vào Cart / Checkout.
     */
    public function recalculate(
        CustomDesign $design
    ): array {
        $design->load('items');


        $selectedItems = $design->items
            ->map(
                function ($item) {
                    return [
                        'component_id' =>
                            $item->component_id,

                        'quantity' =>
                            $item->quantity,
                    ];
                }
            )
            ->values()
            ->all();


        /*
         * Lấy lại giá và stock
         * từ Database hiện tại.
         */
        $pricing = $this->pricingService
            ->calculate($selectedItems);


        /*
         * Kiểm tra lại compatibility.
         */
        $componentIds = collect(
            $pricing['items']
        )
            ->pluck('component_id')
            ->all();


        $compatibility =
            $this->compatibilityService
                ->validate($componentIds);


        return [
            'pricing' =>
                $pricing,

            'compatibility' =>
                $compatibility,
        ];
    }


    /**
     * Xóa hoặc lưu trữ thiết kế.
     */
    public function deleteForUser(
        CustomDesign $design,
        User $user
    ): string {
        /*
         * Không cho Customer khác
         * tác động vào Design.
         */
        if (
            $design->user_id !==
            $user->id
        ) {
            throw ValidationException::withMessages([
                'design' =>
                    'Bạn không có quyền thao tác thiết kế này.',
            ]);
        }


        /*
         * Nếu đã xuất hiện trong Order
         * thì không được xóa cứng.
         */
        if (
            $design->orderDetails()
                ->exists()
        ) {
            $design->update([
                'status' => 'archived',
            ]);

            return 'archived';
        }


        /*
         * Chưa phát sinh Order:
         * có thể xóa.
         */
        $design->delete();

        return 'deleted';
    }


    /**
     * Sinh Design Code.
     */
    private function generateDesignCode(): string
    {
        do {

            $code =
                'MGD-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(
                    Str::random(6)
                );

        } while (
            CustomDesign::where(
                'design_code',
                $code
            )->exists()
        );


        return $code;
    }
}