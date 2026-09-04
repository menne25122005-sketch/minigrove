<?php

namespace App\Services;

use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Validation\ValidationException;

class VoucherService
{
    /**
     * Kiểm tra Voucher và tính số tiền giảm.
     */
    public function validateAndCalculate(
        User $user,
        string $code,
        float $subtotal
    ): array {

        $code = strtoupper(
            trim($code)
        );


        if ($code === '') {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Vui lòng nhập mã Voucher.',
            ]);
        }


        $voucher = Voucher::query()
            ->whereRaw(
                'UPPER(code) = ?',
                [$code]
            )
            ->first();


        if (!$voucher) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Mã Voucher không tồn tại.',
            ]);
        }


        /*
         * Active.
         */
        if (!$voucher->is_active) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher hiện không còn hoạt động.',
            ]);
        }


        $now = now();


        /*
         * Chưa đến thời gian sử dụng.
         */
        if ($voucher->start_at->gt($now)) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher chưa đến thời gian sử dụng.',
            ]);
        }


        /*
         * Đã hết hạn.
         */
        if ($voucher->end_at->lt($now)) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher đã hết hạn.',
            ]);
        }


        /*
         * Tổng lượt sử dụng.
         */
        if (
            $voucher->usage_limit !== null
            &&
            $voucher->usage_count >=
            $voucher->usage_limit
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher đã hết lượt sử dụng.',
            ]);
        }


        /*
         * Giá trị đơn tối thiểu.
         */
        if (
            $subtotal <
            (float) $voucher->min_order_amount
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Đơn hàng chưa đạt giá trị tối thiểu '
                    . number_format(
                        (float)
                        $voucher->min_order_amount,
                        0,
                        ',',
                        '.'
                    )
                    . 'đ để sử dụng Voucher.',
            ]);
        }


        /*
         * Giới hạn theo từng Customer.
         */
        if (
            $voucher->per_user_limit !== null
        ) {

            $usedCount =
                VoucherUsage::query()
                    ->where(
                        'voucher_id',
                        $voucher->id
                    )
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->where(
                        'status',
                        'used'
                    )
                    ->count();


            if (
                $usedCount >=
                $voucher->per_user_limit
            ) {

                throw ValidationException::withMessages([
                    'voucher' =>
                        'Bạn đã sử dụng hết số lượt cho Voucher này.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TÍNH TIỀN GIẢM
        |--------------------------------------------------------------------------
        */

        if (
            $voucher->discount_type
            === 'percentage'
        ) {

            $discount =
                $subtotal
                * (
                    (float)
                    $voucher->discount_value
                    / 100
                );
        }

        elseif (
            $voucher->discount_type
            === 'fixed'
        ) {

            $discount =
                (float)
                $voucher->discount_value;
        }

        else {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher có cấu hình không hợp lệ.',
            ]);
        }


        /*
         * Giới hạn tiền giảm tối đa.
         */
        if (
            $voucher->max_discount_amount
            !== null
        ) {

            $discount = min(
                $discount,
                (float)
                $voucher->max_discount_amount
            );
        }


        /*
         * Không cho tiền giảm lớn hơn tiền hàng.
         */
        $discount = min(
            $discount,
            $subtotal
        );


        $discount = max(
            $discount,
            0
        );


        return [
            'voucher' =>
                $voucher,

            'discount_amount' =>
                $discount,
        ];
    }
}