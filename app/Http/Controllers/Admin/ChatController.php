<?php

namespace App\Http\Controllers\Admin;

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
     * Danh sách hội thoại Customer cần Admin xử lý.
     *
     * Privacy:
     * - Không load thông tin PII của Customer.
     * - Admin chỉ sử dụng Customer #ID.
     */
    public function index(Request $request): View
    {
        $adminId = $request->user()->id;

        $conversations = Conversation::query()
            ->whereIn(
                'status',
                [
                    'open',
                    'waiting',
                ]
            )
            ->where(function ($query) use ($adminId) {
                $query
                    ->whereNull('assigned_admin_id')
                    ->orWhere(
                        'assigned_admin_id',
                        $adminId
                    );
            })
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
            ->orderByRaw(
                'CASE WHEN assigned_admin_id = ? THEN 0 ELSE 1 END',
                [$adminId]
            )
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view(
            'admin.chat.index',
            compact('conversations')
        );
    }

    /**
     * Chi tiết một hội thoại.
     *
     * Admin chỉ được xem:
     * - Hội thoại chưa được nhận;
     * - Hoặc hội thoại do chính Admin đó nhận.
     */
    public function show(
        Request $request,
        int $conversation
    ): View {
        $adminId = $request->user()->id;

        $conversation = Conversation::query()
            ->whereKey($conversation)
            ->whereIn(
                'status',
                [
                    'open',
                    'waiting',
                ]
            )
            ->where(function ($query) use ($adminId) {
                $query
                    ->whereNull('assigned_admin_id')
                    ->orWhere(
                        'assigned_admin_id',
                        $adminId
                    );
            })
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
            ->firstOrFail();

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
        | Đánh dấu tin nhắn Customer đã đọc
        |--------------------------------------------------------------------------
        */

        Message::query()
            ->where(
                'conversation_id',
                $conversation->id
            )
            ->where(
                'sender_id',
                $conversation->customer_id
            )
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return view(
            'admin.chat.show',
            compact(
                'conversation',
                'messages'
            )
        );
    }

    /**
     * Admin nhận xử lý hội thoại.
     */
    public function accept(
        Request $request,
        int $conversation
    ): RedirectResponse {
        $adminId = $request->user()->id;

        DB::transaction(
            function () use (
                $conversation,
                $adminId
            ) {
                $lockedConversation = Conversation::query()
                    ->whereKey($conversation)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_if(
                    $lockedConversation->status === 'closed',
                    404
                );

                abort_if(
                    $lockedConversation->assigned_admin_id !== null
                    && (int) $lockedConversation->assigned_admin_id
                    !== (int) $adminId,
                    409,
                    'Hội thoại đã được Admin khác nhận.'
                );

                if (
                    $lockedConversation->assigned_admin_id
                    === null
                ) {
                    $lockedConversation->update([
                        'assigned_admin_id' => $adminId,
                    ]);
                }
            }
        );

        return redirect()
            ->route(
                'admin.chat.show',
                $conversation
            )
            ->with(
                'success',
                'Bạn đã nhận xử lý hội thoại.'
            );
    }

    /**
     * Admin trả lời Customer.
     *
     * Admin phải nhận hội thoại trước khi trả lời.
     */
    public function reply(
        Request $request,
        int $conversation
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
                    'Vui lòng nhập nội dung phản hồi.',

                'content.max' =>
                    'Tin nhắn không được vượt quá 5000 ký tự.',
            ]
        );

        $adminId = $request->user()->id;

        DB::transaction(
            function () use (
                $conversation,
                $adminId,
                $validated
            ) {
                $lockedConversation = Conversation::query()
                    ->whereKey($conversation)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_if(
                    $lockedConversation->status === 'closed',
                    404
                );

                abort_unless(
                    (int) $lockedConversation->assigned_admin_id
                    === (int) $adminId,
                    403,
                    'Bạn cần nhận hội thoại trước khi phản hồi.'
                );

                $sentAt = now();

                Message::create([
                    'conversation_id' =>
                        $lockedConversation->id,

                    'sender_id' =>
                        $adminId,

                    'content' =>
                        trim($validated['content']),

                    'message_type' =>
                        'TEXT',

                    'sent_at' =>
                        $sentAt,

                    'read_at' =>
                        null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | waiting = Admin đã phản hồi, đang chờ Customer
                |--------------------------------------------------------------------------
                */

                $lockedConversation->update([
                    'status' =>
                        'waiting',

                    'last_message_at' =>
                        $sentAt,
                ]);
            }
        );

        return redirect()
            ->route(
                'admin.chat.show',
                $conversation
            )
            ->with(
                'success',
                'Đã gửi phản hồi cho Customer.'
            );
    }

    /**
     * Admin đóng hội thoại.
     */
    public function close(
        Request $request,
        int $conversation
    ): RedirectResponse {
        $adminId = $request->user()->id;

        DB::transaction(
            function () use (
                $conversation,
                $adminId
            ) {
                $lockedConversation = Conversation::query()
                    ->whereKey($conversation)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_if(
                    $lockedConversation->status === 'closed',
                    404
                );

                abort_unless(
                    (int) $lockedConversation->assigned_admin_id
                    === (int) $adminId,
                    403,
                    'Bạn chỉ có thể đóng hội thoại do mình phụ trách.'
                );

                $lockedConversation->update([
                    'status' =>
                        'closed',

                    'closed_by' =>
                        $adminId,

                    'closed_at' =>
                        now(),
                ]);
            }
        );

        return redirect()
            ->route('admin.chat.index')
            ->with(
                'success',
                'Hội thoại đã được đóng.'
            );
    }
}