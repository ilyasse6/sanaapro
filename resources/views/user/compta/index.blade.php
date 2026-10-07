@extends('user.master')
@section('content')
<div class="section">
    <h1 class="pagetitle">Comptabilité — tableau de bord</h1>
    <p class="text-muted">Synthèse du mois en cours (écritures saisies dans « Mouvements »).</p>
    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card border-success"><div class="card-body"><h6 class="text-muted">Entrées / encaissements</h6><h3 class="text-success mb-0">{{ number_format($entrees, 2, ',', ' ') }} MAD</h3></div></div></div>
        <div class="col-md-4"><div class="card border-danger"><div class="card-body"><h6 class="text-muted">Sorties / dépenses</h6><h3 class="text-danger mb-0">{{ number_format($sorties, 2, ',', ' ') }} MAD</h3></div></div></div>
        <div class="col-md-4"><div class="card border-primary"><div class="card-body"><h6 class="text-muted">Solde indicatif</h6><h3 class="text-primary mb-0">{{ number_format($entrees - $sorties, 2, ',', ' ') }} MAD</h3></div></div></div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-2"><h5 class="mb-0">Factures à surveiller</h5><a href="{{ route('compta.mouvements') }}" class="btn btn-sm btn-primary">Mouvements comptables</a></div>
    <div class="card"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Facture</th><th>Client</th><th>Échéance</th><th>TTC</th><th>Statut</th></tr></thead><tbody>
        @forelse($impayees as $f)
            <tr><td><a href="{{ route('factures.show', $f) }}">{{ $f->numero_facture }}</a></td><td>{{ $f->client->nom }}</td><td>{{ $f->date_echeance?->format('d/m/Y') ?? '—' }}</td><td>{{ number_format($f->montant_ttc, 2, ',', ' ') }}</td><td>{{ $f->statut_label }}</td></tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-3">Aucune facture en attente listée.</td></tr>
        @endforelse
    </tbody></table></div></div>
</div>
@endsection
