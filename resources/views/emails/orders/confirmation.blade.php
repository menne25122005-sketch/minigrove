<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiniGrove - Xác nhận đơn hàng</title>
</head>

<body style="
    margin: 0;
    padding: 0;
    background-color: #f4f7f3;
    font-family: Arial, Helvetica, sans-serif;
    color: #26352d;
">

<div style="
    max-width: 700px;
    margin: 30px auto;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e1e8e2;
">

    {{-- HEADER --}}
    <div style="
        background: #164b35;
        color: white;
        padding: 30px;
        text-align: center;
    ">
        <h1 style="
            margin: 0;
            font-size: 32px;
        ">
            🌿 MiniGrove
        </h1>

        <p style="
            margin: 10px 0 0;
            font-size: 16px;
            color: #dceee4;
        ">
            Nhỏ một khu rừng, lớn một thế giới.
        </p>
    </div>


    {{-- CONTENT --}}
    <div style="padding: 30px;">

        <h2 style="
            margin-top: 0;
            color: #164b35;
            font-size: 28px;
        ">
            Xác nhận đơn hàng
        </h2>

        <p>
            Xin chào
            <strong>{{ $order->customer_name }}</strong>,
        </p>

        <p>
            Cảm ơn bạn đã mua sắm tại MiniGrove.
            Chúng tôi đã tiếp nhận đơn hàng của bạn.
        </p>


        {{-- ORDER INFO --}}
        <div style="
            margin-top: 25px;
            padding: 20px;
            background: #f4f8f4;
            border-radius: 8px;
        ">

            <p style="margin: 6px 0;">
                <strong>Mã đơn hàng:</strong>
                {{ $order->order_code }}
            </p>

            <p style="margin: 6px 0;">
                <strong>Ngày đặt:</strong>
                {{ $order->created_at?->format('d/m/Y H:i') }}
            </p>

            <p style="margin: 6px 0;">
                <strong>Người nhận:</strong>
                {{ $order->customer_name }}
            </p>

            <p style="margin: 6px 0;">
                <strong>Số điện thoại:</strong>
                {{ $order->phone }}
            </p>

            <p style="margin: 6px 0;">
                <strong>Email:</strong>
                {{ $order->email }}
            </p>

            <p style="margin: 6px 0;">
                <strong>Địa chỉ:</strong>
                {{ $order->shipping_address }}
            </p>

        </div>


        {{-- PRODUCTS --}}
        <h3 style="
            margin-top: 30px;
            color: #164b35;
            font-size: 22px;
        ">
            Sản phẩm
        </h3>

        <table
            width="100%"
            cellpadding="10"
            cellspacing="0"
            style="
                border-collapse: collapse;
                border: 1px solid #dfe7e1;
            "
        >

            <thead>

                <tr style="
                    background: #164b35;
                    color: white;
                ">

                    <th align="left">
                        Sản phẩm
                    </th>

                    <th align="center">
                        SL
                    </th>

                    <th align="right">
                        Đơn giá
                    </th>

                    <th align="right">
                        Thành tiền
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach ($order->details as $detail)

                    <tr style="
                        border-bottom: 1px solid #e5ebe6;
                    ">

                        <td>
                            <strong>
                                {{ $detail->item_name }}
                            </strong>

                            @if ($detail->item_code)
                                <br>

                                <small style="color: #718078;">
                                    SKU: {{ $detail->item_code }}
                                </small>
                            @endif
                        </td>

                        <td align="center">
                            {{ $detail->quantity }}
                        </td>

                        <td align="right">
                            {{ number_format($detail->unit_price, 0, ',', '.') }}đ
                        </td>

                        <td align="right">
                            <strong>
                                {{ number_format($detail->subtotal, 0, ',', '.') }}đ
                            </strong>
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>


        {{-- TOTAL --}}
        <div style="
            margin-top: 25px;
            text-align: right;
        ">

            <p>
                Tạm tính:
                <strong>
                    {{ number_format($order->subtotal, 0, ',', '.') }}đ
                </strong>
            </p>

            <p>
                Giảm giá:
                <strong>
                    -{{ number_format($order->discount_amount, 0, ',', '.') }}đ
                </strong>
            </p>

            <p>
                Phí vận chuyển:
                <strong>
                    {{ number_format($order->shipping_fee, 0, ',', '.') }}đ
                </strong>
            </p>

            <p style="
                font-size: 22px;
                color: #164b35;
            ">
                Tổng thanh toán:
                <strong>
                    {{ number_format($order->total_amount, 0, ',', '.') }}đ
                </strong>
            </p>

        </div>


        {{-- PAYMENT --}}
        <div style="
            margin-top: 25px;
            padding: 20px;
            background: #f4f8f4;
            border-radius: 8px;
        ">

            <h3 style="
                margin-top: 0;
                color: #164b35;
            ">
                Thanh toán
            </h3>

            <p>
                Phương thức:
                <strong>
                    {{ $order->payment_method }}
                </strong>
            </p>

            <p>
                Trạng thái thanh toán:
                <strong>
                    {{ $order->payment_status }}
                </strong>
            </p>

            <p>
                Trạng thái đơn hàng:
                <strong>
                    {{ $order->order_status }}
                </strong>
            </p>

        </div>


        <p style="
            margin-top: 30px;
        ">
            Cảm ơn bạn đã lựa chọn MiniGrove 🌿
        </p>

        <p style="
            color: #718078;
            font-size: 14px;
        ">
            Đây là email tự động, vui lòng không trả lời email này.
        </p>

    </div>


    {{-- FOOTER --}}
    <div style="
        background: #f0f4f1;
        padding: 20px;
        text-align: center;
        color: #718078;
        font-size: 13px;
    ">

        MiniGrove © {{ date('Y') }}

    </div>

</div>

</body>
</html>