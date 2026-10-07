<?php

namespace App\Support;

use App\Models\AppSetting;
use App\Models\BonCommande;
use App\Models\BonLivraison;
use App\Models\Devis;
use App\Models\Facture;
use Illuminate\Support\Facades\Storage;

final class SocieteBranding
{
    public const DEFAULT_MENTION_TVA = 'TVA non applicable - Art. 293 B du CGI';

    /** Bleu marine par défaut : titres, en-têtes de tableau, totaux, bordures des devis / factures. */
    public const DEFAULT_ACCENT_COLOR = '#154295';

    /** Texte principal des documents (corps, tableaux, coordonnées, etc.). */
    public const DEFAULT_TEXT_COLOR = '#111111';

    public const DEFAULT_DEVIS_ENTETE_TITRE = 'STE AFIF ALU S.A.R.L';

    public const DEFAULT_DEVIS_ENTETE_ACTIVITE = 'Menuiserie, Aluminium, Volets, Roulants, Vitre et tous travaux aluminium';

    public const DEFAULT_DEVIS_PIED_PAGE = "Siège social : AV. Essalam n° 39 Hay El Barid C.Y.M – Rabat. Tel : 0661 068586  Fax : 0537 693433\nPatente : 27350400_R.C : 73203_I.F : 3383044_C.N.S.S : 7806287";

    private static function normalizeHexColor(?string $raw, string $fallback): string
    {
        $c = is_string($raw) ? trim($raw) : '';
        if ($c !== '' && preg_match('/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/', $c)) {
            if (strlen($c) === 4) {
                return '#'.$c[1].$c[1].$c[2].$c[2].$c[3].$c[3];
            }

            return $c;
        }

        return $fallback;
    }

    /**
     * Couleur d’accent des documents (devis, factures, impression).
     */
    public static function accentColor(?int $entrepriseId = null): string
    {
        return self::normalizeHexColor(AppSetting::get('document_accent_color', null, $entrepriseId), self::DEFAULT_ACCENT_COLOR);
    }

    /**
     * Couleur du texte courant sur les documents (hors bandeaux à fond accent).
     */
    public static function textColor(?int $entrepriseId = null): string
    {
        return self::normalizeHexColor(AppSetting::get('document_text_color', null, $entrepriseId), self::DEFAULT_TEXT_COLOR);
    }

    /**
     * Source img pour DomPDF : file:// (fiable) puis data URI ; les URLs http (asset) restent inutilisables si enable_remote est false.
     * Utilise logo_path ou, à défaut, le chemin dérivé de logo_url (/storage/...).
     */
    public static function logoSrcForDomPdf(array $societe): ?string
    {
        $relative = self::resolveBrandingLogoRelativePublicPath($societe);
        if ($relative === null) {
            return null;
        }

        $fileUri = self::publicDiskFileToDomPdfFileUri($relative);
        if ($fileUri !== null) {
            return $fileUri;
        }

        return self::publicDiskImageToDataUri($relative);
    }

    /**
     * Dimensions explicites pour l’attribut width/height du logo en PDF (évite le rognage avec max-height + object-fit).
     *
     * @return array{w: int, h: int}|null
     */
    public static function logoDisplayDimensionsForPdf(array $societe, int $maxWidthPx = 240, int $maxHeightPx = 96): ?array
    {
        $relative = self::resolveBrandingLogoRelativePublicPath($societe);
        if ($relative === null || ! Storage::disk('public')->exists($relative)) {
            return null;
        }
        $full = Storage::disk('public')->path($relative);
        if (! is_readable($full)) {
            return null;
        }
        $info = @getimagesize($full);
        if ($info === false || ($info[0] ?? 0) < 1 || ($info[1] ?? 0) < 1) {
            return null;
        }
        $w = (int) $info[0];
        $h = (int) $info[1];
        $scale = min($maxWidthPx / $w, $maxHeightPx / $h, 1.0);

        return [
            'w' => max(1, (int) round($w * $scale)),
            'h' => max(1, (int) round($h * $scale)),
        ];
    }

    /**
     * Logo PDF (source + dimensions) ; à défaut de logo dans Paramètres, logo AFIF ALU fourni avec l’application.
     *
     * @return array{0: ?string, 1: ?array{w: int, h: int}}
     */
    public static function logoPourPdfAvecDefaut(array $societe, int $maxWidthPx, int $maxHeightPx): array
    {
        $src = self::logoSrcForDomPdf($societe);
        if ($src !== null) {
            return [$src, self::logoDisplayDimensionsForPdf($societe, $maxWidthPx, $maxHeightPx)];
        }

        $defaut = public_path('assets/imgs/afifalu-logo.png');
        if (is_file($defaut) && ($taille = @getimagesize($defaut))) {
            $echelle = min($maxWidthPx / $taille[0], $maxHeightPx / $taille[1], 1.0);

            return ['file://'.$defaut, ['w' => (int) round($taille[0] * $echelle), 'h' => (int) round($taille[1] * $echelle)]];
        }

        return [null, null];
    }

    private static function resolveBrandingLogoRelativePublicPath(array $societe): ?string
    {
        $p = isset($societe['logo_path']) && is_string($societe['logo_path']) ? trim($societe['logo_path']) : '';
        if ($p !== '' && $p !== '0') {
            return self::normalizePublicDiskRelativePath($p);
        }

        $url = $societe['logo_url'] ?? '';
        if (is_string($url) && $url !== '') {
            $pathPart = parse_url($url, PHP_URL_PATH);
            if (is_string($pathPart) && preg_match('#/(?:storage)/(.+)$#i', $pathPart, $m)) {
                return self::normalizePublicDiskRelativePath($m[1]);
            }
        }

        return null;
    }

    private static function normalizePublicDiskRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));
        $path = ltrim($path, '/');
        foreach (['public/storage/', 'storage/app/public/', 'storage/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
            }
        }

        return $path;
    }

    private static function publicDiskFileToDomPdfFileUri(string $relativePublicDiskPath): ?string
    {
        if (! Storage::disk('public')->exists($relativePublicDiskPath)) {
            return null;
        }
        $absolute = realpath(Storage::disk('public')->path($relativePublicDiskPath));
        if ($absolute === false || ! is_readable($absolute)) {
            return null;
        }

        return self::absoluteFilesystemPathToFileUri($absolute);
    }

    private static function absoluteFilesystemPathToFileUri(string $absolute): string
    {
        $path = str_replace('\\', '/', $absolute);
        if (str_starts_with($path, '/')) {
            return 'file://'.$path;
        }
        if (preg_match('#^[A-Za-z]:/#', $path)) {
            return 'file:///'.$path;
        }

        return 'file:///'.$path;
    }

    private static function publicDiskImageToDataUri(string $relativePublicDiskPath): ?string
    {
        if (! Storage::disk('public')->exists($relativePublicDiskPath)) {
            return null;
        }
        $bytes = Storage::disk('public')->get($relativePublicDiskPath);
        if ($bytes === false || $bytes === '') {
            return null;
        }

        $ext = strtolower(pathinfo($relativePublicDiskPath, PATHINFO_EXTENSION) ?: '');
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            default => null,
        };
        if ($mime === null) {
            $full = Storage::disk('public')->path($relativePublicDiskPath);
            if (is_file($full) && function_exists('mime_content_type')) {
                $detected = @mime_content_type($full);
                if (is_string($detected) && str_starts_with($detected, 'image/')) {
                    $mime = $detected;
                }
            }
        }
        if ($mime === null) {
            $mime = 'application/octet-stream';
        }

        return 'data:'.$mime.';base64,'.base64_encode($bytes);
    }

    /**
     * @return array{entete_titre: string, entete_activite: string, pied_page: string, logo_path: ?string, logo_url: ?string, nom: string, adresse: string, telephone: string, email: string, mention_tva: string, accent_color: string, text_color: string}
     */
    public static function all(?int $entrepriseId = null): array
    {
        $path = AppSetting::get('societe_logo', null, $entrepriseId);
        $logoUrl = null;
        if (is_string($path) && $path !== '' && Storage::disk('public')->exists($path)) {
            $logoUrl = asset('storage/'.$path);
        }

        return self::enteteDocument($entrepriseId) + [
            'logo_path' => is_string($path) && $path !== '' ? $path : null,
            'logo_url' => $logoUrl,
            'nom' => (string) (AppSetting::get('societe_nom_commercial', '', $entrepriseId) ?? ''),
            'adresse' => (string) (AppSetting::get('societe_adresse', '', $entrepriseId) ?? ''),
            'telephone' => (string) (AppSetting::get('societe_telephone', '', $entrepriseId) ?? ''),
            'email' => (string) (AppSetting::get('societe_email', '', $entrepriseId) ?? ''),
            'mention_tva' => (string) (AppSetting::get('devis_mention_tva', self::DEFAULT_MENTION_TVA, $entrepriseId) ?? self::DEFAULT_MENTION_TVA),
            'accent_color' => self::accentColor($entrepriseId),
            'text_color' => self::textColor($entrepriseId),
        ];
    }

    /**
     * En-tête (raison sociale, activité) et pied de page des documents, réglés dans Paramètres.
     *
     * @return array{entete_titre: string, entete_activite: string, pied_page: string}
     */
    public static function enteteDocument(?int $entrepriseId = null): array
    {
        return [
            'entete_titre' => (string) (AppSetting::get('devis_entete_titre', self::DEFAULT_DEVIS_ENTETE_TITRE, $entrepriseId) ?? ''),
            'entete_activite' => (string) (AppSetting::get('devis_entete_activite', self::DEFAULT_DEVIS_ENTETE_ACTIVITE, $entrepriseId) ?? ''),
            'pied_page' => (string) (AppSetting::get('devis_pied_page', self::DEFAULT_DEVIS_PIED_PAGE, $entrepriseId) ?? ''),
        ];
    }

    /**
     * Figée sur le devis : copie le logo actuel et enregistre textes + chemin copie (indépendant des futurs changements dans Paramètres).
     */
    public static function persistSnapshotForDevis(Devis $devis): void
    {
        $live = self::all($devis->id_entreprise);

        $dir = 'devis_branding/'.$devis->id;
        Storage::disk('public')->deleteDirectory($dir);

        $logoPathSnapshot = null;
        if (! empty($live['logo_path']) && Storage::disk('public')->exists($live['logo_path'])) {
            $ext = strtolower(pathinfo($live['logo_path'], PATHINFO_EXTENSION) ?: 'png');
            if (! preg_match('/^[a-z0-9]{1,5}$/', $ext)) {
                $ext = 'png';
            }
            $dest = $dir.'/logo.'.$ext;
            Storage::disk('public')->copy($live['logo_path'], $dest);
            $logoPathSnapshot = $dest;
        }

        $devis->forceFill([
            'branding_snapshot' => self::enteteDocument($devis->id_entreprise) + [
                'nom' => $live['nom'],
                'adresse' => $live['adresse'],
                'telephone' => $live['telephone'],
                'email' => $live['email'],
                'mention_tva' => $live['mention_tva'],
                'accent_color' => $live['accent_color'],
                'text_color' => $live['text_color'],
                'logo_path' => $logoPathSnapshot,
            ],
        ])->save();
    }

    /**
     * Figée sur la facture : copie logo + textes + couleur (indépendant des futurs paramètres).
     */
    public static function persistSnapshotForFacture(Facture $facture): void
    {
        $facture->loadMissing('devis');

        $dir = 'facture_branding/'.$facture->id;
        Storage::disk('public')->deleteDirectory($dir);

        if ($facture->devis) {
            $devi = $facture->devis;
            $aff = $devi->branding_affiche;
            $accent = $aff['accent_color'] ?? self::DEFAULT_ACCENT_COLOR;
            $snapDevis = $devi->branding_snapshot;
            $srcLogo = null;
            if (is_array($snapDevis) && ! empty($snapDevis['logo_path']) && is_string($snapDevis['logo_path'])) {
                $srcLogo = $snapDevis['logo_path'];
            }
            if (! is_string($srcLogo) || $srcLogo === '' || ! Storage::disk('public')->exists($srcLogo)) {
                $live = self::all($facture->id_entreprise);
                $srcLogo = $live['logo_path'] ?? null;
            }
        } else {
            $live = self::all($facture->id_entreprise);
            $aff = $live;
            $accent = $live['accent_color'];
            $srcLogo = $live['logo_path'] ?? null;
        }

        $logoPathSnapshot = null;
        if (is_string($srcLogo) && $srcLogo !== '' && Storage::disk('public')->exists($srcLogo)) {
            $ext = strtolower(pathinfo($srcLogo, PATHINFO_EXTENSION) ?: 'png');
            if (! preg_match('/^[a-z0-9]{1,5}$/', $ext)) {
                $ext = 'png';
            }
            Storage::disk('public')->makeDirectory($dir);
            $dest = $dir.'/logo.'.$ext;
            Storage::disk('public')->copy($srcLogo, $dest);
            $logoPathSnapshot = $dest;
        }

        $facture->forceFill([
            'branding_snapshot' => self::enteteDocument($facture->id_entreprise) + [
                'nom' => (string) ($aff['nom'] ?? ''),
                'adresse' => (string) ($aff['adresse'] ?? ''),
                'telephone' => (string) ($aff['telephone'] ?? ''),
                'email' => (string) ($aff['email'] ?? ''),
                'mention_tva' => (string) ($aff['mention_tva'] ?? self::DEFAULT_MENTION_TVA),
                'accent_color' => $accent,
                'text_color' => (string) ($aff['text_color'] ?? self::DEFAULT_TEXT_COLOR),
                'logo_path' => $logoPathSnapshot,
            ],
        ])->save();
    }

    /**
     * Copie l’instantané branding d’une facture vers une autre (duplication).
     */
    public static function duplicateSnapshotForFacture(Facture $from, Facture $to): void
    {
        $snap = $from->branding_snapshot;
        if (! is_array($snap) || $snap === []) {
            self::persistSnapshotForFacture($to);

            return;
        }

        $dir = 'facture_branding/'.$to->id;
        Storage::disk('public')->deleteDirectory($dir);

        $path = $snap['logo_path'] ?? null;
        $newPath = null;
        if (is_string($path) && $path !== '' && Storage::disk('public')->exists($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: 'png');
            if (! preg_match('/^[a-z0-9]{1,5}$/', $ext)) {
                $ext = 'png';
            }
            Storage::disk('public')->makeDirectory($dir);
            $newPath = $dir.'/logo.'.$ext;
            Storage::disk('public')->copy($path, $newPath);
        }

        $to->forceFill([
            'branding_snapshot' => array_merge($snap, ['logo_path' => $newPath]),
        ])->save();
    }

    /**
     * Duplique les fichiers logo du devis source vers le nouveau devis (après replicate + save).
     *
     * @param  array<string, mixed>  $snapshot
     * @return array<string, mixed>
     */
    public static function duplicateSnapshotFiles(Devis $from, Devis $to, array $snapshot): array
    {
        $path = $snapshot['logo_path'] ?? null;
        if (! is_string($path) || $path === '' || ! Storage::disk('public')->exists($path)) {
            $snapshot['logo_path'] = null;

            return $snapshot;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: 'png');
        if (! preg_match('/^[a-z0-9]{1,5}$/', $ext)) {
            $ext = 'png';
        }
        $dir = 'devis_branding/'.$to->id;
        Storage::disk('public')->deleteDirectory($dir);
        $dest = $dir.'/logo.'.$ext;
        Storage::disk('public')->copy($path, $dest);
        $snapshot['logo_path'] = $dest;

        return $snapshot;
    }

    /**
     * Instantané branding pour le bon de livraison (logo + textes).
     */
    public static function persistSnapshotForBonLivraison(BonLivraison $bl): void
    {
        $live = self::all($bl->id_entreprise);

        $dir = 'bon_livraison_branding/'.$bl->id;
        Storage::disk('public')->deleteDirectory($dir);

        $logoPathSnapshot = null;
        if (! empty($live['logo_path']) && Storage::disk('public')->exists($live['logo_path'])) {
            $ext = strtolower(pathinfo($live['logo_path'], PATHINFO_EXTENSION) ?: 'png');
            if (! preg_match('/^[a-z0-9]{1,5}$/', $ext)) {
                $ext = 'png';
            }
            $dest = $dir.'/logo.'.$ext;
            Storage::disk('public')->copy($live['logo_path'], $dest);
            $logoPathSnapshot = $dest;
        }

        $bl->forceFill([
            'branding_snapshot' => [
                'nom' => $live['nom'],
                'adresse' => $live['adresse'],
                'telephone' => $live['telephone'],
                'email' => $live['email'],
                'mention_tva' => $live['mention_tva'],
                'accent_color' => $live['accent_color'],
                'text_color' => $live['text_color'],
                'logo_path' => $logoPathSnapshot,
            ],
        ])->save();
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @return array<string, mixed>
     */
    public static function duplicateSnapshotFilesBonLivraison(BonLivraison $from, BonLivraison $to, array $snapshot): array
    {
        $path = $snapshot['logo_path'] ?? null;
        if (! is_string($path) || $path === '' || ! Storage::disk('public')->exists($path)) {
            $snapshot['logo_path'] = null;

            return $snapshot;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: 'png');
        if (! preg_match('/^[a-z0-9]{1,5}$/', $ext)) {
            $ext = 'png';
        }
        $dir = 'bon_livraison_branding/'.$to->id;
        Storage::disk('public')->deleteDirectory($dir);
        $dest = $dir.'/logo.'.$ext;
        Storage::disk('public')->copy($path, $dest);
        $snapshot['logo_path'] = $dest;

        return $snapshot;
    }

    /**
     * Instantané branding pour le bon de commande (logo + textes).
     */
    public static function persistSnapshotForBonCommande(BonCommande $bc): void
    {
        $live = self::all($bc->id_entreprise);

        $dir = 'bon_commande_branding/'.$bc->id;
        Storage::disk('public')->deleteDirectory($dir);

        $logoPathSnapshot = null;
        if (! empty($live['logo_path']) && Storage::disk('public')->exists($live['logo_path'])) {
            $ext = strtolower(pathinfo($live['logo_path'], PATHINFO_EXTENSION) ?: 'png');
            if (! preg_match('/^[a-z0-9]{1,5}$/', $ext)) {
                $ext = 'png';
            }
            $dest = $dir.'/logo.'.$ext;
            Storage::disk('public')->copy($live['logo_path'], $dest);
            $logoPathSnapshot = $dest;
        }

        $bc->forceFill([
            'branding_snapshot' => [
                'nom' => $live['nom'],
                'adresse' => $live['adresse'],
                'telephone' => $live['telephone'],
                'email' => $live['email'],
                'mention_tva' => $live['mention_tva'],
                'accent_color' => $live['accent_color'],
                'text_color' => $live['text_color'],
                'logo_path' => $logoPathSnapshot,
            ],
        ])->save();
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @return array<string, mixed>
     */
    public static function duplicateSnapshotFilesBonCommande(BonCommande $from, BonCommande $to, array $snapshot): array
    {
        $path = $snapshot['logo_path'] ?? null;
        if (! is_string($path) || $path === '' || ! Storage::disk('public')->exists($path)) {
            $snapshot['logo_path'] = null;

            return $snapshot;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: 'png');
        if (! preg_match('/^[a-z0-9]{1,5}$/', $ext)) {
            $ext = 'png';
        }
        $dir = 'bon_commande_branding/'.$to->id;
        Storage::disk('public')->deleteDirectory($dir);
        $dest = $dir.'/logo.'.$ext;
        Storage::disk('public')->copy($path, $dest);
        $snapshot['logo_path'] = $dest;

        return $snapshot;
    }
}
