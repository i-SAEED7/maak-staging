<?php

declare(strict_types=1);

namespace App\Http\Resources;

final class PortfolioResource
{
    public function toArray(): array
    {
        return [
            'id' => null,
            'type' => null,
            'title' => null,
            'completion_rate' => null,
        ];
    }
}
