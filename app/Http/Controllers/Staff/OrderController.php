<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng dành cho Staff.
     *
     * Không lấy thông tin cá nhân Customer.
     */
    public function index(
        Request $request
    ): View {

        $status =
            $request->query('status');

        $paymentMethod =
            $request->query('payment_method');

        $keyword =
            trim(
                (string) $request->query(
                    'keyword',
                    ''
                )
            );


        $allowedStatuses = [
            'pending',
            'confirmed',
            'preparing',
            'packed',
            'shipping',
            'completed',
            'cancelled',
        ];


        $allowedPaymentMethods = [
            'COD',
            'QR',
        ];


        /*
        |--------------------------------------------------------------------------
        | QUERY PRIVACY-SAFE
        |--------------------------------------------------------------------------
        |
        | Staff không được lấy:
        | - customer_name
        | - email
        | - phone
        | - shipping_address
        | - note
        |
        | Không load relation user.
        |
        */

        $query =
            Order::query()
                ->select([
                    'id',
                    'order_code',
                    'user_id',
                    'total_amount',
                    'payment_method',
                    'payment_status',
                    'order_status',
                    'created_at',
                ])
                ->withCount('details')
                ->latest('id');


        /*
        |--------------------------------------------------------------------------
        | LỌC TRẠNG THÁI
        |--------------------------------------------------------------------------
        */

        if (
            $status
            &&
            in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            $query->where(
                'order_status',
                $status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LỌC PHƯƠNG THỨC THANH TOÁN
        |--------------------------------------------------------------------------
        */

        if (
            $paymentMethod
            &&
            in_array(
                $paymentMethod,
                $allowedPaymentMethods,
                true
            )
        ) {
            $query->where(
                'payment_method',
                $paymentMethod
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TÌM KIẾM
        |--------------------------------------------------------------------------
        |
        | Staff chỉ tìm:
        | - mã đơn
        | - Customer ID
        |
        */

        if ($keyword !== '') {

            $query->where(
                function ($query) use ($keyword) {

                    $query->where(
                        'order_code',
                        'like',
                        '%' . $keyword . '%'
                    );


                    if (
                        ctype_digit(
                            $keyword
                        )
                    ) {
                        $query->orWhere(
                            'user_id',
                            (int) $keyword
                        );
                    }
                }
            );
        }


        $orders =
            $query
                ->paginate(15)
                ->withQueryString();


        return view(
            'staff.orders.index',
            compact(
                'orders',
                'status',
                'paymentMethod',
                'keyword'
            )
        );
    }


    /**
     * Chi tiết đơn hàng dành cho Staff.
     *
     * Không dùng implicit model binding để tránh
     * tải toàn bộ bản ghi Order có PII.
     */
    public function show(
        int $order
    ): View {

        $order =
            Order::query()
                ->select([
                    'id',
                    'order_code',
                    'user_id',

                    'subtotal',
                    'discount_amount',
                    'shipping_fee',
                    'total_amount',

                    'payment_method',
                    'payment_status',
                    'order_status',

                    'created_at',
                ])
                ->with([

                    /*
                     * Sản phẩm trong đơn.
                     */
                    'details.product.images',


                    /*
                     * Payment.
                     */
                    'payments' =>
                        function ($query) {
                            $query->latest('id');
                        },


                    /*
                     * Timeline trạng thái.
                     */
                    'statusHistories' =>
                        function ($query) {

                            $query
                                ->orderBy('created_at')
                                ->orderBy('id');
                        },


                    /*
                     * Voucher.
                     */
                    'voucherUsage.voucher',


                    /*
                     * Return Request:
                     * chỉ lấy dữ liệu nghiệp vụ cần thiết.
                     */
                    'returnRequests' =>
                        function ($query) {

                            $query
                                ->select([
                                    'id',
                                    'order_id',
                                    'return_code',
                                    'status',
                                    'refund_amount',
                                    'created_at',
                                ])
                                ->latest('id');
                        },
                ])
                ->whereKey($order)
                ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | TRẠNG THÁI TIẾP THEO
        |--------------------------------------------------------------------------
        */

        $nextStatus =
            match (
                $order->order_status
            ) {

                'pending' =>
                    'confirmed',

                'confirmed' =>
                    'preparing',

                'preparing' =>
                    'packed',

                'packed' =>
                    'shipping',

                'shipping' =>
                    'completed',

                default =>
                    null,
            };


        return view(
            'staff.orders.show',
            compact(
                'order',
                'nextStatus'
            )
        );
    }


    /**
     * Staff cập nhật trạng thái đơn hàng.
     *
     * Không dùng Order $order implicit binding.
     */
    public function updateStatus(
        Request $request,
        int $order,
        OrderWorkflowService $service
    ): RedirectResponse {

        $validated =
            $request->validate([
                'status' => [
                    'required',
                    'in:confirmed,preparing,packed,shipping,completed',
                ],

                'description' => [
                    'nullable',
                    'string',
                    'max:500',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | LOAD ORDER TỐI THIỂU
        |--------------------------------------------------------------------------
        */

        $orderModel =
            Order::query()
                ->select([
                    'id',
                    'order_status',
                    'payment_method',
                    'payment_status',
                ])
                ->whereKey($order)
                ->firstOrFail();


        try {

            $service->updateStatus(
                $request->user(),
                $orderModel,
                $validated['status'],
                $validated['description']
                    ?? null
            );

        } catch (
            ValidationException $e
        ) {

            return redirect()
                ->route(
                    'staff.orders.show',
                    $orderModel->id
                )
                ->with(
                    'error',
                    $e
                        ->validator
                        ->errors()
                        ->first()
                );
        }


        return redirect()
            ->route(
                'staff.orders.show',
                $orderModel->id
            )
            ->with(
                'success',
                'Trạng thái đơn hàng đã được cập nhật.'
            );
    }
}