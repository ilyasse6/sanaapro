@extends('user.master')

@section('content')
<style>
    :root {
        --facture-blue: #0f172a;
        --facture-blue-mid: #0f2438;
        --facture-gold: #198754;
        --gradient-facture: linear-gradient(135deg, #0f172a 0%, #0f2438 50%, #198754 100%);
        --card-shadow: 0 10px 40px rgba(15, 23, 42, 0.1);
        --card-hover: 0 20px 60px rgba(30, 64, 175, 0.12);
    }
    .facture-hero {
        background: var(--gradient-facture);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .facture-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .facture-hero-content { position: relative; z-index: 2; }
    .card-facture {
        background: white;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        border: none;
    }
    .card-facture:hover { box-shadow: var(--card-hover); }
    .table-facture thead {
        background: linear-gradient(135deg, #0f172a 0%, #0f2438 100%);
        color: white;
    }
    .table-facture { border-radius: 12px; overflow: visible; }
    .badge-brouillon { background: #6b7280; color: white; }
    .badge-emise { background: #1e4060; color: white; }
    .badge-envoyee { background: #0ea5e9; color: white; }
    .badge-payee_partielle { background: #f59e0b; color: #111; }
    .badge-payee { background: #16a34a; color: white; }
    .badge-annulee { background: #dc2626; color: white; }
    .badge-avoir { background: #7c3aed; color: white; }
    .btn-facture { border-radius: 12px; padding: 0.6rem 1.2rem; font-weight: 600; transition: all 0.2s; }
    .btn-facture-primary { background: linear-gradient(135deg, #0f2438, #0f172a); color: white; border: none; }
    .btn-facture-primary:hover { color: white; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(15,23,42,0.25); }
    .modern-input { border-radius: 8px; border: 1px solid #e2e8f0; }
    .modern-input:focus { border-color: #0f2438; box-shadow: 0 0 0 3px rgba(30,58,138,0.12); outline: none; }
    .facture-bulk-toolbar { background: #f1f5f9; border-radius: 10px; border: 1px solid #e2e8f0 !important; }
</style>

@php
    $canBulkDesact = Auth::user()->hasPermission('Gestion des Factures', 'desactiver');
    $canBulkModif = Auth::user()->hasPermission('Gestion des Factures', 'modifier');
    $showBulkSelection = ($canBulkDesact || $canBulkModif) && $factures->count() > 0;
@endphp

<div class="section">
    <div class="facture-hero">
        <div class="facture-hero-content d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="mb-1"><i class="bi bi-receipt-cutoff me-2"></i>Gestion des factures</h1>
                <p class="mb-0 opacity-90">Création directe ou conversion depuis un devis, suivi des statuts, impression en lot, archives.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if(Auth::user()->hasPermission('Gestion des Factures', 'consulter'))
                    <a href="{{ route('factures.index_desactives') }}" class="btn btn-outline-light btn-facture border-2">
                        <i class="bi bi-archive me-2"></i>Factures désactivées
                    </a>
                @endif
                @if(Auth::user()->hasPermission('Gestion des Factures', 'ajouter'))
                    <a href="{{ route('factures.create') }}" class="btn btn-light btn-facture">
                        <i class="bi bi-plus-circle me-2"></i>Nouvelle facture
                    </a>
                @endif
                <a href="{{ route('devis.index') }}" class="btn btn-outline-light btn-facture border-2">
                    <i class="bi bi-file-earmark-text me-2"></i>Liste des devis
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card-facture card mb-4">
        <div class="card-body">
            <h6 class="card-title mb-3"><i class="bi bi-funnel me-2"></i>Filtres</h6>
            <form method="get" action="{{ route('factures.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">N° facture</label>
                    <input type="text" name="numero" class="form-control modern-input form-control-sm" value="{{ request('numero') }}" placeholder="FAC-2026-...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Client</label>
                    <select name="client_id" class="form-select modern-input form-select-sm">
                        <option value="">Tous</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected(request('client_id')==$c->id)>{{ $c->nom }} @if($c->prenom) — {{ $c->prenom }} @endif</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Statut</label>
                    <select name="statut" class="form-select modern-input form-select-sm">
                        <option value="">Tous</option>
                        @foreach(['brouillon','emise','envoyee','payee_partielle','payee','annulee','avoir'] as $st)
                            <option value="{{ $st }}" @selected(request('statut')==$st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date du</label>
                    <input type="date" name="date_from" class="form-control modern-input form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Au</label>
                    <input type="date" name="date_to" class="form-control modern-input form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-facture btn-facture-primary btn-sm"><i class="bi bi-search"></i></button>
                    <a href="{{ route('factures.index') }}" class="btn btn-outline-secondary btn-sm">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card-facture card">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="card-title mb-0"><i class="bi bi-list-ul me-2"></i>Liste des factures</h5>
                @if($showBulkSelection)
                    <button type="button" id="factureToggleSelectMode" class="btn btn-outline-primary btn-sm btn-facture">
                        <i class="bi bi-check2-square me-1"></i>Sélectionner
                    </button>
                @endif
            </div>

            @if($showBulkSelection)
            <form id="factureBulkForm" method="post" action="{{ route('factures.bulk') }}">
                @csrf
                <input type="hidden" name="scope" value="actifs">
                <div id="factureBulkToolbar" class="facture-bulk-toolbar d-none mb-3 py-3 px-3">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span class="small text-muted" id="factureBulkCount">0 sélectionné(s)</span>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="factureCheckAll" aria-label="Tout sélectionner sur cette page">
                            <label class="form-check-label small" for="factureCheckAll">Tout sur cette page</label>
                        </div>
                        <select name="bulk_action" id="factureBulkAction" class="form-select form-select-sm" style="min-width: 12rem">
                            <option value="">— Choisir une action —</option>
                            @if($canBulkModif)
                                <optgroup label="Changer le statut">
                                    <option value="statut_brouillon">Brouillon</option>
                                    <option value="statut_emise">Émise</option>
                                    <option value="statut_envoyee">Envoyée</option>
                                    <option value="statut_payee_partielle">Payée partiellement</option>
                                    <option value="statut_payee">Payée</option>
                                    <option value="statut_annulee">Annulée</option>
                                    <option value="statut_avoir">Avoir</option>
                                </optgroup>
                            @endif
                            <option value="imprimer">Imprimer</option>
                            @if($canBulkDesact)
                                <option value="desactiver">Désactiver</option>
                            @endif
                        </select>
                        <button type="submit" class="btn btn-sm btn-facture btn-facture-primary">Appliquer</button>
                    </div>
                </div>
            </form>
            @endif

            <div class="facture-table-wrap table-actions-scroll-wrap">
                <table class="table table-facture table-hover align-middle mb-0 table-fixed-action-dropdowns">
                    <thead>
                        <tr>
                            @if($showBulkSelection)
                                <th class="facture-bulk-col d-none text-center" style="width:2.75rem;"><span class="visually-hidden">Sélection</span></th>
                            @endif
                            <th>N° Facture</th>
                            <th>Client</th>
                            <th>Émission</th>
                            <th>Échéance</th>
                            <th>Statut</th>
                            <th class="text-end">Montant TTC</th>
                            <th>Devis</th>
                            <th class="text-center table-actions-col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($factures as $f)
                            <tr>
                                @if($showBulkSelection)
                                    <td class="facture-bulk-col d-none text-center">
                                        <input type="checkbox" form="factureBulkForm" name="ids[]" value="{{ $f->id }}" class="form-check-input facture-bulk-cb" aria-label="Sélectionner {{ $f->numero_facture }}">
                                    </td>
                                @endif
                                <td><strong>{{ $f->numero_facture }}</strong></td>
                                <td>{{ $f->client->nom }}@if($f->client->prenom) – {{ $f->client->prenom }}@endif</td>
                                <td>{{ $f->date_emission->format('d/m/Y') }}</td>
                                <td>{{ $f->date_echeance ? $f->date_echeance->format('d/m/Y') : '—' }}</td>
                                <td>
                                    <span class="badge badge-{{ $f->statut }}">{{ $f->statut_label }}</span>
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($f->montant_ttc, 2, ',', ' ') }} MAD</td>
                                <td>@if($f->devis_id && $f->devis)<a href="{{ route('devis.show', $f->devis_id) }}">{{ $f->devis->numero_devis }}</a>@else — @endif</td>
                                <td class="text-center table-actions-cell">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            id="factureActionDropdown{{ $f->id }}"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            aria-label="Actions facture {{ $f->numero_facture }}">Actions</button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="factureActionDropdown{{ $f->id }}">
                                            <li><a class="dropdown-item" href="{{ route('factures.show', $f) }}"><i class="bi bi-eye me-2"></i>Voir</a></li>
                                            <li><a class="dropdown-item" href="{{ route('factures.show', $f) }}?print=1" target="_blank" rel="noopener noreferrer"><i class="bi bi-printer me-2"></i>Imprimer</a></li>
                                            <li><a class="dropdown-item" href="{{ route('factures.pdf', $f) }}" target="_blank" rel="noopener"><i class="bi bi-file-pdf me-2"></i>PDF</a></li>
                                            @if(Auth::user()->hasPermission('Gestion des Factures', 'modifier') && $f->actif)
                                                <li><a class="dropdown-item" href="{{ route('factures.edit', $f) }}"><i class="bi bi-pencil me-2"></i>Modifier</a></li>
                                                <li><a class="dropdown-item" href="{{ route('factures.duplicate', $f) }}"><i class="bi bi-copy me-2"></i>Dupliquer</a></li>
                                            @endif
                                            @if(Auth::user()->hasPermission('Gestion des Factures', 'desactiver') && $f->actif)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-warning" data-bs-toggle="modal" data-bs-target="#archiverFactureModal-{{ $f->id }}"><i class="bi bi-eye-slash me-2"></i>Désactiver</button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showBulkSelection ? 9 : 8 }}" class="text-center text-muted py-5">
                                    <i class="bi bi-receipt display-4 d-block mb-2"></i>
                                    Aucune facture. <a href="{{ route('factures.create') }}">Créer une facture</a> ou depuis un devis : <a href="{{ route('devis.index') }}">Gestion des devis</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @foreach($factures as $f)
                @if(Auth::user()->hasPermission('Gestion des Factures', 'desactiver') && $f->actif)
                    <div class="modal fade" id="archiverFactureModal-{{ $f->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title">Désactiver la facture</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                </div>
                                <div class="modal-body">
                                    La facture <strong>{{ $f->numero_facture }}</strong> sera retirée de la liste principale et conservée dans <strong>Factures désactivées</strong>.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <form action="{{ route('factures.desactivate', $f->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">Désactiver</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
            @if($factures->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $factures->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@if($showBulkSelection)
<script>
(function () {
    var btn = document.getElementById('factureToggleSelectMode');
    var toolbar = document.getElementById('factureBulkToolbar');
    var form = document.getElementById('factureBulkForm');
    if (!btn || !toolbar || !form) return;
    if (btn.getAttribute('data-facture-bulk-bound') === '1') return;
    btn.setAttribute('data-facture-bulk-bound', '1');

    var cols = document.querySelectorAll('.facture-bulk-col');
    var checkAll = document.getElementById('factureCheckAll');
    var countEl = document.getElementById('factureBulkCount');

    function rowCheckboxes() {
        return document.querySelectorAll('input.facture-bulk-cb[form="factureBulkForm"]');
    }

    function updateCount() {
        var n = document.querySelectorAll('input.facture-bulk-cb[form="factureBulkForm"]:checked').length;
        countEl.textContent = n + ' sélectionné(s)';
    }

    function bindRowChecks() {
        rowCheckboxes().forEach(function (cb) {
            cb.addEventListener('change', function () {
                updateCount();
                if (checkAll) {
                    var all = rowCheckboxes();
                    var checked = document.querySelectorAll('input.facture-bulk-cb[form="factureBulkForm"]:checked');
                    checkAll.checked = all.length > 0 && all.length === checked.length;
                }
            });
        });
    }
    bindRowChecks();

    btn.addEventListener('click', function () {
        var wasHidden = toolbar.classList.contains('d-none');
        if (wasHidden) {
            toolbar.classList.remove('d-none');
            cols.forEach(function (c) { c.classList.remove('d-none'); });
            btn.classList.add('active');
        } else {
            toolbar.classList.add('d-none');
            cols.forEach(function (c) { c.classList.add('d-none'); });
            btn.classList.remove('active');
            rowCheckboxes().forEach(function (cb) { cb.checked = false; });
            if (checkAll) checkAll.checked = false;
        }
        updateCount();
    });

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            rowCheckboxes().forEach(function (cb) { cb.checked = checkAll.checked; });
            updateCount();
        });
    }

    form.addEventListener('submit', function (e) {
        var actionSelect = document.getElementById('factureBulkAction');
        var action = actionSelect ? actionSelect.value : '';
        if (!action) {
            e.preventDefault();
            alert('Choisissez une action.');
            return;
        }
        var selected = document.querySelectorAll('input.facture-bulk-cb[form="factureBulkForm"]:checked');
        if (selected.length === 0) {
            e.preventDefault();
            alert('Cochez au moins une facture.');
            return;
        }
        if (action === 'imprimer') {
            form.target = '_blank';
        } else {
            form.target = '_self';
        }
    });
})();
</script>
@endif
@endsection
