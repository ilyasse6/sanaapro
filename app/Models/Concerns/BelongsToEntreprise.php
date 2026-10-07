<?php

namespace App\Models\Concerns;

use App\Models\Entreprise;
use App\Models\Scopes\TenantEntrepriseScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToEntreprise
{
    public static function bootBelongsToEntreprise(): void
    {
        static::addGlobalScope(new TenantEntrepriseScope);

        static::creating(function (Model $model): void {
            if ($model->getAttribute('id_entreprise')) {
                return;
            }

            $tid = \App\Support\CurrentTenant::id();
            if ($tid !== null) {
                $model->setAttribute('id_entreprise', $tid);

                return;
            }

            $user = Auth::user();
            if ($user && $user->id_entreprise) {
                $model->setAttribute('id_entreprise', $user->id_entreprise);

                return;
            }

            $first = Entreprise::query()->orderBy('id')->value('id');
            if ($first) {
                $model->setAttribute('id_entreprise', $first);
            }
        });
    }

    public function entrepriseTenant()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }
}
