<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tư vấn khách hàng
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <div class="px-6 py-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Hội thoại cần xử lý
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Danh sách các cuộc trò chuyện giữa Customer và Admin.
                    </p>
                </div>

                @if ($conversations->count())
                    <div class="divide-y">
                        @foreach ($conversations as $conversation)
                            <a
                                href="{{ route('admin.chat.show', $conversation->id) }}"
                                class="block px-6 py-5 hover:bg-gray-50 transition"
                            >
                                <div class="flex items-center justify-between gap-4">

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-3">
                                            <span class="font-semibold text-gray-900">
                                                Customer #{{ $conversation->customer_id }}
                                            </span>

                                            <span class="text-xs px-2 py-1 rounded-full
                                                {{ $conversation->status === 'waiting'
                                                    ? 'bg-yellow-100 text-yellow-700'
                                                    : 'bg-blue-100 text-blue-700' }}">
                                                {{ strtoupper($conversation->status) }}
                                            </span>
                                        </div>

                                        <p class="mt-2 text-sm font-medium text-gray-700">
                                            {{ $conversation->subject ?: 'Yêu cầu hỗ trợ' }}
                                        </p>

                                        <p class="mt-1 text-xs text-gray-500">
                                            Chủ đề: {{ $conversation->topic }}
                                        </p>
                                    </div>

                                    <div class="text-right shrink-0">
                                        @if ($conversation->assigned_admin_id)
                                            <span class="text-xs text-green-600 font-medium">
                                                Đã nhận
                                            </span>
                                        @else
                                            <span class="text-xs text-orange-600 font-medium">
                                                Chưa nhận
                                            </span>
                                        @endif

                                        @if ($conversation->last_message_at)
                                            <p class="mt-1 text-xs text-gray-400">
                                                {{ $conversation->last_message_at->format('d/m/Y H:i') }}
                                            </p>
                                        @endif
                                    </div>

                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t">
                        {{ $conversations->links() }}
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <p class="text-gray-500">
                            Hiện không có hội thoại nào cần xử lý.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>