<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Trang Chat của Customer.
     */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | CONVERSATION ĐANG HOẠT ĐỘNG
        |--------------------------------------------------------------------------
        |
        | Mỗi Customer chỉ dùng Conversation mới nhất
        | chưa đóng để tiếp tục trao đổi.
        |
        */

        $conversation = Conversation::query()
            ->where(
                'customer_id',
                $request->user()->id
            )
            ->whereIn(
                'status',
                [
                    'open',
                    'waiting',
                ]
            )
            ->select([
                'id',
                'customer_id',
                'assigned_admin_id',
                'topic',
                'subject',
                'status',
                'last_message_at',
                'created_at',
            ])
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | TIN NHẮN
        |--------------------------------------------------------------------------
        */

        $messages = collect();

        if ($conversation) {
            $messages = Message::query()
                ->where(
                    'conversation_id',
                    $conversation->id
                )
                ->select([
                    'id',
                    'conversation_id',
                    'sender_id',
                    'content',
                    'message_type',
                    'sent_at',
                    'read_at',
                ])
                ->orderBy('sent_at')
                ->orderBy('id')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | ĐÁNH DẤU TIN NHẮN ADMIN ĐÃ ĐỌC
            |--------------------------------------------------------------------------
            |
            | Chỉ đánh dấu những tin không phải Customer gửi.
            |
            */

            Message::query()
                ->where(
                    'conversation_id',
                    $conversation->id
                )
                ->where(
                    'sender_id',
                    '!=',
                    $request->user()->id
                )
                ->whereNull(
                    'read_at'
                )
                ->update([
                    'read_at' => now(),
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | LỊCH SỬ HỘI THOẠI ĐÃ ĐÓNG
        |--------------------------------------------------------------------------
        */

        $closedConversations = Conversation::query()
            ->where(
                'customer_id',
                $request->user()->id
            )
            ->where(
                'status',
                'closed'
            )
            ->select([
                'id',
                'subject',
                'topic',
                'closed_at',
                'created_at',
            ])
            ->latest('id')
            ->limit(10)
            ->get();

        return view(
            'chat.index',
            compact(
                'conversation',
                'messages',
                'closedConversations'
            )
        );
    }

    /**
     * Customer gửi tin nhắn.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate(
            [
                'content' => [
                    'required',
                    'string',
                    'max:5000',
                ],
            ],
            [
                'content.required' =>
                    'Vui lòng nhập nội dung tin nhắn.',

                'content.max' =>
                    'Tin nhắn không được vượt quá 5000 ký tự.',
            ]
        );

        DB::transaction(
            function () use (
                $request,
                $validated
            ) {
                /*
                |--------------------------------------------------------------------------
                | TÌM CONVERSATION ĐANG HOẠT ĐỘNG
                |--------------------------------------------------------------------------
                */

                $conversation = Conversation::query()
                    ->where(
                        'customer_id',
                        $request->user()->id
                    )
                    ->whereIn(
                        'status',
                        [
                            'open',
                            'waiting',
                        ]
                    )
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | NẾU CHƯA CÓ → TẠO MỚI
                |--------------------------------------------------------------------------
                */

                if (!$conversation) {
                    $conversation = Conversation::create([
                        'customer_id' =>
                            $request->user()->id,

                        'assigned_admin_id' =>
                            null,

                        'topic' =>
                            'GENERAL',

                        'subject' =>
                            'Yêu cầu hỗ trợ',

                        'status' =>
                            'open',

                        'last_message_at' =>
                            now(),

                        'closed_by' =>
                            null,

                        'closed_at' =>
                            null,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | TẠO MESSAGE
                |--------------------------------------------------------------------------
                */

                Message::create([
                    'conversation_id' =>
                        $conversation->id,

                    'sender_id' =>
                        $request->user()->id,

                    'content' =>
                        trim(
                            $validated['content']
                        ),

                    'message_type' =>
                        'TEXT',

                    'sent_at' =>
                        now(),

                    'read_at' =>
                        null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | CẬP NHẬT CONVERSATION
                |--------------------------------------------------------------------------
                */

                $conversation->update([
                    'status' =>
                        'open',

                    'last_message_at' =>
                        now(),
                ]);
            }
        );

        return redirect()
            ->route('chat.index')
            ->with(
                'success',
                'Tin nhắn đã được gửi đến MiniGrove.'
            );
    }
}