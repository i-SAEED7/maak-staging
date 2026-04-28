<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RoleName;
use App\Models\EducationProgram;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'stage' => ['required', 'string', 'max:100'],
            'program_type' => [
                'nullable',
                'string',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value !== null && $value !== '' && ! EducationProgram::query()->where('name_ar', $value)->exists()) {
                        $fail('نوع البرنامج المحدد غير موجود ضمن البرامج التعليمية.');
                    }
                }
            ],
            'program_types' => ['nullable', 'array', 'min:1'],
            'program_types.*' => [
                'string',
                'distinct',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! EducationProgram::query()->where('name_ar', $value)->exists()) {
                        $fail('أحد أنواع البرامج المحددة غير موجود ضمن البرامج التعليمية.');
                    }
                },
            ],
            'gender' => ['nullable', 'string', Rule::in(['بنين', 'بنات', 'غير محدد'])],
            'location_lat' => ['nullable', 'numeric'],
            'location_lng' => ['nullable', 'numeric'],
            'principal_id' => ['nullable', 'integer', 'exists:users,id', $this->mustBelongToRole(RoleName::PRINCIPAL)],
            'supervisor_id' => ['nullable', 'integer', 'exists:users,id', $this->mustBelongToRole(RoleName::SUPERVISOR)],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->input('name', $this->input('name_ar')),
            'program_types' => $this->input('program_types', $this->normalizeProgramTypes()),
            'location_lat' => $this->input('location_lat', $this->input('latitude')),
            'location_lng' => $this->input('location_lng', $this->input('longitude')),
            'principal_id' => $this->input('principal_id', $this->input('principal_user_id')),
            'supervisor_id' => $this->input('supervisor_id', $this->input('supervisor_user_id')),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $programType = trim((string) $this->input('program_type', ''));

            if ($programType === '' && count((array) $this->input('program_types', [])) === 0) {
                $validator->errors()->add('program_types', 'يجب اختيار برنامج واحد على الأقل.');
            }
        });
    }

    private function normalizeProgramTypes(): array
    {
        $programType = $this->input('program_type');

        if (is_array($programType)) {
            return $programType;
        }

        if (! is_string($programType) || trim($programType) === '') {
            return [];
        }

        return collect(preg_split('/،|,/', $programType) ?: [])
            ->map(static fn (string $value): string => trim($value))
            ->filter()
            ->values()
            ->all();
    }

    protected function mustBelongToRole(string $roleName): \Closure
    {
        return static function (string $attribute, mixed $value, \Closure $fail) use ($roleName): void {
            if ($value === null || $value === '') {
                return;
            }

            $exists = User::query()
                ->whereKey($value)
                ->whereHas('role', fn ($query) => $query->where('name', $roleName))
                ->exists();

            if (! $exists) {
                $fail('المستخدم المختار لا يطابق الدور المطلوب.');
            }
        };
    }
}
