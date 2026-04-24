<?php

declare(strict_types=1);

namespace App\Http\Requests;

class UpdateSchoolRequest extends StoreSchoolRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
