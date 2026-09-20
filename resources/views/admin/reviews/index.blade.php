@extends('layouts.admin')

@section('title', 'Quản lý đánh giá - MiniGrove Admin')

@section('page-title', 'Quản lý đánh giá')

@push('styles')
<style>
    .reviews-page {
        padding: 24px;
    }

    .reviews-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .reviews-header h1 {
        margin: 0 0 6px;
        font-size: 28px;
        font-weight: 700;
    }

    .reviews-header p {
        margin: 0;
        color: #6b7280;
    }

    .reviews-filter {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) 180px 180px auto;
        gap: 12px;
        padding: 18px;
        margin-bottom: 20px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
    }

    .reviews-filter input,
    .reviews-filter select {
        width: 100%;
        min-height: 42px;
        padding: 9px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        font-size: 14px;
    }

    .reviews-filter button {
        min-height: 42px;
        padding: 9px 18px;
        border: 0;
        border-radius: 8px;
        background: #1f2937;
        color: #fff;
        cursor: pointer;
        font-weight: 600;
    }

    .reviews-filter button:hover {
        opacity: .9;
    }

    .review-alert {
        padding: 12px 16px;
        margin-bottom: 20px;
        border-radius: 8px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .reviews-list {
        display: grid;
        gap: 16px;
    }

    .review-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
    }

    .review-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 14px;
    }

    .review-customer {
        font-weight: 700;
        color: #111827;
    }

    .review-product {
        margin-top: 5px;
        color: #6b7280;
        font-size: 14px;
    }

    .review-rating {
        margin: 10px 0;
        letter-spacing: 2px;
        color: #d97706;
        font-size: 18px;
    }

    .review-comment {
        margin: 0;
        line-height: 1.7;
        color: #374151;
        white-space: pre-line;
    }

    .review-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
    }

    .review-images img {
        width: 96px;
        height: 96px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .review-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
    }

    .review-status.visible {
        background: #ecfdf5;
        color: #047857;
    }

    .review-status.hidden {
        background: #f3f4f6;
        color: #6b7280;
    }

    .review-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
    }

    .review-toggle-button {
        padding: 8px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        color: #374151;
        cursor: pointer;
        font-weight: 600;
    }

    .review-toggle-button:hover {
        background: #f9fafb;
    }

    .review-empty {
        padding: 50px 20px;
        text-align: center;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        color: #6b7280;
    }

    .review-pagination {
        margin-top: 24px;
    }

    .review-replies {
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .review-replies-title {
        margin-bottom: 8px;
        font-weight: 700;
        color: #374151;
    }

    .review-reply-count {
        color: #6b7280;
        font-size: 14px;
    }

    @media (max-width: 900px) {
        .reviews-filter {
            grid-template-columns: 1fr;
        }

        .review-card-header {
            flex-direction: column;
        }
    }

</style>
@endpush

@section('content')

<div class="reviews-page">

    <div class="reviews-header">
        <div>
            <h1>Quản lý đánh giá sản phẩm</h1>
            <p>
                Theo dõi đánh giá, hình ảnh và trạng thái hiển thị của Customer.
            </p>
        </div>
    </div>


    {{-- THÔNG BÁO --}}
    @if (session('success'))
        <div class="review-alert">
            {{ session('success') }}
        </div>
    @endif


    {{-- BỘ LỌC --}}
    <form
        method="GET"
        action="{{ route('admin.reviews.index') }}"
        class="reviews-filter"
    >

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Tìm nội dung hoặc sản phẩm..."
        >

        <select name="rating">
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

        <select name="visibility">
            <option value="">Tất cả trạng thái</option>

            <option
                value="visible"
                @selected(request('visibility') === 'visible')
            >
                Đang hiển thị
            </option>

            <option
                value="hidden"
                @selected(request('visibility') === 'hidden')
            >
                Đang ẩn
            </option>
        </select>

        <button type="submit">
            Lọc
        </button>

    </form>


    {{-- DANH SÁCH REVIEW --}}
    @if ($reviews->count())

        <div class="reviews-list">

            @foreach ($reviews as $review)

                <article class="review-card">

                    <div class="review-card-header">

                        <div>

                            {{-- PRIVACY:
                                 Chỉ hiển thị Customer #ID.
                                 Không hiển thị PII. --}}
                            <div class="review-customer">
                                Customer #{{ $review->user_id }}
                            </div>

                            <div class="review-product">
                                Sản phẩm:
                                {{ $review->product?->name ?? 'Không xác định' }}
                            </div>

                        </div>


                        @if ($review->is_visible)

                            <span class="review-status visible">
                                ● Đang hiển thị
                            </span>

                        @else

                            <span class="review-status hidden">
                                ● Đang ẩn
                            </span>

                        @endif

                    </div>


                    {{-- RATING --}}
                    <div
                        class="review-rating"
                        aria-label="Rating {{ $review->rating }} trên 5"
                    >
                        @for ($star = 1; $star <= 5; $star++)
                            {{ $star <= $review->rating ? '★' : '☆' }}
                        @endfor
                    </div>


                    {{-- COMMENT --}}
                    @if ($review->comment)

                        <p class="review-comment">
                            {{ $review->comment }}
                        </p>

                    @else

                        <p class="review-comment">
                            Customer không nhập nội dung đánh giá.
                        </p>

                    @endif


                    {{-- ẢNH REVIEW --}}
                    @if ($review->images->isNotEmpty())

                        <div class="review-images">

                            @foreach ($review->images as $image)

                                <img
                                    src="{{ asset('storage/' . $image->image_path) }}"
                                    alt="Ảnh đánh giá của Customer #{{ $review->user_id }}"
                                >

                            @endforeach

                        </div>

                    @endif


                    {{-- PHẢN HỒI --}}
@if ($review->replies_count > 0)

    <div class="review-replies">

        <div class="review-replies-title">
            Phản hồi
        </div>

        <div class="review-reply-count">
            {{ $review->replies_count }} phản hồi
        </div>

    </div>

@endif


{{-- ADMIN PHẢN HỒI REVIEW --}}
<form
    method="POST"
    action="{{ route('admin.reviews.reply', $review) }}"
    class="review-reply-form"
>
    @csrf

    <textarea
        name="content"
        rows="3"
        maxlength="2000"
        placeholder="Nhập phản hồi cho Customer..."
        required
    ></textarea>

    <button type="submit">
        Gửi phản hồi
    </button>

</form>


                    {{-- ẨN / HIỆN --}}
                    <div class="review-actions">

                        <form
                            method="POST"
                            action="{{ route(
                                'admin.reviews.toggle-visibility',
                                $review
                            ) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="review-toggle-button"
                            >
                                {{ $review->is_visible
                                    ? 'Ẩn đánh giá'
                                    : 'Hiển thị đánh giá' }}
                            </button>

                        </form>

                    </div>

                </article>

            @endforeach

        </div>


        {{-- PHÂN TRANG --}}
        <div class="review-pagination">
            {{ $reviews->links() }}
        </div>

    @else

        <div class="review-empty">
            Chưa có đánh giá nào phù hợp với điều kiện tìm kiếm.
        </div>

    @endif

</div>

@endsection
