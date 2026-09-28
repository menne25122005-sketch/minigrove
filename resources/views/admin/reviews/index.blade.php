@extends('layouts.admin')

@section('title', 'Quản lý đánh giá - MiniGrove Admin')

@section('page-title', 'Đánh giá sản phẩm')

@section('content')
<div class="admin-list-page">

    {{-- PAGE HEADER --}}
    <div class="admin-index-header">
        <div class="admin-index-heading">
            <h1 class="admin-index-title">Quản lý đánh giá sản phẩm</h1>
            <p class="admin-index-subtitle">Theo dõi phản hồi của khách hàng, kiểm duyệt hình ảnh và quản trị hiển thị đánh giá.</p>
        </div>
    </div>

    {{-- PRIVACY NOTICE --}}
    <div class="admin-privacy-note">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span>Chỉ hiển thị định danh <strong>Customer #ID</strong> để bảo vệ thông tin riêng tư của khách hàng theo chính sách bảo mật MiniGrove.</span>
    </div>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <div class="admin-alert admin-alert-success" role="alert" style="margin-bottom: 20px;">
            <div class="admin-alert-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="admin-alert-content">{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="admin-alert admin-alert-danger" role="alert" style="margin-bottom: 20px;">
            <div class="admin-alert-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="admin-alert-content">{{ session('error') }}</div>
        </div>
    @endif

    {{-- TOOLBAR / FILTER --}}
    <div class="admin-toolbar">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="admin-toolbar-form">
            <div class="admin-toolbar-search">
                <span class="admin-toolbar-search-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Tìm nội dung hoặc sản phẩm..."
                    aria-label="Tìm kiếm đánh giá"
                >
            </div>

            <div class="admin-toolbar-select">
                <select name="rating" aria-label="Lọc theo rating">
                    <option value="">Tất cả rating</option>
                    @for ($rating = 5; $rating >= 1; $rating--)
                        <option
                            value="{{ $rating }}"
                            @selected((string) request('rating') === (string) $rating)
                        >
                            {{ $rating }} sao
                        </option>
                    @endfor
                </select>
            </div>

            <div class="admin-toolbar-select">
                <select name="visibility" aria-label="Lọc theo trạng thái hiển thị">
                    <option value="">Tất cả trạng thái</option>
                    <option value="visible" @selected(request('visibility') === 'visible')>
                        Đang hiển thị
                    </option>
                    <option value="hidden" @selected(request('visibility') === 'hidden')>
                        Đang ẩn
                    </option>
                </select>
            </div>

            <div class="admin-toolbar-actions">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Lọc
                </button>
                @if (request()->hasAny(['search', 'rating', 'visibility']))
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline" title="Xóa bộ lọc">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- FEED DANH SÁCH REVIEW --}}
    @if ($reviews->count())
        <div class="admin-reviews-feed">
            @foreach ($reviews as $review)
                <article class="admin-review-card">
                    <div class="admin-review-card-header">
                        <div>
                            <div class="admin-review-customer-badge">
                                <div class="admin-privacy-avatar">
                                    #{{ $review->user_id }}
                                </div>
                                <div>
                                    <div class="admin-text-strong" style="font-size: 14.5px;">Customer #{{ $review->user_id }}</div>
                                    <div class="admin-review-product-meta">
                                        Sản phẩm: <strong>{{ $review->product?->name ?? 'Không xác định' }}</strong>
                                        @if ($review->created_at)
                                            <span style="margin: 0 4px; color: var(--admin-text-muted);">•</span>
                                            <span>{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            @if ($review->is_visible)
                                <span class="badge badge-success">
                                    <span class="badge-dot"></span> Đang hiển thị
                                </span>
                            @else
                                <span class="badge badge-neutral">
                                    <span class="badge-dot"></span> Đang ẩn
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- RATING --}}
                    <div class="admin-review-stars" aria-label="Rating {{ $review->rating }} trên 5">
                        <span style="letter-spacing: 2px;">
                            @for ($star = 1; $star <= 5; $star++)
                                {{ $star <= $review->rating ? '★' : '☆' }}
                            @endfor
                        </span>
                        <span style="font-size: 13px; font-weight: 700; color: #B45309; margin-left: 6px;">
                            {{ $review->rating }}/5
                        </span>
                    </div>

                    {{-- COMMENT --}}
                    @if ($review->comment)
                        <p class="admin-review-comment">
                            {{ $review->comment }}
                        </p>
                    @else
                        <p class="admin-review-comment" style="color: var(--admin-text-muted); font-style: italic;">
                            Customer không nhập nội dung đánh giá.
                        </p>
                    @endif

                    {{-- ẢNH REVIEW --}}
                    @if ($review->images->isNotEmpty())
                        <div class="admin-review-images">
                            @foreach ($review->images as $image)
                                <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank" rel="noopener" title="Xem ảnh gốc">
                                    <img
                                        src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="Ảnh đánh giá của Customer #{{ $review->user_id }}"
                                        loading="lazy"
                                    >
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- PHẢN HỒI (REPLIES COUNT) --}}
                    @if ($review->replies_count > 0)
                        <div style="font-size: 13px; font-weight: 600; color: var(--admin-forest-800); margin: 10px 0 6px; display: flex; align-items: center; gap: 6px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            <span>{{ $review->replies_count }} phản hồi từ quản trị viên</span>
                        </div>
                    @endif

                    {{-- ADMIN PHẢN HỒI REVIEW FORM --}}
                    <div class="admin-review-reply-section">
                        <form
                            method="POST"
                            action="{{ route('admin.reviews.reply', $review) }}"
                            class="admin-review-reply-form"
                        >
                            @csrf
                            <textarea
                                name="content"
                                rows="2"
                                maxlength="2000"
                                placeholder="Nhập phản hồi chính thức cho đánh giá này..."
                                required
                                class="form-input"
                            ></textarea>
                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 5px;"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                    Gửi phản hồi
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ẨN / HIỆN TOGGLE --}}
                    <div class="admin-review-actions">
                        <span style="font-size: 12.5px; color: var(--admin-text-secondary);">
                            Hành động kiểm duyệt:
                        </span>
                        <form
                            method="POST"
                            action="{{ route('admin.reviews.toggle-visibility', $review) }}"
                            style="margin: 0;"
                        >
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                class="btn {{ $review->is_visible ? 'btn-outline' : 'btn-primary' }} btn-sm"
                            >
                                @if ($review->is_visible)
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 5px;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                    Ẩn đánh giá
                                @else
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 5px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Hiển thị đánh giá
                                @endif
                            </button>
                        </form>
                    </div>

                </article>
            @endforeach
        </div>

        {{-- PHÂN TRANG --}}
        @include('admin.partials.pagination', ['paginator' => $reviews])
    @else
        <div class="empty-state" style="padding: 48px 24px; text-align: center; background: #fff; border: 1px dashed var(--admin-border); border-radius: var(--admin-radius-lg);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 12px; color: var(--admin-text-muted); display: block;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <h3 style="font-size: 15px; font-weight: 600; color: var(--admin-text-primary); margin: 0 0 4px;">Chưa có đánh giá nào phù hợp</h3>
            <p style="font-size: 13px; color: var(--admin-text-secondary); margin: 0;">Hãy thử điều chỉnh bộ lọc hoặc từ khóa tìm kiếm.</p>
        </div>
    @endif

</div>
@endsection
