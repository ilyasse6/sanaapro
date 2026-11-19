@extends('user.master')
@section('content')
@include('user.credits.partials.styles')
@php
    $dh = fn ($n) => number_format((float) $n, 2, ',', ' ').' DH';
    $peutAjouter = Auth::user()->hasPermission('Gestion des Crédits & Chèques', 'ajouter');
    $peutSupprimer = Auth::user()->hasPermission('Gestion des Crédits & Chèques', 'desactiver');
    $peutModifier = Auth::user()->hasPermission('Gestion des Crédits & Chèques', 'modifier');
    $nomClient = trim($client->nom.' '.$client->prenom);
    $badgeFacture = [
        'emise' => 'text-bg-info', 'envoyee' => 'text-bg-info', 'payee_partielle' => 'text-bg-warning',
        'payee' => 'text-bg-success', 'avoir' => 'text-bg-secondary',
    ];
@endphp
<div class="section">
    <div class="cr-hero d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="small opacity-75 cr-no-print"><a href="{{ route('credits.index') }}" class="text-white text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Crédits clients</a></div>
            <h1 class="mb-1">{{ $nomClient }}</h1>
            <p class="mb-0 opacity-90">
                Relevé de compte au {{ today()->format('d/m/Y') }}
                @if($client->telephone) · <i class="bi bi-telephone"></i> {{ $client->telephone }}@endif
                @if($client->ville) · {{ $client->ville }}@endif
            </p>
        </div>
        <div class="d-flex gap-2 cr-no-print">
            <button type="button" class="btn btn-light" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimer le relevé</button>
        </div>
    </div>

    @include('user.partials.flash')
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="cr-stat {{ $situation['solde'] > 0.005 ? 'danger' : 'success' }}">
                <div class="lbl">{{ $situation['solde'] < -0.005 ? 'Avance du client' : 'Solde dû' }}</div>
                <div class="val">{{ $dh(abs($situation['solde'])) }}</div>
                <div class="sub">
                    @if($situation['credit_initial'] > 0)dont crédit initial {{ $dh($situation['credit_initial']) }}@else{{ $situation['nb_factures_ouvertes'] }} facture(s) ouverte(s)@endif
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="cr-stat">
                <div class="lbl">Total facturé TTC</div>
                <div class="val">{{ $dh($situation['facture'] - $situation['avoirs']) }}</div>
                <div class="sub">@if($situation['avoirs'] > 0)après {{ $dh($situation['avoirs']) }} d’avoirs@else factures émises @endif</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="cr-stat success">
                <div class="lbl">Total réglé</div>
                <div class="val">{{ $dh($situation['regle']) }}</div>
                <div class="sub">{{ $reglements->count() }} règlement(s)</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="cr-stat {{ $situation['montant_echu'] > 0 ? 'warning' : 'info' }}">
                <div class="lbl">{{ $situation['montant_echu'] > 0 ? 'Échu (en retard)' : 'Chèques non encaissés' }}</div>
                <div class="val">{{ $dh($situation['montant_echu'] > 0 ? $situation['montant_echu'] : $situation['cheques_en_cours']) }}</div>
                <div class="sub">
                    @if($situation['montant_echu'] > 0)
                        depuis le {{ $situation['plus_ancienne_echeance']->format('d/m/Y') }}
                    @else
                        {{ $situation['nb_cheques_en_cours'] }} chèque(s) en attente
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="cr-card mb-4">
                <div class="card-body pb-0"><div class="cr-section-title mb-3"><i class="bi bi-receipt me-1"></i>Factures</div></div>
                <div class="table-responsive">
                    <table class="table cr-table mb-0">
                        <thead><tr><th>N°</th><th>Date</th><th>Échéance</th><th class="text-end">TTC</th><th class="text-end">Réglé</th><th class="text-end">Reste</th><th>Statut</th><th class="cr-no-print"></th></tr></thead>
                        <tbody>
                            @forelse($factures as $f)
                                @php $fa = $f['facture']; @endphp
                                <tr>
                                    <td><a href="{{ route('factures.show', $fa) }}" class="text-decoration-none">{{ $fa->numero_facture }}</a></td>
                                    <td>{{ $fa->date_emission->format('d/m/Y') }}</td>
                                    <td class="{{ $f['en_retard'] ? 'text-danger fw-semibold' : '' }}">{{ $fa->date_echeance?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="cr-montant">{{ $fa->statut === 'avoir' ? '− ' : '' }}{{ $dh($f['ttc']) }}</td>
                                    <td class="cr-montant">{{ $f['regle'] > 0 ? $dh($f['regle']) : '—' }}</td>
                                    <td class="cr-montant {{ $f['reste'] > 0 ? 'cr-solde-pos' : '' }}">{{ $f['reste'] > 0 ? $dh($f['reste']) : '—' }}</td>
                                    <td><span class="badge {{ $badgeFacture[$fa->statut] ?? 'text-bg-secondary' }}">{{ $fa->statut_label }}</span></td>
                                    <td class="text-end cr-no-print">
                                        @if($f['reste'] > 0 && $peutAjouter)
                                            <button type="button" class="btn btn-sm btn-outline-success js-regler"
                                                data-facture="{{ $fa->id }}" data-reste="{{ number_format($f['reste'], 2, '.', '') }}">Régler</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-4">Aucune facture émise pour ce client.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="cr-card mb-4">
                <div class="card-body pb-0"><div class="cr-section-title mb-3"><i class="bi bi-cash-coin me-1"></i>Règlements reçus</div></div>
                <div class="table-responsive">
                    <table class="table cr-table mb-0">
                        <thead><tr><th>Date</th><th>Mode</th><th>Référence</th><th>Facture</th><th class="text-end">Montant</th><th class="cr-no-print"></th></tr></thead>
                        <tbody>
                            @forelse($reglements as $r)
                                <tr>
                                    <td>{{ $r->date_operation->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $r->mode_paiement ?: '—' }}
                                        @if($r->cheque)<span class="badge {{ $r->cheque->statut_badge }} ms-1">{{ $r->cheque->statut_label }}</span>@endif
                                    </td>
                                    <td class="small">
                                        {{ $r->reference_externe ?: '—' }}
                                        @if($r->cheque?->banque)<div class="text-muted">{{ $r->cheque->banque }}</div>@endif
                                    </td>
                                    <td>@if($r->facture)<a href="{{ route('factures.show', $r->facture) }}" class="text-decoration-none">{{ $r->facture->numero_facture }}</a>@else<span class="text-muted small">Acompte</span>@endif</td>
                                    <td class="cr-montant fw-semibold">{{ $dh($r->montant) }}</td>
                                    <td class="text-end cr-no-print">
                                        @if($peutSupprimer)
                                            <form method="post" action="{{ route('credits.reglements.destroy', $r) }}" onsubmit="return confirm('Supprimer ce règlement ? Le montant redeviendra dû.{{ $r->cheque ? ' Le chèque associé sera aussi supprimé.' : '' }}');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-link text-danger p-0" title="Supprimer"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">Aucun règlement enregistré.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($cheques->isNotEmpty())
                <div class="cr-card mb-4">
                    <div class="card-body pb-0"><div class="cr-section-title mb-3"><i class="bi bi-credit-card-2-front me-1"></i>Chèques du client</div></div>
                    <div class="table-responsive">
                        <table class="table cr-table mb-0">
                            <thead><tr><th>N°</th><th>Banque</th><th>Reçu le</th><th>Échéance</th><th class="text-end">Montant</th><th>Statut</th><th class="cr-no-print"></th></tr></thead>
                            <tbody>
                                @foreach($cheques as $chq)
                                    <tr>
                                        <td class="fw-semibold">{{ $chq->numero }}</td>
                                        <td>{{ $chq->banque ?: '—' }}</td>
                                        <td>{{ $chq->date_reception->format('d/m/Y') }}</td>
                                        <td class="{{ $chq->a_deposer ? 'text-danger fw-semibold' : '' }}">{{ $chq->date_echeance?->format('d/m/Y') ?? '—' }}</td>
                                        <td class="cr-montant">{{ $dh($chq->montant) }}</td>
                                        <td>
                                            <span class="badge {{ $chq->statut_badge }}">{{ $chq->statut_label }}</span>
                                            @if($chq->statut === 'impaye' && $chq->motif_impaye)<div class="small text-danger">{{ $chq->motif_impaye }}</div>@endif
                                        </td>
                                        <td class="text-end cr-no-print">@include('user.cheques.partials.actions', ['cheque' => $chq])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4 cr-no-print">
            @if($peutAjouter)
                <div class="cr-card mb-4" id="nouveau-reglement">
                    <div class="card-body">
                        <div class="cr-section-title mb-3"><i class="bi bi-plus-circle me-1"></i>Nouveau règlement</div>
                        <form method="post" action="{{ route('credits.reglements.store', $client) }}" id="form-reglement">
                            @csrf
                            <div class="row g-2">
                                <div class="col-7">
                                    <label class="form-label small">Montant (DH)</label>
                                    <input type="number" step="0.01" min="0.01" name="montant" class="form-control" value="{{ old('montant') }}" required>
                                </div>
                                <div class="col-5">
                                    <label class="form-label small">Date</label>
                                    <input type="date" name="date_reglement" class="form-control" value="{{ old('date_reglement', today()->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Mode de paiement</label>
                                    <select name="mode" class="form-select" id="reglement-mode">
                                        @foreach(\App\Support\CreditsClients::MODES as $cle => $lib)
                                            <option value="{{ $cle }}" @selected(old('mode', 'especes') === $cle)>{{ $lib }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Facture réglée</label>
                                    <select name="facture_id" class="form-select" id="reglement-facture">
                                        <option value="">Acompte (non affecté à une facture)</option>
                                        @foreach($facturesOuvertes as $f)
                                            <option value="{{ $f['facture']->id }}" data-reste="{{ number_format($f['reste'], 2, '.', '') }}" @selected((string) old('facture_id') === (string) $f['facture']->id)>
                                                {{ $f['facture']->numero_facture }} — reste {{ $dh($f['reste']) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12" id="reglement-reference">
                                    <label class="form-label small">Référence (virement, effet…)</label>
                                    <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
                                </div>
                                <div class="col-12" id="reglement-cheque">
                                    <div class="cr-cheque-fields row g-2 mx-0">
                                        <div class="col-6">
                                            <label class="form-label small">N° du chèque</label>
                                            <input type="text" name="cheque_numero" class="form-control form-control-sm" value="{{ old('cheque_numero') }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">Banque</label>
                                            <input type="text" name="cheque_banque" class="form-control form-control-sm" value="{{ old('cheque_banque') }}" placeholder="Attijariwafa, BMCE…">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">Tireur</label>
                                            <input type="text" name="cheque_tireur" class="form-control form-control-sm" value="{{ old('cheque_tireur') }}" placeholder="{{ $nomClient }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">Date d’encaissement</label>
                                            <input type="date" name="cheque_echeance" class="form-control form-control-sm" value="{{ old('cheque_echeance') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Notes</label>
                                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}">
                                </div>
                                <div class="col-12 d-grid mt-2">
                                    <button class="btn btn-success"><i class="bi bi-check2-circle me-1"></i>Enregistrer le règlement</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @if($peutModifier)
                <div class="cr-card mb-4">
                    <div class="card-body">
                        <div class="cr-section-title mb-1"><i class="bi bi-journal-text me-1"></i>Crédit initial</div>
                        <p class="small text-muted mb-2">Dette du client antérieure au logiciel (ancien carnet, reprise de solde).</p>
                        <form method="post" action="{{ route('credits.credit-initial', $client) }}" class="d-flex gap-2">
                            @csrf @method('PUT')
                            <input type="number" step="0.01" min="0" name="credit_initial" class="form-control" value="{{ number_format((float) $client->credit_initial, 2, '.', '') }}">
                            <button class="btn btn-outline-primary">OK</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@include('user.cheques.partials.action-modal')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-reglement');
    if (!form) return;
    const mode = document.getElementById('reglement-mode');
    const facture = document.getElementById('reglement-facture');
    const montant = form.elements.montant;

    function majMode() {
        const cheque = mode.value === 'cheque';
        document.getElementById('reglement-cheque').style.display = cheque ? '' : 'none';
        document.getElementById('reglement-reference').style.display = cheque || mode.value === 'especes' ? 'none' : '';
        form.elements.cheque_numero.required = cheque;
    }
    mode.addEventListener('change', majMode);
    majMode();

    facture.addEventListener('change', function () {
        const reste = facture.selectedOptions[0]?.dataset.reste;
        if (reste && !montant.value) montant.value = reste;
    });

    document.querySelectorAll('.js-regler').forEach(function (b) {
        b.addEventListener('click', function () {
            facture.value = b.dataset.facture;
            montant.value = b.dataset.reste;
            document.getElementById('nouveau-reglement').scrollIntoView({ behavior: 'smooth', block: 'start' });
            montant.focus();
        });
    });
});
</script>
@endsection
