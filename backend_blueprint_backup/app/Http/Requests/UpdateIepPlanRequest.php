<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class UpdateIepPlanRequest
{
    public function rules(): array
    {
        return (new StoreIepPlanRequest())->rules();
    }
}
