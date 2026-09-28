@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng ' . $order->order_code . ' - MiniGrove')
@section('page-title', 'Chi tiết đơn hàng')

@section('content')

@php
    $statusLabels = [
        'pending'   => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'preparing' => 'Đang chuẩn bị',
        'packed'    => 'Đã đóng gói',
        'shipping'  => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];

    $paymentLabels = [
        'pending'  => 'Chờ thanh toán',
        'paid'     => 'Đã thanh toán',
        'failed'   => 'Thất bại',
        'refunded' => 'Đã hoàn tiền',
    ];

    $nextStatusLabels = [
        'confirmed' => 'Xác nhận đơn hàng',
        'preparing' => 'Bắt đầu chuẩn bị hàng',
        'packed'    => 'Xác nhận đã đóng gói',
        'shipping'  => 'Bàn giao vận chuyển',
        'completed' => 'Xác nhận hoàn thành',
    ];

    $qrNeedsPayment = $order->payment_method === 'QR'
        && $order->order_status === 'pending'
        && $order->payment_status !== 'paid';

    $latestPayment = $order->payments->sortByDesc('id')->first();
@endphp

<div class="admin-ops-page">

    {{-- =========================================================
         TOP NAVIGATION & BACK LINK
    ========================================================== --}}
    <div class="admin-ops-header-bar">
        <a href="{{ route('admin.orders.index') }}" class="admin-ops-back-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Quay lại danh sách đơn hàng
        </a>
    </div>

    {{-- =========================================================
         ORDER DETAIL HEADER CARD
    ========================================================== --}}
    <header class="admin-ops-heading-card">
        <div class="admin-ops-heading-content">
            <div>
                <div class="admin-ops-code-row">
                    <span class="admin-code" style="font-size: 15px; padding: 4px 10px;">
                        {{ $order->order_code }}
                    </span>
                    <span class="badge {{ $order->payment_method === 'QR' ? 'badge-method-qr' : 'badge-method-cod' }}">
                        {{ $order->payment_method ?? '—' }}
                    </span>
                </div>
                <p class="admin-ops-meta">
                    <span>
                        Thời gian đặt: <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                    </span>
                    <span>•</span>
                    <span>
                        Định danh: <strong>Customer #{{ $order->user_id }}</strong>
                    </span>
                </p>
            </div>

            <div class="admin-ops-badge-group">
                <span class="badge badge-payment-{{ $order->payment_status }}" style="font-size: 13px; padding: 6px 12px;">
                    Thanh toán: {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
                </span>

                <span class="badge badge-order-{{ $order->order_status }}" style="font-size: 13px; padding: 6px 12px;">
                    Trạng thái: {{ $statusLabels[$order->order_status] ?? $order->order_status }}
                </span>
            </div>
        </div>
    </header>

    {{-- =========================================================
         MAIN 2-COLUMN OPERATIONS LAYOUT
    ========================================================== --}}
    <div class="admin-ops-layout">

        {{-- =====================================================
             LEFT COLUMN: MAIN CONTENT
        ====================================================== --}}
        <main class="admin-ops-main">

            {{-- 1. CUSTOMER IDENTITY & PRIVACY PROTECTION --}}
            <section class="card" style="margin-bottom: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 style="font-size: 16px; margin: 0;">
                        Định danh khách hàng
                    </h2>
                    <span class="badge badge-info" style="font-size: 11px;">
                        Bảo vệ quyền riêng tư
                    </span>
                </div>

                <div class="admin-privacy-card">
                    <svg class="admin-privacy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <div>
                        <div class="admin-privacy-title">
                            Khách hàng: Customer #{{ $order->user_id }} (ID #{{ $order->user_id }})
                        </div>
                        <p class="admin-privacy-desc">
                            Họ tên, Email, số điện thoại, thông tin người nhận, địa chỉ giao hàng và ghi chú của Customer được bảo mật nghiêm ngặt và không hiển thị trong bảng điều khiển quản trị.
                        </p>
                    </div>
                </div>
            </section>

            {{-- 2. ORDER ITEMS SNAPSHOT --}}
            <section class="card" style="margin-bottom: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div>
                        <h2 style="font-size: 16px; margin: 0 0 4px;">
                            Sản phẩm trong đơn hàng
                        </h2>
                        <p style="font-size: 13px; color: var(--admin-text-secondary); margin: 0;">
                            Snapshot mặt hàng tại thời điểm khách đặt hàng ({{ $order->details->count() }} mặt hàng)
                        </p>
                    </div>
                </div>

                <div class="admin-ops-items-list">
                    @foreach ($order->details as $detail)
                        @php
                            $product = $detail->product;
                            $primaryImage = $product?->images?->firstWhere('is_primary', true) ?? $product?->images?->first();
                        @endphp

                        <div class="admin-ops-item-row">
                            {{-- THUMBNAIL --}}
                            <div class="admin-ops-item-thumb">
                                @if ($primaryImage)
                                    <img
                                        src="{{ asset('storage/' . $primaryImage->image_path) }}"
                                        alt="{{ $detail->item_name }}"
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;"
                                    >
                                @else
                                    <span style="font-size: 20px;" aria-hidden="true">🌱</span>
                                @endif
                            </div>

                            {{-- DETAILS --}}
                            <div class="admin-ops-item-details">
                                <h3 class="admin-ops-item-name">
                                    {{ $detail->item_name }}
                                </h3>
                                <div class="admin-ops-item-meta">
                                    <span>Mã: <code class="admin-code" style="font-size: 11px; padding: 1px 5px;">{{ $detail->item_code ?? '—' }}</code></span>
                                    <span>Loại: <strong>{{ $detail->item_type }}</strong></span>
                                </div>
                            </div>

                            {{-- PRICING & QUANTITY --}}
                            <div class="admin-ops-item-pricing">
                                <div class="admin-ops-item-subtotal">
                                    {{ number_format((float) $detail->subtotal, 0, ',', '.') }}đ
                                </div>
                                <div class="admin-ops-item-unit">
                                    {{ number_format((float) $detail->unit_price, 0, ',', '.') }}đ × {{ $detail->quantity }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- 3. STATUS TIMELINE / HISTORY --}}
            <section class="card" style="margin-bottom: 0;">
                <h2 style="font-size: 16px; margin: 0 0 16px;">
                    Lịch sử trạng thái đơn hàng
                </h2>

                <div class="admin-ops-timeline">
                    @forelse ($order->statusHistories as $history)
                        <div class="admin-ops-timeline-step">
                            <span class="admin-ops-timeline-dot"></span>
                            <div class="admin-ops-timeline-header">
                                <span class="admin-ops-timeline-title">
                                    <span class="badge badge-order-{{ $history->order_status }}">
                                        {{ $statusLabels[$history->order_status] ?? $history->order_status }}
                                    </span>
                                    @if ($history->updated_by)
                                        <small style="color: var(--admin-text-muted); margin-left: 6px;">
                                            (Bởi: {{ $history->creator?->name ?? ('#' . $history->updated_by) }})
                                        </small>
                                    @endif
                                </span>
                                <span class="admin-ops-timeline-time">
                                    {{ $history->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            @if ($history->description)
                                <p class="admin-ops-timeline-desc">
                                    {{ $history->description }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p style="font-size: 13.5px; color: var(--admin-text-muted); margin: 0;">
                            Chưa có lịch sử trạng thái bổ sung.
                        </p>
                    @endforelse
                </div>
            </section>

            {{-- 4. POST-SALE RETURN / REFUND REQUESTS (IF ANY) --}}
            @if ($order->returnRequests->isNotEmpty())
                <section class="card" style="margin-bottom: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h2 style="font-size: 16px; margin: 0;">
                            Yêu cầu trả hàng / hoàn tiền liên quan
                        </h2>
                        <span class="badge badge-warning">
                            {{ $order->returnRequests->count() }} yêu cầu
                        </span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach ($order->returnRequests as $returnReq)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: var(--admin-canvas); border: 1px solid var(--admin-border); border-radius: var(--admin-radius-md); flex-wrap: wrap; gap: 12px;">
                                <div>
                                    <a href="{{ route('admin.returns.show', $returnReq) }}" class="admin-code" style="font-size: 13px;">
                                        {{ $returnReq->return_code }}
                                    </a>
                                    <span style="font-size: 12px; color: var(--admin-text-muted); margin-left: 8px;">
                                        Gửi lúc {{ $returnReq->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span class="badge badge-order-{{ $returnReq->status }}">
                                        {{ $returnReq->status }}
                                    </span>
                                    <strong style="color: var(--admin-forest-900);">
                                        {{ number_format((float) $returnReq->refund_amount, 0, ',', '.') }}đ
                                    </strong>
                                    <a href="{{ route('admin.returns.show', $returnReq) }}" class="btn btn-sm btn-secondary">
                                        Xem yêu cầu
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

        </main>

        {{-- =====================================================
             RIGHT COLUMN: SIDEBAR (TOTALS, PAYMENTS, ACTION)
        ====================================================== --}}
        <aside class="admin-ops-sidebar">

            {{-- 1. PRICE BREAKDOWN CARD --}}
            <section class="card" style="margin-bottom: 0;">
                <h2 style="font-size: 16px; margin: 0 0 16px;">
                    Tổng tiền thanh toán
                </h2>

                <div class="admin-ops-summary-list">
                    <div class="admin-ops-summary-row">
                        <span>Tạm tính hàng hóa</span>
                        <strong>{{ number_format((float) $order->subtotal, 0, ',', '.') }}đ</strong>
                    </div>

                    <div class="admin-ops-summary-row admin-ops-summary-discount">
                        <span>Voucher giảm giá</span>
                        <strong>
                            @if ((float) $order->discount_amount > 0)
                                -{{ number_format((float) $order->discount_amount, 0, ',', '.') }}đ
                            @else
                                0đ
                            @endif
                        </strong>
                    </div>

                    <div class="admin-ops-summary-row">
                        <span>Phí vận chuyển</span>
                        <strong>{{ number_format((float) $order->shipping_fee, 0, ',', '.') }}đ</strong>
                    </div>

                    <div class="admin-ops-summary-row admin-ops-summary-total">
                        <span>Tổng cộng</span>
                        <strong>{{ number_format((float) $order->total_amount, 0, ',', '.') }}đ</strong>
                    </div>
                </div>
            </section>

            {{-- 2. PAYMENT DETAILS & PROVIDER INFO --}}
            <section class="card" style="margin-bottom: 0;">
                <h2 style="font-size: 16px; margin: 0 0 14px;">
                    Thông tin thanh toán
                </h2>

                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13.5px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--admin-text-secondary);">Phương thức:</span>
                        <span class="badge {{ $order->payment_method === 'QR' ? 'badge-method-qr' : 'badge-method-cod' }}">
                            {{ $order->payment_method ?? '—' }}
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--admin-text-secondary);">Trạng thái:</span>
                        <span class="badge badge-payment-{{ $order->payment_status }}">
                            {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
                        </span>
                    </div>

                    @if ($latestPayment && $latestPayment->paid_at)
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--admin-text-secondary);">Thời gian trả:</span>
                            <span style="font-weight: 500;">{{ $latestPayment->paid_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif

                    @if ($order->payment_method === 'QR')
                        <div style="background: var(--admin-canvas); border: 1px solid var(--admin-border); border-radius: var(--admin-radius-sm); padding: 10px 12px; font-size: 12.5px; line-height: 1.45; color: var(--admin-text-secondary); margin-top: 4px;">
                            @if ($order->payment_status === 'paid')
                                <span>Thanh toán tự động qua PayOS Webhook đã hoàn tất.</span>
                            @else
                                <span>Chờ xác nhận giao dịch tự động qua cổng PayOS.</span>
                            @endif
                        </div>
                    @endif
                </div>
            </section>

            {{-- 3. SAFE STATUS TRANSITION FORM --}}
            <section class="card" style="margin-bottom: 0;">
                <h2 style="font-size: 16px; margin: 0 0 8px;">
                    Xử lý trạng thái đơn
                </h2>

                @if ($nextStatus)
                    <div class="admin-ops-action-box">
                        <div class="admin-ops-action-label">Bước tiếp theo</div>
                        <div class="admin-ops-next-badge">
                            <span class="badge badge-order-{{ $nextStatus }}" style="font-size: 13px; padding: 4px 10px;">
                                {{ $statusLabels[$nextStatus] ?? $nextStatus }}
                            </span>
                        </div>

                        @if ($qrNeedsPayment)
                            <div class="admin-ops-warning-banner" role="alert">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <div>
                                    Đơn QR đang chờ PayOS Webhook xác nhận thanh toán trước khi chuyển bước.
                                </div>
                            </div>
                        @endif

                        <form
                            action="{{ route('admin.orders.status.update', $order) }}"
                            method="POST"
                            onsubmit="return confirm('Bạn chắc chắn muốn cập nhật trạng thái đơn hàng sang {{ $statusLabels[$nextStatus] ?? $nextStatus }}?');"
                        >
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="status" value="{{ $nextStatus }}">

                            <div style="margin-bottom: 14px;">
                                <label for="description" style="font-size: 13px; margin-bottom: 5px;">
                                    Ghi chú xử lý
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="3"
                                    maxlength="500"
                                    placeholder="Ghi chú nội bộ (không bắt buộc)..."
                                    style="min-height: 80px; font-size: 13px;"
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                style="width: 100%;"
                                @disabled($qrNeedsPayment)
                            >
                                {{ $nextStatusLabels[$nextStatus] ?? 'Cập nhật trạng thái' }}
                            </button>
                        </form>
                    </div>
                @else
                    <div style="padding: 16px; background: var(--admin-canvas); border-radius: var(--admin-radius-md); border: 1px solid var(--admin-border); text-align: center;">
                        @if ($order->order_status === 'completed')
                            <span style="font-size: 18px; display: block; margin-bottom: 4px;">✅</span>
                            <strong style="color: var(--admin-forest-900);">Đơn hàng đã hoàn thành</strong>
                            <p style="font-size: 12.5px; color: var(--admin-text-secondary); margin: 4px 0 0;">
                                Quy trình giao dịch đã kết thúc thành công.
                            </p>
                        @elseif ($order->order_status === 'cancelled')
                            <span style="font-size: 18px; display: block; margin-bottom: 4px;">⛔</span>
                            <strong style="color: var(--admin-danger);">Đơn hàng đã hủy</strong>
                            <p style="font-size: 12.5px; color: var(--admin-text-secondary); margin: 4px 0 0;">
                                Đơn đã đóng và không thể thao tác thêm.
                            </p>
                        @else
                            <p style="font-size: 13px; color: var(--admin-text-muted); margin: 0;">
                                Không có thao tác chuyển trạng thái tiếp theo.
                            </p>
                        @endif
                    </div>
                @endif
            </section>

        </aside>

    </div>

</div>

@endsection