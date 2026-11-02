<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    public function scopeWithoutTenant(Builder $builder): Builder
    {
        return $builder->withoutGlobalScope(TenantScope::class);
    }
}
