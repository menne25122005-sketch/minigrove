@extends('layouts.staff')

@section('title', 'Chat - Staff')


@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/staff-chat.css') }}"
    >

@endpush


@section('content')

<div class="staff-chat-page">

    <div class="staff-chat-header">

        <div>

            <span class="staff-chat-eyebrow">
                HỖ TRỢ KHÁCH HÀNG
            </span>

            <h1>
                Chat
            </h1>

            <p>
                Tiếp nhận và xử lý các cuộc hội thoại hỗ trợ của Customer.
            </p>

        </div>

        <div class="staff-chat-count">
            {{ $conversations->total() }}
            hội thoại
        </div>

    </div>


    @if (session('success'))

        <div class="staff-chat-alert staff-chat-alert-success">
            {{ session('success') }}
        </div>

    @endif


    <div class="staff-chat-privacy">

        Staff chỉ sử dụng
        <strong>Customer ID</strong>
        để nhận diện khách hàng.

        Tên, email, số điện thoại, địa chỉ
        và các thông tin cá nhân khác của Customer
        không được hiển thị trong khu vực này.

    </div>


    <div class="staff-chat-card">

        <div class="staff-chat-card-header">

            <div>

                <h2>
                    Hàng đợi hội thoại
                </h2>

                <p>
                    Hội thoại chưa có người nhận và hội thoại bạn đang phụ trách.
                </p>

            </div>

        </div>


        @if ($conversations->isEmpty())

            <div class="staff-chat-empty">

                <div class="staff-chat-empty-icon">
                    💬
                </div>

                <h3>
                    Chưa có hội thoại cần xử lý
                </h3>

                <p>
                    Khi Customer gửi yêu cầu mới, hội thoại sẽ xuất hiện tại đây.
                </p>

            </div>

        @else

            <div class="staff-chat-table-wrap">

                <table class="staff-chat-table">

                    <thead>

                        <tr>

                            <th>
                                Hội thoại
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Chủ đề
                            </th>

                            <th>
                                Trạng thái
                            </th>

                            <th>
                                Tin nhắn cuối
                            </th>

                            <th class="staff-chat-actions-heading">
                                Thao tác
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($conversations as $conversation)

                            @php
                                $isMine =
                                    (int) $conversation->assigned_staff_id
                                    === (int) auth()->id();

                                $isUnassigned =
                                    $conversation->assigned_staff_id === null;
                            @endphp

                            <tr>

                                <td>

                                    <div class="staff-chat-conversation-id">
                                        #{{ $conversation->id }}
                                    </div>

                                    <div class="staff-chat-small">
                                        {{
                                            $conversation->created_at
                                                ? $conversation->created_at->format('d/m/Y H:i')
                                                : ''
                                        }}
                                    </div>

                                </td>

                                <td>

                                    <span class="staff-chat-customer-badge">
                                        Customer #{{ $conversation->customer_id }}
                                    </span>

                                </td>

                                <td>

                                    <div class="staff-chat-subject">
                                        {{ $conversation->subject ?: 'Yêu cầu hỗ trợ' }}
                                    </div>

                                    <div class="staff-chat-small">
                                        {{ $conversation->topic ?: 'GENERAL' }}
                                    </div>

                                </td>

                                <td>

                                    @if ($isMine)

                                        <span class="staff-chat-status staff-chat-status-mine">
                                            Đang phụ trách
                                        </span>

                                    @elseif ($conversation->status === 'waiting')

                                        <span class="staff-chat-status staff-chat-status-waiting">
                                            Đang chờ
                                        </span>

                                    @else

                                        <span class="staff-chat-status staff-chat-status-open">
                                            Chờ tiếp nhận
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <span class="staff-chat-time">
                                        {{
                                            $conversation->last_message_at
                                                ? $conversation->last_message_at->format('d/m/Y H:i')
                                                : 'Chưa có'
                                        }}
                                    </span>

                                </td>

                                <td>

                                    <div class="staff-chat-actions">

                                        <a
                                            href="{{ route('staff.chat.show', $conversation->id) }}"
                                            class="staff-chat-btn staff-chat-btn-secondary"
                                        >
                                            Xem
                                        </a>


                                        @if ($isUnassigned)

                                            <form
                                                method="POST"
                                                action="{{ route('staff.chat.accept', $conversation->id) }}"
                                            >

                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="staff-chat-btn staff-chat-btn-primary"
                                                >
                                                    Nhận xử lý
                                                </button>

                                            </form>

                                        @elseif ($isMine)

                                            <a
                                                href="{{ route('staff.chat.show', $conversation->id) }}"
                                                class="staff-chat-btn staff-chat-btn-primary"
                                            >
                                                Tiếp tục
                                            </a>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            @if ($conversations->hasPages())

                <div class="staff-chat-pagination">
                    {{ $conversations->links() }}
                </div>

            @endif

        @endif

    </div>

</div>

@endsection
