<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetTenantScope
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {
    }

    public function handle(Request $request, Closure $next, string $mode = 'optional'): Response
    {
        $user = $request->user();

        if ($user !== null) {
            $schoolId = $this->extractRequestedSchoolId($request);
            $resolvedSchoolId = $this->tenantService->resolveSchoolIdForRequest(
                $user,
                $schoolId,
                $mode === 'required',
            );

            $request->attributes->set('tenant_school_id', $resolvedSchoolId);
        }

        return $next($request);
    }

    private function extractRequestedSchoolId(Request $request): ?int
    {
        $headerValue = $request->header('X-School-Id');
        $inputValue = $request->input('school_id');
        $queryValue = $request->query('school_id');

        $candidate = $headerValue ?? $inputValue ?? $queryValue;

        if ($candidate === null || $candidate === '') {
            return null;
        }

        return (int) $candidate;
    }
}
