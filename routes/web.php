<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AddressController;

use App\Http\Controllers\Shop\ProductController as ShopProductController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ComponentController;
use App\Http\Controllers\Admin\ComponentCompatibilityRuleController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReturnRequestController as AdminReturnRequestController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\StaffController;


use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\OrderController as StaffOrderController;

use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ReturnRequestController as CustomerReturnRequestController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Trang chủ MiniGrove
|--------------------------------------------------------------------------
|
| Guest / Customer / Staff / Admin
| đều có thể xem trang chủ.
|
*/

Route::get(
    '/',
    [HomeController::class, 'index']
)->name('home');


/*
|--------------------------------------------------------------------------
| MiniGrove Studio
|--------------------------------------------------------------------------
|
| Guest vẫn được:
| - xem Studio
| - chọn thành phần
| - xem Preview
|
| Việc lưu thiết kế sẽ yêu cầu
| Customer đăng nhập.
|
*/

Route::get(
    '/studio',
    [StudioController::class, 'index']
)->name('studio.index');


Route::post(
    '/studio/preview',
    [StudioController::class, 'preview']
)->name('studio.preview');


/*
|--------------------------------------------------------------------------
| Product - Customer / Guest
|--------------------------------------------------------------------------
|
| Không yêu cầu đăng nhập.
|
| Guest vẫn được:
| - xem danh sách sản phẩm
| - tìm kiếm
| - lọc
| - sắp xếp
| - xem chi tiết sản phẩm
|
*/

Route::get(
    '/products',
    [ShopProductController::class, 'index']
)->name('products.index');


Route::get(
    '/products/{product}',
    [ShopProductController::class, 'show']
)->name('products.show');


/*
|--------------------------------------------------------------------------
| Cart - Customer
|--------------------------------------------------------------------------
|
| Chỉ Customer đã đăng nhập mới được:
| - xem giỏ hàng
| - thêm sản phẩm
| - cập nhật số lượng
| - xóa sản phẩm
| - xóa nhiều sản phẩm đã chọn
|
| Guest khi bấm mua / thêm giỏ
| sẽ được chuyển tới Login.
|
| Staff / Admin không dùng tài khoản
| quản trị để mua hàng.
|
*/

Route::middleware([
    'auth',
    'role:customer',
])
    ->group(function () {


        /*
         * ---------------------------------------------------------------
         * Xem giỏ hàng
         * ---------------------------------------------------------------
         */

        Route::get(
            '/cart',
            [CartController::class, 'index']
        )->name(
            'cart.index'
        );


        /*
         * ---------------------------------------------------------------
         * Thêm Product vào Cart
         * ---------------------------------------------------------------
         */

        Route::post(
            '/cart/products/{product}',
            [CartController::class, 'add']
        )->name(
            'cart.add'
        );


        /*
         * ---------------------------------------------------------------
         * Cập nhật số lượng CartItem
         * ---------------------------------------------------------------
         */

        Route::patch(
            '/cart/items/{cartItem}',
            [CartController::class, 'update']
        )->name(
            'cart.update'
        );


        /*
         * ---------------------------------------------------------------
         * Xóa nhiều CartItem được chọn
         * ---------------------------------------------------------------
         */

        Route::post(
            '/cart/selected/delete',
            [
                CartController::class,
                'destroySelected',
            ]
        )->name(
            'cart.selected.destroy'
        );


        /*
         * ---------------------------------------------------------------
         * Xóa một CartItem
         * ---------------------------------------------------------------
         */

        Route::delete(
            '/cart/items/{cartItem}',
            [CartController::class, 'destroy']
        )->name(
            'cart.destroy'
        );

    });


/*
|--------------------------------------------------------------------------
| Checkout - Customer
|--------------------------------------------------------------------------
|
| Chỉ Customer đã đăng nhập mới được:
|
| - mua các sản phẩm đã tick trong Cart
| - Mua ngay một Product
| - xem Checkout
| - thêm địa chỉ nhận hàng
| - chọn địa chỉ nhận hàng
| - áp dụng Voucher
| - bỏ Voucher
| - đặt hàng thật
| - xem trang đặt hàng thành công
| - xem thanh toán QR của đơn mình
|
*/

Route::middleware([
    'auth',
    'role:customer',
])
    ->group(function () {


        /*
         * ---------------------------------------------------------------
         * Chuẩn bị Checkout từ Cart
         * ---------------------------------------------------------------
         *
         * Chỉ những CartItem Customer đã tick
         * mới được đưa sang Checkout.
         */

        Route::post(
            '/checkout/cart',
            [
                CheckoutController::class,
                'prepareCart',
            ]
        )->name(
            'checkout.cart'
        );


        /*
         * ---------------------------------------------------------------
         * Mua ngay một Product
         * ---------------------------------------------------------------
         *
         * Không thêm Product vào Cart
         * và không ảnh hưởng Cart hiện tại.
         */

        Route::post(
            '/checkout/buy-now/{product}',
            [
                CheckoutController::class,
                'prepareBuyNow',
            ]
        )->name(
            'checkout.buy-now'
        );


        /*
         * ---------------------------------------------------------------
         * Trang Checkout
         * ---------------------------------------------------------------
         */

        Route::get(
            '/checkout',
            [
                CheckoutController::class,
                'index',
            ]
        )->name(
            'checkout.index'
        );


        /*
         * ---------------------------------------------------------------
         * Thêm địa chỉ giao hàng
         * ---------------------------------------------------------------
         */

        Route::post(
            '/checkout/address',
            [
                AddressController::class,
                'store',
            ]
        )->name(
            'checkout.address.store'
        );


        /*
         * ---------------------------------------------------------------
         * Chọn địa chỉ giao hàng
         * ---------------------------------------------------------------
         */

        Route::post(
            '/checkout/address/select',
            [
                AddressController::class,
                'select',
            ]
        )->name(
            'checkout.address.select'
        );


        /*
         * ---------------------------------------------------------------
         * Áp dụng Voucher
         * ---------------------------------------------------------------
         */

        Route::post(
            '/checkout/voucher',
            [
                CheckoutController::class,
                'applyVoucher',
            ]
        )->name(
            'checkout.voucher.apply'
        );


        /*
         * ---------------------------------------------------------------
         * Bỏ Voucher
         * ---------------------------------------------------------------
         */

        Route::delete(
            '/checkout/voucher',
            [
                CheckoutController::class,
                'removeVoucher',
            ]
        )->name(
            'checkout.voucher.remove'
        );


        /*
         * ---------------------------------------------------------------
         * Đặt hàng thật
         * ---------------------------------------------------------------
         *
         * CheckoutController gọi OrderService
         * để chạy Database Transaction:
         *
         * - kiểm tra Address
         * - kiểm tra giá
         * - kiểm tra Stock
         * - kiểm tra Voucher
         * - tạo Order
         * - tạo OrderDetail
         * - tạo Payment
         * - ghi OrderStatusHistory
         * - ghi StockHistory
         * - ghi VoucherUsage nếu có
         * - trừ tồn kho
         * - xóa đúng CartItem đã mua
         */

        Route::post(
            '/checkout/place-order',
            [
                CheckoutController::class,
                'placeOrder',
            ]
        )->name(
            'checkout.place-order'
        );


        /*
         * ---------------------------------------------------------------
         * Trang đặt hàng thành công
         * ---------------------------------------------------------------
         *
         * Controller phải kiểm tra Order
         * thuộc Customer hiện tại.
         */

        Route::get(
            '/checkout/success/{order}',
            [
                CheckoutController::class,
                'success',
            ]
        )->name(
            'checkout.success'
        );


        /*
         * ---------------------------------------------------------------
         * Thanh toán QR
         * ---------------------------------------------------------------
         *
         * Chỉ Order có payment_method = QR
         * và thuộc Customer hiện tại mới xem được.
         */

        Route::get(
            '/checkout/qr/{order}',
            [
                CheckoutController::class,
                'qr',
            ]
        )->name(
            'checkout.qr'
        );

    });


/*
|--------------------------------------------------------------------------
| Customer Orders
|--------------------------------------------------------------------------
|
| Chỉ Customer đã đăng nhập.
|
| Gồm:
|
| - Đơn hàng của tôi
| - Xem chi tiết Order
| - Hủy Order khi còn Pending
| - Tạo yêu cầu trả hàng / hoàn tiền
| - Xem yêu cầu trả hàng / hoàn tiền
|
*/

Route::middleware([
    'auth',
    'role:customer',
])
    ->group(function () {


        /*
         * ---------------------------------------------------------------
         * Đơn hàng của tôi
         * ---------------------------------------------------------------
         *
         * Giao diện sử dụng 7 tab:
         *
         * - Tất cả
         * - Chờ xác nhận
         * - Đang chuẩn bị
         * - Đang giao
         * - Hoàn thành
         * - Đã hủy
         * - Trả hàng / Hoàn tiền
         */

        Route::get(
            '/orders',
            [
                CustomerOrderController::class,
                'index',
            ]
        )->name(
            'orders.index'
        );


        /*
         * ---------------------------------------------------------------
         * Chi tiết Order
         * ---------------------------------------------------------------
         *
         * Customer chỉ được xem
         * Order thuộc chính mình.
         */

        Route::get(
            '/orders/{order}',
            [
                CustomerOrderController::class,
                'show',
            ]
        )->name(
            'orders.show'
        );


        /*
         * ---------------------------------------------------------------
         * Customer hủy Order
         * ---------------------------------------------------------------
         *
         * Chỉ Order Status = pending
         * mới được Customer hủy.
         *
         * Khi hủy:
         *
         * - Order -> cancelled
         * - hoàn Stock
         * - xử lý Payment
         * - release Voucher nếu có
         * - ghi OrderStatusHistory
         * - ghi StockHistory
         */

        Route::post(
            '/orders/{order}/cancel',
            [
                CustomerOrderController::class,
                'cancel',
            ]
        )->name(
            'orders.cancel'
        );


        /*
         * ---------------------------------------------------------------
         * Form yêu cầu trả hàng / hoàn tiền
         * ---------------------------------------------------------------
         *
         * Chỉ Order = completed
         * mới được tạo yêu cầu.
         */

        Route::get(
            '/orders/{order}/return',
            [
                CustomerReturnRequestController::class,
                'create',
            ]
        )->name(
            'returns.create'
        );


        /*
         * ---------------------------------------------------------------
         * Gửi yêu cầu trả hàng / hoàn tiền
         * ---------------------------------------------------------------
         */

        Route::post(
            '/orders/{order}/return',
            [
                CustomerReturnRequestController::class,
                'store',
            ]
        )->name(
            'returns.store'
        );


        /*
         * ---------------------------------------------------------------
         * Chi tiết yêu cầu trả hàng / hoàn tiền
         * ---------------------------------------------------------------
         *
         * Customer chỉ được xem
         * ReturnRequest của chính mình.
         */

        Route::get(
            '/returns/{returnRequest}',
            [
                CustomerReturnRequestController::class,
                'show',
            ]
        )->name(
            'returns.show'
        );

    });


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
|
| Customer / Staff / Admin đã đăng nhập
| đều có thể truy cập Profile của chính mình.
|
*/

Route::middleware('auth')
    ->group(function () {


        Route::get(
            '/profile',
            [ProfileController::class, 'edit']
        )->name(
            'profile.edit'
        );


        Route::patch(
            '/profile',
            [ProfileController::class, 'update']
        )->name(
            'profile.update'
        );


        Route::delete(
            '/profile',
            [ProfileController::class, 'destroy']
        )->name(
            'profile.destroy'
        );

    });


/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
|
| Điều kiện:
|
| 1. Đã đăng nhập.
| 2. Role phải là admin.
|
| Customer / Staff truy cập /admin/*
| sẽ nhận 403 Forbidden.
|
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'role:admin',
    ])
    ->group(function () {


        /*
         * ---------------------------------------------------------------
         * Dashboard
         * ---------------------------------------------------------------
         */

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name(
            'dashboard'
        );

        /*
|--------------------------------------------------------------------------
| Staff - Nhân viên
|--------------------------------------------------------------------------
*/

Route::get(
    '/staff',
    [StaffController::class, 'index']
)->name('staff.index');


Route::get(
    '/staff/create',
    [StaffController::class, 'create']
)->name('staff.create');


Route::post(
    '/staff',
    [StaffController::class, 'store']
)->name('staff.store');


Route::get(
    '/staff/{staff}/edit',
    [StaffController::class, 'edit']
)->name('staff.edit');


Route::put(
    '/staff/{staff}',
    [StaffController::class, 'update']
)->name('staff.update');


Route::patch(
    '/staff/{staff}/toggle-status',
    [StaffController::class, 'toggleStatus']
)->name('staff.toggle-status');

        /*
|--------------------------------------------------------------------------
| Stock - Tồn kho
|--------------------------------------------------------------------------
*/

Route::get(
    '/stocks',
    [StockController::class, 'index']
)->name(
    'stocks.index'
);


Route::patch(
    '/stocks/{stockType}/{itemId}',
    [StockController::class, 'change']
)->name(
    'stocks.change'
);


Route::patch(
    '/stocks/{stockType}/{itemId}/threshold',
    [StockController::class, 'updateThreshold']
)->name(
    'stocks.threshold.update'
);


Route::get(
    '/stocks/{stockType}/{itemId}/history',
    [StockController::class, 'history']
)->name(
    'stocks.history'
);

/*
|--------------------------------------------------------------------------
| Voucher
|--------------------------------------------------------------------------
*/

Route::patch(
    '/vouchers/{voucher}/toggle-status',
    [VoucherController::class, 'toggleStatus']
)->name(
    'vouchers.toggle-status'
);


Route::get(
    '/vouchers/{voucher}/history',
    [VoucherController::class, 'history']
)->name(
    'vouchers.history'
);


Route::resource(
    'vouchers',
    VoucherController::class
)->except(
    'show'
);

/*
|--------------------------------------------------------------------------
| Payment Management
|--------------------------------------------------------------------------
*/

Route::get(
    '/payments',
    [PaymentController::class, 'index']
)->name(
    'payments.index'
);


Route::patch(
    '/payments/settings/{paymentSetting}',
    [PaymentController::class, 'updateSetting']
)->name(
    'payments.settings.update'
);

/*
 * ---------------------------------------------------------------
 * Order Management
 * ---------------------------------------------------------------
 */


/*
 * Danh sách Order
 */
Route::get(
    '/orders',
    [
        AdminOrderController::class,
        'index',
    ]
)->name(
    'orders.index'
);


/*
 * Chi tiết Order
 */
Route::get(
    '/orders/{order}',
    [
        AdminOrderController::class,
        'show',
    ]
)->name(
    'orders.show'
);


/*
 * Chuyển trạng thái Order
 *
 * pending
 * → confirmed
 * → preparing
 * → packed
 * → shipping
 * → completed
 */
Route::patch(
    '/orders/{order}/status',
    [
        AdminOrderController::class,
        'updateStatus',
    ]
)->name(
    'orders.status.update'
);

/*
 * ---------------------------------------------------------------
 * Return / Refund Management
 * ---------------------------------------------------------------
 */


/*
 * Danh sách yêu cầu trả hàng / hoàn tiền
 */
Route::get(
    '/returns',
    [
        AdminReturnRequestController::class,
        'index',
    ]
)->name(
    'returns.index'
);


/*
 * Chi tiết yêu cầu
 */
Route::get(
    '/returns/{returnRequest}',
    [
        AdminReturnRequestController::class,
        'show',
    ]
)->name(
    'returns.show'
);


/*
 * Pending -> Approved
 */
Route::patch(
    '/returns/{returnRequest}/approve',
    [
        AdminReturnRequestController::class,
        'approve',
    ]
)->name(
    'returns.approve'
);


/*
 * Pending -> Rejected
 */
Route::patch(
    '/returns/{returnRequest}/reject',
    [
        AdminReturnRequestController::class,
        'reject',
    ]
)->name(
    'returns.reject'
);


/*
 * Approved -> Returning
 */
Route::patch(
    '/returns/{returnRequest}/returning',
    [
        AdminReturnRequestController::class,
        'markReturning',
    ]
)->name(
    'returns.returning'
);


/*
 * Returning -> Received
 */
Route::patch(
    '/returns/{returnRequest}/received',
    [
        AdminReturnRequestController::class,
        'markReceived',
    ]
)->name(
    'returns.received'
);


/*
 * Received -> Refunded
 */
Route::patch(
    '/returns/{returnRequest}/refund',
    [
        AdminReturnRequestController::class,
        'refund',
    ]
)->name(
    'returns.refund'
);
/*
 * Admin xác nhận đã nhận tiền QR
 *
 * Payment:
 * pending → paid
 */
Route::patch(
    '/orders/{order}/confirm-qr-payment',
    [
        AdminOrderController::class,
        'confirmQrPayment',
    ]
)->name(
    'orders.payment.confirm-qr'
);

/*
|--------------------------------------------------------------------------
| Customer Management
|--------------------------------------------------------------------------
*/

Route::get(
    '/customers',
    [CustomerController::class, 'index']
)->name(
    'customers.index'
);


Route::get(
    '/customers/{customer}',
    [CustomerController::class, 'show']
)->name(
    'customers.show'
);


Route::patch(
    '/customers/{customer}/toggle-status',
    [CustomerController::class, 'toggleStatus']
)->name(
    'customers.toggle-status'
);

        /*
         * ---------------------------------------------------------------
         * Category
         * ---------------------------------------------------------------
         */

        Route::patch(
            '/categories/{category}/toggle-status',
            [
                CategoryController::class,
                'toggleStatus',
            ]
        )->name(
            'categories.toggle-status'
        );


        Route::resource(
            'categories',
            CategoryController::class
        )->except(
            'show'
        );


        /*
         * ---------------------------------------------------------------
         * Product
         * ---------------------------------------------------------------
         */

        Route::patch(
            '/products/{product}/toggle-status',
            [
                ProductController::class,
                'toggleStatus',
            ]
        )->name(
            'products.toggle-status'
        );


        Route::patch(
            '/products/{product}/images/{image}/primary',
            [
                ProductController::class,
                'setPrimaryImage',
            ]
        )->name(
            'products.images.primary'
        );


        Route::delete(
            '/products/{product}/images/{image}',
            [
                ProductController::class,
                'deleteImage',
            ]
        )->name(
            'products.images.destroy'
        );


        Route::resource(
            'products',
            ProductController::class
        )->except(
            'show'
        );


        /*
         * ---------------------------------------------------------------
         * Component
         * ---------------------------------------------------------------
         */

        Route::patch(
            '/components/{component}/toggle-status',
            [
                ComponentController::class,
                'toggleStatus',
            ]
        )->name(
            'components.toggle-status'
        );


        Route::patch(
            '/components/{component}/images/{image}/primary',
            [
                ComponentController::class,
                'setPrimaryImage',
            ]
        )->name(
            'components.images.primary'
        );


        Route::delete(
            '/components/{component}/images/{image}',
            [
                ComponentController::class,
                'deleteImage',
            ]
        )->name(
            'components.images.destroy'
        );


        Route::resource(
            'components',
            ComponentController::class
        )->except(
            'show'
        );


        /*
         * ---------------------------------------------------------------
         * Component Compatibility Rule
         * ---------------------------------------------------------------
         */

        Route::patch(
            '/compatibility-rules/{compatibilityRule}/toggle-status',
            [
                ComponentCompatibilityRuleController::class,
                'toggleStatus',
            ]
        )->name(
            'compatibility-rules.toggle-status'
        );


        Route::resource(
            'compatibility-rules',
            ComponentCompatibilityRuleController::class
        )
            ->except(
                'show'
            )
            ->parameters([
                'compatibility-rules'
                    => 'compatibilityRule',
            ]);

    });



/*
|--------------------------------------------------------------------------
| Staff
|--------------------------------------------------------------------------
|
| Điều kiện:
|
| 1. Đã đăng nhập.
| 2. Role phải là staff.
|
| Admin / Customer truy cập /staff/*
| sẽ nhận 403 Forbidden.
|
*/

Route::prefix('staff')
    ->name('staff.')
    ->middleware([
        'auth',
        'role:staff',
    ])
    ->group(function () {

        /*
         * ---------------------------------------------------------------
         * Dashboard
         * ---------------------------------------------------------------
         */

        Route::get(
            '/dashboard',
            [StaffDashboardController::class, 'index']
        )->name(
            'dashboard'
        );


        /*
        |--------------------------------------------------------------------------
        | Order Management - Staff
        |--------------------------------------------------------------------------
        |
        | Staff chỉ xử lý dữ liệu nghiệp vụ Order.
        | Không truy cập thông tin cá nhân Customer.
        |
        */

        Route::get(
            '/orders',
            [StaffOrderController::class, 'index']
        )->name(
            'orders.index'
        );


        Route::get(
            '/orders/{order}',
            [StaffOrderController::class, 'show']
        )->name(
            'orders.show'
        );


        Route::patch(
            '/orders/{order}/status',
            [StaffOrderController::class, 'updateStatus']
        )->name(
            'orders.status.update'
        );

    });


/*
|--------------------------------------------------------------------------
| Breeze Authentication
|--------------------------------------------------------------------------
|
| Bao gồm:
|
| - Login
| - Register
| - Logout
| - Forgot Password
| - Reset Password
| - Confirm Password
| - Email Verification
|
*/

require __DIR__ . '/auth.php';