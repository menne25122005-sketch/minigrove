<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Preview - MiniGrove Studio
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family:
                Arial,
                Helvetica,
                sans-serif;
            background: #f5f7f5;
            color: #27332c;
        }

        .preview-container {
            max-width: 1050px;
            margin: 35px auto;
            padding: 0 20px 50px;
        }

        .preview-card {
            background: white;
            border-radius: 14px;
            padding: 28px;
            box-shadow:
                0 3px 15px
                rgba(0, 0, 0, 0.06);
        }

        h1 {
            margin-top: 0;
            color: #28633d;
        }

        .success-box {
            padding: 15px;
            background: #e8f5ec;
            border: 1px solid #bcdac5;
            border-radius: 9px;
            color: #25633a;
            margin-bottom: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e7ebe8;
            text-align: left;
        }

        th {
            background: #f1f5f2;
        }

        .total-box {
            display: flex;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .total {
            min-width: 300px;
            background: #f3f7f4;
            padding: 18px;
            border-radius: 10px;
        }

        .total-price {
            font-size: 25px;
            color: #b34e38;
            font-weight: bold;
        }

        .info-row {
            margin-bottom: 12px;
        }

        .label {
            color: #747d76;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #28633d;
            color: white;
        }

        .btn-secondary {
            background: #e8ece9;
            color: #303a33;
        }

        .notice {
            margin-top: 25px;
            padding: 15px;
            border-radius: 9px;
            background: #fff7df;
            color: #79621b;
        }

        @media (max-width: 700px) {

            .table-wrapper {
                overflow-x: auto;
            }

        }

    </style>

</head>

<body>


<main class="preview-container">

    <div class="preview-card">

        <h1>
            Preview MiniGrove
        </h1>


        <div class="success-box">
            ✓ Cấu hình hợp lệ,
            các thành phần đã vượt qua
            kiểm tra tương thích và tồn kho.
        </div>


        <div class="info-row">

            <span class="label">
                Phong cách:
            </span>

            <strong>

                @switch($style)

                    @case('natural')
                        Tự nhiên
                        @break

                    @case('forest')
                        Rừng xanh
                        @break

                    @case('zen')
                        Zen
                        @break

                    @case('fairy')
                        Cổ tích
                        @break

                    @case('tropical')
                        Nhiệt đới
                        @break

                    @case('minimal')
                        Tối giản
                        @break

                    @default
                        {{ $style }}

                @endswitch

            </strong>

        </div>


        <div class="info-row">

            <span class="label">
                Hệ sinh thái:
            </span>

            <strong>

                @switch(
                    $compatibility[
                        'ecosystem_type'
                    ]
                )

                    @case('closed')
                        Kín
                        @break

                    @case('open')
                        Mở
                        @break

                    @case('dry')
                        Khô
                        @break

                    @default
                        {{
                            $compatibility[
                                'ecosystem_type'
                            ]
                        }}

                @endswitch

            </strong>

        </div>


        <div class="info-row">

            <span class="label">
                Bình:
            </span>

            <strong>
                {{
                    $compatibility[
                        'container'
                    ]['name']
                }}
            </strong>

        </div>


        <div class="info-row">

            <span class="label">
                Số Compatibility Rule đã kiểm tra:
            </span>

            <strong>
                {{
                    $compatibility[
                        'checked_rules'
                    ]
                }}
            </strong>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>Thành phần</th>
                        <th>Loại</th>
                        <th>SKU</th>
                        <th>Đơn giá</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                    </tr>

                </thead>


                <tbody>

                    @foreach ($items as $item)

                        <tr>

                            <td>
                                {{ $item['component_name'] }}
                            </td>

                            <td>

                                @switch(
                                    $item['component_type']
                                )

                                    @case('container')
                                        Bình
                                        @break

                                    @case('plant')
                                        Cây
                                        @break

                                    @case('moss')
                                        Rêu
                                        @break

                                    @case('substrate')
                                        Nền / Giá thể
                                        @break

                                    @case('stone')
                                        Đá
                                        @break

                                    @case('accessory')
                                        Phụ kiện
                                        @break

                                    @default
                                        {{
                                            $item[
                                                'component_type'
                                            ]
                                        }}

                                @endswitch

                            </td>

                            <td>
                                {{ $item['sku'] }}
                            </td>

                            <td>

                                {{ number_format(
                                    $item['unit_price'],
                                    0,
                                    ',',
                                    '.'
                                ) }}đ

                            </td>

                            <td>
                                {{ $item['quantity'] }}
                            </td>

                            <td>

                                <strong>

                                    {{ number_format(
                                        $item['subtotal'],
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="total-box">

            <div class="total">

                <div>
                    Giá tạm tính
                </div>

                <div class="total-price">

                    {{ number_format(
                        $estimatedPrice,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </div>

            </div>

        </div>


        <div class="notice">

            <strong>
                Lưu ý:
            </strong>

            Giá trên là giá tạm tính theo dữ liệu
            Component hiện tại. Giá và tồn kho sẽ
            được kiểm tra lại khi thêm thiết kế vào
            giỏ hàng và Checkout.

        </div>


        <div class="actions">

            <a
                href="{{ route('studio.index') }}"
                class="btn btn-secondary"
            >
                ← Thiết kế lại
            </a>


            <button
                type="button"
                class="btn btn-primary"
                disabled
                title="Sẽ bật sau khi hoàn thành đăng nhập Customer"
            >
                Lưu thiết kế
            </button>

        </div>

    </div>

</main>


</body>
</html>