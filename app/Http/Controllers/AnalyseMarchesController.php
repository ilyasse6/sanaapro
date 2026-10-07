<?php

namespace App\Http\Controllers;

use App\Support\MarchesPublicsConsultationImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AnalyseMarchesController extends Controller
{
    private function perm(): ?\Illuminate\Http\RedirectResponse
    {
        $u = Auth::user();
        if (! $u) {
            return redirect()->route('login')->with('error', 'Connexion requise.');
        }
        if (! $u->hasPermission('Gestion des Marchés & Cautions', 'consulter')) {
            return redirect()->back()->with('error', 'Permission insuffisante.');
        }

        return null;
    }

    public function index()
    {
        if ($r = $this->perm()) {
            return $r;
        }

        return view('user.analyse-marches.index', [
            'result' => session('analyse_marches_result'),
            'url' => old('url', session('analyse_marches_url', '')),
        ]);
    }

    public function analyser(Request $request, MarchesPublicsConsultationImporter $importer)
    {
        if ($r = $this->perm()) {
            return $r;
        }

        $request->validate([
            'url' => 'nullable|string|max:2000',
        ]);

        $url = trim((string) $request->input('url', ''));

        if ($url === '') {
            $result = $this->buildAnalysis(null, true, '');
            session([
                'analyse_marches_result' => $result,
                'analyse_marches_url' => '',
            ]);

            return redirect()
                ->route('analyse-marches.index')
                ->with('success', 'Analyse de démonstration générée (~3 s).');
        }

        $imported = null;
        $importNote = null;
        $parsed = parse_url($url);
        $host = strtolower((string) ($parsed['host'] ?? ''));

        if (str_contains($host, 'marchespublics.gov.ma')) {
            $import = $importer->importFromUrl($url);
            if (($import['ok'] ?? false) && ! empty($import['data'])) {
                $imported = $import['data'];
            } else {
                $importNote = $import['error'] ?? 'Import portail indisponible — simulation utilisée.';
            }
        } else {
            $importNote = 'URL hors marchespublics.gov.ma — analyse simulée à partir du lien fourni.';
        }

        $result = $this->buildAnalysis($imported, false, $url);
        if ($importNote) {
            $result['note'] = $importNote;
        }

        session([
            'analyse_marches_result' => $result,
            'analyse_marches_url' => $url,
        ]);

        return redirect()
            ->route('analyse-marches.index')
            ->with('success', 'Analyse terminée.');
    }

    public function clear()
    {
        if ($r = $this->perm()) {
            return $r;
        }

        session()->forget(['analyse_marches_result', 'analyse_marches_url']);

        return redirect()->route('analyse-marches.index');
    }

    /**
     * @param  array<string, mixed>|null  $imported
     * @return array<string, mixed>
     */
    private function buildAnalysis(?array $imported, bool $pureDemo, string $sourceUrl): array
    {
        $seed = crc32(($sourceUrl !== '' ? $sourceUrl : 'demo').'|'.($imported['reference_consultation'] ?? $imported['reference'] ?? 'REF'));
        mt_srand($seed);

        $allot = $imported['allotissement'] ?? $imported['nbr_lots'] ?? null;
        if ($allot !== null && $allot !== '') {
            $nbLots = max(1, min(8, (int) preg_replace('/\D/', '', (string) $allot)));
            if ($nbLots < 1) {
                $nbLots = $pureDemo ? 3 : mt_rand(2, 5);
            }
        } else {
            $nbLots = $pureDemo ? 3 : mt_rand(2, 5);
        }

        $objetCourt = Str::limit((string) ($imported['objet'] ?? $imported['intitule'] ?? 'Fournitures / travaux'), 48);
        $lots = [];

        for ($i = 1; $i <= $nbLots; $i++) {
            $offers = [];
            $nOffers = mt_rand(3, 7);
            for ($j = 0; $j < $nOffers; $j++) {
                $montant = mt_rand(180000, 4200000) + (mt_rand(0, 99) / 100);
                $offers[] = [
                    'entreprise' => $this->fakeEntreprise($j + $i),
                    'montant_ttc' => $montant,
                    'rang' => 0,
                ];
            }
            usort($offers, fn ($a, $b) => $a['montant_ttc'] <=> $b['montant_ttc']);
            foreach ($offers as $idx => &$o) {
                $o['rang'] = $idx + 1;
            }
            unset($o);

            $montants = array_column($offers, 'montant_ttc');
            $avg = array_sum($montants) / max(1, count($montants));
            if (count($montants) >= 4) {
                $trimmed = array_slice($montants, 1, -1);
                $ref = array_sum($trimmed) / count($trimmed);
            } else {
                $ref = $avg;
            }

            $gagnant = $offers[0];
            $lots[] = [
                'numero' => $i,
                'libelle' => 'Lot '.$i.' — '.$objetCourt,
                'prix_reference' => round($ref, 2),
                'moyenne' => round($avg, 2),
                'min' => round(min($montants), 2),
                'max' => round(max($montants), 2),
                'nb_offres' => count($offers),
                'gagnant' => [
                    'entreprise' => $gagnant['entreprise'],
                    'montant_ttc' => round($gagnant['montant_ttc'], 2),
                ],
                'offres' => array_map(fn ($o) => [
                    'entreprise' => $o['entreprise'],
                    'montant_ttc' => round($o['montant_ttc'], 2),
                    'rang' => $o['rang'],
                ], $offers),
            ];
        }

        mt_srand();

        $dateLimite = $imported['date_limite_plis'] ?? $imported['date_limite'] ?? null;
        if (is_string($dateLimite) && preg_match('/^\d{4}-\d{2}-\d{2}/', $dateLimite)) {
            try {
                $dateLimite = \Carbon\Carbon::parse($dateLimite)->format('d/m/Y H:i');
            } catch (\Throwable) {
                // keep raw
            }
        }

        $acheteur = $imported['acheteur_public']
            ?? $imported['entite_achat']
            ?? $imported['acheteur']
            ?? 'Établissement public (exemple)';

        return [
            'mode' => $pureDemo ? 'demo' : ($imported ? 'hybride' : 'simulation'),
            'source_url' => $sourceUrl !== '' ? $sourceUrl : null,
            'consultation' => [
                'reference' => $imported['reference_consultation'] ?? $imported['reference'] ?? ('AO-'.date('Y').'-'.str_pad((string) (abs($seed) % 9999), 4, '0', STR_PAD_LEFT)),
                'objet' => $imported['objet'] ?? $imported['intitule'] ?? 'Fourniture et installation d\'équipements — consultation publique',
                'acheteur' => $acheteur,
                'procedure' => $imported['procedure_text'] ?? $imported['procedure'] ?? 'Appel d\'offres ouvert',
                'categorie' => $imported['categorie_principale'] ?? $imported['categorie'] ?? 'Fournitures',
                'date_limite' => $dateLimite ?: now()->addDays(12)->format('d/m/Y H:i'),
                'nb_lots' => count($lots),
            ],
            'lots' => $lots,
            'recap_gagnants' => array_map(fn ($l) => [
                'lot' => $l['numero'],
                'entreprise' => $l['gagnant']['entreprise'],
                'montant' => $l['gagnant']['montant_ttc'],
                'prix_reference' => $l['prix_reference'],
                'ecart_pct' => $l['prix_reference'] > 0
                    ? round((($l['gagnant']['montant_ttc'] - $l['prix_reference']) / $l['prix_reference']) * 100, 1)
                    : 0,
            ], $lots),
            'analyse_en' => '~'.mt_rand(2, 4).' s',
            'generated_at' => now()->format('d/m/Y H:i'),
        ];
    }

    private function fakeEntreprise(int $i): string
    {
        $names = [
            'Atlas Equipements SARL',
            'Maroc Tech Solutions',
            'Casa Industrie SA',
            'Nord Fournitures',
            'Sud Médical Pro',
            'Bâtiment Plus',
            'Electro Maghreb',
            'Prestige Services',
            'Horizon Trading',
            'Ribat Logistique',
        ];

        return $names[$i % count($names)];
    }
}
