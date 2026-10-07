@extends('user.master')

@section('content')
<style>
    :root {
        --prowise-blue: #17324D;
        --prowise-blue-dark: #0f2438;
        --prowise-gold: #198754;
        --gradient-devis: linear-gradient(135deg, #17324D 0%, #0f2438 50%, #198754 100%);
        --card-shadow: 0 10px 40px rgba(30, 64, 175, 0.08);
        --card-hover: 0 20px 60px rgba(30, 64, 175, 0.12);
    }
    .devis-hero {
        background: var(--gradient-devis);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .devis-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .devis-hero-content { position: relative; z-index: 2; }
    .card-devis {
        background: white;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        border: none;
    }
    .card-devis:hover { box-shadow: var(--card-hover); }
    .table-devis thead {
        background: linear-gradient(135deg, #17324D 0%, #0f2438 100%);
        color: white;
    }
    .table-devis { border-radius: 12px; overflow: visible; }
    .badge-brouillon { background: #6b7280; color: white; }
    .badge-envoye { background: #1e4060; color: white; }
    .badge-accepte { background: #16a34a; color: white; }
    .badge-refuse { background: #dc2626; color: white; }
    .btn-devis {
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-devis-primary { background: linear-gradient(135deg, #17324D, #0f2438); color: white; border: none; }
    .btn-devis-primary:hover { color: white; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(30,64,175,0.3); }
    .filter-card { background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; }
    .modern-input { border-radius: 8px; border: 1px solid #e2e8f0; }
    .modern-input:focus { border-color: #17324D; box-shadow: 0 0 0 3px rgba(30,64,175,0.1); outline: none; }
    .devis-bulk-toolbar { background: #f1f5f9; border-radius: 10px; border: 1px solid #e2e8f0 !important; }
</style>

@php
    $canBulkDesact = Auth::user()->hasPermission('Gestion des Devis', 'desactiver');
    $canBulkModif = Auth::user()->hasPermission('Gestion des Devis', 'modifier');
    $showBulkSelection = ($canBulkDesact || $canBulkModif) && $devis->count() > 0;
@endphp

<div class="section">
    <div class="devis-hero">
        <div class="devis-hero-content d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="mb-1"><i class="bi bi-file-earmark-text me-2"></i>Gestion des devis</h1>
                <p class="mb-0 opacity-90">Créez et suivez vos devis commerciaux : prestations, équipements, packs. Suivi des statuts (brouillon, envoyé, accepté, refusé).</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if(Auth::user()->hasPermission('Gestion des Devis', 'consulter'))
                    <a href="{{ route('devis.index_desactives') }}" class="btn btn-outline-light btn-devis border-2">
                        <i class="bi bi-archive me-2"></i>Devis désactivés
                    </a>
                @endif
                @if(Auth::user()->hasPermission('Gestion des Devis', 'ajouter'))
                    <a href="{{ route('devis.create') }}" class="btn btn-light btn-devis">
                        <i class="bi bi-plus-circle me-2"></i>Nouveau devis
                    </a>
                @endif
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

    <div class="card-devis card mb-4">
        <div class="card-body">
            <h6 class="card-title mb-3"><i class="bi bi-funnel me-2"></i>Filtres</h6>
            <form method="get" action="{{ route('devis.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">N° devis</label>
                    <input type="text" name="numero" class="form-control modern-input form-control-sm" placeholder="DEV-2026-..." value="{{ request('numero') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Client</label>
                    <select name="client_id" class="form-select modern-input form-select-sm">
                        <option value="">Tous</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Statut</label>
                    <select name="statut" class="form-select modern-input form-select-sm">
                        <option value="">Tous</option>
                        <option value="brouillon" {{ request('statut') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="envoye" {{ request('statut') === 'envoye' ? 'selected' : '' }}>Envoyé</option>
                        <option value="accepte" {{ request('statut') === 'accepte' ? 'selected' : '' }}>Accepté</option>
                        <option value="refuse" {{ request('statut') === 'refuse' ? 'selected' : '' }}>Refusé</option>
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
                    <button type="submit" class="btn btn-devis btn-devis-primary btn-sm"><i class="bi bi-search"></i></button>
                    <a href="{{ route('devis.index') }}" class="btn btn-outline-secondary btn-sm">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card-devis card">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="card-title mb-0"><i class="bi bi-list-ul me-2"></i>Liste des devis</h5>
                @if($showBulkSelection)
                    <button type="button" id="devisToggleSelectMode" class="btn btn-outline-primary btn-sm btn-devis">
                        <i class="bi bi-check2-square me-1"></i>Sélectionner
                    </button>
                @endif
            </div>

            @if($showBulkSelection)
            <form id="devisBulkForm" method="post" action="{{ route('devis.bulk') }}">
                @csrf
                <input type="hidden" name="scope" value="actifs">
                {{-- Pas de classe .alert : le master ferme toutes les .alert avec bootstrap.Alert après 5s --}}
                <div id="devisBulkToolbar" class="devis-bulk-toolbar d-none mb-3 py-3 px-3">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span class="small text-muted" id="devisBulkCount">0 sélectionné(s)</span>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="devisCheckAll" aria-label="Tout sélectionner sur cette page">
                            <label class="form-check-label small" for="devisCheckAll">Tout sur cette page</label>
                        </div>
                        <select name="bulk_action" id="devisBulkAction" class="form-select form-select-sm" style="min-width: 12rem">
                            <option value="">— Choisir une action —</option>
                            @if($canBulkModif)
                                <optgroup label="Changer le statut">
                                    <option value="statut_brouillon">Brouillon</option>
                                    <option value="statut_envoye">Envoyé</option>
                                    <option value="statut_accepte">Accepté</option>
                                    <option value="statut_refuse">Refusé</option>
                                </optgroup>
                            @endif
                            <option value="imprimer">Imprimer</option>
                            @if($canBulkDesact)
                                <option value="desactiver">Désactiver</option>
                            @endif
                        </select>
                        <button type="submit" class="btn btn-sm btn-devis btn-devis-primary">Appliquer</button>
                    </div>
                </div>
            </form>
            @endif

            <div class="devis-table-wrap table-actions-scroll-wrap">
                <table class="table table-devis table-hover align-middle mb-0 table-fixed-action-dropdowns">
                    <thead>
                        <tr>
                            @if($showBulkSelection)
                                <th class="devis-bulk-col d-none text-center" style="width:2.75rem;"><span class="visually-hidden">Sélection</span></th>
                            @endif
                            <th>N° Devis</th>
                            <th>Client</th>
                            <th>Date émission</th>
                            <th>Validité</th>
                            <th>Statut</th>
                            <th class="text-end">Montant TTC</th>
                            <th class="text-center table-actions-col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devis as $d)
                            <tr>
                                @if($showBulkSelection)
                                    <td class="devis-bulk-col d-none text-center">
                                        <input type="checkbox" form="devisBulkForm" name="ids[]" value="{{ $d->id }}" class="form-check-input devis-bulk-cb" aria-label="Sélectionner {{ $d->numero_devis }}">
                                    </td>
                                @endif
                                <td><strong>{{ $d->numero_devis }}</strong></td>
                                <td>{{ $d->client->nom }}@if($d->client->prenom) – {{ $d->client->prenom }}@endif</td>
                                <td>{{ $d->date_emission->format('d/m/Y') }}</td>
                                <td>{{ $d->date_validite->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $d->statut }}">{{ $d->statut_label }}</span>
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($d->montant_ttc, 2, ',', ' ') }} MAD</td>
                                <td class="text-center table-actions-cell">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            id="devisActionDropdown{{ $d->id }}"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            aria-haspopup="true"
                                            aria-label="Actions pour le devis {{ $d->numero_devis }}">Actions</button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="devisActionDropdown{{ $d->id }}">
                                            <li><a class="dropdown-item" href="{{ route('devis.show', $d) }}"><i class="bi bi-eye me-2"></i>Voir</a></li>
                                            <li><a class="dropdown-item" href="{{ route('devis.show', $d) }}?print=1" target="_blank" rel="noopener noreferrer"><i class="bi bi-printer me-2"></i>Imprimer</a></li>
                                            <li><a class="dropdown-item" href="{{ route('devis.pdf', $d) }}" target="_blank" rel="noopener"><i class="bi bi-file-pdf me-2"></i>PDF</a></li>
                                            @if(Auth::user()->hasPermission('Gestion des Devis', 'modifier') && $d->actif)
                                                <li><a class="dropdown-item" href="{{ route('devis.edit', $d) }}"><i class="bi bi-pencil me-2"></i>Modifier</a></li>
                                                <li><a class="dropdown-item" href="{{ route('devis.duplicate', $d) }}"><i class="bi bi-copy me-2"></i>Dupliquer</a></li>
                                            @endif
                                            @if($d->facture && Auth::user()->hasPermission('Gestion des Factures', 'consulter'))
                                                <li><a class="dropdown-item" href="{{ route('factures.show', $d->facture) }}"><i class="bi bi-receipt me-2"></i>Voir la facture</a></li>
                                            @endif
                                            @if(Auth::user()->hasPermission('Gestion des Factures', 'ajouter') && $d->actif && ! $d->facture && in_array($d->statut, ['accepte', 'envoye'], true))
                                                <li>
                                                    <button type="submit" form="devis-convert-form-{{ $d->id }}" class="dropdown-item text-success">
                                                        <i class="bi bi-receipt-cutoff me-2"></i>Transformer en facture
                                                    </button>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission('Gestion des Devis', 'desactiver') && $d->actif)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-warning" data-bs-toggle="modal" data-bs-target="#desactiverModal-{{ $d->id }}"><i class="bi bi-eye-slash me-2"></i>Désactiver</button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showBulkSelection ? 8 : 7 }}" class="text-center text-muted py-5">
                                    <i class="bi bi-file-earmark-x display-4 d-block mb-2"></i>
                                    Aucun devis pour le moment. <a href="{{ route('devis.create') }}">Créer un devis</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Formulaires conversion devis→facture hors du <ul> (évite layout / Popper cassé dans le menu) --}}
            @foreach($devis as $d)
                @if(Auth::user()->hasPermission('Gestion des Factures', 'ajouter') && $d->actif && ! $d->facture && in_array($d->statut, ['accepte', 'envoye'], true))
                    <form id="devis-convert-form-{{ $d->id }}" action="{{ route('factures.convertir-devis', $d) }}" method="post" class="d-none" onsubmit="return confirm('Créer la facture à partir de ce devis ? Elle sera disponible dans Gestion des factures.');">@csrf</form>
                @endif
            @endforeach
            {{-- Modales hors du tableau (évite bugs DOM / overflow) --}}
            @foreach($devis as $d)
                @if(Auth::user()->hasPermission('Gestion des Devis', 'desactiver') && $d->actif)
                    <div class="modal fade" id="desactiverModal-{{ $d->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title">Désactiver le devis</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                </div>
                                <div class="modal-body">
                                    Le devis <strong>{{ $d->numero_devis }}</strong> sera retiré de la liste principale et conservé dans <strong>Devis désactivés</strong>. Vous pourrez le réactiver plus tard.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <form action="{{ route('devis.desactivate', $d) }}" method="post" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">Désactiver</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
            @if($devis->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $devis->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@if($showBulkSelection)
<script>
(function () {
    var btn = document.getElementById('devisToggleSelectMode');
    var toolbar = document.getElementById('devisBulkToolbar');
    var form = document.getElementById('devisBulkForm');
    if (!btn || !toolbar || !form) return;
    if (btn.getAttribute('data-devis-bulk-bound') === '1') return;
    btn.setAttribute('data-devis-bulk-bound', '1');

    var cols = document.querySelectorAll('.devis-bulk-col');
    var checkAll = document.getElementById('devisCheckAll');
    var countEl = document.getElementById('devisBulkCount');

    function rowCheckboxes() {
        return document.querySelectorAll('input.devis-bulk-cb[form="devisBulkForm"]');
    }

    function updateCount() {
        var n = document.querySelectorAll('input.devis-bulk-cb[form="devisBulkForm"]:checked').length;
        countEl.textContent = n + ' sélectionné(s)';
    }

    function bindRowChecks() {
        rowCheckboxes().forEach(function (cb) {
            cb.addEventListener('change', function () {
                updateCount();
                if (checkAll) {
                    var all = rowCheckboxes();
                    var checked = document.querySelectorAll('input.devis-bulk-cb[form="devisBulkForm"]:checked');
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
        var actionSelect = document.getElementById('devisBulkAction');
        var action = actionSelect ? actionSelect.value : '';
        if (!action) {
            e.preventDefault();
            alert('Choisissez une action.');
            return;
        }
        var selected = document.querySelectorAll('input.devis-bulk-cb[form="devisBulkForm"]:checked');
        if (selected.length === 0) {
            e.preventDefault();
            alert('Cochez au moins un devis.');
            return;
        }
        if (action === 'imprimer') {
            form.target = '_blank';
        } else {
            form.target = '_self';
        }
        if (action === 'desactiver' && !confirm('Désactiver ' + selected.length + ' devis sélectionné(s) ?')) {
            e.preventDefault();
            form.target = '_self';
        }
    });
})();
</script>
@endif
@endsection
