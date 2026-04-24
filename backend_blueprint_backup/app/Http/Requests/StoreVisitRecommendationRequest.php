<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class StoreVisitRecommendationRequest
{
    public function rules(): array
    {
        return [
            'recommendation_text' => ['required', 'string'],
            'owner_user_id' => ['nullable', 'integer'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
