@extends('layouts.customer')

@section('title', 'Thanh toán - MiniGrove')

@push('styles')
    <link
        rel="stylesheet"
        href="{{
            asset('css/checkout.css')
            . '?v='
            . filemtime(
                public_path(
                    'css/checkout.css'
                )
            )
        }}"
    >
@endpush


@section('content')

<section class="checkout-page">

    <div class="customer-container">


        {{-- =====================================================
             BACK
        ====================================================== --}}

        <a
            href="{{
                $mode === 'cart'
                    ? route('cart.index')
                    : route(
                        'products.show',
                        $checkoutItems->first()->product
                    )
            }}"
            class="checkout-back-button"
        >
            ← Quay lại
        </a>


        {{-- =====================================================
             HEADING
        ====================================================== --}}

        <div class="checkout-heading">

            <h1>
                Thanh toán
            </h1>

            <p>
                Kiểm tra thông tin trước khi đặt hàng.
            </p>

        </div>


        {{-- =====================================================
             VOUCHER ERROR
        ====================================================== --}}

        @if ($voucherError)

            <div
                class="
                    checkout-alert
                    checkout-alert-error
                "
            >
                {{ $voucherError }}
            </div>

        @endif


        {{-- =====================================================
             ADDRESS
        ====================================================== --}}

        <div class="checkout-card">

            <div class="checkout-card-header">

                <div>

                    <div class="checkout-card-title">
                        📍 Địa chỉ nhận hàng
                    </div>

                    <div class="checkout-card-subtitle">
                        Chọn địa chỉ giao hàng cho đơn này.
                    </div>

                </div>

            </div>


            {{-- =================================================
     ADDRESS LIST
================================================== --}}

@if ($addresses->isNotEmpty())

    <div class="checkout-address-list">

        @foreach ($addresses as $address)

            <div
                class="
                    checkout-address-item
                    {{
                        $selectedAddress
                        && $selectedAddress->id === $address->id
                            ? 'selected'
                            : ''
                    }}
                "
            >

                {{-- =================================================
                     SELECT ADDRESS
                ================================================== --}}

                <div class="checkout-address-radio">

                    <form
                        method="POST"
                        action="{{ route('checkout.address.select') }}"
                    >

                        @csrf

                        <input
                            type="hidden"
                            name="address_id"
                            value="{{ $address->id }}"
                        >

                        <button
                            type="submit"
                            class="checkout-address-select-button"
                            title="Chọn địa chỉ này"
                        >

                            @if (
                                $selectedAddress
                                && $selectedAddress->id === $address->id
                            )

                                ●

                            @else

                                ○

                            @endif

                        </button>

                    </form>

                </div>


                {{-- =================================================
                     ADDRESS CONTENT
                ================================================== --}}

                <div class="checkout-address-content">

                    <div class="checkout-address-main">

                        <strong>
                            {{ $address->receiver_name }}
                        </strong>

                        <span>
                            {{ $address->receiver_phone }}
                        </span>

                        @if ($address->label)

                            <span class="checkout-address-label">
                                {{ $address->label }}
                            </span>

                        @endif

                        @if ($address->is_default)

                            <span class="checkout-address-default">
                                Mặc định
                            </span>

                        @endif

                    </div>


                    <div class="checkout-address-text">

                        {{ $address->address_detail }},
                        {{ $address->ward }},
                        {{ $address->district }},
                        {{ $address->province }}

                    </div>


                    {{-- =================================================
                         ADDRESS ACTIONS
                    ================================================== --}}

                    <div
                        class="checkout-address-actions"
                        style="
                            display: flex;
                            gap: 8px;
                            margin-top: 10px;
                            flex-wrap: wrap;
                        "
                    >

                        {{-- SỬA --}}

                        <button
                            type="button"
                            class="checkout-secondary-button checkout-address-edit-button"
                            data-address-edit="{{ $address->id }}"
                        >
                            Sửa
                        </button>


                        {{-- XÓA --}}

                        <form
                            method="POST"
                            action="{{
                                route(
                                    'checkout.address.destroy',
                                    $address->id
                                )
                            }}"
                            onsubmit="
                                return confirm(
                                    'Bạn có chắc muốn xóa địa chỉ này không?'
                                );
                            "
                        >

                            @csrf

                            @method('DELETE')

                            <button
                                type="submit"
                                class="checkout-secondary-button checkout-address-delete-button"
                                 data-address-id="{{ $address->id }}"
                            >
                                Xóa
                            </button>

                        </form>

                    </div>


                    {{-- =================================================
                         EDIT ADDRESS FORM
                    ================================================== --}}

                    <div
                        class="checkout-address-edit-form"
                        id="editAddress{{ $address->id }}"
                        style="display: none; margin-top: 16px;"
                    >

                        <form
                            method="POST"
                            action="{{
                                route(
                                    'checkout.address.update',
                                    $address->id
                                )
                            }}"
                            class="checkout-address-form"
                        >

                            @csrf

                            @method('PATCH')


                            <div class="checkout-form-grid">

                                {{-- TÊN NGƯỜI NHẬN --}}

                                <div class="checkout-field">

                                    <label>
                                        Tên người nhận
                                    </label>

                                    <input
                                        type="text"
                                        name="receiver_name"
                                        value="{{ $address->receiver_name }}"
                                        maxlength="100"
                                        required
                                    >

                                </div>


                                {{-- SỐ ĐIỆN THOẠI --}}

                                <div class="checkout-field">

                                    <label>
                                        Số điện thoại
                                    </label>

                                    <input
                                        type="text"
                                        name="receiver_phone"
                                        value="{{ $address->receiver_phone }}"
                                        maxlength="10"
                                        inputmode="numeric"
                                        required
                                    >

                                </div>


                                {{-- TỈNH --}}

                                <div class="checkout-field">

                                    <label>
                                        Tỉnh / Thành phố
                                    </label>

                                    <select
                                        class="checkout-edit-province"
                                        name="province"
                                        data-address-id="{{ $address->id }}"
                                        data-current-province="{{ $address->province }}"
                                        required
                                    >
                                        <option value="">
                                            Đang tải Tỉnh / Thành phố...
                                        </option>
                                    </select>

                                </div>


                                {{-- QUẬN --}}

                                <div class="checkout-field">

                                    <label>
                                        Quận / Huyện
                                    </label>

                                    <select
                                        class="checkout-edit-district"
                                        name="district"
                                        data-address-id="{{ $address->id }}"
                                        data-current-district="{{ $address->district }}"
                                        required
                                        disabled
                                    >
                                        <option value="">
                                            -- Chọn Quận / Huyện --
                                        </option>
                                    </select>

                                </div>


                                {{-- PHƯỜNG --}}

                                <div class="checkout-field">

                                    <label>
                                        Phường / Xã
                                    </label>

                                    <select
                                        class="checkout-edit-ward"
                                        name="ward"
                                        data-address-id="{{ $address->id }}"
                                        data-current-ward="{{ $address->ward }}"
                                        required
                                        disabled
                                    >
                                        <option value="">
                                            -- Chọn Phường / Xã --
                                        </option>
                                    </select>

                                </div>


                                {{-- GHN DISTRICT ID --}}

                                <input
                                    type="hidden"
                                    class="checkout-edit-ghn-district"
                                    name="ghn_district_id"
                                    data-address-id="{{ $address->id }}"
                                    value="{{ $address->ghn_district_id }}"
                                >


                                {{-- GHN WARD CODE --}}

                                <input
                                    type="hidden"
                                    class="checkout-edit-ghn-ward"
                                    name="ghn_ward_code"
                                    data-address-id="{{ $address->id }}"
                                    value="{{ $address->ghn_ward_code }}"
                                >


                                {{-- NHÃN --}}

                                <div class="checkout-field">

                                    <label>
                                        Nhãn địa chỉ
                                    </label>

                                    <input
                                        type="text"
                                        name="label"
                                        value="{{ $address->label }}"
                                        maxlength="50"
                                        placeholder="Ví dụ: Nhà riêng"
                                    >

                                </div>


                                {{-- ĐỊA CHỈ CHI TIẾT --}}

                                <div
                                    class="
                                        checkout-field
                                        checkout-field-full
                                    "
                                >

                                    <label>
                                        Số nhà / Tên đường
                                    </label>

                                    <input
                                        type="text"
                                        name="address_detail"
                                        value="{{ $address->address_detail }}"
                                        maxlength="255"
                                        required
                                    >

                                </div>

                            </div>


                            {{-- MẶC ĐỊNH --}}

                            <label class="checkout-default-check">

                                <input
                                    type="checkbox"
                                    name="is_default"
                                    value="1"
                                    @checked($address->is_default)
                                >

                                Đặt làm địa chỉ mặc định

                            </label>


                            <div
                                style="
                                    display: flex;
                                    gap: 8px;
                                    margin-top: 12px;
                                "
                            >

                                <button
                                    type="submit"
                                    class="checkout-secondary-button"
                                >
                                    Lưu thay đổi
                                </button>


                                <button
    type="button"
    class="checkout-secondary-button checkout-address-cancel-edit"
    data-address-id="{{ $address->id }}"
>
    Hủy
</button>

                            </div>

                        </form>

                    </div>

                </div>


                {{-- =================================================
                     CURRENT ADDRESS
                ================================================== --}}

                @if (
                    $selectedAddress
                    && $selectedAddress->id === $address->id
                )

                    <div class="checkout-address-current">
                        Đang chọn
                    </div>

                @endif

            </div>

        @endforeach

    </div>

@else

    <div class="checkout-no-address">

        Bạn chưa có địa chỉ nhận hàng.

    </div>

@endif




            {{-- =================================================
                 ADD ADDRESS
            ================================================== --}}

            <details
                class="checkout-address-create"

                @if (
                    $errors->has('receiver_name')
                    || $errors->has('receiver_phone')
                    || $errors->has('province')
                    || $errors->has('district')
                    || $errors->has('ward')
                    || $errors->has('address_detail')
                    || $errors->has('label')
                )
                    open
                @endif
            >

                <summary>
                    + Thêm địa chỉ mới
                </summary>


                <form
                    method="POST"
                    action="{{
                        route(
                            'checkout.address.store'
                        )
                    }}"
                    class="checkout-address-form"
                >

                    @csrf


                    <div class="checkout-form-grid">


                        {{-- RECEIVER NAME --}}

                        <div class="checkout-field">

                            <label for="receiverName">
                                Tên người nhận
                            </label>

                            <input
                                id="receiverName"
                                type="text"
                                name="receiver_name"
                                value="{{
                                    old(
                                        'receiver_name',
                                        auth()->user()->name
                                    )
                                }}"
                                maxlength="100"
                                required
                            >

                        </div>


                        {{-- PHONE --}}

                        <div class="checkout-field">

                            <label for="receiverPhone">
                                Số điện thoại
                            </label>

                            <input
                                id="receiverPhone"
                                type="text"
                                name="receiver_phone"
                                value="{{
                                    old(
                                        'receiver_phone',
                                        auth()->user()->phone
                                    )
                                }}"
                                maxlength="10"
                                inputmode="numeric"
                                required
                            >

                        </div>



{{-- =================================================
     PROVINCE
================================================== --}}

<div class="checkout-field">

    <label for="checkoutProvince">
        Tỉnh / Thành phố
    </label>

    <select
        id="checkoutProvince"
        name="province"
        data-old="{{ old('province') }}"
        required
    >
        <option value="">
            -- Chọn Tỉnh / Thành phố --
        </option>
    </select>

</div>


{{-- =================================================
     DISTRICT
================================================== --}}

<div class="checkout-field">

    <label for="checkoutDistrict">
        Quận / Huyện
    </label>

    <select
        id="checkoutDistrict"
        name="district"
        data-old="{{ old('district') }}"
        required
        disabled
    >
        <option value="">
            -- Chọn Quận / Huyện --
        </option>
    </select>

</div>


{{-- =================================================
     WARD
================================================== --}}

<div class="checkout-field">

    <label for="checkoutWard">
        Phường / Xã
    </label>

    <select
        id="checkoutWard"
        name="ward"
        data-old="{{ old('ward') }}"
        required
        disabled
    >
        <option value="">
            -- Chọn Phường / Xã --
        </option>
    </select>

</div>


{{-- =================================================
     GHN CODES

     Các giá trị này KHÔNG cho người dùng nhập.
     JavaScript/backend sẽ tự điền.
================================================== --}}

<input
    type="hidden"
    name="ghn_district_id"
    id="ghnDistrictId"
    value="{{ old('ghn_district_id') }}"
>

<input
    type="hidden"
    name="ghn_ward_code"
    id="ghnWardCode"
    value="{{ old('ghn_ward_code') }}"
>




                        {{-- LABEL --}}

                        <div class="checkout-field">

                            <label for="addressLabel">
                                Nhãn địa chỉ
                            </label>

                            <input
                                id="addressLabel"
                                type="text"
                                name="label"
                                value="{{ old('label') }}"
                                maxlength="50"
                                placeholder="Ví dụ: Nhà riêng"
                            >

                        </div>


                        {{-- ADDRESS DETAIL --}}

                        <div
                            class="
                                checkout-field
                                checkout-field-full
                            "
                        >

                            <label for="addressDetail">
                                Số nhà / Tên đường
                            </label>

                            <input
                                id="addressDetail"
                                type="text"
                                name="address_detail"
                                value="{{
                                    old(
                                        'address_detail'
                                    )
                                }}"
                                maxlength="255"
                                placeholder="
                                    Ví dụ: Ngõ 85, số 12
                                "
                                required
                            >

                        </div>

                    </div>


                    {{-- DEFAULT ADDRESS --}}

                    <label class="checkout-default-check">

                        <input
                            type="checkbox"
                            name="is_default"
                            value="1"
                            @checked(old('is_default'))
                        >

                        Đặt làm địa chỉ mặc định

                    </label>


                    <button
                        type="submit"
                        class="
                            checkout-secondary-button
                        "
                    >
                        Lưu địa chỉ
                    </button>

                </form>

            </details>

        </div>


        {{-- =====================================================
             PRODUCTS
        ====================================================== --}}

        <div class="checkout-card">

            <div class="checkout-card-title">
                Sản phẩm
            </div>


            <div class="checkout-table-header">

                <div>
                    Sản phẩm
                </div>

                <div class="checkout-center">
                    Đơn giá
                </div>

                <div class="checkout-center">
                    Số lượng
                </div>

                <div class="checkout-center">
                    Thành tiền
                </div>

            </div>


            @foreach ($checkoutItems as $item)

                @php

                    $product = $item->product;
                    $design = $item->custom_design ?? null;

                    $primaryImage =
                        $product
                            ? (
                                $product->images
                                    ->firstWhere(
                                        'is_primary',
                                        true
                                    )
                                ?? $product->images->first()
                            )
                            : null;

                @endphp


                <div class="checkout-product-row">


                    {{-- PRODUCT --}}

                    <div class="checkout-product">

                        <div class="checkout-product-image">

                            @if ($primaryImage && $product)

                                <img
                                    src="{{
                                        asset(
                                            'storage/'
                                            . $primaryImage
                                                ->image_path
                                        )
                                    }}"
                                    alt="{{ $product->name }}"
                                >

                            @else

                                <div class="checkout-no-image">
                                    🌱
                                </div>

                            @endif

                        </div>


                        <div>

                            <div class="checkout-category">
                                {{
                                    $product
                                        ? (
                                            $product
                                                ->category
                                                ?->name
                                            ?? 'MiniGrove'
                                        )
                                        : 'MiniGrove Studio'
                                }}
                            </div>


                            <strong>
                                {{
                                    $product
                                        ? $product->name
                                        : ($design?->name ?? 'Thiết kế Terrarium')
                                }}
                            </strong>

                            @if ($design)
                                <div class="checkout-category">
                                    {{ $design->design_code }}
                                    · {{ ucfirst($design->style) }}
                                </div>
                            @endif

                        </div>

                    </div>


                    {{-- UNIT PRICE --}}

                    <div class="checkout-center">

                        {{
                            number_format(
                                $item->unit_price,
                                0,
                                ',',
                                '.'
                            )
                        }}đ

                    </div>


                    {{-- QUANTITY --}}

                    <div class="checkout-center">

                        {{ $item->quantity }}

                    </div>


                    {{-- SUBTOTAL --}}

                    <div
                        class="
                            checkout-center
                            checkout-line-total
                        "
                    >

                        {{
                            number_format(
                                $item->subtotal,
                                0,
                                ',',
                                '.'
                            )
                        }}đ

                    </div>

                </div>

            @endforeach

        </div>


        {{-- =====================================================
             VOUCHER
        ====================================================== --}}

        <div class="checkout-card checkout-card-voucher">

            <div class="checkout-card-title">
                🎟 Voucher MiniGrove
            </div>


            @if ($appliedVoucher)


                {{-- APPLIED VOUCHER --}}

                <div class="checkout-voucher-applied">

                    <div>

                        <strong>
                            {{ $appliedVoucher->code }}
                        </strong>

                        <div>
                            Đã áp dụng mã giảm giá.
                        </div>

                    </div>


                    <form
                        method="POST"
                        action="{{
                            route(
                                'checkout.voucher.remove'
                            )
                        }}"
                    >

                        @csrf
                        @method('DELETE')


                        <button
                            type="submit"
                            class="
                                checkout-voucher-remove
                            "
                        >
                            Bỏ Voucher
                        </button>

                    </form>

                </div>


            @else


                {{-- APPLY VOUCHER --}}

                <form
                    method="POST"
                    action="{{
                        route(
                            'checkout.voucher.apply'
                        )
                    }}"
                    class="checkout-voucher-form"
                >

                    @csrf

                    <div
                        class="checkout-voucher-picker"
                        id="checkoutVoucherPicker"
                    >
                        <input
                            type="text"
                            id="checkoutVoucherInput"
                            name="voucher_code"
                            value="{{
                                old(
                                    'voucher_code'
                                )
                            }}"
                            maxlength="50"
                            placeholder="Nhập hoặc chọn Voucher đã lưu"
                            autocomplete="off"
                            aria-expanded="false"
                            aria-controls="checkoutSavedVoucherList"
                        >

                        <div
                            id="checkoutSavedVoucherList"
                            class="checkout-saved-vouchers"
                        >
                            <div class="checkout-saved-vouchers-title">
                                Voucher đã lưu
                            </div>

                            @forelse (
                                $savedVouchers
                                as $savedVoucher
                            )

                                <button
                                    type="button"
                                    class="
                                        checkout-saved-voucher-option
                                        {{
                                            $savedVoucher
                                                ->checkout_eligible
                                                ? ''
                                                : 'is-disabled'
                                        }}
                                    "
                                    data-saved-voucher-option
                                    data-voucher-code="{{
                                        $savedVoucher->code
                                    }}"
                                    data-voucher-search="{{
                                        strtolower(
                                            $savedVoucher->code
                                        )
                                    }}"
                                    onclick="
                                        document
                                            .getElementById(
                                                'checkoutVoucherInput'
                                            )
                                            .value =
                                            this.dataset.voucherCode;
                                    "
                                    @disabled(
                                        !$savedVoucher
                                            ->checkout_eligible
                                    )
                                >
                                    <span class="checkout-saved-voucher-main">
                                        <strong>
                                            {{ $savedVoucher->code }}
                                        </strong>

                                        <span>
                                            @if (
                                                $savedVoucher
                                                    ->discount_type
                                                === 'percentage'
                                            )
                                                Giảm
                                                {{
                                                    rtrim(
                                                        rtrim(
                                                            number_format(
                                                                $savedVoucher
                                                                    ->discount_value,
                                                                2,
                                                                '.',
                                                                ''
                                                            ),
                                                            '0'
                                                        ),
                                                        '.'
                                                    )
                                                }}%
                                            @else
                                                Giảm
                                                {{
                                                    number_format(
                                                        $savedVoucher
                                                            ->discount_value,
                                                        0,
                                                        ',',
                                                        '.'
                                                    )
                                                }}đ
                                            @endif
                                        </span>
                                    </span>

                                    <span
                                        class="
                                            checkout-saved-voucher-status
                                            {{
                                                $savedVoucher
                                                    ->checkout_eligible
                                                    ? 'is-eligible'
                                                    : ''
                                            }}
                                        "
                                    >
                                        @if (
                                            $savedVoucher
                                                ->checkout_eligible
                                        )
                                            Có thể dùng · giảm
                                            {{
                                                number_format(
                                                    $savedVoucher
                                                        ->checkout_discount_amount,
                                                    0,
                                                    ',',
                                                    '.'
                                                )
                                            }}đ
                                        @else
                                            {{
                                                $savedVoucher
                                                    ->checkout_error
                                            }}
                                        @endif
                                    </span>
                                </button>

                            @empty

                                <div class="checkout-saved-vouchers-empty">
                                    Bạn chưa lưu Voucher nào.
                                    <a
                                        href="{{
                                            route(
                                                'profile.vouchers'
                                            )
                                        }}"
                                    >
                                        Mở Kho Voucher
                                    </a>
                                </div>

                            @endforelse
                        </div>
                    </div>


                    <button
                        type="submit"
                        class="
                            checkout-secondary-button
                        "
                    >
                        Áp dụng
                    </button>

                </form>

            @endif

        </div>


        {{-- =====================================================
             PLACE ORDER FORM
        ====================================================== --}}

        <form
            method="POST"
            action="{{
                route(
                    'checkout.place-order'
                )
            }}"
            id="checkoutPlaceOrderForm"
        >

            @csrf


            {{-- SELECTED ADDRESS --}}

            @if ($selectedAddress)

                <input
                    type="hidden"
                    name="address_id"
                    value="{{ $selectedAddress->id }}"
                >

            @endif


            {{-- =================================================
                 PAYMENT
            ================================================== --}}

            {{-- =================================================
     PAYMENT
================================================== --}}

<div class="checkout-card">

    <div class="checkout-card-title">
        💳 Phương thức thanh toán
    </div>


    @php

        /*
         * Phương thức đang được chọn.
         *
         * Nếu old() chứa một phương thức
         * vừa bị Admin tắt thì bỏ lựa chọn cũ
         * và chọn phương thức Active đầu tiên.
         */
        $selectedPaymentMethod =
            old('payment_method');


        if (
            !$selectedPaymentMethod
            ||
            !$paymentSettings->has(
                $selectedPaymentMethod
            )
        ) {

            $selectedPaymentMethod =
                $paymentSettings
                    ->keys()
                    ->first();
        }


        $codSetting =
            $paymentSettings->get('COD');


        $qrSetting =
            $paymentSettings->get('QR');

        $onePaySetting =
    $paymentSettings->get('ONEPAY');

    @endphp


    @if ($paymentSettings->isEmpty())


        <div class="checkout-payment-unavailable">

            Hiện MiniGrove chưa có phương thức
            thanh toán khả dụng.

            Vui lòng quay lại sau.

        </div>


    @else


        <div class="checkout-payment-options">


            {{-- =============================================
                 COD
            ============================================== --}}
            @if ($codSetting)


                <label class="checkout-payment-option">


                    <input
                        type="radio"
                        name="payment_method"
                        value="COD"
                        @checked(
                            $selectedPaymentMethod
                            === 'COD'
                        )
                    >


                    <span>

                        <strong>

                            {{
                                $codSetting
                                    ->display_name
                            }}

                        </strong>


                        <small>

                            COD - thanh toán khi đơn hàng
                            được giao đến bạn.

                        </small>

                    </span>


                </label>


            @endif



            {{-- =============================================
                 QR
            ============================================== --}}
            @if ($qrSetting)


                <label class="checkout-payment-option">


                    <input
                        type="radio"
                        name="payment_method"
                        value="QR"
                        @checked(
                            $selectedPaymentMethod
                            === 'QR'
                        )
                    >


                    <span>

                        <strong>

                            {{
                                $qrSetting
                                    ->display_name
                            }}

                        </strong>


                        <small>

                            Chuyển sang PayOS để quét QR.
                            PayOS tự động xác nhận sau khi
                            thanh toán thành công.

                        </small>

                    </span>


                </label>


            @endif
{{-- =============================================
     ONEPAY
============================================== --}}

@if ($onePaySetting)

    <label class="checkout-payment-option">

        <input
            type="radio"
            name="payment_method"
            value="ONEPAY"
            @checked(
                $selectedPaymentMethod
                === 'ONEPAY'
            )
        >

        <span>

            <strong>
                {{ $onePaySetting->display_name }}
            </strong>

            <small>
                Thanh toán thẻ qua OnePay
                (môi trường TEST)
            </small>

        </span>

    </label>

@endif

        </div>


    @endif


</div>


            {{-- =================================================
                 NOTE
            ================================================== --}}

            <div class="checkout-card">

                <div class="checkout-card-title">
                    📝 Ghi chú đơn hàng
                </div>


                <div class="checkout-note-box">

                    <textarea
                        name="note"
                        rows="4"
                        maxlength="1000"
                        placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."
                    >{{ old('note') }}</textarea>

                </div>

            </div>


            {{-- =================================================
                 TOTAL
            ================================================== --}}

            <div class="checkout-bottom">

                <div class="checkout-summary">


                    {{-- SUBTOTAL --}}

                    <div class="checkout-summary-row">

                        <span>
                            Tạm tính
                        </span>

                        <strong>

                            {{
                                number_format(
                                    $subtotal,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </strong>

                    </div>


                    {{-- DISCOUNT --}}

                    <div class="checkout-summary-row">

                        <span>
                            Giảm giá
                        </span>

                        <strong class="checkout-discount">

                            -{{
                                number_format(
                                    $discountAmount,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </strong>

                    </div>


                    {{-- SHIPPING --}}

                    <div class="checkout-summary-row">

                        <span>
                            Phí vận chuyển
                        </span>

                        <strong>

                            {{
                                number_format(
                                    $shippingFee,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </strong>

                    </div>


                    {{-- TOTAL --}}

                    <div class="checkout-summary-total">

                        <span>
                            Tổng thanh toán
                        </span>

                        <strong>

                            {{
                                number_format(
                                    $total,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </strong>

                    </div>


                    {{-- ADDRESS WARNING --}}

                    @if (!$selectedAddress)

                        <div class="checkout-order-warning">

                            Vui lòng thêm hoặc chọn
                            địa chỉ nhận hàng
                            trước khi đặt hàng.

                        </div>

                    @endif


                    {{-- PLACE ORDER --}}

                    <button
                        type="submit"
                        class="checkout-order-button"
                        id="checkoutOrderButton"
                        @disabled(!$selectedAddress)
                    >
                        Đặt hàng
                    </button>


                    <div class="checkout-note">

                        Khi bấm Đặt hàng,
                        MiniGrove sẽ kiểm tra lại
                        giá và tồn kho trước khi
                        tạo đơn.

                    </div>

                </div>

            </div>

        </form>

    </div>

</section>

@endsection


@push('scripts')

    <script
        src="{{ asset('js/checkout-address.js') }}"
    ></script>

    <script
        src="{{
            asset('js/checkout-voucher.js')
            . '?v='
            . filemtime(
                public_path(
                    'js/checkout-voucher.js'
                )
            )
        }}"
    ></script>

@endpush