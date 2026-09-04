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
                        ◈
                    </div>


                    <div>

                        <h1>
                            Thanh toán QR
                        </h1>

                        <p>
                            Quét mã QR hoặc chuyển khoản theo đúng
                            thông tin của đơn hàng bên dưới.
                        </p>

                    </div>

                </div>


                {{-- =================================================
                     VIETQR
                ================================================== --}}

                @php

                    /*
                    |--------------------------------------------------------------------------
                    | BANK CODE
                    |--------------------------------------------------------------------------
                    |
                    | MiniGrove hiện đang cấu hình MB Bank.
                    | VietQR sử dụng mã ngân hàng MB.
                    |
                    */

                    $bankCode = 'MB';


                    /*
                    |--------------------------------------------------------------------------
                    | QR DATA
                    |--------------------------------------------------------------------------
                    */

                    $qrAmount =
                        (int) round(
                            (float) $payment->amount
                        );


                    $qrContent =
                        $payment->transfer_content
                        ?: (
                            'MINIGROVE '
                            . $order->order_code
                        );


                    $qrAccountName =
                        $payment->account_name
                        ?: 'MINIGROVE';


                    /*
                    |--------------------------------------------------------------------------
                    | VIETQR URL
                    |--------------------------------------------------------------------------
                    */

                    $qrUrl =
                        'https://img.vietqr.io/image/'
                        . $bankCode
                        . '-'
                        . $payment->account_number
                        . '-compact2.png'
                        . '?amount='
                        . $qrAmount
                        . '&addInfo='
                        . urlencode($qrContent)
                        . '&accountName='
                        . urlencode($qrAccountName);

                @endphp


                <div class="checkout-qr-code-area">

                    <div class="checkout-qr-real">

                        <img
                            src="{{ $qrUrl }}"
                            alt="QR thanh toán đơn {{ $order->order_code }}"
                            class="checkout-qr-real-image"
                        >

                        <div class="checkout-qr-real-label">

                            Quét QR để thanh toán

                            <strong>
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

                    </div>

                </div>


                <div class="checkout-qr-instruction">

                    <strong>
                        Hướng dẫn thanh toán
                    </strong>

                    <p>
                        Mở ứng dụng ngân hàng, quét mã QR và kiểm tra
                        số tiền cùng nội dung chuyển khoản trước khi
                        xác nhận giao dịch.
                    </p>

                    <p>
                        Vui lòng giữ nguyên nội dung chuyển khoản
                        để MiniGrove đối chiếu đúng đơn hàng.
                    </p>

                    <p>
                        Trang này sẽ tự động kiểm tra trạng thái.
                        Khi Casso xác nhận giao dịch thành công,
                        MiniGrove sẽ tự chuyển sang trang
                        <strong>Đặt hàng thành công</strong>.
                    </p>

                </div>

            </div>


            {{-- RIGHT --}}
            <div class="checkout-qr-detail">


                <div
                    class="checkout-qr-status"
                    id="qrPaymentStatus"
                >

                    <span
                        class="checkout-qr-status-dot"
                        id="qrPaymentStatusDot"
                    ></span>

                    <span id="qrPaymentStatusText">
                        @if ($order->payment_status === 'paid')
                            Đã thanh toán
                        @else
                            Đang chờ Casso xác nhận tự động
                        @endif
                    </span>

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

                        <strong class="checkout-qr-money">
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

                        <strong class="checkout-qr-transfer">
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


                <div
                    class="checkout-qr-warning"
                    id="qrPaymentNotice"
                >

                    @if ($order->payment_status === 'paid')

                        Thanh toán đã được hệ thống xác nhận
                        tự động qua Casso.

                    @else

                        MiniGrove đang chờ Casso Webhook
                        xác nhận giao dịch.
                        <strong>
                            Admin và Staff không xác nhận
                            thanh toán thủ công.
                        </strong>

                    @endif

                </div>


                <div class="checkout-qr-actions">

                    <a
                        href="{{ route('products.index') }}"
                        class="checkout-secondary-button"
                    >
                        Tiếp tục mua sắm
                    </a>


                    <a
                        href="{{ route('home') }}"
                        class="checkout-success-main-button"
                    >
                        Về trang chủ
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const statusUrl = @json(
        route(
            'checkout.qr.status',
            $order
        )
    );

    const initialPaid = @json(
        $order->payment_status === 'paid'
    );

    const statusText =
        document.getElementById(
            'qrPaymentStatusText'
        );

    const notice =
        document.getElementById(
            'qrPaymentNotice'
        );

    let isRedirecting = false;


    function goToSuccess(url) {

        if (isRedirecting) {
            return;
        }

        isRedirecting = true;

        if (statusText) {
            statusText.textContent =
                'Đã thanh toán - đang chuyển trang...';
        }

        if (notice) {
            notice.innerHTML =
                'Casso đã xác nhận giao dịch thành công. '
                + '<strong>MiniGrove đang chuyển tới trang '
                + 'Đặt hàng thành công.</strong>';
        }

        window.setTimeout(
            function () {
                window.location.href = url;
            },
            700
        );
    }


    async function checkPaymentStatus() {

        if (isRedirecting) {
            return;
        }

        try {

            const response =
                await fetch(
                    statusUrl,
                    {
                        method: 'GET',
                        headers: {
                            'Accept':
                                'application/json'
                        },
                        credentials:
                            'same-origin',
                        cache:
                            'no-store'
                    }
                );


            if (!response.ok) {
                return;
            }


            const data =
                await response.json();


            if (
                data.success
                &&
                data.paid
                &&
                data.success_url
            ) {
                goToSuccess(
                    data.success_url
                );
            }

        } catch (error) {

            /*
             * Nếu mất mạng tạm thời,
             * giữ trang QR và thử lại ở lần polling sau.
             */

        }
    }


    if (initialPaid) {

        checkPaymentStatus();

        return;
    }


    /*
     * Kiểm tra ngay khi trang vừa mở.
     */
    checkPaymentStatus();


    /*
     * Sau đó kiểm tra mỗi 3 giây.
     */
    window.setInterval(
        checkPaymentStatus,
        3000
    );

});
</script>

@endsection
