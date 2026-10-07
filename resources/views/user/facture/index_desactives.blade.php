@extends('user.master')

@section('content')
<style>
    :root {
        --gradient-facture-muted: linear-gradient(135deg, #64748b 0%, #475569 50%, #94a3b8 100%);
        --card-shadow: 0 10px 40px rgba(30, 64, 175, 0.08);
    }
    .facture-hero-muted {
        background: var(--gradient-facture-muted);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .facture-hero-muted::before {
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
        border: none;
    }
    .table-facture thead {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
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
    tr.table-facture-muted { --bs-table-bg: #f8fafc; }
    .btn-facture { border-radius: 12px; padding: 0.6rem 1.2rem; font-weight: 600; }
    .btn-facture-primary { background: linear-gradient(135deg, #0f2438, #0f172a); color: white; border: none; }
    .modern-input { border-radius: 8px; border: 1px solid #e2e8f0; }
    .facture-bulk-toolbar { background: #f1f5f9; border-radius: 10px; border: 1px solid #e2e8f0 !important; }
</style>

@php
    $canBulkAct = Auth::user()->hasPermission('Gestion des Factures', 'activer');
    $canBulkModif = Auth::user()->hasPermission('Gestion des Factures', 'modifier');
    $showBulkSelection = ($canBulkAct || $canBulkModif) && $factures->count() > 0;
@endphp

<div class="section">
    <div class="facture-hero-muted">
        <div class="facture-hero-content d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="mb-1"><i class="bi bi-archive me-2"></i>Factures désactivées</h1>
                <p class="mb-0 opacity-90">Archives : réactivation, changement de statut ou impression groupée.</p>
            </div>
            <a href="{{ route('factures.index') }}" class="btn btn-light btn-facture">
                <i class="bi bi-arrow-left me-2"></i>Liste des factures actives
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card-facture card mb-4">
        <div class="card-body">
            <h6 class="card-title mb-3"><i class="bi bi-funnel me-2"></i>Filtres</h6>
            <form method="get" action="{{ route('factures.index_desactives') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">N° facture</label>
                    <input type="text" name="numero" class="form-control modern-input form-control-sm" value="{{ request('numero') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Client</label>
                    <select name="client_id" class="form-select modern-input form-select-sm">
                        <option value="">Tous</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected(request('client_id')==$c->id)>{{ $c->nom }}</option>
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
                    <a href="{{ route('factures.index_desactives') }}" class="btn btn-outline-secondary btn-sm">Réinit.</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card-facture card">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="card-title mb-0"><i class="bi bi-list-ul me-2"></i>Archives</h5>
                @if($showBulkSelection)
                    <button type="button" id="factureDesactToggleSelectMode" class="btn btn-outline-primary btn-sm btn-facture">
                        <i class="bi bi-check2-square me-1"></i>Sélectionner
                    </button>
                @endif
            </div>

            @if($showBulkSelection)
            <form id="factureDesactBulkForm" method="post" action="{{ route('factures.bulk') }}">
                @csrf
                <input type="hidden" name="scope" value="inactifs">
                <div id="factureDesactBulkToolbar" class="facture-bulk-toolbar d-none mb-3 py-3 px-3">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span class="small text-muted" id="factureDesactBulkCount">0 sélectionné(s)</span>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="factureDesactCheckAll" aria-label="Tout sélectionner sur cette page">
                            <label class="form-check-label small" for="factureDesactCheckAll">Tout sur cette page</label>
                        </div>
                        <select name="bulk_action" id="factureDesactBulkAction" class="form-select form-select-sm" style="min-width: 12rem">
                            <option value="">— Choisir une action —</option>
                            @if($canBulkAct)
                                <option value="activer">Réactiver</option>
                            @endif
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
                                <th class="facture-desact-bulk-col d-none text-center" style="width:2.75rem;"><span class="visually-hidden">Sélection</span></th>
                            @endif
                            <th>N°</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th class="text-end">TTC</th>
                            <th>Statut</th>
                            <th class="small">Maj.</th>
                            <th class="text-center table-actions-col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($factures as $f)
                            <tr class="table-facture-muted">
                                @if($showBulkSelection)
                                    <td class="facture-desact-bulk-col d-none text-center">
                                        <input type="checkbox" form="factureDesactBulkForm" name="ids[]" value="{{ $f->id }}" class="form-check-input facture-desact-bulk-cb" aria-label="Sélectionner {{ $f->numero_facture }}">
                                    </td>
                                @endif
                                <td><strong>{{ $f->numero_facture }}</strong> <span class="badge bg-secondary">Inactif</span></td>
                                <td>{{ $f->client->nom }}</td>
                                <td>{{ $f->date_emission->format('d/m/Y') }}</td>
                                <td class="text-end">{{ number_format($f->montant_ttc, 2, ',', ' ') }} MAD</td>
                                <td><span class="badge badge-{{ $f->statut }}">{{ $f->statut_label }}</span></td>
                                <td class="small text-muted">{{ $f->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center table-actions-cell">
                                    <div class="dropdown d-inline-block">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li><a class="dropdown-item" href="{{ route('factures.show', $f) }}"><i class="bi bi-eye me-2"></i>Voir</a></li>
                                            <li><a class="dropdown-item" href="{{ route('factures.show', $f) }}?print=1" target="_blank" rel="noopener"><i class="bi bi-printer me-2"></i>Imprimer</a></li>
                                            <li><a class="dropdown-item" href="{{ route('factures.pdf', $f) }}" target="_blank" rel="noopener"><i class="bi bi-file-pdf me-2"></i>PDF</a></li>
                                            @if(Auth::user()->hasPermission('Gestion des Factures', 'activer'))
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-success" data-bs-toggle="modal" data-bs-target="#reactiverFactureModal-{{ $f->id }}"><i class="bi bi-arrow-counterclockwise me-2"></i>Réactiver</button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showBulkSelection ? 8 : 7 }}" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    Aucune facture désactivée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @foreach($factures as $f)
                @if(Auth::user()->hasPermission('Gestion des Factures', 'activer'))
                    <div class="modal fade" id="reactiverFactureModal-{{ $f->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">Réactiver la facture</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                </div>
                                <div class="modal-body">
                                    La facture <strong>{{ $f->numero_facture }}</strong> sera de nouveau visible dans la liste principale.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <form action="{{ route('factures.activate', $f->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Réactiver</button>
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
    var btn = document.getElementById('factureDesactToggleSelectMode');
    var toolbar = document.getElementById('factureDesactBulkToolbar');
    var form = document.getElementById('factureDesactBulkForm');
    if (!btn || !toolbar || !form) return;
    if (btn.getAttribute('data-facture-bulk-bound') === '1') return;
    btn.setAttribute('data-facture-bulk-bound', '1');

    var cols = document.querySelectorAll('.facture-desact-bulk-col');
    var checkAll = document.getElementById('factureDesactCheckAll');
    var countEl = document.getElementById('factureDesactBulkCount');

    function rowCheckboxes() {
        return document.querySelectorAll('input.facture-desact-bulk-cb[form="factureDesactBulkForm"]');
    }

    function updateCount() {
        var n = document.querySelectorAll('input.facture-desact-bulk-cb[form="factureDesactBulkForm"]:checked').length;
        countEl.textContent = n + ' sélectionné(s)';
    }

    function bindRowChecks() {
        rowCheckboxes().forEach(function (cb) {
            cb.addEventListener('change', function () {
                updateCount();
                if (checkAll) {
                    var all = rowCheckboxes();
                    var checked = document.querySelectorAll('input.facture-desact-bulk-cb[form="factureDesactBulkForm"]:checked');
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
        var actionSelect = document.getElementById('factureDesactBulkAction');
        var action = actionSelect ? actionSelect.value : '';
        if (!action) {
            e.preventDefault();
            alert('Choisissez une action.');
            return;
        }
        var selected = document.querySelectorAll('input.facture-desact-bulk-cb[form="factureDesactBulkForm"]:checked');
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
