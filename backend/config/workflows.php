<?php

declare(strict_types=1);

return [
    'iep' => [
        'draft' => ['pending_principal_review'],
        'pending_principal_review' => ['approved', 'rejected'],
        'pending_supervisor_review' => ['approved', 'rejected'],
        'rejected' => ['draft'],
        'approved' => ['archived'],
        'archived' => [],
    ],
];
