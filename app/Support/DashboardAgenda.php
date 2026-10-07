<?php

namespace App\Support;

use App\Models\AppSetting;
use App\Models\Article;
use App\Models\CatalogueService;
use App\Models\Caution;
use App\Models\Client;
use App\Models\ComptaMouvement;
use App\Models\Devis;
use App\Models\Facture;
use App\Models\Pack;
use App\Models\RappelPersonnel;
use App\Models\RendezVous;
use App\Models\User;
use Carbon\Carbon;

class DashboardAgenda
{
    /**
     * Données pour les blocs Agenda (Visites, RDV, Rappels) et le modal multitâche.
     *
     * @param  'home'|'rendez-vous'  $rdvFormRedirect  Valeur du champ hidden redirect_to après création RDV.
     */
    public static function build(?User $user, string $rdvFormRedirect = 'home', ?string $rappelPersonnelListUrl = null): array
    {
        $visitesAujourdhui = collect();
        $rdvProchains = collect();
        $rappelsHome = collect();
        $clients = collect();
        $services = collect();
        $packs = collect();

        $showVisitesBloc = (bool) ($user && $user->hasPermission('Gestion des Rendez-vous', 'consulter'));
        $showRappelsBloc = (bool) ($user && $user->hasPermission('Alertes & Paramètres', 'consulter'));
        $canAjouterRdv = (bool) ($user && $user->hasPermission('Gestion des Rendez-vous', 'ajouter'));

        $rappelPersoUrl = $rappelPersonnelListUrl ?? route('home');

        $chartEid = ($user && $user->isSuperAdmin()) ? AppSetting::resolvedEntrepriseId() : null;

        if ($showVisitesBloc || $canAjouterRdv) {
            $clients = Client::where('actif', 1)->orderBy('nom')->get();
            $services = CatalogueService::where('actif', 1)->orderBy('nom')->get();
            $packs = Pack::where('actif', 1)->orderBy('nom')->get();
        }

        if ($showVisitesBloc) {
            $with = ['client', 'services', 'packs'];
            $visitesAujourdhui = RendezVous::with($with)
                ->where('actif', 1)
                ->whereDate('date_rdv', now()->toDateString())
                ->orderBy('heure_rdv')
                ->limit(10)
                ->get();

            $rdvProchains = RendezVous::with($with)
                ->where('actif', 1)
                ->whereDate('date_rdv', '>', now()->toDateString())
                ->orderBy('date_rdv')
                ->orderBy('heure_rdv')
                ->limit(10)
                ->get();
        }

        if ($showRappelsBloc) {
            $joursCaution = (int) AppSetting::get('alerte_caution_jours', 30);
            $joursFacture = (int) AppSetting::get('alerte_facture_impayee_jours', 7);
            $stockActif = AppSetting::get('alerte_stock_actif', '1') === '1';

            $cautions = Caution::with('marche')
                ->where('statut', 'active')
                ->whereNotNull('date_fin')
                ->where('date_fin', '<=', now()->addDays($joursCaution))
                ->where('date_fin', '>=', now()->subDay())
                ->orderBy('date_fin')
                ->limit(6)
                ->get();

            foreach ($cautions as $c) {
                $m = $c->marche;
                $label = $m ? ($m->intitule ?: $m->objet ?: $m->reference) : 'Marché';
                $rappelsHome->push([
                    'kind' => 'caution',
                    'title' => 'Caution — '.$label,
                    'subtitle' => 'Échéance '.$c->date_fin->format('d/m/Y'),
                    'sort' => $c->date_fin->copy()->startOfDay(),
                    'url' => $m ? route('marches.show', $c->marche_id) : route('alertes.index'),
                    'icon' => 'bi-shield-check',
                    'accent' => '#FF9500',
                ]);
            }

            $factures = Facture::with('client')
                ->where('actif', 1)
                ->whereIn('statut', ['emise', 'envoyee', 'payee_partielle'])
                ->whereNotNull('date_echeance')
                ->where('date_echeance', '<=', now()->addDays($joursFacture))
                ->orderBy('date_echeance')
                ->limit(6)
                ->get();

            foreach ($factures as $f) {
                $clientNom = $f->client ? trim(($f->client->nom ?? '').' '.($f->client->prenom ?? '')) : '';
                $rappelsHome->push([
                    'kind' => 'facture',
                    'title' => 'Facture '.$f->numero_facture,
                    'subtitle' => 'Échéance '.$f->date_echeance->format('d/m/Y').($clientNom ? ' · '.$clientNom : ''),
                    'sort' => $f->date_echeance->copy()->startOfDay(),
                    'url' => route('factures.show', $f),
                    'icon' => 'bi-receipt-cutoff',
                    'accent' => '#AF52DE',
                ]);
            }

            if ($stockActif) {
                $articlesBas = Article::where('actif', 1)
                    ->whereColumn('stock_quantity', '<=', 'seuil_alerte')
                    ->orderBy('nom')
                    ->limit(6)
                    ->get();

                foreach ($articlesBas as $a) {
                    $rappelsHome->push([
                        'kind' => 'stock',
                        'title' => $a->nom,
                        'subtitle' => 'Stock bas · '.$a->stock_quantity.' / seuil '.$a->seuil_alerte,
                        'sort' => now()->startOfDay(),
                        'url' => route('stock.articles.edit', $a),
                        'icon' => 'bi-box-seam',
                        'accent' => '#FF3B30',
                    ]);
                }
            }
        }

        if ($user) {
            $persos = RappelPersonnel::where('user_id', $user->id)
                ->whereDate('date_rappel', '>=', now()->toDateString())
                ->orderBy('date_rappel')
                ->orderBy('heure_rappel')
                ->limit(20)
                ->get();

            foreach ($persos as $rp) {
                $heureStr = $rp->heure_rappel ? substr((string) $rp->heure_rappel, 0, 5) : '';
                $rappelsHome->push([
                    'kind' => 'personnel',
                    'title' => $rp->titre,
                    'subtitle' => 'Rappel perso · '.$rp->date_rappel->format('d/m/Y').($heureStr ? ' · '.$heureStr : ''),
                    'sort' => $rp->date_rappel->copy()->startOfDay(),
                    'url' => $rappelPersoUrl,
                    'accent' => '#5856D6',
                ]);
            }
        }

        $sorted = $rappelsHome->sortBy('sort')->take(24)->values();

        return [
            'visitesAujourdhui' => $visitesAujourdhui,
            'rdvProchains' => $rdvProchains,
            'rappelsHome' => $sorted,
            'showVisitesBloc' => $showVisitesBloc,
            'showRappelsBloc' => $showRappelsBloc,
            'clients' => $clients,
            'services' => $services,
            'packs' => $packs,
            'canAjouterRdv' => $canAjouterRdv,
            'agenda_redirect_to' => $rdvFormRedirect,
            'chartCa6Mois' => self::chartCa6Mois($chartEid),
            'chartClientsSecteur' => self::chartClientsSecteur($chartEid),
            'dashboardStats' => self::buildDashboardStats($user, $chartEid),
        ];
    }

    /**
     * Indicateurs et listes du tableau de bord (données réelles en base).
     *
     * @return array<string, mixed>
     */
    public static function buildDashboardStats(?User $user, ?int $entrepriseId = null): array
    {
        $eid = $entrepriseId;
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $prevMonthStart = now()->subMonth()->startOfMonth();
        $prevMonthEnd = now()->subMonth()->endOfMonth();

        $facturesMois = self::facturesValidesQuery($eid)
            ->whereBetween('date_emission', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->with('lignes')
            ->get();

        $facturesMoisPrec = self::facturesValidesQuery($eid)
            ->whereBetween('date_emission', [$prevMonthStart->toDateString(), $prevMonthEnd->toDateString()])
            ->with('lignes')
            ->get();

        $facturesMoisCount = $facturesMois->count();
        $facturesMoisPrecCount = $facturesMoisPrec->count();
        $caMois = round((float) $facturesMois->sum(fn (Facture $f) => $f->montant_ttc), 2);
        $caMoisPrec = round((float) $facturesMoisPrec->sum(fn (Facture $f) => $f->montant_ttc), 2);
        $panierMoyen = $facturesMoisCount > 0 ? round($caMois / $facturesMoisCount, 2) : 0.0;
        $panierMoyenPrec = $facturesMoisPrecCount > 0 ? round($caMoisPrec / $facturesMoisPrecCount, 2) : 0.0;

        $devisAttente = self::scopedQuery(Devis::query()->where('actif', 1)->where('statut', 'envoye'), $eid)->count();
        $devisAttentePrec = self::scopedQuery(
            Devis::query()->where('actif', 1)->where('statut', 'envoye')
                ->whereBetween('updated_at', [$prevMonthStart, $prevMonthEnd]),
            $eid
        )->count();

        $articlesQuery = self::scopedQuery(Article::query()->where('actif', 1), $eid);
        $articlesStockTotal = (int) round((float) (clone $articlesQuery)->sum('stock_quantity'));
        $stockAlertes = (clone $articlesQuery)->whereColumn('stock_quantity', '<=', 'seuil_alerte')->count();

        $clientsActifs = self::scopedQuery(Client::query()->where('actif', 1), $eid)->count();
        $clientsActifsPrec = self::scopedQuery(
            Client::query()->where('actif', 1)->where('created_at', '<=', $prevMonthEnd),
            $eid
        )->count();

        $encaissementsMois = self::encaissementsQuery($eid)
            ->whereBetween('date_operation', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->sum('montant');
        $encaissementsMoisPrec = self::encaissementsQuery($eid)
            ->whereBetween('date_operation', [$prevMonthStart->toDateString(), $prevMonthEnd->toDateString()])
            ->sum('montant');

        $facturesImpayees = self::scopedQuery(
            Facture::query()
                ->where('actif', 1)
                ->whereIn('statut', ['emise', 'envoyee', 'payee_partielle']),
            $eid
        )->with('lignes')->get();

        $montantImpaye = round((float) $facturesImpayees->sum(fn (Facture $f) => $f->montant_ttc), 2);

        $facturesImpayeesPrec = self::scopedQuery(
            Facture::query()
                ->where('actif', 1)
                ->whereIn('statut', ['emise', 'envoyee', 'payee_partielle'])
                ->where('date_emission', '<=', $prevMonthEnd),
            $eid
        )->with('lignes')->get();
        $montantImpayePrec = round((float) $facturesImpayeesPrec->sum(fn (Facture $f) => $f->montant_ttc), 2);

        $paiementsMois = self::encaissementsQuery($eid)
            ->whereBetween('date_operation', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->groupBy(fn (ComptaMouvement $m) => self::normalizeModePaiement($m->mode_paiement))
            ->map(fn ($group, $mode) => [
                'mode' => $mode,
                'label' => self::modePaiementLabel($mode),
                'icon' => self::modePaiementIcon($mode),
                'color' => self::modePaiementColor($mode),
                'montant' => round((float) $group->sum('montant'), 2),
            ])
            ->sortByDesc('montant')
            ->values();

        $totalEncaisse = round((float) $paiementsMois->sum('montant'), 2);
        $paiementsMois = $paiementsMois->map(function (array $row) use ($totalEncaisse) {
            $row['pct'] = $totalEncaisse > 0 ? round($row['montant'] / $totalEncaisse * 100) : 0;

            return $row;
        });

        $dernieresFactures = self::scopedQuery(
            Facture::query()->where('actif', 1)->with('client')->orderByDesc('date_emission')->orderByDesc('id'),
            $eid
        )->limit(4)->get()->map(function (Facture $f) {
            $client = $f->client;
            $clientNom = $client ? trim(($client->nom ?? '').' '.($client->prenom ?? '')) : '—';
            if ($clientNom === '') {
                $clientNom = '—';
            }

            return [
                'url' => route('factures.show', $f),
                'numero' => $f->numero_facture,
                'client' => $clientNom,
                'date_label' => self::relativeDateLabel($f->date_emission),
                'montant' => $f->montant_ttc,
                'montant_fmt' => self::formatMad($f->montant_ttc),
            ];
        });

        $stockBas = (clone $articlesQuery)
            ->whereColumn('stock_quantity', '<=', 'seuil_alerte')
            ->orderBy('stock_quantity')
            ->limit(4)
            ->get()
            ->map(function (Article $a) {
                $ratio = (float) $a->seuil_alerte > 0
                    ? (float) $a->stock_quantity / (float) $a->seuil_alerte
                    : 0;

                return [
                    'url' => route('stock.articles.edit', $a),
                    'nom' => $a->nom,
                    'reference' => $a->reference ?: '—',
                    'stock' => $a->stock_quantity,
                    'seuil' => $a->seuil_alerte,
                    'niveau' => $ratio <= 0.5 ? 'Critique' : 'Bas',
                    'niveau_class' => $ratio <= 0.5 ? 'stock-critique' : 'stock-low',
                    'icon' => 'bi-box-seam',
                ];
            });

        return [
            'factures_ce_mois' => $facturesMoisCount,
            'factures_ce_mois_trend' => self::trendBadge($facturesMoisCount, $facturesMoisPrecCount),
            'devis_en_attente' => $devisAttente,
            'devis_en_attente_trend' => self::trendBadge($devisAttente, $devisAttentePrec),
            'articles_stock_total' => $articlesStockTotal,
            'stock_alertes' => $stockAlertes,
            'clients_actifs' => $clientsActifs,
            'clients_actifs_trend' => self::trendBadge($clientsActifs, $clientsActifsPrec),
            'ca_ce_mois' => $caMois,
            'ca_ce_mois_fmt' => self::formatMad($caMois),
            'ca_ce_mois_trend' => self::trendBadge($caMois, $caMoisPrec),
            'panier_moyen' => $panierMoyen,
            'panier_moyen_fmt' => self::formatMad($panierMoyen),
            'panier_moyen_trend' => self::trendBadge($panierMoyen, $panierMoyenPrec),
            'encaissements_ce_mois' => round((float) $encaissementsMois, 2),
            'encaissements_ce_mois_fmt' => self::formatMad((float) $encaissementsMois),
            'encaissements_ce_mois_trend' => self::trendBadge((float) $encaissementsMois, (float) $encaissementsMoisPrec),
            'factures_en_attente_montant' => $montantImpaye,
            'factures_en_attente_montant_fmt' => self::formatMad($montantImpaye),
            'factures_en_attente_trend' => self::trendBadge($montantImpaye, $montantImpayePrec),
            'paiements_mois' => $paiementsMois,
            'total_encaisse_fmt' => self::formatMad($totalEncaisse),
            'dernieres_factures' => $dernieresFactures,
            'stock_bas' => $stockBas,
        ];
    }

    /**
     * Indicateurs opérationnels destinés au profil Comptable.
     *
     * @return array<string, mixed>
     */
    public static function buildComptableStats(?int $entrepriseId = null): array
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $facturesMois = self::scopedQuery(
            Facture::query()
                ->where('actif', 1)
                ->whereNotIn('statut', ['annulee', 'avoir', 'brouillon'])
                ->whereBetween('date_emission', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->with('lignes'),
            $entrepriseId
        )->get();

        $caFacture = round((float) $facturesMois->sum(fn (Facture $facture) => $facture->montant_ttc), 2);
        $tvaFacturee = round((float) $facturesMois->sum(fn (Facture $facture) => $facture->montant_tva), 2);
        $facturesPayees = $facturesMois->where('statut', 'payee')->count();
        $tauxFacturesPayees = $facturesMois->count() > 0
            ? (int) round($facturesPayees / $facturesMois->count() * 100)
            : 0;

        $encaissements = (float) self::scopedQuery(
            ComptaMouvement::query()
                ->whereIn('type', ['entree', 'paiement_client'])
                ->whereBetween('date_operation', [$monthStart->toDateString(), $monthEnd->toDateString()]),
            $entrepriseId
        )->sum('montant');

        $decaissements = (float) self::scopedQuery(
            ComptaMouvement::query()
                ->whereIn('type', ['depense', 'paiement_fournisseur', 'salaire'])
                ->whereBetween('date_operation', [$monthStart->toDateString(), $monthEnd->toDateString()]),
            $entrepriseId
        )->sum('montant');

        $facturesOuvertes = self::scopedQuery(
            Facture::query()
                ->where('actif', 1)
                ->whereIn('statut', ['emise', 'envoyee', 'payee_partielle'])
                ->with(['client', 'lignes', 'comptaMouvements']),
            $entrepriseId
        )->get();

        $remainingAmount = static function (Facture $facture): float {
            $regle = (float) $facture->comptaMouvements
                ->whereIn('type', ['entree', 'paiement_client'])
                ->sum('montant');

            return max(0, round($facture->montant_ttc - $regle, 2));
        };

        $encoursClient = round((float) $facturesOuvertes->sum($remainingAmount), 2);
        $echeancesDepassees = $facturesOuvertes
            ->filter(fn (Facture $facture) => $facture->date_echeance?->isBefore(now()->startOfDay()))
            ->sortBy('date_echeance')
            ->values();
        $montantEchu = round((float) $echeancesDepassees->sum($remainingAmount), 2);

        $echeancesProches = $facturesOuvertes
            ->filter(fn (Facture $facture) => $facture->date_echeance
                && $facture->date_echeance->between(now()->startOfDay(), now()->addDays(7)->endOfDay()))
            ->sortBy('date_echeance')
            ->values();

        $facturesPrioritaires = $echeancesDepassees
            ->concat($echeancesProches)
            ->unique('id')
            ->take(8)
            ->map(function (Facture $facture) use ($remainingAmount) {
                $client = $facture->client;
                $clientNom = $client ? trim(($client->nom ?? '').' '.($client->prenom ?? '')) : '—';
                $isOverdue = $facture->date_echeance?->isBefore(now()->startOfDay()) ?? false;

                return [
                    'url' => route('factures.show', $facture),
                    'numero' => $facture->numero_facture,
                    'client' => $clientNom !== '' ? $clientNom : '—',
                    'echeance' => $facture->date_echeance?->format('d/m/Y') ?? '—',
                    'retard_jours' => $isOverdue
                        ? max(1, (int) $facture->date_echeance->startOfDay()->diffInDays(now()->startOfDay()))
                        : 0,
                    'is_overdue' => $isOverdue,
                    'reste_fmt' => self::formatMad($remainingAmount($facture)),
                ];
            })
            ->values();

        $mouvementsRecents = self::scopedQuery(
            ComptaMouvement::query()
                ->with(['client', 'fournisseur', 'facture', 'employe'])
                ->orderByDesc('date_operation')
                ->orderByDesc('id'),
            $entrepriseId
        )->limit(8)->get()->map(function (ComptaMouvement $mouvement) {
            $isSortie = in_array($mouvement->type, ['depense', 'paiement_fournisseur', 'salaire'], true);
            $tiers = $mouvement->client?->nom
                ?? $mouvement->fournisseur?->raison_sociale
                ?? $mouvement->employe?->nom
                ?? null;

            return [
                'libelle' => $mouvement->libelle,
                'type' => $mouvement->type_label,
                'tiers' => $tiers,
                'date' => $mouvement->date_operation->format('d/m/Y'),
                'montant_fmt' => self::formatMad((float) $mouvement->montant),
                'is_sortie' => $isSortie,
            ];
        });

        return [
            'ca_facture_fmt' => self::formatMad($caFacture),
            'tva_facturee_fmt' => self::formatMad($tvaFacturee),
            'encaissements_fmt' => self::formatMad($encaissements),
            'decaissements_fmt' => self::formatMad($decaissements),
            'solde_mois' => round($encaissements - $decaissements, 2),
            'solde_mois_fmt' => self::formatMad($encaissements - $decaissements),
            'encours_client_fmt' => self::formatMad($encoursClient),
            'montant_echu_fmt' => self::formatMad($montantEchu),
            'factures_mois_count' => $facturesMois->count(),
            'factures_payees_count' => $facturesPayees,
            'taux_factures_payees' => $tauxFacturesPayees,
            'echeances_depassees_count' => $echeancesDepassees->count(),
            'echeances_proches_count' => $echeancesProches->count(),
            'factures_prioritaires' => $facturesPrioritaires,
            'mouvements_recents' => $mouvementsRecents,
        ];
    }

    private static function facturesValidesQuery(?int $entrepriseId)
    {
        return self::scopedQuery(
            Facture::query()
                ->where('actif', 1)
                ->whereNotIn('statut', ['annulee', 'avoir', 'brouillon']),
            $entrepriseId
        );
    }

    private static function encaissementsQuery(?int $entrepriseId)
    {
        return self::scopedQuery(
            ComptaMouvement::query()->whereIn('type', ['paiement_client', 'entree']),
            $entrepriseId
        );
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private static function scopedQuery($query, ?int $entrepriseId)
    {
        if ($entrepriseId !== null) {
            $query->where($query->getModel()->getTable().'.id_entreprise', $entrepriseId);
        }

        return $query;
    }

    private static function formatMad(float $amount): string
    {
        return number_format($amount, 0, ',', ' ').' MAD';
    }

    private static function relativeDateLabel(Carbon $date): string
    {
        if ($date->isToday()) {
            return "Aujourd'hui";
        }
        if ($date->isYesterday()) {
            return 'Hier';
        }

        return $date->locale('fr')->isoFormat('D MMM YYYY');
    }

    /**
     * @return array{class: string, icon: string, text: string}|null
     */
    private static function trendBadge(float|int $current, float|int $previous): ?array
    {
        $current = (float) $current;
        $previous = (float) $previous;

        if ($current == 0.0 && $previous == 0.0) {
            return null;
        }

        if ($previous == 0.0) {
            return ['class' => 'up', 'icon' => 'bi-arrow-up', 'text' => 'Nouveau'];
        }

        $pct = (int) round((($current - $previous) / $previous) * 100);

        if ($pct === 0) {
            return ['class' => 'warning', 'icon' => 'bi-dash', 'text' => '0%'];
        }

        return [
            'class' => $pct > 0 ? 'up' : 'down',
            'icon' => $pct > 0 ? 'bi-arrow-up' : 'bi-arrow-down',
            'text' => ($pct > 0 ? '+' : '').$pct.'%',
        ];
    }

    private static function normalizeModePaiement(?string $mode): string
    {
        $mode = mb_strtolower(trim((string) $mode));

        return match (true) {
            str_contains($mode, 'virement') => 'virement',
            str_contains($mode, 'carte') => 'carte',
            str_contains($mode, 'espèce'), str_contains($mode, 'espece') => 'especes',
            str_contains($mode, 'chèque'), str_contains($mode, 'cheque') => 'cheque',
            $mode === '' => 'autre',
            default => $mode,
        };
    }

    private static function modePaiementLabel(string $mode): string
    {
        return match ($mode) {
            'virement' => 'Virement',
            'carte' => 'Carte bancaire',
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'autre' => 'Autre',
            default => ucfirst($mode),
        };
    }

    private static function modePaiementIcon(string $mode): string
    {
        return match ($mode) {
            'virement' => 'bi-credit-card-2-front',
            'carte' => 'bi-credit-card',
            'especes' => 'bi-cash',
            'cheque' => 'bi-bank',
            default => 'bi-wallet2',
        };
    }

    private static function modePaiementColor(string $mode): string
    {
        return match ($mode) {
            'virement' => 'var(--prowise-blue)',
            'carte' => 'var(--prowise-gold-dark)',
            'especes' => 'var(--prowise-gold)',
            'cheque' => 'var(--prowise-blue-light)',
            default => 'var(--prowise-blue-dark)',
        };
    }

    /**
     * CA TTC facturé par mois sur les 6 derniers mois (factures actives hors brouillon / annulées / avoir).
     *
     * @return array{labels: list<string>, values: list<float>}
     */
    private static function chartCa6Mois(?int $entrepriseId = null): array
    {
        $months = [];
        $labels = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i)->startOfMonth();
            $months[] = $d->format('Y-m');
            $labels[] = ucfirst($d->locale('fr')->isoFormat('MMM')).' '.$d->format('y');
        }
        $values = array_fill(0, 6, 0.0);

        $start = now()->subMonths(5)->startOfMonth()->toDateString();

        $q = Facture::with('lignes')
            ->where('actif', 1)
            ->whereNotIn('statut', ['annulee', 'avoir', 'brouillon'])
            ->where('date_emission', '>=', $start);
        if ($entrepriseId !== null) {
            $q->where('id_entreprise', $entrepriseId);
        }
        $factures = $q->get();

        foreach ($factures as $f) {
            $key = $f->date_emission->format('Y-m');
            $idx = array_search($key, $months, true);
            if ($idx !== false) {
                $values[$idx] += $f->montant_ttc;
            }
        }

        foreach ($values as $k => $v) {
            $values[$k] = round($v, 2);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Répartition des clients actifs par secteur d'activité.
     *
     * @return array{labels: list<string>, values: list<int>}
     */
    private static function chartClientsSecteur(?int $entrepriseId = null): array
    {
        $q = Client::query()->where('actif', 1);
        if ($entrepriseId !== null) {
            $q->where('id_entreprise', $entrepriseId);
        }
        $grouped = $q->get()
            ->groupBy(function (Client $c) {
                $s = trim((string) $c->secteur_activite);

                return $s !== '' ? $s : 'Non renseigné';
            })
            ->map(fn ($col) => $col->count())
            ->sortDesc();

        return [
            'labels' => $grouped->keys()->values()->all(),
            'values' => $grouped->values()->all(),
        ];
    }
}
