<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUserId = $request->user()?->id;
        $currentRecipient = $this->whenLoaded('recipients', fn () => $this->recipients
            ->firstWhere('recipient_user_id', $currentUserId));

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'school_id' => $this->school_id,
            'school' => $this->whenLoaded('school', fn (): ?array => $this->school ? [
                'id' => $this->school->id,
                'name_ar' => $this->school->name_ar,
                'program_type' => $this->school->program_type,
            ] : null),
            'thread_key' => $this->thread_key,
            'subject' => $this->subject,
            'body' => $this->body,
            'parent_message_id' => $this->parent_message_id,
            'created_at' => $this->created_at?->toAtomString(),
            'is_sent_by_current_user' => $currentUserId !== null && (int) $this->sender_user_id === (int) $currentUserId,
            'current_user_read_at' => $currentRecipient?->read_at?->toAtomString(),
            'sender' => $this->whenLoaded('sender', fn (): ?array => $this->sender ? [
                'id' => $this->sender->id,
                'full_name' => $this->sender->full_name,
                'email' => $this->sender->email,
                'role' => $this->sender->role?->name,
            ] : null),
            'recipients' => $this->whenLoaded('recipients', function () {
                return $this->recipients->map(static fn ($recipient): array => [
                    'id' => $recipient->id,
                    'recipient_user_id' => $recipient->recipient_user_id,
                    'recipient_name' => $recipient->recipient?->full_name,
                    'recipient_email' => $recipient->recipient?->email,
                    'recipient_role' => $recipient->recipient?->role?->name,
                    'read_at' => $recipient->read_at?->toAtomString(),
                ])->values()->all();
            }),
        ];
    }
}
