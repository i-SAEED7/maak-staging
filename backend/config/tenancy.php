<?php

declare(strict_types=1);

return [
    'column' => 'school_id',
    'bypass_roles' => ['super_admin', 'admin'],
    'supervisor_assignment_table' => 'user_school_assignments',
];
