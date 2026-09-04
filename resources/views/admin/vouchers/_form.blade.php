@php
    $isEdit = isset($voucher);
@endphp


@if ($errors->any())

    <div class="alert alert-danger">

        <strong>
            Vui lòng kiểm tra lại dữ liệu:
        </strong>

        <ul class="voucher-error-list">

            @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


<div class="voucher-form-grid">


    {{-- MÃ VOUCHER --}}
    <div class="form-group">

        <label for="code">
            Mã Voucher (*)
        </label>

        <input
            type="text"
            id="code"
            name="code"
            maxlength="50"
            value="{{ old('code', $voucher->code ?? '') }}"
            placeholder="Ví dụ: MINIGROVE20"
            required
        >

        <div class="voucher-form-help">
            Hệ thống sẽ tự chuyển mã Voucher thành chữ in hoa.
        </div>

    </div>


    {{-- LOẠI GIẢM --}}
    <div class="form-group">

        <label for="discount_type">
            Loại giảm giá (*)
        </label>

        <select
            id="discount_type"
            name="discount_type"
            required
        >

            <option
                value="percentage"
                @selected(
                    old(
                        'discount_type',
                        $voucher->discount_type ?? 'percentage'
                    ) === 'percentage'
                )
            >
                Giảm theo phần trăm (%)
            </option>

            <option
                value="fixed"
                @selected(
                    old(
                        'discount_type',
                        $voucher->discount_type ?? ''
                    ) === 'fixed'
                )
            >
                Giảm số tiền cố định
            </option>

        </select>

    </div>


    {{-- GIÁ TRỊ GIẢM --}}
    <div class="form-group">

        <label for="discount_value">
            Giá trị giảm (*)
        </label>

        <input
            type="number"
            id="discount_value"
            name="discount_value"
            min="0.01"
            step="0.01"
            value="{{
                old(
                    'discount_value',
                    $voucher->discount_value ?? ''
                )
            }}"
            required
        >

        <div class="voucher-form-help">
            Nếu là phần trăm: nhập 10 = giảm 10%.
        </div>

    </div>


    {{-- ĐƠN TỐI THIỂU --}}
    <div class="form-group">

        <label for="min_order_amount">
            Giá trị đơn tối thiểu (*)
        </label>

        <input
            type="number"
            id="min_order_amount"
            name="min_order_amount"
            min="0"
            step="1000"
            value="{{
                old(
                    'min_order_amount',
                    $voucher->min_order_amount ?? 0
                )
            }}"
            required
        >

    </div>


    {{-- GIẢM TỐI ĐA --}}
    <div class="form-group">

        <label for="max_discount_amount">
            Số tiền giảm tối đa
        </label>

        <input
    type="number"
    id="max_discount_amount"
    name="max_discount_amount"
    min="1000"
    step="1000"
            value="{{
                old(
                    'max_discount_amount',
                    $voucher->max_discount_amount ?? ''
                )
            }}"
        >

        <div class="voucher-form-help">
            Chủ yếu dùng với Voucher phần trăm.
            Có thể để trống.
        </div>

    </div>


    {{-- TỔNG LƯỢT --}}
<div class="form-group">

    <label for="usage_limit">
        Tổng lượt sử dụng
    </label>

    <input
        type="number"
        id="usage_limit"
        name="usage_limit"
        min="1"
        value="{{
            old(
                'usage_limit',
                $voucher->usage_limit ?? ''
            )
        }}"
    >

    <div class="voucher-form-help">
        Để trống = không giới hạn.
    </div>

</div>


{{-- GIỚI HẠN MỖI CUSTOMER --}}
<div class="form-group">

    <label for="per_user_limit">
        Giới hạn mỗi Customer
    </label>

    <input
        type="number"
        id="per_user_limit"
        name="per_user_limit"
        min="1"
        value="{{
            old(
                'per_user_limit',
                $voucher->per_user_limit ?? ''
            )
        }}"
    >

    <div class="voucher-form-help">
        Để trống = không giới hạn theo Customer.
    </div>

</div>


@if ($isEdit)

    <div class="form-group voucher-full-width">

        <label>
            Đã sử dụng
        </label>

        <div class="voucher-readonly-value">
            {{ $voucher->usage_count }} lượt
        </div>

        <div class="voucher-form-help">
            Số lượt này do hệ thống quản lý,
            Admin không được chỉnh trực tiếp.
        </div>

    </div>

@endif


{{-- THỜI GIAN BẮT ĐẦU --}}
<div class="form-group voucher-time-start">

    <label for="start_at">
        Thời gian bắt đầu (*)
    </label>

    <input
        type="datetime-local"
        id="start_at"
        name="start_at"
        value="{{
            old(
                'start_at',
                isset($voucher)
                    ? $voucher->start_at->format('Y-m-d\TH:i')
                    : now()->format('Y-m-d\TH:i')
            )
        }}"
        required
    >

</div>


{{-- THỜI GIAN KẾT THÚC --}}
<div class="form-group">

    <label for="end_at">
        Thời gian kết thúc (*)
    </label>

    <input
        type="datetime-local"
        id="end_at"
        name="end_at"
        value="{{
            old(
                'end_at',
                isset($voucher)
                    ? $voucher->end_at->format('Y-m-d\TH:i')
                    : now()->addMonth()->format('Y-m-d\TH:i')
            )
        }}"
        required
    >

</div>

{{-- ACTIVE --}}
<div class="voucher-active-box">

    <input
        type="hidden"
        name="is_active"
        value="0"
    >

    <label class="voucher-active-label">

        <input
            type="checkbox"
            name="is_active"
            value="1"
            @checked(
                (string) old(
                    'is_active',
                    isset($voucher)
                        ? (int) $voucher->is_active
                        : 1
                ) === '1'
            )
        >

        <span>
            Voucher đang hoạt động
        </span>

    </label>

</div>


<div class="voucher-form-actions">

    <a
        href="{{ route('admin.vouchers.index') }}"
        class="btn btn-secondary"
    >
        Hủy
    </a>

    <button
        type="submit"
        class="btn btn-primary"
    >
        {{ $isEdit ? 'Cập nhật Voucher' : 'Thêm Voucher' }}
    </button>

</div>