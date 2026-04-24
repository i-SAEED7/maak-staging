<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class AssignPrincipalRequest
{
    public function rules(): array
    {
        return [
            'principal_user_id' => ['required', 'integer'],
        ];
    }
}
