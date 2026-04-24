<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class UpdateSupervisorVisitRequest
{
    public function rules(): array
    {
        return (new StoreSupervisorVisitRequest())->rules();
    }
}
