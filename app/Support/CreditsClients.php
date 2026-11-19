<?php

namespace App\Support;

use App\Models\Cheque;
use App\Models\Client;
use App\Models\ComptaMouvement;
use App\Models\Facture;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Crédit client = crédit initial + factures dues (TTC) − avoirs − règlements.
 * Un règlement est un mouvement comptable « paiement_client » ; un chèque y est rattaché tant qu’il n’est pas impayé ou restitué.
 */
class CreditsClients
{
    public const MODES = [
        'especes' => 'Espèces',
        'cheque' => 'Chèque',
        'virement' => 'Virement',
        'effet' => 'Effet',
        'carte' => 'Carte bancaire',
        'autre' => 'Autre',
    ];

    /** Statuts de facture qui génèrent une dette client. */
    public const STATUTS_DUS = ['emise', 'envoyee', 'payee_partielle', 'payee'];

    /**
     * Situation de chaque client : facturé, réglé, solde, retard, chèques en cours.
     *
     * @param  iterable<Client>  $clients
     * @return Collection<int, array<string, mixed>> indexée par id client
     */
    public static function soldes(iterable $clients): Collection
    {
        $clients = collect($clients)->keyBy('id');
        $ids = $clients->keys()->all();
        if ($ids === []) {
            return collect();
        }

        $factures = Facture::query()
            ->where('actif', 1)
            ->whereIn('client_id', $ids)
            ->whereIn('statut', [...self::STATUTS_DUS, 'avoir'])
            ->with('lignes')
            ->get();
        $payeParFacture = self::payeParFacture($factures->pluck('id')->all());

        $regle = ComptaMouvement::query()
            ->where('type', 'paiement_client')
            ->whereIn('client_id', $ids)
            ->groupBy('client_id')
            ->selectRaw('client_id, SUM(montant) AS total')
            ->pluck('total', 'client_id');

        $cheques = Cheque::query()
            ->whereIn('client_id', $ids)
            ->whereIn('statut', Cheque::STATUTS_EN_COURS)
            ->groupBy('client_id')
            ->selectRaw('client_id, SUM(montant) AS total, COUNT(*) AS nb')
            ->get()
            ->keyBy('client_id');

        $parClient = $factures->groupBy('client_id');

        return $clients->map(function (Client $client) use ($parClient, $payeParFacture, $regle, $cheques) {
            $siennes = $parClient->get($client->id, collect());
            $dues = $siennes->where('statut', '!=', 'avoir');

            $echues = $dues->filter(function (Facture $f) use ($payeParFacture) {
                return $f->date_echeance !== null
                    && $f->date_echeance->lt(today())
                    && $f->montant_ttc - ($payeParFacture[$f->id] ?? 0) > 0.005;
            });

            $credit = (float) $client->credit_initial;
            $facture = round($dues->sum(fn (Facture $f) => $f->montant_ttc), 2);
            $avoirs = round($siennes->where('statut', 'avoir')->sum(fn (Facture $f) => $f->montant_ttc), 2);
            $paye = round((float) ($regle[$client->id] ?? 0), 2);

            return [
                'client' => $client,
                'credit_initial' => $credit,
                'facture' => $facture,
                'avoirs' => $avoirs,
                'regle' => $paye,
                'solde' => round($credit + $facture - $avoirs - $paye, 2),
                'nb_factures_ouvertes' => $dues->filter(fn (Facture $f) => $f->montant_ttc - ($payeParFacture[$f->id] ?? 0) > 0.005)->count(),
                'montant_echu' => round($echues->sum(fn (Facture $f) => $f->montant_ttc - ($payeParFacture[$f->id] ?? 0)), 2),
                'plus_ancienne_echeance' => $echues->min('date_echeance'),
                'cheques_en_cours' => round((float) ($cheques[$client->id]->total ?? 0), 2),
                'nb_cheques_en_cours' => (int) ($cheques[$client->id]->nb ?? 0),
            ];
        });
    }

    /**
     * Factures d’un client avec montant réglé et reste à payer.
     *
     * @return Collection<int, array{facture: Facture, ttc: float, regle: float, reste: float, en_retard: bool}>
     */
    public static function facturesClient(Client $client): Collection
    {
        $factures = Facture::query()
            ->where('actif', 1)
            ->where('client_id', $client->id)
            ->whereIn('statut', [...self::STATUTS_DUS, 'avoir'])
            ->with('lignes')
            ->orderByDesc('date_emission')
            ->orderByDesc('id')
            ->get();
        $paye = self::payeParFacture($factures->pluck('id')->all());

        return $factures->map(function (Facture $f) use ($paye) {
            $ttc = $f->montant_ttc;
            $regle = round((float) ($paye[$f->id] ?? 0), 2);
            $reste = $f->statut === 'avoir' ? 0.0 : round(max(0, $ttc - $regle), 2);

            return [
                'facture' => $f,
                'ttc' => $ttc,
                'regle' => $regle,
                'reste' => $reste,
                'en_retard' => $reste > 0 && $f->date_echeance !== null && $f->date_echeance->lt(today()),
            ];
        });
    }

    /** @return array<int, float> montant réglé par facture */
    public static function payeParFacture(array $factureIds): array
    {
        if ($factureIds === []) {
            return [];
        }

        return ComptaMouvement::query()
            ->where('type', 'paiement_client')
            ->whereIn('facture_id', $factureIds)
            ->groupBy('facture_id')
            ->selectRaw('facture_id, SUM(montant) AS total')
            ->pluck('total', 'facture_id')
            ->map(fn ($v) => (float) $v)
            ->all();
    }

    /** Statut payée / payée partiellement / émise d’après les règlements enregistrés. */
    public static function recalculerStatutFacture(?Facture $facture, ?int $userId = null): void
    {
        if (! $facture || ! in_array($facture->statut, self::STATUTS_DUS, true)) {
            return;
        }

        $facture->loadMissing('lignes');
        $ttc = $facture->montant_ttc;
        $paye = self::payeParFacture([$facture->id])[$facture->id] ?? 0.0;

        $statut = match (true) {
            $ttc > 0 && $paye >= $ttc - 0.005 => 'payee',
            $paye > 0.005 => 'payee_partielle',
            in_array($facture->statut, ['payee', 'payee_partielle'], true) => 'emise',
            default => $facture->statut,
        };

        if ($statut !== $facture->statut) {
            $facture->update(['statut' => $statut, 'user_update' => $userId]);
        }
    }

    /**
     * @param  array{montant: float|string, date_reglement: string, mode: string, facture_id?: ?int, reference?: ?string, notes?: ?string,
     *               cheque_numero?: ?string, cheque_banque?: ?string, cheque_tireur?: ?string, cheque_echeance?: ?string}  $d
     */
    public static function enregistrerReglement(Client $client, array $d, ?int $userId): ComptaMouvement
    {
        return DB::transaction(function () use ($client, $d, $userId) {
            $facture = ! empty($d['facture_id']) ? Facture::where('client_id', $client->id)->find($d['facture_id']) : null;
            $mode = $d['mode'];
            $estCheque = $mode === 'cheque';

            $mouvement = self::creerMouvement($client, [
                'montant' => $d['montant'],
                'date_operation' => $d['date_reglement'],
                'mode' => $mode,
                'facture' => $facture,
                'reference' => $estCheque ? ($d['cheque_numero'] ?? null) : ($d['reference'] ?? null),
                'notes' => $d['notes'] ?? null,
            ], $userId);

            if ($estCheque) {
                Cheque::create([
                    'id_entreprise' => $client->id_entreprise,
                    'client_id' => $client->id,
                    'facture_id' => $facture?->id,
                    'compta_mouvement_id' => $mouvement->id,
                    'numero' => $d['cheque_numero'],
                    'banque' => $d['cheque_banque'] ?? null,
                    'tireur' => ($d['cheque_tireur'] ?? null) ?: trim($client->nom.' '.$client->prenom),
                    'montant' => $d['montant'],
                    'date_reception' => $d['date_reglement'],
                    'date_echeance' => ($d['cheque_echeance'] ?? null) ?: $d['date_reglement'],
                    'statut' => 'en_portefeuille',
                    'notes' => $d['notes'] ?? null,
                    'user_insert' => $userId,
                ]);
            }

            self::recalculerStatutFacture($facture, $userId);

            return $mouvement;
        });
    }

    public static function supprimerReglement(ComptaMouvement $mouvement, ?int $userId): void
    {
        DB::transaction(function () use ($mouvement, $userId) {
            $facture = $mouvement->facture;
            $mouvement->cheque()->delete();
            $mouvement->delete();
            self::recalculerStatutFacture($facture, $userId);
        });
    }

    /**
     * Fait avancer un chèque dans son cycle de vie.
     * Impayé / restitué : le règlement associé est retiré, la dette du client réapparaît.
     */
    public static function changerStatutCheque(Cheque $cheque, string $action, array $d, ?int $userId): string
    {
        $date = ($d['date'] ?? null) ?: today()->toDateString();

        $autorise = [
            'remettre' => ['en_portefeuille'],
            'encaisser' => ['en_portefeuille', 'remis'],
            'impaye' => ['en_portefeuille', 'remis', 'encaisse'],
            'restituer' => ['en_portefeuille', 'impaye'],
            'representer' => ['impaye'],
        ];
        if (! isset($autorise[$action]) || ! in_array($cheque->statut, $autorise[$action], true)) {
            throw new InvalidArgumentException('Action impossible pour un chèque « '.$cheque->statut_label.' ».');
        }

        return DB::transaction(function () use ($cheque, $action, $d, $date, $userId) {
            switch ($action) {
                case 'remettre':
                    $cheque->update(['statut' => 'remis', 'date_remise' => $date, 'user_update' => $userId]);

                    return 'Chèque n° '.$cheque->numero.' remis en banque.';

                case 'encaisser':
                    $cheque->update(['statut' => 'encaisse', 'date_encaissement' => $date, 'user_update' => $userId]);

                    return 'Chèque n° '.$cheque->numero.' encaissé.';

                case 'impaye':
                    self::retirerReglementDuCheque($cheque, $userId);
                    $cheque->update([
                        'statut' => 'impaye',
                        'date_impaye' => $date,
                        'motif_impaye' => $d['motif'] ?? null,
                        'date_encaissement' => null,
                        'user_update' => $userId,
                    ]);

                    return 'Chèque n° '.$cheque->numero.' marqué impayé : le montant est de nouveau dû par le client.';

                case 'restituer':
                    self::retirerReglementDuCheque($cheque, $userId);
                    $cheque->update(['statut' => 'restitue', 'user_update' => $userId]);

                    return 'Chèque n° '.$cheque->numero.' restitué au client.';

                case 'representer':
                    $mouvement = self::creerMouvement($cheque->client, [
                        'montant' => $cheque->montant,
                        'date_operation' => $date,
                        'mode' => 'cheque',
                        'facture' => $cheque->facture,
                        'reference' => $cheque->numero,
                        'notes' => 'Chèque représenté',
                    ], $userId);
                    $historique = trim(($cheque->notes ? $cheque->notes."\n" : '')
                        .'Impayé le '.$cheque->date_impaye?->format('d/m/Y').($cheque->motif_impaye ? ' ('.$cheque->motif_impaye.')' : '')
                        .', représenté le '.\Illuminate\Support\Carbon::parse($date)->format('d/m/Y').'.');
                    $cheque->update([
                        'statut' => 'en_portefeuille',
                        'compta_mouvement_id' => $mouvement->id,
                        'date_remise' => null,
                        'date_impaye' => null,
                        'motif_impaye' => null,
                        'notes' => $historique,
                        'user_update' => $userId,
                    ]);
                    self::recalculerStatutFacture($cheque->facture, $userId);

                    return 'Chèque n° '.$cheque->numero.' remis en portefeuille.';
            }

            return '';
        });
    }

    private static function retirerReglementDuCheque(Cheque $cheque, ?int $userId): void
    {
        $mouvement = $cheque->mouvement;
        $cheque->update(['compta_mouvement_id' => null]);
        if ($mouvement) {
            $mouvement->delete();
        }
        self::recalculerStatutFacture($cheque->facture, $userId);
    }

    /** @param array{montant: mixed, date_operation: string, mode: string, facture: ?Facture, reference: ?string, notes: ?string} $d */
    private static function creerMouvement(Client $client, array $d, ?int $userId): ComptaMouvement
    {
        $libelleMode = self::MODES[$d['mode']] ?? ucfirst($d['mode']);

        return ComptaMouvement::create([
            'id_entreprise' => $client->id_entreprise,
            'type' => 'paiement_client',
            'montant' => $d['montant'],
            'date_operation' => $d['date_operation'],
            'libelle' => 'Règlement '.mb_strtolower($libelleMode).' — '.trim($client->nom.' '.$client->prenom)
                .($d['facture'] ? ' — '.$d['facture']->numero_facture : ''),
            'mode_paiement' => $libelleMode,
            'reference_externe' => $d['reference'],
            'client_id' => $client->id,
            'facture_id' => $d['facture']?->id,
            'notes' => $d['notes'],
            'user_insert' => $userId,
        ]);
    }
}
