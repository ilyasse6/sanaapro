@extends('user.master')
@section('content')
@include('user.marche.partials.styles')
@php
    $decision = $marche->decision_offre ?? 'en_attente';
    $tranches = $marche->tranchesMontantEstimes();
    $showAcceptPanel = $errors->has('date_depart');
    $lotsCount = $marche->lots->count();
    $cautionsActives = $marche->cautions->where('statut', 'active')->count();
    $docsCount = $marche->documents->count();
    $typesCaution = [
        'provisoire' => 'Provisoire',
        'definitive' => 'Définitive',
        'retenue_garantie' => 'Retenue de garantie',
        'autre' => 'Autre',
    ];
    $statutsCaution = [
        'active' => 'Active',
        'liberee' => 'Libérée',
        'expiree' => 'Expirée',
        'annulee' => 'Annulée',
    ];
    $joursRestants = null;
    if ($marche->date_limite_plis) {
        $joursRestants = (int) now()->startOfDay()->diffInDays($marche->date_limite_plis->copy()->startOfDay(), false);
    }
@endphp
<style>
    .marche-fiche { --navy: #0f2438; --blue: #17324D; --gold: #198754; --muted: #64748b; --border: rgba(30,64,175,.1); }
    .marche-fiche .fiche-hero {
        background: linear-gradient(135deg, #17324D 0%, #0f2438 55%, #146c43 160%);
        border-radius: 18px;
        color: #fff;
        padding: 1.35rem 1.5rem;
        margin-bottom: 1.1rem;
        position: relative;
        overflow: hidden;
    }
    .marche-fiche .fiche-hero::after {
        content: '';
        position: absolute;
        right: -40px; top: -50px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,.08);
        border-radius: 50%;
    }
    .marche-fiche .fiche-hero > * { position: relative; z-index: 1; }
    .marche-fiche .fiche-ref { font-size: .75rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #f5e6a8; margin-bottom: .2rem; }
    .marche-fiche .fiche-title { font-size: clamp(1.05rem, 2vw, 1.35rem); font-weight: 700; margin: 0 0 .5rem; line-height: 1.35; }
    .marche-fiche .kpi-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem; margin-bottom: 1.1rem; }
    @media (max-width: 991px) { .marche-fiche .kpi-row { grid-template-columns: repeat(2, 1fr); } }
    .marche-fiche .kpi {
        background: #fff;
        border-radius: 14px;
        border: 1px solid var(--border);
        box-shadow: 0 8px 24px rgba(30,64,175,.06);
        padding: .9rem 1rem;
    }
    .marche-fiche .kpi-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); margin-bottom: .25rem; }
    .marche-fiche .kpi-value { font-size: 1.05rem; font-weight: 700; color: var(--navy); line-height: 1.3; }
    .marche-fiche .kpi-value.sm { font-size: .92rem; font-weight: 600; }
    .marche-fiche .kpi-warn { color: #b45309; }
    .marche-fiche .kpi-danger { color: #be123c; }
    .marche-fiche .panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(30,64,175,.06);
        padding: 1.1rem 1.2rem;
        margin-bottom: 1rem;
        height: 100%;
    }
    .marche-fiche .panel-title {
        font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
        color: var(--muted); margin-bottom: .85rem; padding-bottom: .5rem;
        border-bottom: 1px solid #eef1f7;
        display: flex; align-items: center; gap: .4rem;
    }
    .marche-fiche .panel-title i { color: #146c43; }
    .marche-fiche .kv { margin-bottom: .7rem; font-size: .9rem; }
    .marche-fiche .kv:last-child { margin-bottom: 0; }
    .marche-fiche .kv strong { display: block; font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); margin-bottom: .15rem; }
    .marche-fiche .tabs-bar {
        display: flex; flex-wrap: wrap; gap: .4rem;
        background: #f1f5f9; border-radius: 9999px; padding: .35rem;
        border: 1px solid var(--border); margin-bottom: .85rem;
    }
    .marche-fiche .tabs-bar .nav-link {
        border: none; border-radius: 9999px; font-weight: 600; font-size: .85rem;
        color: var(--muted); padding: .45rem 1rem;
    }
    .marche-fiche .tabs-bar .nav-link.active {
        background: linear-gradient(135deg, #17324D, #0f2438); color: #fff !important;
        box-shadow: 0 4px 12px rgba(30,64,175,.3);
    }
    .marche-fiche .tab-box {
        background: #fff; border: 1px solid var(--border); border-radius: 14px;
        box-shadow: 0 8px 24px rgba(30,64,175,.06); padding: 1rem 1.15rem 1.15rem;
    }
    .marche-fiche .table-wrap {
        border: 1px solid #eef1f7; border-radius: 10px; overflow: auto; margin-bottom: 1rem; background: #fafbff;
    }
    .marche-fiche .table-wrap table { margin: 0; font-size: .875rem; }
    .marche-fiche .table-wrap thead th {
        background: linear-gradient(135deg, #17324D, #0f2438); color: #fff; border: none;
        font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; padding: .7rem .85rem; white-space: nowrap;
    }
    .marche-fiche .table-wrap tbody td { padding: .7rem .85rem; vertical-align: middle; border-color: #eef1f7; }
    .marche-fiche .empty-hint {
        text-align: center; padding: 1.5rem 1rem; color: var(--muted); background: #f8fafc;
        border-radius: 10px; border: 1px dashed #cbd5e1; margin-bottom: 1rem;
    }
    .marche-fiche .btn-decision {
        border: none; border-radius: 9999px; padding: .4rem 1rem; font-weight: 600; font-size: .85rem;
        background: linear-gradient(135deg, #16a34a, #15803d); color: #fff;
        box-shadow: 0 4px 14px rgba(21,128,61,.3);
    }
    .marche-fiche .btn-archiver {
        border-radius: 9999px; padding: .35rem .9rem; font-weight: 600; font-size: .85rem;
        border: 1.5px solid #be123c; color: #be123c; background: #fff;
    }
    .marche-fiche .btn-archiver:hover { background: #be123c; color: #fff; }
    .marche-fiche .accept-box {
        border: 1px solid rgba(22,163,74,.35); border-radius: 14px;
        background: linear-gradient(145deg, rgba(22,163,74,.07), #fff 50%);
        padding: 1.1rem 1.25rem; margin-bottom: 1rem;
    }
    .marche-fiche .doc-row {
        display: flex; justify-content: space-between; align-items: center; gap: .75rem; flex-wrap: wrap;
        padding: .7rem .85rem; border: 1px solid #eef1f7; border-radius: 10px; margin-bottom: .5rem; background: #fafbff;
    }
    .marche-fiche .doc-row a { font-weight: 600; color: var(--navy); text-decoration: none; }
    .marche-fiche .doc-row a:hover { text-decoration: underline; }
    .marche-fiche .side-portail { border-left: 3px solid #17324D; }
    .marche-fiche .side-ok { border-left: 3px solid #16a34a; }
</style>

<div class="section marche-fiche">
    {{-- Hero --}}
    <div class="fiche-hero">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div class="flex-grow-1 min-w-0" style="max-width: 720px;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <a href="{{ route('marches.index') }}" class="btn btn-sm btn-marche btn-marche-outline py-1 px-2" title="Retour"><i class="bi bi-arrow-left"></i></a>
                    <span class="fiche-ref">{{ $marche->reference }}</span>
                </div>
                <h1 class="fiche-title" title="{{ e($marche->intitule) }}">{{ $marche->intitule }}</h1>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="{{ $marche->statut_badge_class }}">{{ $marche->statut_label }}</span>
                    @if($decision === 'accepte')
                        <span class="badge rounded-pill text-white" style="background:linear-gradient(135deg,#16a34a,#15803d)">{{ $marche->decision_offre_label }}</span>
                    @elseif($decision === 'refuse')
                        <span class="badge rounded-pill text-white" style="background:linear-gradient(135deg,#dc2626,#b91c1c)">{{ $marche->decision_offre_label }}</span>
                    @else
                        <span class="badge rounded-pill text-white" style="background:linear-gradient(135deg,#64748b,#475569)">{{ $marche->decision_offre_label }}</span>
                    @endif
                    @if($marche->reference_consultation)
                        <span class="badge rounded-pill text-bg-light text-dark">Portail : {{ $marche->reference_consultation }}</span>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'modifier') && $decision === 'en_attente')
                    <div class="dropdown">
                        <button class="btn btn-decision dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="marcheDecisionDropdown">
                            <i class="bi bi-flag me-1"></i>Décision offre
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                            <li>
                                <button type="button" class="dropdown-item py-2" data-bs-toggle="collapse" data-bs-target="#marcheAcceptPanel">
                                    <i class="bi bi-check-lg text-success me-2"></i>Accepter
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('marches.decision-offre', $marche) }}" method="post" class="px-1 mb-0" onsubmit="return confirm('Refuser cette offre ?');">
                                    @csrf
                                    <input type="hidden" name="decision" value="refuse">
                                    <button type="submit" class="dropdown-item text-danger py-2"><i class="bi bi-x-lg me-2"></i>Refuser</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endif
                @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'modifier'))
                    <a href="{{ route('marches.edit', $marche) }}" class="btn btn-marche btn-marche-light btn-sm"><i class="bi bi-pencil me-1"></i>Modifier</a>
                @endif
                @if($marche->source_url)
                    <a href="{{ $marche->source_url }}" target="_blank" rel="noopener" class="btn btn-marche btn-marche-outline btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i>Portail</a>
                @endif
                @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'desactiver'))
                    <form action="{{ route('marches.desactivate', $marche) }}" method="post" class="d-inline mb-0" onsubmit="return confirm('Archiver ce marché ?');">@csrf
                        <button type="submit" class="btn btn-sm btn-archiver">Archiver</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'modifier') && $decision === 'en_attente')
        <div class="collapse {{ $showAcceptPanel ? 'show' : '' }}" id="marcheAcceptPanel">
            <div class="accept-box">
                <h6 class="fw-semibold text-success mb-1"><i class="bi bi-calendar-check me-1"></i>Confirmer l’acceptation</h6>
                <p class="small text-muted mb-3">Indiquez la date de départ d’exécution. Le montant estimé sera réparti en 4 tranches de 3 mois.</p>
                <form action="{{ route('marches.decision-offre', $marche) }}" method="post" class="row g-3 align-items-end">
                    @csrf
                    <input type="hidden" name="decision" value="accepte">
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small fw-semibold">Date de départ</label>
                        <input type="date" name="date_depart" class="form-control marche-input @error('date_depart') is-invalid @enderror" required value="{{ old('date_depart') }}">
                        @error('date_depart')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-success rounded-pill px-3"><i class="bi bi-check2-circle me-1"></i>Valider</button>
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-toggle="collapse" data-bs-target="#marcheAcceptPanel">Fermer</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @include('user.partials.flash')

    {{-- KPIs --}}
    <div class="kpi-row">
        <div class="kpi">
            <div class="kpi-label">Montant estimé</div>
            <div class="kpi-value">
                @if($marche->montant_estime !== null)
                    {{ number_format($marche->montant_estime, 2, ',', ' ') }} <span class="fs-6 fw-semibold text-muted">MAD</span>
                @else
                    —
                @endif
            </div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Limite remise des plis</div>
            <div class="kpi-value sm @if($joursRestants !== null && $joursRestants < 0) kpi-danger @elseif($joursRestants !== null && $joursRestants <= 7) kpi-warn @endif">
                @if($marche->date_limite_plis)
                    {{ $marche->date_limite_plis->format('d/m/Y H:i') }}
                    @if($joursRestants !== null)
                        <div class="small fw-semibold mt-1">
                            @if($joursRestants < 0) Échue @elseif($joursRestants === 0) Aujourd’hui @else J−{{ $joursRestants }} @endif
                        </div>
                    @endif
                @else
                    —
                @endif
            </div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Lots / Documents</div>
            <div class="kpi-value">{{ $lotsCount }} <span class="fs-6 text-muted fw-semibold">lots</span> · {{ $docsCount }} <span class="fs-6 text-muted fw-semibold">docs</span></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Cautions actives</div>
            <div class="kpi-value @if($cautionsActives) kpi-warn @endif">{{ $cautionsActives }}</div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Colonne infos --}}
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-title"><i class="bi bi-info-circle"></i> Synthèse</div>
                <div class="kv">
                    <strong>Maître d’ouvrage</strong>
                    @if($marche->acheteur_public)
                        {{ $marche->acheteur_public }}
                    @elseif($marche->client)
                        {{ $marche->client->nom }} <span class="text-muted small">(client)</span>
                    @else
                        —
                    @endif
                </div>
                @if($marche->date_ouverture)
                    <div class="kv"><strong>Ouverture</strong> {{ $marche->date_ouverture->format('d/m/Y') }}</div>
                @endif
                @if($marche->date_limite)
                    <div class="kv"><strong>Échéance interne</strong> {{ $marche->date_limite->format('d/m/Y') }}</div>
                @endif
                @if($marche->objet)
                    <div class="kv"><strong>Objet</strong> {{ \Illuminate\Support\Str::limit($marche->objet, 220) }}</div>
                @endif
                @if($marche->notes)
                    <div class="kv"><strong>Notes</strong> <span class="text-muted">{{ $marche->notes }}</span></div>
                @endif
            </div>

            @if($decision === 'accepte' && $marche->date_depart)
            <div class="panel side-ok">
                <div class="panel-title"><i class="bi bi-calendar3"></i> Échéancier (4 × 3 mois)</div>
                <p class="small mb-2"><strong>Départ :</strong> {{ $marche->date_depart->format('d/m/Y') }}</p>
                @if(count($tranches))
                    <div class="table-wrap mb-0">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Tranche</th><th>Période</th><th class="text-end">Montant</th></tr></thead>
                            <tbody>
                                @foreach($tranches as $t)
                                    <tr>
                                        <td>{{ $t['index'] }}/4</td>
                                        <td class="small">{{ $t['debut']->format('d/m/Y') }} — {{ $t['fin']->format('d/m/Y') }}</td>
                                        <td class="text-end text-nowrap">{{ number_format($t['montant'], 2, ',', ' ') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="small text-muted mb-0">Ajoutez un montant estimé pour voir les tranches.</p>
                @endif
            </div>
            @endif

            @if($marche->reference_consultation || $marche->type_annonce || $marche->procedure_text || $marche->lieu_execution || $marche->contact_nom)
            <div class="panel side-portail">
                <div class="panel-title"><i class="bi bi-building"></i> Consultation portail</div>
                @if($marche->type_annonce)<div class="kv"><strong>Type</strong> {{ $marche->type_annonce }}</div>@endif
                @if($marche->procedure_text)<div class="kv"><strong>Procédure</strong> {{ $marche->procedure_text }}</div>@endif
                @if($marche->categorie_principale)<div class="kv"><strong>Catégorie</strong> {{ $marche->categorie_principale }}</div>@endif
                @if($marche->allotissement)<div class="kv"><strong>Allotissement</strong> {{ $marche->allotissement }}</div>@endif
                @if($marche->lieu_execution)<div class="kv"><strong>Lieu d’exécution</strong> {{ $marche->lieu_execution }}</div>@endif
                @if($marche->adresse_depot_offres)<div class="kv"><strong>Dépôt des offres</strong> {{ $marche->adresse_depot_offres }}</div>@endif
                @if($marche->contact_nom || $marche->contact_email || $marche->contact_telephone)
                    <div class="kv">
                        <strong>Contact</strong>
                        @if($marche->contact_nom){{ $marche->contact_nom }}@endif
                        @if($marche->contact_email)<br><a href="mailto:{{ $marche->contact_email }}">{{ $marche->contact_email }}</a>@endif
                        @if($marche->contact_telephone)<br>{{ $marche->contact_telephone }}@endif
                    </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Onglets opérationnels --}}
        <div class="col-lg-8">
            <ul class="nav tabs-bar" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-lots" type="button">
                        Lots <span class="badge bg-white text-primary ms-1 rounded-pill">{{ $lotsCount }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-cautions" type="button">
                        Cautions
                        @if($cautionsActives)
                            <span class="badge bg-warning text-dark ms-1 rounded-pill">{{ $cautionsActives }}</span>
                        @else
                            <span class="badge bg-white text-primary ms-1 rounded-pill">{{ $marche->cautions->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-docs" type="button">
                        Documents <span class="badge bg-white text-primary ms-1 rounded-pill">{{ $docsCount }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content tab-box">
                {{-- Lots --}}
                <div class="tab-pane fade show active" id="tab-lots">
                    @if($lotsCount)
                        <div class="table-wrap">
                            <table class="table table-hover mb-0">
                                <thead><tr><th>Libellé</th><th class="text-end">Montant</th><th></th></tr></thead>
                                <tbody>
                                    @foreach($marche->lots as $lot)
                                        <tr>
                                            <td>{{ $lot->libelle }}</td>
                                            <td class="text-end text-nowrap">{{ $lot->montant !== null ? number_format($lot->montant, 2, ',', ' ').' MAD' : '—' }}</td>
                                            <td class="text-end">
                                                @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'modifier'))
                                                    <form action="{{ route('marches.lots.destroy', $lot) }}" method="post" class="d-inline" onsubmit="return confirm('Supprimer ce lot ?');">@csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0">Suppr.</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-hint"><i class="bi bi-layers d-block mb-1 fs-4"></i>Aucun lot. Ajoutez les lots de la consultation ici.</div>
                    @endif
                    @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'modifier'))
                        <form class="row g-2 align-items-end" method="post" action="{{ route('marches.lots.store', $marche) }}">@csrf
                            <div class="col-md-6"><label class="form-label small fw-semibold mb-1">Libellé *</label><input name="libelle" class="form-control marche-input form-control-sm" required placeholder="Lot 1 — …"></div>
                            <div class="col-md-3"><label class="form-label small fw-semibold mb-1">Montant</label><input name="montant" type="number" step="0.01" class="form-control marche-input form-control-sm" placeholder="MAD"></div>
                            <div class="col-md-3"><button class="btn btn-marche btn-marche-primary btn-sm w-100"><i class="bi bi-plus-lg me-1"></i>Ajouter</button></div>
                        </form>
                    @endif
                </div>

                {{-- Cautions --}}
                <div class="tab-pane fade" id="tab-cautions">
                    @if($marche->cautions->count())
                        <div class="table-wrap">
                            <table class="table table-hover mb-0">
                                <thead><tr><th>Libellé</th><th>Type</th><th class="text-end">Montant</th><th>Fin</th><th>Statut</th><th></th></tr></thead>
                                <tbody>
                                    @foreach($marche->cautions as $c)
                                        @php
                                            $alerte = $c->date_fin && $c->date_fin->lte(now()->addDays((int)\App\Models\AppSetting::get('alerte_caution_jours', 30, $marche->id_entreprise))) && $c->date_fin->gte(now()) && $c->statut === 'active';
                                        @endphp
                                        <tr class="{{ $alerte ? 'table-warning' : '' }}">
                                            <td>{{ $c->libelle }}</td>
                                            <td>{{ $typesCaution[$c->type_caution] ?? $c->type_caution }}</td>
                                            <td class="text-end text-nowrap">{{ number_format($c->montant, 2, ',', ' ') }}</td>
                                            <td>{{ $c->date_fin?->format('d/m/Y') ?? '—' }}</td>
                                            <td>{{ $statutsCaution[$c->statut] ?? $c->statut }}</td>
                                            <td class="text-end">
                                                @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'modifier'))
                                                    <form action="{{ route('marches.cautions.destroy', $c) }}" method="post" class="d-inline" onsubmit="return confirm('Supprimer cette caution ?');">@csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0">×</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-hint"><i class="bi bi-shield-check d-block mb-1 fs-4"></i>Aucune caution. Suivez ici les cautions provisoires / définitives.</div>
                    @endif
                    @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'modifier'))
                        <form method="post" action="{{ route('marches.cautions.store', $marche) }}" class="row g-2 align-items-end">@csrf
                            <div class="col-md-3"><label class="form-label small fw-semibold mb-1">Libellé *</label><input name="libelle" class="form-control marche-input form-control-sm" required></div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold mb-1">Type</label>
                                <select name="type_caution" class="form-select marche-select form-select-sm">
                                    @foreach($typesCaution as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><label class="form-label small fw-semibold mb-1">Montant *</label><input name="montant" type="number" step="0.01" class="form-control marche-input form-control-sm" required></div>
                            <div class="col-md-2"><label class="form-label small fw-semibold mb-1">Fin</label><input name="date_fin" type="date" class="form-control marche-input form-control-sm"></div>
                            <div class="col-md-2"><button class="btn btn-success btn-sm w-100 rounded-pill">Ajouter</button></div>
                        </form>
                    @endif
                </div>

                {{-- Documents --}}
                <div class="tab-pane fade" id="tab-docs">
                    @forelse($marche->documents as $doc)
                        <div class="doc-row">
                            <a href="{{ asset('storage/'.$doc->chemin) }}" target="_blank"><i class="bi bi-file-earmark-text me-1"></i>{{ $doc->nom_fichier }}</a>
                            @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'modifier'))
                                <form action="{{ route('marches.documents.destroy', $doc) }}" method="post" onsubmit="return confirm('Supprimer ce document ?');">@csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Suppr.</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="empty-hint"><i class="bi bi-folder2-open d-block mb-1 fs-4"></i>Aucun document. Déposez RC, CPS, offre, etc.</div>
                    @endforelse
                    @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'modifier'))
                        <form method="post" action="{{ route('marches.documents.store', $marche) }}" enctype="multipart/form-data" class="d-flex flex-wrap gap-2 align-items-center mt-2">@csrf
                            <input type="file" name="fichier" class="form-control form-control-sm marche-input" style="max-width:320px" required>
                            <button type="submit" class="btn btn-marche btn-marche-primary btn-sm"><i class="bi bi-upload me-1"></i>Envoyer</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var acceptItem = document.querySelector('[data-bs-target="#marcheAcceptPanel"]');
    var toggle = document.getElementById('marcheDecisionDropdown');
    if (!acceptItem || !toggle || typeof bootstrap === 'undefined' || !bootstrap.Dropdown) return;
    acceptItem.addEventListener('click', function () {
        var inst = bootstrap.Dropdown.getInstance(toggle);
        if (inst) inst.hide();
    });
});
</script>
@endpush
