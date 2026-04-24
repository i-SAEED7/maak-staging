<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class SendMessageRequest
{
    public function rules(): array
    {
        return [
            'recipient_ids' => ['required', 'array'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ];
    }
}
