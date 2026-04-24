<?php

use App\Http\Middleware\AuditLog;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\SetTenantScope;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(static function ($request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            return '/login';
        });

        $middleware->alias([
            'audit' => AuditLog::class,
            'role' => CheckRole::class,
            'tenant' => SetTenantScope::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
