<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Import des champs affichés sur une fiche « Détails consultation » du portail
 * {@link https://www.marchespublics.gov.ma/} en s'appuyant sur les span idEntrepriseConsultationSummary_*.
 */
class MarchesPublicsConsultationImporter
{
    public const HOST = 'marchespublics.gov.ma';

    /**
     * @return array{ok: bool, error?: string, data?: array<string, mixed>}
     */
    public function importFromUrl(string $url): array
    {
        $url = trim($url);
        if ($url === '') {
            return ['ok' => false, 'error' => 'URL vide.'];
        }

        $parsed = parse_url($url);
        if (! $parsed || empty($parsed['host']) || ! str_contains(strtolower($parsed['host']), self::HOST)) {
            return ['ok' => false, 'error' => 'URL invalide : le lien doit pointer vers marchespublics.gov.ma.'];
        }

        if (empty($parsed['scheme'])) {
            $url = 'https://'.$url;
        }

        try {
            $response = Http::timeout(45)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; ProwiseApp/1.0; +https://example.invalid)',
                    'Accept' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'fr-FR,fr;q=0.9',
                ])
                ->get($url);
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => 'Impossible de joindre le portail : '.$e->getMessage()];
        }

        if (! $response->successful()) {
            return ['ok' => false, 'error' => 'Réponse HTTP '.$response->status().'.'];
        }

        $html = $response->body();
        if ($html === '' || ! str_contains($html, 'idEntrepriseConsultationSummary')) {
            return ['ok' => false, 'error' => 'Page inattendue : pas de bloc consultation détecté.'];
        }

        $data = $this->parseHtml($html);
        $data['source_url'] = $url;

        return ['ok' => true, 'data' => $data];
    }

    /**
     * @return array<string, mixed>
     */
    public function parseHtml(string $html): array
    {
        $dateHeure = $this->extractSpanPlain($html, 'dateHeureLimiteRemisePlis');
        $reference = $this->extractSpanPlain($html, 'reference');
        $objet = $this->extractSpanPlain($html, 'objet');
        $acheteur = $this->extractSpanPlain($html, 'entiteAchat');
        $annonce = $this->extractSpanPlain($html, 'annonce');
        $typeProc = $this->extractSpanPlain($html, 'typeProcedure');
        $modePass = $this->extractSpanPlain($html, 'modePassation');
        $procedure = trim($typeProc.' '.trim((string) $modePass));
        $categorie = $this->extractSpanPlain($html, 'categoriePrincipale');
        $lots = $this->extractSpanPlain($html, 'nbrLots');
        $lieuExec = $this->extractSpanPlain($html, 'lieuxExecutions');
        $domaines = $this->extractDomaines($html);
        $adresseRetrait = $this->extractSpanPlain($html, 'adresseRetraitDossiers');
        $adresseDepot = $this->extractSpanPlain($html, 'adresseDepotOffres');
        $lieuOuverture = $this->extractSpanPlain($html, 'lieuOuverturePlis');
        $prixPlans = $this->extractSpanPlain($html, 'prixAcquisitionPlan');
        $contactNom = $this->extractSpanPlain($html, 'contactAdministratif');
        $email = $this->extractSpanPlain($html, 'email');
        $tel = $this->extractSpanPlain($html, 'telephone');
        $fax = $this->extractSpanPlain($html, 'telecopieur');

        $dateLimitePlis = null;
        if ($dateHeure !== null && $dateHeure !== '') {
            try {
                $dateLimitePlis = Carbon::createFromFormat('d/m/Y H:i', trim($dateHeure));
            } catch (\Throwable) {
                try {
                    $dateLimitePlis = Carbon::createFromFormat('d/m/Y H:i:s', trim($dateHeure));
                } catch (\Throwable) {
                    $dateLimitePlis = null;
                }
            }
        }

        $prixMAD = null;
        if ($prixPlans !== null && $prixPlans !== '') {
            $normalized = str_replace([' ', 'MAD', "\xc2\xa0"], '', trim($prixPlans));
            $normalized = str_replace(',', '.', $normalized);
            if (is_numeric($normalized)) {
                $prixMAD = round((float) $normalized, 2);
            }
        }

        $intitule = '';
        if ($objet) {
            $intitule = Str::limit(preg_replace('/\s+/', ' ', $objet), 255, '');
        } elseif ($reference) {
            $intitule = 'Consultation '.$reference;
        } else {
            $intitule = 'Marché importé';
        }

        return [
            'reference_consultation' => $reference ?: null,
            'objet' => $objet ?: null,
            'intitule' => $intitule,
            'date_limite_plis' => $dateLimitePlis?->format('Y-m-d\TH:i'),
            'date_limite' => $dateLimitePlis?->format('Y-m-d'),
            'acheteur_public' => $acheteur ?: null,
            'type_annonce' => $annonce ?: null,
            'procedure_text' => $procedure !== '' ? trim($procedure) : null,
            'categorie_principale' => $categorie ?: null,
            'allotissement' => $lots ? trim($lots) : null,
            'lieu_execution' => $lieuExec ?: null,
            'domaines_activite' => $domaines ?: null,
            'adresse_retrait_dossiers' => $adresseRetrait ?: null,
            'adresse_depot_offres' => $adresseDepot ?: null,
            'lieu_ouverture_plis' => $lieuOuverture ?: null,
            'prix_acquisition_plans' => $prixMAD,
            'contact_nom' => $contactNom ?: null,
            'contact_email' => $email ?: null,
            'contact_telephone' => $tel ?: null,
            'contact_fax' => $fax ?: null,
        ];
    }

    private function extractSpanPlain(string $html, string $suffix): ?string
    {
        // Ne pas utiliser id="[^"]*…" : [^"]* consomme toute la valeur de l’attribut avant le suffixe attendu.
        // Sans modificateur « u » : certaines pages du portail mélangent encodages et PCRE échoue avec /u.
        $pattern = '/idEntrepriseConsultationSummary_'.preg_quote($suffix, '/').'"[^>]*>([^<]*)/i';
        if (preg_match($pattern, $html, $m)) {
            $t = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            return $t !== '' ? $t : null;
        }

        return null;
    }

    private function extractDomaines(string $html): ?string
    {
        if (preg_match('/idEntrepriseConsultationSummary_domainesActivite"[^>]*>(.*?)<\/span>/is', $html, $m)) {
            $inner = $m[1];
            $text = html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />'], ' ', $inner)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim((string) $text);

            return $text !== '' ? $text : null;
        }

        return null;
    }
}
