<?php

namespace App\Models\Scopes;

use App\Support\CurrentTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantEntrepriseScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $id = CurrentTenant::id();
        if ($id === null) {
            return;
        }

        $builder->where($model->getTable().'.id_entreprise', $id);
    }
}
