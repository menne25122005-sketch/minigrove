@extends('layouts.staff')

@section('title', 'Chi tiết Chat - Staff')


@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/staff-chat.css') }}"
    >

@endpush


@section('content')

<div class="staff-chat-page">

    @php
        $isMine =
            (int) $conversation->assigned_staff_id
            === (int) auth()->id();

        $isUnassigned =
            $conversation->assigned_staff_id === null;
    @endphp


    <div class="staff-chat-header staff-chat-header-detail">

        <div>

            <a
                href="{{ route('staff.chat.index') }}"
                class="staff-chat-back"
            >
                ← Quay lại danh sách Chat
            </a>

            <span class="staff-chat-eyebrow">
                HỘI THOẠI #{{ $conversation->id }}
            </span>

            <h1>
                {{ $conversation->subject ?: 'Yêu cầu hỗ trợ' }}
            </h1>

            <p>
                Customer #{{ $conversation->customer_id }}
            </p>

        </div>

        <div>

            @if ($isMine)

                <span class="staff-chat-status staff-chat-status-mine">
                    Bạn đang phụ trách
                </span>

            @elseif ($isUnassigned)

                <span class="staff-chat-status staff-chat-status-open">
                    Chưa có Staff nhận
                </span>

            @endif

        </div>

    </div>


    @if (session('success'))

        <div class="staff-chat-alert staff-chat-alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if ($errors->any())

        <div class="staff-chat-alert staff-chat-alert-error">

            @foreach ($errors->all() as $error)

                <div>
                    {{ $error }}
                </div>

            @endforeach

        </div>

    @endif


    <div class="staff-chat-privacy">

        Staff đang trao đổi với
        <strong>Customer #{{ $conversation->customer_id }}</strong>.

        Không hiển thị tên, email, số điện thoại
        hoặc địa chỉ của Customer.

    </div>


    <div class="staff-chat-detail-grid">

        <div class="staff-chat-conversation-card">

            <div class="staff-chat-messages">

                @forelse ($messages as $message)

                    @php
                        $isCustomer =
                            (int) $message->sender_id
                            === (int) $conversation->customer_id;
                    @endphp

                    <div
                        class="
                            staff-chat-message-row
                            {{
                                $isCustomer
                                    ? 'staff-chat-message-row-customer'
                                    : 'staff-chat-message-row-staff'
                            }}
                        "
                    >

                        <div
                            class="
                                staff-chat-bubble
                                {{
                                    $isCustomer
                                        ? 'staff-chat-bubble-customer'
                                        : 'staff-chat-bubble-staff'
                                }}
                            "
                        >

                            <div class="staff-chat-message-author">

                                @if ($isCustomer)

                                    Customer #{{ $conversation->customer_id }}

                                @else

                                    MiniGrove Staff

                                @endif

                            </div>

                            <div class="staff-chat-message-content">
                                {{ $message->content }}
                            </div>

                            <div class="staff-chat-message-time">
                                {{
                                    $message->sent_at
                                        ? $message->sent_at->format('H:i d/m/Y')
                                        : ''
                                }}
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="staff-chat-empty staff-chat-empty-messages">

                        <div class="staff-chat-empty-icon">
                            💬
                        </div>

                        <h3>
                            Chưa có tin nhắn
                        </h3>

                    </div>

                @endforelse

            </div>


            <div class="staff-chat-composer">

                @if ($isUnassigned)

                    <div class="staff-chat-accept-panel">

                        <p>
                            Bạn cần nhận hội thoại trước khi gửi phản hồi.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('staff.chat.accept', $conversation->id) }}"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="staff-chat-btn staff-chat-btn-primary"
                            >
                                Nhận xử lý hội thoại
                            </button>

                        </form>

                    </div>

                @elseif ($isMine)

                    <form
                        method="POST"
                        action="{{ route('staff.chat.reply', $conversation->id) }}"
                        class="staff-chat-reply-form"
                    >

                        @csrf

                        <label for="content">
                            Phản hồi Customer
                        </label>

                        <textarea
                            id="content"
                            name="content"
                            rows="4"
                            maxlength="5000"
                            required
                            placeholder="Nhập nội dung phản hồi..."
                        >{{ old('content') }}</textarea>

                        <div class="staff-chat-reply-actions">

                            <button
                                type="submit"
                                class="staff-chat-btn staff-chat-btn-primary"
                            >
                                Gửi phản hồi
                            </button>

                        </div>

                    </form>

                @endif

            </div>

        </div>


        <aside class="staff-chat-side">

            <div class="staff-chat-info-card">

                <h3>
                    Thông tin hội thoại
                </h3>

                <div class="staff-chat-info-row">

                    <span>
                        Mã hội thoại
                    </span>

                    <strong>
                        #{{ $conversation->id }}
                    </strong>

                </div>

                <div class="staff-chat-info-row">

                    <span>
                        Customer
                    </span>

                    <strong>
                        #{{ $conversation->customer_id }}
                    </strong>

                </div>

                <div class="staff-chat-info-row">

                    <span>
                        Chủ đề
                    </span>

                    <strong>
                        {{ $conversation->topic ?: 'GENERAL' }}
                    </strong>

                </div>

                <div class="staff-chat-info-row">

                    <span>
                        Tạo lúc
                    </span>

                    <strong>
                        {{
                            $conversation->created_at
                                ? $conversation->created_at->format('d/m/Y H:i')
                                : ''
                        }}
                    </strong>

                </div>

                <div class="staff-chat-info-row">

                    <span>
                        Tin cuối
                    </span>

                    <strong>
                        {{
                            $conversation->last_message_at
                                ? $conversation->last_message_at->format('d/m/Y H:i')
                                : 'Chưa có'
                        }}
                    </strong>

                </div>

            </div>


            @if ($isMine)

                <div class="staff-chat-close-card">

                    <h3>
                        Hoàn tất hỗ trợ
                    </h3>

                    <p>
                        Chỉ đóng hội thoại khi yêu cầu của Customer đã được xử lý xong.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('staff.chat.close', $conversation->id) }}"
                        onsubmit="return confirm('Bạn có chắc muốn đóng hội thoại này?')"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="staff-chat-btn staff-chat-btn-danger"
                        >
                            Đóng hội thoại
                        </button>

                    </form>

                </div>

            @endif

        </aside>

    </div>

</div>

@endsection
