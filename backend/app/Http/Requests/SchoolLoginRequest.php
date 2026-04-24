<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class SchoolLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'min:8'],
            'school_code' => ['required', 'string', 'max:20', 'regex:/^JED-(S|I|P)-\d{5}$/'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'أكمل بيانات الدخول المطلوبة.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
