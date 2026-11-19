@extends('user.master')
@section('content')
@include('user.credits.partials.styles')
@php
    $dh = fn ($n) => number_format((float) $n, 2, ',', ' ').' DH';
    $onglets = [
        'en_cours' => 'En cours',
        'a_deposer' => 'À déposer',
        'en_portefeuille' => 'En portefeuille',
        'remis' => 'Remis en banque',
        'encaisse' => 'Encaissés',
        'impaye' => 'Impayés',
        'restitue' => 'Restitués',
        'tous' => 'Tous',
    ];
    $filtresActifs = request()->only(['client_id', 'q', 'du', 'au']);
@endphp
<div class="section">
    <div class="cr-hero d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="mb-1"><i class="bi bi-credit-card-2-front me-2"></i>Suivi des chèques</h1>
            <p class="mb-0 opacity-90">Chèques reçus des clients : portefeuille, remise en banque, encaissement et impayés.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('credits.index') }}" class="btn btn-outline-light"><i class="bi bi-wallet2 me-1"></i>Crédits clients</a>
            @if(Auth::user()->hasPermission('Gestion des Crédits & Chèques', 'ajouter'))
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#nouveauChequeModal"><i class="bi bi-plus-lg me-1"></i>Nouveau chèque</button>
            @endif
        </div>
    </div>

    @include('user.partials.flash')
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-2">
            <div class="cr-stat">
                <div class="lbl">En portefeuille</div>
                <div class="val">{{ $dh($stats['portefeuille']->total ?? 0) }}</div>
                <div class="sub">{{ $stats['portefeuille']->nb ?? 0 }} chèque(s)</div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <a href="{{ route('cheques.index', ['statut' => 'a_deposer']) }}" class="text-decoration-none">
                <div class="cr-stat {{ $stats['a_deposer_nb'] ? 'danger' : '' }}">
                    <div class="lbl">À déposer</div>
                    <div class="val">{{ $dh($stats['a_deposer_total']) }}</div>
                    <div class="sub">{{ $stats['a_deposer_nb'] }} échéance(s) atteinte(s)</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-2">
            <div class="cr-stat warning">
                <div class="lbl">Sous 7 jours</div>
                <div class="val">{{ $dh($stats['semaine_total']) }}</div>
                <div class="sub">{{ $stats['semaine_nb'] }} chèque(s)</div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="cr-stat info">
                <div class="lbl">Remis en banque</div>
                <div class="val">{{ $dh($stats['remis']->total ?? 0) }}</div>
                <div class="sub">{{ $stats['remis']->nb ?? 0 }} en attente d’encaissement</div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="cr-stat success">
                <div class="lbl">Encaissés ce mois</div>
                <div class="val">{{ $dh($stats['encaisse_mois']) }}</div>
                <div class="sub">{{ ucfirst(now()->locale('fr')->translatedFormat('F Y')) }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <a href="{{ route('cheques.index', ['statut' => 'impaye']) }}" class="text-decoration-none">
                <div class="cr-stat danger">
                    <div class="lbl">Impayés</div>
                    <div class="val">{{ $dh($stats['impaye']->total ?? 0) }}</div>
                    <div class="sub">{{ $stats['impaye']->nb ?? 0 }} chèque(s) rejeté(s)</div>
                </div>
            </a>
        </div>
    </div>

    <div class="cr-card mb-4">
        <div class="card-body">
            <div class="cr-filtres d-flex flex-wrap gap-2 mb-3">
                @foreach($onglets as $cle => $lib)
                    <a href="{{ route('cheques.index', ['statut' => $cle] + $filtresActifs) }}"
                       class="btn btn-sm {{ $statut === $cle ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $lib }}</a>
                @endforeach
            </div>
            <form method="get" class="row g-2 align-items-end">
                <input type="hidden" name="statut" value="{{ $statut }}">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Client</label>
                    <select name="client_id" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected((string) request('client_id') === (string) $c->id)>{{ trim($c->nom.' '.$c->prenom) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">N°, banque ou tireur</label>
                    <input type="search" name="q" value="{{ $recherche }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Échéance du</label>
                    <input type="date" name="du" value="{{ request('du') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">au</label>
                    <input type="date" name="au" value="{{ request('au') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-sm btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrer</button>
                    <a href="{{ route('cheques.index', ['statut' => $statut]) }}" class="btn btn-sm btn-outline-secondary" title="Réinitialiser"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="cr-card">
        <div class="table-responsive">
            <table class="table cr-table mb-0">
                <thead>
                    <tr>
                        <th>Échéance</th>
                        <th>N° chèque</th>
                        <th>Banque</th>
                        <th>Client / tireur</th>
                        <th>Facture</th>
                        <th class="text-end">Montant</th>
                        <th>Statut</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cheques as $chq)
                        @php
                            $jours = $chq->date_echeance ? (int) today()->diffInDays($chq->date_echeance, false) : null;
                        @endphp
                        <tr>
                            <td>
                                {{ $chq->date_echeance?->format('d/m/Y') ?? '—' }}
                                @if($chq->statut === 'en_portefeuille' && $jours !== null)
                                    <div>
                                        @if($jours < 0)
                                            <span class="badge text-bg-danger">Échu depuis {{ abs($jours) }} j</span>
                                        @elseif($jours === 0)
                                            <span class="badge text-bg-danger">Aujourd’hui</span>
                                        @elseif($jours <= 7)
                                            <span class="badge text-bg-warning">Dans {{ $jours }} j</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $chq->numero }}</td>
                            <td>{{ $chq->banque ?: '—' }}</td>
                            <td>
                                <a href="{{ route('credits.show', $chq->client) }}" class="text-decoration-none">{{ trim($chq->client->nom.' '.$chq->client->prenom) }}</a>
                                @if($chq->tireur && $chq->tireur !== trim($chq->client->nom.' '.$chq->client->prenom))
                                    <div class="small text-muted">Tireur : {{ $chq->tireur }}</div>
                                @endif
                            </td>
                            <td>
                                @if($chq->facture)
                                    <a href="{{ route('factures.show', $chq->facture) }}" class="text-decoration-none">{{ $chq->facture->numero_facture }}</a>
                                @else
                                    <span class="text-muted small">Acompte</span>
                                @endif
                            </td>
                            <td class="cr-montant fw-semibold">{{ $dh($chq->montant) }}</td>
                            <td>
                                <span class="badge {{ $chq->statut_badge }}">{{ $chq->statut_label }}</span>
                                <div class="small text-muted">
                                    @switch($chq->statut)
                                        @case('en_portefeuille') reçu le {{ $chq->date_reception->format('d/m/Y') }} @break
                                        @case('remis') remis le {{ $chq->date_remise?->format('d/m/Y') }} @break
                                        @case('encaisse') le {{ $chq->date_encaissement?->format('d/m/Y') }} @break
                                        @case('impaye') le {{ $chq->date_impaye?->format('d/m/Y') }}@if($chq->motif_impaye) — {{ $chq->motif_impaye }}@endif @break
                                    @endswitch
                                </div>
                            </td>
                            <td class="text-end">@include('user.cheques.partials.actions', ['cheque' => $chq])</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>Aucun chèque pour ce filtre.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cheques->hasPages())
            <div class="p-3">{{ $cheques->links() }}</div>
        @endif
    </div>
</div>

@include('user.cheques.partials.action-modal')

@if(Auth::user()->hasPermission('Gestion des Crédits & Chèques', 'ajouter'))
<div class="modal fade" id="nouveauChequeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" action="{{ route('cheques.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-credit-card-2-front me-2"></i>Nouveau chèque reçu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Client <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-select" id="chq-client" required>
                            <option value="">— Choisir —</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" @selected((string) old('client_id') === (string) $c->id)>{{ trim($c->nom.' '.$c->prenom) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Facture réglée</label>
                        <select name="facture_id" class="form-select" id="chq-facture">
                            <option value="">Acompte (non affecté)</option>
                            @foreach($facturesOuvertes as $f)
                                <option value="{{ $f->id }}" data-client="{{ $f->client_id }}">{{ $f->numero_facture }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">N° du chèque <span class="text-danger">*</span></label>
                        <input type="text" name="cheque_numero" class="form-control" value="{{ old('cheque_numero') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Banque</label>
                        <input type="text" name="cheque_banque" class="form-control" value="{{ old('cheque_banque') }}" placeholder="Attijariwafa, BMCE, CIH…">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Montant (DH) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="montant" class="form-control" value="{{ old('montant') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tireur</label>
                        <input type="text" name="cheque_tireur" class="form-control" value="{{ old('cheque_tireur') }}" placeholder="Par défaut : le client">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Reçu le <span class="text-danger">*</span></label>
                        <input type="date" name="date_reglement" class="form-control" value="{{ old('date_reglement', today()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date d’encaissement</label>
                        <input type="date" name="cheque_echeance" class="form-control" value="{{ old('cheque_echeance') }}">
                        <div class="form-text">Laisser vide si encaissable immédiatement.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control" value="{{ old('notes') }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button class="btn btn-success"><i class="bi bi-check2-circle me-1"></i>Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const client = document.getElementById('chq-client');
    const facture = document.getElementById('chq-facture');
    function filtrer() {
        facture.querySelectorAll('option[data-client]').forEach(function (o) {
            o.hidden = o.dataset.client !== client.value;
        });
        if (facture.selectedOptions[0]?.hidden) facture.value = '';
    }
    client.addEventListener('change', filtrer);
    filtrer();
    @if($errors->any() && old('cheque_numero') !== null)
        bootstrap.Modal.getOrCreateInstance(document.getElementById('nouveauChequeModal')).show();
    @endif
});
</script>
@endif
@endsection
