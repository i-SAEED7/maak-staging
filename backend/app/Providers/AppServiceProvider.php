<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\TenantContext;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantContext::class, static fn (): TenantContext => new TenantContext());
    }

    public function boot(): void
    {
        // Bootstrap application-wide behavior.
    }
}
