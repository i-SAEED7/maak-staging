<?php

declare(strict_types=1);

namespace App\Providers;

use App\Adapters\NullExternalAdapter;
use App\Contracts\ExternalStudentProvider;
use App\Support\TenantContext;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantContext::class, static fn (): TenantContext => new TenantContext());

        // External integration: swap NullExternalAdapter with NoorAdapter when ready.
        $this->app->singleton(ExternalStudentProvider::class, NullExternalAdapter::class);
    }

    public function boot(): void
    {
        // Bootstrap application-wide behavior.
    }
}
