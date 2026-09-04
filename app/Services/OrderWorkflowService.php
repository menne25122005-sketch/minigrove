<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderWorkflowService
{
    /**
     * Chuyển trạng thái Order theo đúng luồng nghiệp vụ.
     */
    public function updateStatus(
        User $actor,
        Order $order,
        string $newStatus,
        ?string $description = null
    ): Order {

        $newStatus =
            strtolower(
                trim($newStatus)
            );


        /*
        |--------------------------------------------------------------------------
        | LUỒNG TRẠNG THÁI CHO PHÉP
        |--------------------------------------------------------------------------
        */

        $allowedTransitions = [

            'pending' => [
                'confirmed',
            ],

            'confirmed' => [
                'preparing',
            ],

            'preparing' => [
                'packed',
            ],

            'packed' => [
                'shipping',
            ],

            'shipping' => [
                'completed',
            ],

            /*
             * Completed và Cancelled
             * là trạng thái kết thúc.
             */
            'completed' => [],

            'cancelled' => [],
        ];


        return DB::transaction(
            function () use (
                $actor,
                $order,
                $newStatus,
                $description,
                $allowedTransitions
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder =
                    Order::query()
                        ->whereKey(
                            $order->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                $currentStatus =
                    $lockedOrder
                        ->order_status;


                /*
                |--------------------------------------------------------------------------
                | KIỂM TRA TRẠNG THÁI HIỆN TẠI
                |--------------------------------------------------------------------------
                */

                if (
                    !array_key_exists(
                        $currentStatus,
                        $allowedTransitions
                    )
                ) {

                    throw ValidationException::withMessages([
                        'status' =>
                            'Trạng thái hiện tại của đơn hàng không hợp lệ.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | KIỂM TRA CHUYỂN TRẠNG THÁI
                |--------------------------------------------------------------------------
                */

                if (
                    !in_array(
                        $newStatus,
                        $allowedTransitions[
                            $currentStatus
                        ],
                        true
                    )
                ) {

                    throw ValidationException::withMessages([
                        'status' =>
                            'Không thể chuyển đơn hàng từ '
                            . $currentStatus
                            . ' sang '
                            . $newStatus
                            . '.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | QR PHẢI ĐƯỢC XÁC NHẬN THANH TOÁN
                |--------------------------------------------------------------------------
                |
                | Với QR:
                | Staff/Admin phải xác nhận đã nhận tiền
                | trước khi xác nhận Order.
                |
                */

                if (
                    $currentStatus === 'pending'
                    &&
                    $newStatus === 'confirmed'
                    &&
                    $lockedOrder->payment_method
                        === 'QR'
                    &&
                    $lockedOrder->payment_status
                        !== 'paid'
                ) {

                    throw ValidationException::withMessages([
                        'payment' =>
                            'Đơn QR chưa được xác nhận thanh toán.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | SHIPPING -> COMPLETED
                |--------------------------------------------------------------------------
                |
                | COD:
                | Khi giao thành công thì mới Paid.
                |
                | QR:
                | Phải Paid từ trước.
                |
                */

                if (
                    $newStatus === 'completed'
                ) {

                    $payment =
                        Payment::query()
                            ->where(
                                'order_id',
                                $lockedOrder->id
                            )
                            ->latest('id')
                            ->lockForUpdate()
                            ->first();


                    if (!$payment) {

                        throw ValidationException::withMessages([
                            'payment' =>
                                'Không tìm thấy thông tin thanh toán của đơn hàng.',
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | COD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $lockedOrder->payment_method
                        === 'COD'
                    ) {

                        if (
                            $payment->status
                            === 'pending'
                        ) {

                            $payment->status =
                                'paid';

                            $payment->confirmed_by =
                                $actor->id;

                            $payment->paid_at =
                                now();

                            $payment->note =
                                $this->appendNote(
                                    $payment->note,
                                    'COD đã được xác nhận thanh toán khi giao hàng hoàn tất.'
                                );

                            $payment->save();
                        }


                        elseif (
                            $payment->status
                            !== 'paid'
                        ) {

                            throw ValidationException::withMessages([
                                'payment' =>
                                    'Trạng thái Payment COD không cho phép hoàn thành đơn.',
                            ]);
                        }


                        $lockedOrder->payment_status =
                            'paid';
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | QR
                    |--------------------------------------------------------------------------
                    */

                    elseif (
                        $lockedOrder->payment_method
                        === 'QR'
                    ) {

                        if (
                            $payment->status
                                !== 'paid'
                            ||
                            $lockedOrder
                                ->payment_status
                                !== 'paid'
                        ) {

                            throw ValidationException::withMessages([
                                'payment' =>
                                    'Thanh toán QR chưa được xác nhận.',
                            ]);
                        }
                    }


                    else {

                        throw ValidationException::withMessages([
                            'payment' =>
                                'Phương thức thanh toán không hợp lệ.',
                        ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | UPDATE ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder->order_status =
                    $newStatus;

                $lockedOrder->save();


                /*
                |--------------------------------------------------------------------------
                | HISTORY
                |--------------------------------------------------------------------------
                */

                $historyDescription =
                    $description
                        && trim(
                            $description
                        ) !== ''
                            ? trim(
                                $description
                            )
                            : $this
                                ->defaultDescription(
                                    $newStatus
                                );


                OrderStatusHistory::create([
                    'order_id' =>
                        $lockedOrder->id,

                    'status' =>
                        $newStatus,

                    'description' =>
                        $historyDescription,

                    'updated_by' =>
                        $actor->id,
                ]);


                return $lockedOrder
                    ->fresh([
                        'details',
                        'payments',
                        'statusHistories',
                    ]);
            }
        );
    }


    /**
     * Nội dung History mặc định.
     */
    private function defaultDescription(
        string $status
    ): string {

        return match ($status) {

            'confirmed' =>
                'MiniGrove đã xác nhận đơn hàng.',

            'preparing' =>
                'MiniGrove đang chuẩn bị Terrarium.',

            'packed' =>
                'Đơn hàng đã được đóng gói.',

            'shipping' =>
                'Đơn hàng đang được giao đến Customer.',

            'completed' =>
                'Đơn hàng đã giao thành công.',

            default =>
                'Trạng thái đơn hàng đã được cập nhật.',
        };
    }


    /**
     * Nối ghi chú mà không làm mất
     * ghi chú Payment trước đó.
     */
    private function appendNote(
        ?string $currentNote,
        string $newNote
    ): string {

        $currentNote =
            trim(
                (string)
                $currentNote
            );


        if (
            $currentNote === ''
        ) {

            return $newNote;
        }


        return $currentNote
            . ' '
            . $newNote;
    }
}