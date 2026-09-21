<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tư vấn Customer #{{ $conversation->customer_id }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $conversation->subject ?: 'Yêu cầu hỗ trợ' }}
                </p>
            </div>

            <a
                href="{{ route('admin.chat.index') }}"
                class="text-sm text-gray-600 hover:text-gray-900"
            >
                ← Quay lại
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl overflow-hidden">

                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <div>
                        <span class="text-sm text-gray-500">
                            Trạng thái:
                        </span>

                        <span class="ml-2 font-semibold text-gray-800">
                            {{ strtoupper($conversation->status) }}
                        </span>
                    </div>

                    @if ($conversation->assigned_admin_id === null)
                        <form
                            method="POST"
                            action="{{ route('admin.chat.accept', $conversation->id) }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                            >
                                Nhận hội thoại
                            </button>
                        </form>
                    @else
                        <span class="text-sm text-green-600 font-medium">
                            Admin #{{ $conversation->assigned_admin_id }} đang xử lý
                        </span>
                    @endif
                </div>

                <div class="p-6 space-y-4 max-h-[600px] overflow-y-auto">
                    @forelse ($messages as $message)

                        @php
                            $isCustomer =
                                (int) $message->sender_id ===
                                (int) $conversation->customer_id;
                        @endphp

                        <div class="flex {{ $isCustomer ? 'justify-start' : 'justify-end' }}">
                            <div class="max-w-[75%]">

                                <div class="text-xs text-gray-400 mb-1">
                                    {{ $isCustomer ? 'Customer #' . $conversation->customer_id : 'Admin #' . $message->sender_id }}
                                </div>

                                <div class="rounded-xl px-4 py-3
                                    {{ $isCustomer
                                        ? 'bg-gray-100 text-gray-800'
                                        : 'bg-green-600 text-white' }}"
                                >
                                    <p class="whitespace-pre-wrap break-words">
                                        {{ $message->content }}
                                    </p>
                                </div>

                                @if ($message->sent_at)
                                    <div class="mt-1 text-xs text-gray-400
                                        {{ $isCustomer ? 'text-left' : 'text-right' }}">
                                        {{ $message->sent_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif

                            </div>
                        </div>

                    @empty
                        <div class="text-center text-gray-500 py-10">
                            Chưa có tin nhắn.
                        </div>
                    @endforelse
                </div>

                @if ($conversation->status !== 'closed')

                    <div class="border-t p-6">

                        @if ((int) $conversation->assigned_admin_id === (int) auth()->id())

                            <form
                                method="POST"
                                action="{{ route('admin.chat.reply', $conversation->id) }}"
                                class="space-y-4"
                            >
                                @csrf

                                <textarea
                                    name="content"
                                    rows="4"
                                    maxlength="5000"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                    placeholder="Nhập nội dung phản hồi..."
                                ></textarea>

                                @error('content')
                                    <p class="text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                                <div class="flex items-center justify-between gap-3">

                                    <button
                                        type="submit"
                                        class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700"
                                    >
                                        Gửi phản hồi
                                    </button>

                                    <button
                                        type="submit"
                                        form="close-conversation-form"
                                        class="px-5 py-2.5 bg-gray-700 text-white rounded-lg hover:bg-gray-800"
                                    >
                                        Đóng hội thoại
                                    </button>

                                </div>
                            </form>

                            <form
                                id="close-conversation-form"
                                method="POST"
                                action="{{ route('admin.chat.close', $conversation->id) }}"
                                class="hidden"
                            >
                                @csrf
                            </form>

                        @else

                            <div class="rounded-lg bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-700">
                                Bạn cần nhận hội thoại trước khi phản hồi.
                            </div>

                        @endif

                    </div>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>