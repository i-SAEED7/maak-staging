<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class UpdateStudentRequest
{
    public function rules(): array
    {
        return (new StoreStudentRequest())->rules();
    }
}
