@extends('user.master')
@section('content')
<div class="section">
    <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
        <h1 class="pagetitle mb-0">Articles & stock</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('stock.mouvements') }}" class="btn btn-outline-secondary btn-sm">Mouvements</a>
            @if(Auth::user()->hasPermission('Gestion du Stock', 'ajouter'))<a href="{{ route('stock.articles.create') }}" class="btn btn-primary btn-sm">Nouvel article</a>@endif
        </div>
    </div>
    @include('user.partials.flash')
    <div class="card"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Réf.</th><th>Nom</th><th>Stock</th><th>Seuil</th><th>Fournisseur</th><th>Catalogue lié</th><th></th></tr></thead><tbody>
        @foreach($articles as $a)
            <tr class="{{ $a->stock_bas ? 'table-warning' : '' }}">
                <td>{{ $a->reference }}</td><td>{{ $a->nom }}</td><td>{{ $a->stock_quantity }}</td><td>{{ $a->seuil_alerte }}</td>
                <td>{{ $a->fournisseur?->raison_sociale ?? '—' }}</td><td>{{ $a->catalogueService?->nom ?? '—' }}</td>
                <td class="text-end">@if(Auth::user()->hasPermission('Gestion du Stock', 'modifier'))<a href="{{ route('stock.articles.edit', $a) }}" class="btn btn-sm btn-outline-primary">Éditer</a>@endif</td>
            </tr>
        @endforeach
    </tbody></table></div>{{ $articles->links() }}</div>
</div>
@endsection
