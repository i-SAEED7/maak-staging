<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class StudentLifecycleTest extends TestCase
{
    public function test_student_lifecycle_blueprint_is_pending_implementation(): void
    {
        $this->markTestSkipped('Blueprint test pending factories, authenticated fixtures, and tenant-aware assertions.');
    }
}
