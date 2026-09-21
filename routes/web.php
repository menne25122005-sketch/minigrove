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
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReturnRequestController as AdminReturnRequestController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\GroveController;
use App\Http\Controllers\Admin\CompatibilityRuleController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;



use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ReturnRequestController as CustomerReturnRequestController;
use App\Http\Controllers\Customer\GroveController as CustomerGroveController;
use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Models\Review;


/*
|--------------------------------------------------------------------------
| Explicit Route Model Binding
|--------------------------------------------------------------------------
|
| Đảm bảo {review} luôn được resolve thành App\Models\Review.
|
*/
Route::model('review', Review::class);


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
/*
 * ---------------------------------------------------------------
 * Kiểm tra trạng thái thanh toán QR
 * ---------------------------------------------------------------
 */

Route::get(
    '/checkout/qr/{order}/status',
    [
        CheckoutController::class,
        'qrStatus',
    ]
)->name(
    'checkout.qr.status'
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
        |--------------------------------------------------------------------------
        | My Grove
        |--------------------------------------------------------------------------
        |
        | Customer chỉ được xem Grove thuộc chính tài khoản của mình.
        |
        */

        Route::get(
            '/groves',
            [
                CustomerGroveController::class,
                'index',
            ]
        )->name(
            'groves.index'
        );


        Route::get(
            '/groves/{grove}',
            [
                CustomerGroveController::class,
                'show',
            ]
        )->whereNumber(
            'grove'
        )->name(
            'groves.show'
        );



        /*
        |--------------------------------------------------------------------------
        | Customer Chat
        |--------------------------------------------------------------------------
        |
        | Customer chỉ được truy cập hội thoại của chính mình.
        |
        */

        Route::get(
            '/chat',
            [
                CustomerChatController::class,
                'index',
            ]
        )->name(
            'chat.index'
        );


        Route::post(
            '/chat',
            [
                CustomerChatController::class,
                'store',
            ]
        )->name(
            'chat.store'
        );



        /*
|--------------------------------------------------------------------------
| Customer Reviews
|--------------------------------------------------------------------------
|
| Customer chỉ được đánh giá Product đã mua.
|
*/

Route::post(
    '/products/{product}/reviews',
    [
        CustomerReviewController::class,
        'store',
    ]
)->name(
    'customer.reviews.store'
);


/*
 * ---------------------------------------------------------------
 * Sửa Review
 * ---------------------------------------------------------------
 *
 * Customer chỉ được sửa Review của chính mình.
 */

Route::patch(
    '/products/{product}/reviews/{review}',
    [
        CustomerReviewController::class,
        'update',
    ]
)->whereNumber(
    'review'
)->name(
    'customer.reviews.update'
);


/*
 * ---------------------------------------------------------------
 * Xóa Review
 * ---------------------------------------------------------------
 *
 * Customer chỉ được xóa Review của chính mình.
 */

Route::delete(
    '/products/{product}/reviews/{review}',
    [
        CustomerReviewController::class,
        'destroy',
    ]
)->whereNumber(
    'review'
)->name(
    'customer.reviews.destroy'
);




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
         * ---------------------------------------------------------------
         * Customer Support Chat
         * ---------------------------------------------------------------
         *
         * Admin xử lý hội thoại với Customer.
         * Không còn sử dụng Staff.
         */

        Route::get(
            '/chat',
            [AdminChatController::class, 'index']
        )->name(
            'chat.index'
        );

        Route::get(
            '/chat/{conversation}',
            [AdminChatController::class, 'show']
        )->name(
            'chat.show'
        );

        Route::post(
            '/chat/{conversation}/accept',
            [AdminChatController::class, 'accept']
        )->name(
            'chat.accept'
        );

        Route::post(
            '/chat/{conversation}/reply',
            [AdminChatController::class, 'reply']
        )->name(
            'chat.reply'
        );

        Route::post(
            '/chat/{conversation}/close',
            [AdminChatController::class, 'close']
        )->name(
            'chat.close'
        );

    /*
 * ---------------------------------------------------------------
 * Reports
 * ---------------------------------------------------------------
 *
 * Báo cáo & thống kê dành cho Admin.
 * Phạm vi TV3.
 */

Route::get(
    '/reports',
    [ReportController::class, 'index']
)->name(
    'reports.index'
);

        /*
         * ---------------------------------------------------------------
         * Review Management
         * ---------------------------------------------------------------
         *
         * Admin quản lý đánh giá sản phẩm.
         *
         * Privacy:
         * - Chỉ hiển thị Customer #ID.
         * - Không hiển thị PII của Customer.
         * - Cho phép ẩn / hiện Review.
         */

        Route::get(
            '/reviews',
            [AdminReviewController::class, 'index']
        )->name(
            'reviews.index'
        );


        Route::patch(
            '/reviews/{review}/toggle-visibility',
            [AdminReviewController::class, 'toggleVisibility']
        )->name(
            'reviews.toggle-visibility'
        );
        Route::post(
    '/reviews/{review}/reply',
    [AdminReviewController::class, 'reply']
)->name(
    'reviews.reply'
);


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


Route::post(
    '/stocks/{stockType}/{itemId}/change',
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
|--------------------------------------------------------------------------
| Grove Management
|--------------------------------------------------------------------------
|
| Quản lý Grove của khách hàng.
|
| Privacy:
| - Chỉ hiển thị Customer ID.
| - Không hiển thị thông tin cá nhân Customer.
|
*/


Route::get(
    '/groves',
    [
        GroveController::class,
        'index',
    ]
)->name(
    'groves.index'
);


Route::get(
    '/groves/{grove}',
    [
        GroveController::class,
        'show',
    ]
)->whereNumber(
    'grove'
)->name(
    'groves.show'
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
|--------------------------------------------------------------------------
| Product Compatibility Rules
|--------------------------------------------------------------------------
*/
Route::patch(
    'compatibility-rules/{compatibilityRule}/toggle-status',
    [CompatibilityRuleController::class, 'toggleStatus']
)->name(
    'compatibility-rules.toggle-status'
);
Route::resource(
    'compatibility-rules',
    CompatibilityRuleController::class
)
    ->except('show')
    ->parameters([
        'compatibility-rules' => 'compatibilityRule',
    ]);



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
