<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Storage;

final class AppHeaderBranding
{
    /**
     * URL publique du logo affiché dans la barre du haut (paramètre séparé des devis/factures).
     */
    public static function logoUrl(?int $entrepriseId = null): ?string
    {
        $path = AppSetting::get('app_header_logo', null, $entrepriseId);
        if (! is_string($path) || $path === '' || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/'.$path);
    }
}
