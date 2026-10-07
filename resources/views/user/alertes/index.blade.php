@extends('user.master')
@section('content')
<div class="section">
    <div class="d-flex justify-content-between mb-3">
        <h1 class="pagetitle mb-0"><i class="bi bi-bell me-2"></i>Centre d'alertes</h1>
    </div>
    <p class="text-muted small">Cautions actives arrivant à échéance ({{ $joursCaution }} j.), factures proches échéance ({{ $joursFacture }} j.)@if($stockActif), stock sous seuil@endif.</p>

    <h6 class="mt-4">Cautions</h6>
    <div class="card mb-3"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Marché</th><th>Libellé</th><th>Montant</th><th>Fin</th></tr></thead><tbody>
        @forelse($cautions as $c)
            <tr class="table-warning"><td><a href="{{ route('marches.show', $c->marche_id) }}">{{ $c->marche->reference }}</a></td><td>{{ $c->libelle }}</td><td>{{ number_format($c->montant, 2, ',', ' ') }}</td><td>{{ $c->date_fin->format('d/m/Y') }}</td></tr>
        @empty
            <tr><td colspan="4" class="text-muted text-center py-3">Aucune alerte caution.</td></tr>
        @endforelse
    </tbody></table></div></div>

    <h6>Factures</h6>
    <div class="card mb-3"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>N°</th><th>Client</th><th>Échéance</th></tr></thead><tbody>
        @forelse($factures as $f)
            <tr><td><a href="{{ route('factures.show', $f) }}">{{ $f->numero_facture }}</a></td><td>{{ $f->client->nom }}</td><td>{{ $f->date_echeance?->format('d/m/Y') }}</td></tr>
        @empty
            <tr><td colspan="3" class="text-muted text-center py-3">Aucune alerte facture.</td></tr>
        @endforelse
    </tbody></table></div></div>

    @if($stockActif)
    <h6>Stock bas</h6>
    <div class="card"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Article</th><th>Stock</th><th>Seuil</th></tr></thead><tbody>
        @forelse($articlesBas as $a)
            <tr><td><a href="{{ route('stock.articles.edit', $a) }}">{{ $a->reference }}</a> {{ $a->nom }}</td><td>{{ $a->stock_quantity }}</td><td>{{ $a->seuil_alerte }}</td></tr>
        @empty
            <tr><td colspan="3" class="text-muted text-center py-3">Aucun article sous le seuil.</td></tr>
        @endforelse
    </tbody></table></div></div>
    @endif
</div>
@endsection
