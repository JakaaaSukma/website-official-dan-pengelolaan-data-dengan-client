<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        if (in_array($user->role, ['super_admin_recruitment', 'admin'])) {
            return;
        }

        if (($user->role === 'hrd_company' || $user->role === 'company') && $user->company_id) {
            $builder->where($model->getTable() . '.company_id', $user->company_id);
        }
    }
}
