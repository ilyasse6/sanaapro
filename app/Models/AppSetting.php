<?php

namespace App\Models;

use App\Support\CurrentTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AppSetting extends Model
{
    protected $table = 'app_settings';

    protected $fillable = ['cle', 'valeur', 'id_entreprise'];

    /**
     * Entreprise cible pour lecture/écriture (employé = son entreprise ; Super Admin = session ou son id_entreprise).
     */
    public static function resolvedEntrepriseId(?int $explicit = null): ?int
    {
        if ($explicit !== null) {
            return $explicit;
        }

        $tid = CurrentTenant::id();
        if ($tid !== null) {
            return $tid;
        }

        $user = Auth::user();
        if ($user?->isSuperAdmin()) {
            $sid = session('parametres_entreprise_id');
            if ($sid !== null && $sid !== '') {
                return (int) $sid;
            }

            return $user->id_entreprise ? (int) $user->id_entreprise : null;
        }

        return $user?->id_entreprise ? (int) $user->id_entreprise : null;
    }

    public static function get(string $cle, mixed $default = null, ?int $idEntreprise = null): mixed
    {
        $eid = self::resolvedEntrepriseId($idEntreprise);
        if ($eid === null) {
            return $default;
        }

        $row = static::query()->where('cle', $cle)->where('id_entreprise', $eid)->first();

        return $row?->valeur ?? $default;
    }

    public static function set(string $cle, mixed $valeur, ?int $idEntreprise = null): void
    {
        $eid = self::resolvedEntrepriseId($idEntreprise);
        if ($eid === null) {
            throw new \RuntimeException('Impossible d\'enregistrer un paramètre sans entreprise cible.');
        }

        static::query()->updateOrCreate(
            ['cle' => $cle, 'id_entreprise' => $eid],
            ['valeur' => is_string($valeur) ? $valeur : json_encode($valeur)]
        );
    }
}
