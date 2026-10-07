@extends('user.master')

@section('content')
@php
    $c = $comptableStats ?? [];
    $user = Auth::user();
@endphp

<style>
    .accounting-dashboard {
        --accounting-blue: #17324D;
        --accounting-navy: #0f2438;
        --accounting-gold: #198754;
        --accounting-green: #15803d;
        --accounting-red: #dc2626;
        --accounting-muted: #64748b;
        width: 100%;
        min-height: 100vh;
    }
    .accounting-hero {
        position: relative;
        overflow: hidden;
        color: #fff;
        background: linear-gradient(135deg, #17324D 0%, #0f2438 62%, #146c43 155%);
        border-radius: 20px;
        padding: 1.45rem 1.65rem;
        margin-bottom: 1.1rem;
        box-shadow: 0 14px 34px rgba(30, 64, 175, .18);
    }
    .accounting-hero::after {
        content: "";
        position: absolute;
        right: -75px;
        top: -105px;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .1);
    }
    .accounting-hero > * { position: relative; z-index: 1; }
    .accounting-hero h1 { font-size: 1.5rem; font-weight: 700; margin: 0 0 .25rem; }
    .accounting-hero p { margin: 0; opacity: .88; font-size: .88rem; }
    .accounting-period {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .48rem .8rem;
        border: 1px solid rgba(255, 255, 255, .35);
        border-radius: 10px;
        background: rgba(255, 255, 255, .12);
        font-size: .8rem;
        font-weight: 600;
    }
    .accounting-kpi {
        height: 100%;
        background: #fff;
        border: 1px solid rgba(30, 64, 175, .08);
        border-radius: 15px;
        padding: 1rem 1.05rem;
        box-shadow: 0 9px 28px rgba(30, 64, 175, .07);
        transition: transform .2s, box-shadow .2s;
    }
    .accounting-kpi:hover { transform: translateY(-3px); box-shadow: 0 14px 34px rgba(30, 64, 175, .11); }
    .accounting-kpi-top { display: flex; align-items: center; justify-content: space-between; gap: .75rem; margin-bottom: .8rem; }
    .accounting-kpi-icon {
        width: 40px;
        height: 40px;
        display: grid;
        place-items: center;
        border-radius: 11px;
        font-size: 1.05rem;
    }
    .kpi-blue { color: var(--accounting-blue); background: rgba(30, 64, 175, .1); }
    .kpi-green { color: var(--accounting-green); background: rgba(21, 128, 61, .1); }
    .kpi-gold { color: #9a7208; background: rgba(212, 175, 55, .16); }
    .kpi-red { color: var(--accounting-red); background: rgba(220, 38, 38, .1); }
    .accounting-kpi-label { color: var(--accounting-muted); font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
    .accounting-kpi-value { color: var(--accounting-navy); font-size: 1.35rem; font-weight: 800; line-height: 1.2; }
    .accounting-kpi-note { color: var(--accounting-muted); font-size: .74rem; margin-top: .3rem; }
    .accounting-panel {
        height: 100%;
        background: #fff;
        border: 1px solid rgba(30, 64, 175, .08);
        border-radius: 16px;
        padding: 1.15rem;
        box-shadow: 0 9px 28px rgba(30, 64, 175, .07);
    }
    .accounting-panel-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin-bottom: 1rem;
    }
    .accounting-panel-title h2 { color: var(--accounting-navy); font-size: 1rem; font-weight: 700; margin: 0; }
    .accounting-panel-title h2 i { color: #146c43; margin-right: .4rem; }
    .accounting-link { color: var(--accounting-blue); font-size: .78rem; font-weight: 600; text-decoration: none; }
    .accounting-link:hover { text-decoration: underline; }
    .due-row, .movement-row {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .72rem .75rem;
        border-radius: 11px;
        background: #f8faff;
        border: 1px solid #edf1f8;
        margin-bottom: .55rem;
        text-decoration: none;
        color: inherit;
    }
    .due-row:hover, .movement-row:hover { background: #f1f5ff; }
    .due-status {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        border-radius: 10px;
        color: #b45309;
        background: rgba(245, 158, 11, .12);
    }
    .due-row.overdue .due-status { color: var(--accounting-red); background: rgba(220, 38, 38, .1); }
    .due-main, .movement-main { min-width: 0; flex: 1; }
    .due-ref, .movement-label { color: var(--accounting-navy); font-size: .84rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .due-meta, .movement-meta { color: var(--accounting-muted); font-size: .72rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .due-amount, .movement-amount { color: var(--accounting-navy); font-size: .8rem; font-weight: 700; white-space: nowrap; text-align: right; }
    .movement-amount.out { color: var(--accounting-red); }
    .movement-amount.in { color: var(--accounting-green); }
    .movement-icon {
        width: 34px;
        height: 34px;
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        border-radius: 9px;
        color: var(--accounting-blue);
        background: rgba(30, 64, 175, .09);
    }
    .movement-icon.out { color: var(--accounting-red); background: rgba(220, 38, 38, .09); }
    .accounting-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: .7rem;
        margin-bottom: 1rem;
    }
    .summary-item { background: #f8faff; border: 1px solid #edf1f8; border-radius: 11px; padding: .8rem; }
    .summary-label { color: var(--accounting-muted); font-size: .7rem; margin-bottom: .2rem; }
    .summary-value { color: var(--accounting-navy); font-size: .95rem; font-weight: 700; }
    .accounting-progress { height: 9px; background: #e7ecf5; border-radius: 999px; overflow: hidden; }
    .accounting-progress span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #17324D, #198754); }
    .accounting-actions { display: flex; flex-wrap: wrap; gap: .55rem; }
    .accounting-action {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border: 1px solid rgba(30, 64, 175, .16);
        border-radius: 10px;
        padding: .58rem .78rem;
        color: var(--accounting-navy);
        background: #fff;
        text-decoration: none;
        font-size: .8rem;
        font-weight: 600;
    }
    .accounting-action:hover { color: #fff; background: linear-gradient(135deg, #17324D, #0f2438); border-color: transparent; }
    .accounting-empty { padding: 2rem 1rem; text-align: center; color: var(--accounting-muted); font-size: .82rem; }
    @media (max-width: 767.98px) {
        .accounting-summary { grid-template-columns: 1fr; }
        .accounting-kpi-value { font-size: 1.15rem; }
        .due-amount, .movement-amount { display: none; }
    }
</style>

<section class="section accounting-dashboard">
    <div class="accounting-hero">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1><i class="bi bi-calculator me-2"></i>Tableau de bord comptable</h1>
                <p>{{ $user->entreprise?->abbreviation ?? $user->entreprise?->description ?? 'Votre entreprise' }} · Bonjour {{ $user->prenom }}</p>
            </div>
            <div class="accounting-period"><i class="bi bi-calendar3"></i>{{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-xxl-2 col-xl-4 col-md-6">
            <div class="accounting-kpi">
                <div class="accounting-kpi-top">
                    <span class="accounting-kpi-label">CA facturé</span>
                    <span class="accounting-kpi-icon kpi-blue"><i class="bi bi-receipt"></i></span>
                </div>
                <div class="accounting-kpi-value">{{ $c['ca_facture_fmt'] ?? '0 MAD' }}</div>
                <div class="accounting-kpi-note">{{ $c['factures_mois_count'] ?? 0 }} facture(s) ce mois</div>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-md-6">
            <div class="accounting-kpi">
                <div class="accounting-kpi-top">
                    <span class="accounting-kpi-label">Encaissements</span>
                    <span class="accounting-kpi-icon kpi-green"><i class="bi bi-arrow-down-left"></i></span>
                </div>
                <div class="accounting-kpi-value">{{ $c['encaissements_fmt'] ?? '0 MAD' }}</div>
                <div class="accounting-kpi-note">Entrées du mois</div>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-md-6">
            <div class="accounting-kpi">
                <div class="accounting-kpi-top">
                    <span class="accounting-kpi-label">Décaissements</span>
                    <span class="accounting-kpi-icon kpi-red"><i class="bi bi-arrow-up-right"></i></span>
                </div>
                <div class="accounting-kpi-value">{{ $c['decaissements_fmt'] ?? '0 MAD' }}</div>
                <div class="accounting-kpi-note">Sorties du mois</div>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-md-6">
            <div class="accounting-kpi">
                <div class="accounting-kpi-top">
                    <span class="accounting-kpi-label">Solde du mois</span>
                    <span class="accounting-kpi-icon {{ ($c['solde_mois'] ?? 0) >= 0 ? 'kpi-green' : 'kpi-red' }}"><i class="bi bi-wallet2"></i></span>
                </div>
                <div class="accounting-kpi-value" style="color:{{ ($c['solde_mois'] ?? 0) >= 0 ? 'var(--accounting-green)' : 'var(--accounting-red)' }}">{{ $c['solde_mois_fmt'] ?? '0 MAD' }}</div>
                <div class="accounting-kpi-note">Encaissements − sorties</div>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-md-6">
            <div class="accounting-kpi">
                <div class="accounting-kpi-top">
                    <span class="accounting-kpi-label">Encours clients</span>
                    <span class="accounting-kpi-icon kpi-gold"><i class="bi bi-hourglass-split"></i></span>
                </div>
                <div class="accounting-kpi-value">{{ $c['encours_client_fmt'] ?? '0 MAD' }}</div>
                <div class="accounting-kpi-note">Reste à encaisser</div>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-md-6">
            <div class="accounting-kpi">
                <div class="accounting-kpi-top">
                    <span class="accounting-kpi-label">Montant échu</span>
                    <span class="accounting-kpi-icon kpi-red"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
                <div class="accounting-kpi-value" style="color:var(--accounting-red)">{{ $c['montant_echu_fmt'] ?? '0 MAD' }}</div>
                <div class="accounting-kpi-note">{{ $c['echeances_depassees_count'] ?? 0 }} facture(s) en retard</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-xl-7">
            <div class="accounting-panel">
                <div class="accounting-panel-title">
                    <h2><i class="bi bi-alarm"></i>Échéances à traiter</h2>
                    @if($user->hasPermission('Gestion des Factures', 'consulter'))
                        <a href="{{ route('factures.index') }}" class="accounting-link">Toutes les factures <i class="bi bi-arrow-right"></i></a>
                    @endif
                </div>
                @forelse(($c['factures_prioritaires'] ?? collect()) as $facture)
                    <a href="{{ $facture['url'] }}" class="due-row {{ $facture['is_overdue'] ? 'overdue' : '' }}">
                        <span class="due-status"><i class="bi {{ $facture['is_overdue'] ? 'bi-exclamation-circle' : 'bi-clock' }}"></i></span>
                        <span class="due-main">
                            <span class="due-ref d-block">{{ $facture['numero'] }} · {{ $facture['client'] }}</span>
                            <span class="due-meta d-block">
                                Échéance {{ $facture['echeance'] }}
                                @if($facture['is_overdue']) · {{ $facture['retard_jours'] }} jour(s) de retard @else · À venir @endif
                            </span>
                        </span>
                        <span class="due-amount">{{ $facture['reste_fmt'] }}</span>
                    </a>
                @empty
                    <div class="accounting-empty"><i class="bi bi-check-circle text-success fs-3 d-block mb-2"></i>Aucune échéance urgente dans les 7 prochains jours.</div>
                @endforelse
            </div>
        </div>

        <div class="col-xl-5">
            <div class="accounting-panel">
                <div class="accounting-panel-title">
                    <h2><i class="bi bi-activity"></i>Mouvements récents</h2>
                    @if($user->hasPermission('Gestion de la Comptabilité', 'consulter'))
                        <a href="{{ route('compta.mouvements') }}" class="accounting-link">Journal <i class="bi bi-arrow-right"></i></a>
                    @endif
                </div>
                @forelse(($c['mouvements_recents'] ?? collect()) as $mouvement)
                    <div class="movement-row">
                        <span class="movement-icon {{ $mouvement['is_sortie'] ? 'out' : '' }}"><i class="bi {{ $mouvement['is_sortie'] ? 'bi-arrow-up-right' : 'bi-arrow-down-left' }}"></i></span>
                        <span class="movement-main">
                            <span class="movement-label d-block">{{ $mouvement['libelle'] }}</span>
                            <span class="movement-meta d-block">{{ $mouvement['type'] }}@if($mouvement['tiers']) · {{ $mouvement['tiers'] }}@endif · {{ $mouvement['date'] }}</span>
                        </span>
                        <span class="movement-amount {{ $mouvement['is_sortie'] ? 'out' : 'in' }}">{{ $mouvement['is_sortie'] ? '−' : '+' }} {{ $mouvement['montant_fmt'] }}</span>
                    </div>
                @empty
                    <div class="accounting-empty"><i class="bi bi-journal-text fs-3 d-block mb-2"></i>Aucun mouvement comptable enregistré.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="accounting-panel">
        <div class="accounting-panel-title">
            <h2><i class="bi bi-speedometer2"></i>Synthèse du mois</h2>
        </div>
        <div class="accounting-summary">
            <div class="summary-item">
                <div class="summary-label">TVA facturée</div>
                <div class="summary-value">{{ $c['tva_facturee_fmt'] ?? '0 MAD' }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Factures payées</div>
                <div class="summary-value">{{ $c['factures_payees_count'] ?? 0 }} / {{ $c['factures_mois_count'] ?? 0 }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Échéances dans les 7 jours</div>
                <div class="summary-value">{{ $c['echeances_proches_count'] ?? 0 }}</div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
            <span class="small fw-semibold" style="color:var(--accounting-navy)">Taux de factures du mois réglées</span>
            <span class="small fw-bold" style="color:var(--accounting-blue)">{{ $c['taux_factures_payees'] ?? 0 }}%</span>
        </div>
        <div class="accounting-progress mb-4"><span style="width:{{ min(100, $c['taux_factures_payees'] ?? 0) }}%"></span></div>

        <div class="accounting-actions">
            @if($user->hasPermission('Gestion des Factures', 'ajouter'))
                <a href="{{ route('factures.create') }}" class="accounting-action"><i class="bi bi-receipt-cutoff"></i>Nouvelle facture</a>
            @endif
            @if($user->hasPermission('Gestion de la Comptabilité', 'ajouter'))
                <a href="{{ route('compta.mouvements') }}" class="accounting-action"><i class="bi bi-plus-circle"></i>Saisir un mouvement</a>
            @endif
            @if($user->hasPermission('Gestion des Factures', 'consulter'))
                <a href="{{ route('factures.index') }}" class="accounting-action"><i class="bi bi-list-check"></i>Suivre les factures</a>
            @endif
            @if($user->hasPermission('Gestion de la Comptabilité', 'consulter'))
                <a href="{{ route('compta.index') }}" class="accounting-action"><i class="bi bi-calculator"></i>Ouvrir la comptabilité</a>
            @endif
        </div>
    </div>
</section>
@endsection
