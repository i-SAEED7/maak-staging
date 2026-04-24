<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\EducationProgram;
use Illuminate\Foundation\Http\FormRequest;

final class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
            'program_type' => [
                'nullable',
                'string',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    if (! EducationProgram::query()->where('name_ar', $value)->exists()) {
                        $fail('نوع البرنامج المحدد غير موجود ضمن البرامج التعليمية.');
                    }
                }
            ],
            'recipient_ids' => ['required', 'array', 'min:1'],
            'recipient_ids.*' => ['integer', 'exists:users,id', 'distinct'],
            'thread_key' => ['nullable', 'string', 'max:100'],
            'parent_message_id' => ['nullable', 'integer', 'exists:messages,id'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ];
    }
}
