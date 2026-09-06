@extends('layouts.customer')


@section(
    'title',
    'Chat hỗ trợ - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/chat.css') }}"
>

@endpush


@section('content')

<section class="customer-chat-page">

    <div class="customer-container">

        <div class="customer-chat-heading">

            <div>

                <span class="customer-chat-eyebrow">
                    💬 HỖ TRỢ MINI GROVE
                </span>

                <h1>
                    Chat với MiniGrove
                </h1>

                <p>
                    Gửi câu hỏi về sản phẩm, đơn hàng hoặc cách chăm sóc Terrarium.
                </p>

            </div>

            @if ($conversation)

                <span
                    class="
                        customer-chat-status
                        customer-chat-status-{{ $conversation->status }}
                    "
                >
                    @if ($conversation->status === 'open')
                        Đang mở
                    @elseif ($conversation->status === 'waiting')
                        Đang chờ phản hồi
                    @else
                        {{ ucfirst($conversation->status) }}
                    @endif
                </span>

            @endif

        </div>


        <div class="customer-chat-shell">

            <div class="customer-chat-main">

                <div class="customer-chat-messages">

                    @if ($conversation && $messages->isNotEmpty())

                        @foreach ($messages as $message)

                            @php
                                $isMine =
                                    (int) $message->sender_id
                                    === (int) auth()->id();
                            @endphp

                            <div
                                class="
                                    customer-chat-message-row
                                    {{
                                        $isMine
                                            ? 'customer-chat-message-row-mine'
                                            : 'customer-chat-message-row-support'
                                    }}
                                "
                            >

                                <div
                                    class="
                                        customer-chat-bubble
                                        {{
                                            $isMine
                                                ? 'customer-chat-bubble-mine'
                                                : 'customer-chat-bubble-support'
                                        }}
                                    "
                                >

                                    <div class="customer-chat-message-author">
                                        {{
                                            $isMine
                                                ? 'Bạn'
                                                : 'MiniGrove Support'
                                        }}
                                    </div>

                                    <div class="customer-chat-message-content">
                                        {{ $message->content }}
                                    </div>

                                    <div class="customer-chat-message-time">
                                        {{
                                            $message->sent_at
                                                ? $message->sent_at->format('H:i d/m/Y')
                                                : ''
                                        }}
                                    </div>

                                </div>

                            </div>

                        @endforeach

                    @else

                        <div class="customer-chat-empty">

                            <div class="customer-chat-empty-icon">
                                🌿
                            </div>

                            <h2>
                                Bắt đầu cuộc trò chuyện
                            </h2>

                            <p>
                                Hãy gửi tin nhắn đầu tiên. Nhân viên MiniGrove sẽ phản hồi trong hội thoại này.
                            </p>

                        </div>

                    @endif

                </div>


                <form
                    method="POST"
                    action="{{ route('chat.store') }}"
                    class="customer-chat-form"
                >

                    @csrf

                    <label
                        for="content"
                        class="customer-chat-label"
                    >
                        Tin nhắn
                    </label>

                    <div class="customer-chat-compose">

                        <textarea
                            id="content"
                            name="content"
                            rows="3"
                            maxlength="5000"
                            placeholder="Nhập nội dung cần MiniGrove hỗ trợ..."
                            required
                        >{{ old('content') }}</textarea>

                        <button
                            type="submit"
                            class="customer-chat-send"
                        >
                            Gửi tin nhắn
                        </button>

                    </div>

                    @error('content')

                        <div class="customer-chat-error">
                            {{ $message }}
                        </div>

                    @enderror

                </form>

            </div>


            <aside class="customer-chat-sidebar">

                <div class="customer-chat-info-card">

                    <h3>
                        Hỗ trợ trực tiếp
                    </h3>

                    <p>
                        Cuộc trò chuyện của bạn chỉ được dùng để xử lý yêu cầu hỗ trợ trong MiniGrove.
                    </p>

                    @if ($conversation)

                        <div class="customer-chat-info-row">

                            <span>
                                Mã hội thoại
                            </span>

                            <strong>
                                #{{ $conversation->id }}
                            </strong>

                        </div>

                        <div class="customer-chat-info-row">

                            <span>
                                Chủ đề
                            </span>

                            <strong>
                                {{ $conversation->subject ?: 'Yêu cầu hỗ trợ' }}
                            </strong>

                        </div>

                    @endif

                </div>


                @if ($closedConversations->isNotEmpty())

                    <div class="customer-chat-history">

                        <h3>
                            Hội thoại đã đóng
                        </h3>

                        @foreach ($closedConversations as $closedConversation)

                            <div class="customer-chat-history-item">

                                <strong>
                                    #{{ $closedConversation->id }}
                                </strong>

                                <span>
                                    {{
                                        $closedConversation->subject
                                        ?: 'Yêu cầu hỗ trợ'
                                    }}
                                </span>

                                <small>
                                    {{
                                        $closedConversation->closed_at
                                            ? $closedConversation->closed_at->format('d/m/Y H:i')
                                            : ''
                                    }}
                                </small>

                            </div>

                        @endforeach

                    </div>

                @endif

            </aside>

        </div>

    </div>

</section>

@endsection
