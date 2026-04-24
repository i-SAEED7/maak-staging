<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\School;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class MessagingService
{
    public function __construct(
        private readonly TenantService $tenantService,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function listThreads(User $actor): array
    {
        $messages = $this->visibleMessagesQuery($actor)
            ->with(['sender.role', 'recipients.recipient.role', 'school'])
            ->latest('created_at')
            ->get();

        return $messages
            ->groupBy('thread_key')
            ->map(function (Collection $threadMessages) use ($actor): array {
                $latest = $threadMessages->sortByDesc('created_at')->first();
                $recipientEntries = $threadMessages
                    ->flatMap(fn (Message $message) => $message->recipients)
                    ->filter();
                $latestUnread = $threadMessages
                    ->filter(fn (Message $message): bool => $message->recipients
                        ->contains(fn (MessageRecipient $recipient): bool => $recipient->read_at === null))
                    ->sortByDesc('created_at')
                    ->first();

                $participants = $threadMessages
                    ->flatMap(function (Message $message) {
                        return collect([$message->sender])
                            ->merge($message->recipients->map->recipient);
                    })
                    ->filter()
                    ->unique('id')
                    ->values()
                    ->map(static fn (User $user): array => [
                        'id' => $user->id,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'role' => $user->role?->name,
                    ])
                    ->all();

                $unreadCount = $recipientEntries
                    ->filter(fn (MessageRecipient $recipient): bool => $recipient->read_at === null)
                    ->count();
                $recipientNames = $recipientEntries
                    ->map(fn (MessageRecipient $recipient): ?string => $recipient->recipient?->full_name)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                return [
                    'thread_key' => $latest->thread_key,
                    'school' => $latest->school ? [
                        'id' => $latest->school->id,
                        'name_ar' => $latest->school->name_ar,
                    ] : null,
                    'subject' => $latest->subject,
                    'latest_message_id' => $latest->id,
                    'latest_message_excerpt' => Str::limit($latest->body, 120),
                    'latest_message_at' => $latest->created_at?->toAtomString(),
                    'latest_sender_name' => $latest->sender?->full_name,
                    'recipient_names' => $recipientNames,
                    'read_status' => $unreadCount > 0 ? 'غير مقروءة' : 'مقروءة',
                    'thread_status' => $unreadCount > 0 ? 'مفتوحة' : 'مغلقة',
                    'latest_unread_message_id' => $latestUnread?->id,
                    'message_count' => $threadMessages->count(),
                    'unread_count' => $unreadCount,
                    'participants' => $participants,
                ];
            })
            ->sortByDesc('latest_message_at')
            ->values()
            ->all();
    }

    public function getThread(string $threadKey, User $actor): array
    {
        $messages = $this->visibleMessagesQuery($actor)
            ->where('thread_key', $threadKey)
            ->with(['sender.role', 'recipients.recipient.role', 'school'])
            ->orderBy('created_at')
            ->get();

        if ($messages->isEmpty()) {
            throw ValidationException::withMessages([
                'thread_key' => ['سلسلة الرسائل غير موجودة أو غير متاحة لهذا المستخدم.'],
            ]);
        }

        $participants = $messages
            ->flatMap(function (Message $message) {
                return collect([$message->sender])
                    ->merge($message->recipients->map->recipient);
            })
            ->filter()
            ->unique('id')
            ->values()
            ->map(static fn (User $user): array => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role?->name,
            ])
            ->all();

        return [
            'thread' => [
                'thread_key' => $threadKey,
                'subject' => $messages->last()?->subject,
                'participants' => $participants,
                'message_count' => $messages->count(),
            ],
            'messages' => MessageResource::collection($messages)->resolve(),
        ];
    }

    public function send(array $data, User $actor): Message
    {
        $schoolId = $data['school_id']
            ?? $this->tenantContext->schoolId()
            ?? ($actor->school_id !== null ? (int) $actor->school_id : null);

        if ($schoolId === null) {
            $schoolId = (int) User::query()
                ->whereIn('id', collect($data['recipient_ids'] ?? [])->map(static fn (mixed $id): int => (int) $id)->all())
                ->whereNotNull('school_id')
                ->value('school_id');
        }

        if ($schoolId === null || $schoolId <= 0) {
            throw ValidationException::withMessages([
                'school_id' => ['تعذر تحديد المدرسة الحالية لإرسال الرسالة.'],
            ]);
        }

        $this->tenantService->assertCanAccessSchoolId($actor, $schoolId);

        $recipientIds = collect($data['recipient_ids'] ?? [])
            ->map(static fn (mixed $id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0 && $id !== (int) $actor->id)
            ->unique()
            ->values();

        if ($recipientIds->isEmpty()) {
            throw ValidationException::withMessages([
                'recipient_ids' => ['يجب تحديد مستلم واحد على الأقل بخلاف المرسل نفسه.'],
            ]);
        }

        $recipients = User::query()
            ->with('role')
            ->whereIn('id', $recipientIds)
            ->when($actor->role?->name !== 'super_admin', function (Builder $query) use ($schoolId): void {
                $query->where(function (Builder $recipientQuery) use ($schoolId): void {
                    $recipientQuery
                        ->where('school_id', $schoolId)
                        ->orWhereHas('schoolAssignments', fn (Builder $assignmentQuery) => $assignmentQuery->where('school_id', $schoolId));
                });
            })
            ->get();

        if ($actor->role?->name === 'supervisor') {
            $recipients = $recipients->filter(static function (User $recipient): bool {
                $roleName = $recipient->role?->name;

                return in_array($roleName, ['teacher', 'principal'], true);
            })->values();
        }

        if ($recipients->count() !== $recipientIds->count()) {
            throw ValidationException::withMessages([
                'recipient_ids' => ['بعض المستلمين غير متاحين داخل نطاق المدرسة الحالية.'],
            ]);
        }

        $parentMessage = null;

        if (! empty($data['parent_message_id'])) {
            $parentMessage = $this->visibleMessagesQuery($actor)->find($data['parent_message_id']);

            if ($parentMessage === null) {
                throw ValidationException::withMessages([
                    'parent_message_id' => ['الرسالة الأب غير متاحة داخل هذا السياق.'],
                ]);
            }
        }

        $threadKey = $data['thread_key']
            ?? $parentMessage?->thread_key
            ?? 'thread_' . Str::lower(Str::random(16));

        return DB::transaction(function () use ($actor, $data, $schoolId, $recipients, $threadKey, $parentMessage): Message {
            $message = Message::query()->create([
                'uuid' => (string) Str::uuid(),
                'school_id' => $schoolId,
                'thread_key' => $threadKey,
                'sender_user_id' => $actor->id,
                'subject' => $data['subject'] ?? $parentMessage?->subject,
                'body' => $data['body'],
                'parent_message_id' => $parentMessage?->id,
            ]);

            foreach ($recipients as $recipient) {
                MessageRecipient::query()->create([
                    'message_id' => $message->id,
                    'recipient_user_id' => $recipient->id,
                    'read_at' => null,
                    'created_at' => now(),
                ]);
            }

            return $message->load(['sender.role', 'recipients.recipient.role', 'school']);
        });
    }

    public function markRead(Message $message, User $actor): MessageRecipient
    {
        $recipient = MessageRecipient::query()
            ->where('message_id', $message->id)
            ->where('recipient_user_id', $actor->id)
            ->first();

        if ($recipient === null) {
            throw ValidationException::withMessages([
                'message_id' => ['هذه الرسالة ليست موجهة للمستخدم الحالي.'],
            ]);
        }

        if ($recipient->read_at === null) {
            $recipient->update([
                'read_at' => now(),
            ]);
        }

        return $recipient->refresh()->load('recipient');
    }

    public function assertAccessible(Message $message, User $actor): void
    {
        if ($actor->role?->name === 'super_admin') {
            return;
        }

        $this->tenantService->assertCanAccessSchoolId($actor, $message->school_id !== null ? (int) $message->school_id : null);

        $isVisible = $this->visibleMessagesQuery($actor)
            ->whereKey($message->id)
            ->exists();

        if (! $isVisible) {
            throw ValidationException::withMessages([
                'message_id' => ['الرسالة غير متاحة ضمن هذا السياق.'],
            ]);
        }
    }

    public function recipientsForSchool(User $actor, int $schoolId): \Illuminate\Database\Eloquent\Collection
    {
        if ($schoolId <= 0 || ! School::query()->whereKey($schoolId)->exists()) {
            throw ValidationException::withMessages([
                'school_id' => ['المدرسة المحددة غير موجودة.'],
            ]);
        }

        $this->tenantService->assertCanAccessSchoolId($actor, $schoolId);

        return User::query()
            ->with(['role', 'school'])
            ->where('status', 'active')
            ->where(function (Builder $query) use ($schoolId): void {
                $query
                    ->where('school_id', $schoolId)
                    ->orWhereHas('schoolAssignments', fn (Builder $assignmentQuery) => $assignmentQuery->where('school_id', $schoolId));
            })
            ->whereHas('role', function (Builder $roleQuery) use ($actor): void {
                $allowedRoles = match ($actor->role?->name) {
                    'supervisor' => ['teacher', 'principal'],
                    'principal' => ['teacher', 'parent', 'supervisor'],
                    'teacher' => ['principal', 'parent'],
                    'parent' => ['teacher', 'principal'],
                    default => ['teacher', 'principal', 'supervisor', 'parent', 'admin'],
                };

                $roleQuery->whereIn('name', $allowedRoles);
            })
            ->whereKeyNot($actor->id)
            ->orderBy('full_name')
            ->get();
    }

    private function visibleMessagesQuery(User $actor): Builder
    {
        if ($actor->role?->name === 'super_admin') {
            return Message::query();
        }

        if ($actor->role?->name === 'supervisor') {
            $schoolIds = $this->tenantService->accessibleSchoolIds($actor);

            return Message::query()->whereIn('school_id', $schoolIds);
        }

        return Message::query()
            ->where(function (Builder $query) use ($actor): void {
                $query
                    ->where('sender_user_id', $actor->id)
                    ->orWhereHas('recipients', fn (Builder $recipientQuery) => $recipientQuery->where('recipient_user_id', $actor->id));
            });
    }
}
