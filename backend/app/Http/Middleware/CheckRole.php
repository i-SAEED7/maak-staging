<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(401, 'يجب تسجيل الدخول أولًا.');
        }

        if ($roles !== [] && ! in_array($user->role?->name, $roles, true)) {
            abort(403, 'ليس لديك الدور المطلوب لتنفيذ هذا الإجراء.');
        }

        return $next($request);
    }
}
