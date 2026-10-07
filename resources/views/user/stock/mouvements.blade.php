@extends('user.master')
@section('content')
<div class="section">
    <div class="d-flex justify-content-between mb-3"><h1 class="pagetitle mb-0">Mouvements de stock</h1><a href="{{ route('stock.articles') }}" class="btn btn-outline-secondary btn-sm">Articles</a></div>
    @include('user.partials.flash')
    @if(Auth::user()->hasPermission('Gestion du Stock', 'ajouter') && $articles->count())
    <div class="card mb-3"><div class="card-body"><h6>Nouveau mouvement</h6>
        <form method="post" action="{{ route('stock.mouvements.store') }}" class="row g-2 align-items-end">@csrf
            <div class="col-md-4"><label class="form-label">Article</label><select name="article_id" class="form-select" required>@foreach($articles as $a)<option value="{{ $a->id }}">{{ $a->reference }} — {{ $a->nom }} ({{ $a->stock_quantity }})</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Type</label><select name="type" class="form-select"><option value="entree">Entrée</option><option value="sortie">Sortie</option><option value="ajustement">Ajustement</option></select></div>
            <div class="col-md-2"><label class="form-label">Quantité</label><input type="number" step="0.001" name="quantite" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Motif</label><input name="motif" class="form-control"></div>
            <div class="col-md-1"><button class="btn btn-primary w-100">OK</button></div>
        </form>
    </div></div>
    @elseif(Auth::user()->hasPermission('Gestion du Stock', 'ajouter'))
        <div class="alert alert-info">Créez d’abord un <a href="{{ route('stock.articles.create') }}">article</a> pour saisir des mouvements.</div>
    @endif
    <div class="card"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Article</th><th>Type</th><th>Qté</th><th>Utilisateur</th></tr></thead><tbody>
        @foreach($mouvements as $m)
            <tr><td>{{ $m->created_at->format('d/m/Y H:i') }}</td><td>{{ $m->article->nom }}</td><td>{{ $m->type }}</td><td>{{ $m->quantite }}</td><td>{{ $m->user?->nom ?? '—' }}</td></tr>
        @endforeach
    </tbody></table></div>{{ $mouvements->links() }}</div>
</div>
@endsection
