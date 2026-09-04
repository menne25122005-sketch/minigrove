@extends('layouts.customer')


@section(
    'title',
    'Thanh toán QR - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/checkout.css') }}"
>

@endpush


@section('content')

<section class="checkout-page">

    <div class="customer-container">


        {{-- =====================================================
             BACK
        ====================================================== --}}

        <a
            href="{{ route('home') }}"
            class="checkout-back-button"
        >
            ← Về trang chủ
        </a>


        {{-- =====================================================
             QR PAYMENT
        ====================================================== --}}

        <div class="checkout-qr-page">


            {{-- LEFT --}}
            <div class="checkout-qr-main">


                <div class="checkout-qr-heading">

                    <div class="checkout-qr-heading-icon">
                       ▣
                    </div>


                    <div>

                        <h1>
                            Thanh toán QR
                        </h1>

                        <p>
                            Quét mã hoặc chuyển khoản
                            theo thông tin bên dưới.
                        </p>

                    </div>

                </div>


                {{-- QR DEMO --}}
                <div class="checkout-qr-code-area">

                    <div class="checkout-qr-code">

                        <div class="checkout-qr-pattern">

                            <span>
                                QR
                            </span>

                        </div>


                        <div class="checkout-qr-demo-label">
                            QR MÔ PHỎNG
                        </div>

                    </div>

                </div>


                <div class="checkout-qr-instruction">

                    <strong>
                        Hướng dẫn thanh toán
                    </strong>

                    <p>
                        Mở ứng dụng ngân hàng,
                        chọn quét QR hoặc nhập
                        thông tin chuyển khoản
                        chính xác như bên cạnh.
                    </p>

                    <p>
                        Vui lòng giữ nguyên
                        nội dung chuyển khoản
                        để MiniGrove đối chiếu
                        đơn hàng.
                    </p>

                </div>

            </div>


            {{-- RIGHT --}}
            <div class="checkout-qr-detail">


                <div class="checkout-qr-status">

                    <span class="checkout-qr-status-dot">
                    </span>

                    Chờ xác nhận thanh toán

                </div>


                <div class="checkout-qr-order-code">

                    <span>
                        Mã đơn hàng
                    </span>

                    <strong>
                        {{ $order->order_code }}
                    </strong>

                </div>


                <div class="checkout-qr-data">


                    <div>

                        <span>
                            Ngân hàng
                        </span>

                        <strong>
                            {{
                                $payment->bank_name
                                ?: 'Chưa cấu hình'
                            }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Chủ tài khoản
                        </span>

                        <strong>
                            {{
                                $payment->account_name
                                ?: 'Chưa cấu hình'
                            }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Số tài khoản
                        </span>

                        <strong>
                            {{
                                $payment->account_number
                                ?: 'Chưa cấu hình'
                            }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Số tiền
                        </span>

                        <strong
                            class="
                                checkout-qr-money
                            "
                        >

                            {{
                                number_format(
                                    $payment->amount,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </strong>

                    </div>


                    <div>

                        <span>
                            Nội dung chuyển khoản
                        </span>

                        <strong
                            class="
                                checkout-qr-transfer
                            "
                        >

                            {{
                                $payment->transfer_content
                                ?: (
                                    'MINIGROVE '
                                    . $order->order_code
                                )
                            }}

                        </strong>

                    </div>

                </div>


                <div class="checkout-qr-warning">

                    Sau khi chuyển khoản,
                    đơn hàng vẫn ở trạng thái
                    <strong>chờ xác nhận</strong>.

                    Staff/Admin MiniGrove sẽ
                    kiểm tra giao dịch và xác nhận
                    thanh toán.

                </div>


                <div class="checkout-qr-actions">

                    <a
                        href="{{ route('products.index') }}"
                        class="
                            checkout-secondary-button
                        "
                    >
                        Tiếp tục mua sắm
                    </a>


                    <a
                        href="{{ route('home') }}"
                        class="
                            checkout-success-main-button
                        "
                    >
                        Về trang chủ
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection