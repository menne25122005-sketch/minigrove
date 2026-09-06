<?php

namespace App\Http\Controllers\Staff;

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
     * Danh sách hội thoại Staff có thể xử lý.
     *
     * Privacy:
     * - Không load quan hệ customer().
     * - Không lấy name/email/phone/address của Customer.
     * - Staff chỉ thấy Customer #ID.
     */
    public function index(Request $request): View
    {
        $staffId = $request->user()->id;

        $conversations = Conversation::query()
            ->whereIn('status', ['open', 'waiting'])
            ->where(function ($query) use ($staffId) {
                $query
                    ->whereNull('assigned_staff_id')
                    ->orWhere('assigned_staff_id', $staffId);
            })
            ->select([
                'id',
                'customer_id',
                'assigned_staff_id',
                'topic',
                'subject',
                'status',
                'last_message_at',
                'created_at',
            ])
            ->orderByRaw(
                'CASE WHEN assigned_staff_id = ? THEN 0 ELSE 1 END',
                [$staffId]
            )
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view(
            'staff.chat.index',
            compact('conversations')
        );
    }

    /**
     * Chi tiết một hội thoại.
     *
     * Staff chỉ được mở:
     * - hội thoại chưa ai nhận; hoặc
     * - hội thoại do chính Staff đó nhận.
     */
    public function show(
        Request $request,
        int $conversation
    ): View {
        $staffId = $request->user()->id;

        $conversation = Conversation::query()
            ->whereKey($conversation)
            ->whereIn('status', ['open', 'waiting'])
            ->where(function ($query) use ($staffId) {
                $query
                    ->whereNull('assigned_staff_id')
                    ->orWhere('assigned_staff_id', $staffId);
            })
            ->select([
                'id',
                'customer_id',
                'assigned_staff_id',
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
        | Đánh dấu tin nhắn Customer đã được Staff đọc
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
            'staff.chat.show',
            compact(
                'conversation',
                'messages'
            )
        );
    }

    /**
     * Staff nhận xử lý hội thoại.
     */
    public function accept(
        Request $request,
        int $conversation
    ): RedirectResponse {
        $staffId = $request->user()->id;

        DB::transaction(
            function () use (
                $conversation,
                $staffId
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
                    $lockedConversation->assigned_staff_id !== null
                    && (int) $lockedConversation->assigned_staff_id !== (int) $staffId,
                    409,
                    'Hội thoại đã được nhân viên khác nhận.'
                );

                if ($lockedConversation->assigned_staff_id === null) {
                    $lockedConversation->update([
                        'assigned_staff_id' => $staffId,
                    ]);
                }
            }
        );

        return redirect()
            ->route(
                'staff.chat.show',
                $conversation
            )
            ->with(
                'success',
                'Bạn đã nhận xử lý hội thoại.'
            );
    }

    /**
     * Staff trả lời Customer.
     *
     * Staff phải nhận hội thoại trước khi gửi tin.
     */
    public function reply(
        Request $request,
        int $conversation
    ): RedirectResponse {
        $validated = $request->validate([
            'content' => [
                'required',
                'string',
                'max:5000',
            ],
        ], [
            'content.required' =>
                'Vui lòng nhập nội dung phản hồi.',

            'content.max' =>
                'Tin nhắn không được vượt quá 5000 ký tự.',
        ]);

        $staffId = $request->user()->id;

        DB::transaction(
            function () use (
                $conversation,
                $staffId,
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
                    (int) $lockedConversation->assigned_staff_id
                    === (int) $staffId,
                    403,
                    'Bạn cần nhận hội thoại trước khi phản hồi.'
                );

                $sentAt = now();

                Message::create([
                    'conversation_id' =>
                        $lockedConversation->id,

                    'sender_id' =>
                        $staffId,

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
                | waiting = Staff đã phản hồi, đang chờ Customer
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
                'staff.chat.show',
                $conversation
            )
            ->with(
                'success',
                'Đã gửi phản hồi cho Customer.'
            );
    }

    /**
     * Staff đóng hội thoại.
     */
    public function close(
        Request $request,
        int $conversation
    ): RedirectResponse {
        $staffId = $request->user()->id;

        DB::transaction(
            function () use (
                $conversation,
                $staffId
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
                    (int) $lockedConversation->assigned_staff_id
                    === (int) $staffId,
                    403,
                    'Bạn chỉ có thể đóng hội thoại do mình phụ trách.'
                );

                $lockedConversation->update([
                    'status' =>
                        'closed',

                    'closed_by' =>
                        $staffId,

                    'closed_at' =>
                        now(),
                ]);
            }
        );

        return redirect()
            ->route('staff.chat.index')
            ->with(
                'success',
                'Hội thoại đã được đóng.'
            );
    }
}
