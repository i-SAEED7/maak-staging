<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Models\Message;
use App\Services\MessagingService;
use App\Services\NotificationService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MessageController extends Controller
{
    public function __construct(
        private readonly MessagingService $messagingService,
        private readonly NotificationService $notificationService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Message::class);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المحادثات',
            'data' => $this->messagingService->listThreads($request->user()),
        ]);
    }

    public function recipients(Request $request): JsonResponse
    {
        $this->authorize('send', Message::class);
        $schoolId = $request->integer('school_id');
        $recipients = $this->messagingService->recipientsForSchool($request->user(), $schoolId);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المستلمين المتاحين',
            'data' => UserResource::collection($recipients),
        ]);
    }

    public function thread(Request $request, string $threadKey): JsonResponse
    {
        $this->authorize('viewThread', Message::class);
        $payload = $this->messagingService->getThread($threadKey, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم جلب سلسلة الرسائل',
            'data' => $payload['messages'],
            'meta' => [
                'thread' => $payload['thread'],
            ],
        ]);
    }

    public function store(SendMessageRequest $request): JsonResponse
    {
        $this->authorize('send', Message::class);
        $message = $this->messagingService->send($request->validated(), $request->user());
        $senderRoleLabel = $request->user()->role?->name === 'supervisor'
            ? 'المشرف التربوي'
            : $request->user()->full_name;

        foreach ($message->recipients as $recipient) {
            if ($recipient->recipient_user_id === null) {
                continue;
            }

            $this->notificationService->send([
                'school_id' => $message->school_id,
                'user_id' => $recipient->recipient_user_id,
                'created_by_user_id' => $request->user()->id,
                'type' => 'message.received',
                'title' => sprintf('رسالة جديدة من %s', $senderRoleLabel),
                'body' => $message->subject ?: 'لديك رسالة داخلية جديدة.',
                'data' => [
                    'thread_key' => $message->thread_key,
                    'school_name' => $message->school?->name_ar,
                    'sender_name' => $request->user()->full_name,
                    'entity_type' => 'message_thread',
                    'entity_id' => $message->id,
                    'action_url' => '/app/messages/' . $message->thread_key,
                    'action_label' => 'عرض الرسالة',
                ],
            ]);
        }

        app(AuditLogger::class)->log(
            $request->user(),
            'send',
            $message,
            [],
            [
                'subject' => $message->subject,
                'school_id' => $message->school_id,
                'sender_user_id' => $message->sender_user_id,
                'thread_key' => $message->thread_key,
            ],
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الرسالة',
            'data' => new MessageResource($message),
        ], 201);
    }

    public function markRead(Request $request, Message $message): JsonResponse
    {
        $this->authorize('markRead', $message);
        $this->messagingService->assertAccessible($message, $request->user());
        $recipient = $this->messagingService->markRead($message, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'تم تعليم الرسالة كمقروءة',
            'data' => [
                'message_id' => $message->id,
                'recipient_user_id' => $recipient->recipient_user_id,
                'read_at' => $recipient->read_at?->toAtomString(),
            ],
        ]);
    }
}
