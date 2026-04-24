<?php

declare(strict_types=1);

namespace App\Traits;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        static::addGlobalScope('tenant_school', static function (Builder $builder): void {
            $schoolId = app(TenantContext::class)->schoolId();

            if ($schoolId !== null) {
                $builder->where($builder->getModel()->qualifyColumn('school_id'), $schoolId);
            }
        });

        static::creating(static function ($model): void {
            $schoolId = app(TenantContext::class)->schoolId();

            if ($schoolId !== null && empty($model->school_id)) {
                $model->school_id = $schoolId;
            }
        });
    }
}
