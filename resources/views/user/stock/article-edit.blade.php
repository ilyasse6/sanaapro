@extends('user.master')
@section('content')
<div class="section" style="max-width:700px"><h1 class="pagetitle">Article {{ $article->reference }}</h1>
<div class="card card-body"><form method="post" action="{{ route('stock.articles.update', $article) }}">@csrf @method('PUT')
    <div class="row g-2 mb-2"><div class="col-md-4"><label class="form-label">Référence *</label><input name="reference" class="form-control" required value="{{ old('reference', $article->reference) }}"></div><div class="col-md-8"><label class="form-label">Nom *</label><input name="nom" class="form-control" required value="{{ old('nom', $article->nom) }}"></div></div>
    <div class="mb-2"><label class="form-label">Description</label><textarea name="description" class="form-control">{{ old('description', $article->description) }}</textarea></div>
    <p class="small text-muted">Le stock se modifie via les <a href="{{ route('stock.mouvements') }}">mouvements</a> (actuel : <strong>{{ $article->stock_quantity }}</strong>).</p>
    <div class="row g-2 mb-2"><div class="col"><label class="form-label">Unité</label><input name="unite" class="form-control" value="{{ old('unite', $article->unite) }}"></div><div class="col"><label class="form-label">Seuil alerte</label><input type="number" step="0.001" name="seuil_alerte" class="form-control" value="{{ old('seuil_alerte', $article->seuil_alerte) }}"></div></div>
    <div class="row g-2 mb-2"><div class="col"><label class="form-label">PA HT</label><input type="number" step="0.01" name="prix_achat_ht" class="form-control" value="{{ old('prix_achat_ht', $article->prix_achat_ht) }}"></div><div class="col"><label class="form-label">PV HT</label><input type="number" step="0.01" name="prix_vente_ht" class="form-control" value="{{ old('prix_vente_ht', $article->prix_vente_ht) }}"></div></div>
    <div class="row g-2 mb-3"><div class="col"><label class="form-label">Fournisseur</label><select name="fournisseur_id" class="form-select"><option value="">—</option>@foreach($fournisseurs as $f)<option value="{{ $f->id }}" @selected(old('fournisseur_id', $article->fournisseur_id)==$f->id)>{{ $f->raison_sociale }}</option>@endforeach</select></div><div class="col"><label class="form-label">Catalogue</label><select name="catalogue_service_id" class="form-select"><option value="">—</option>@foreach($services as $s)<option value="{{ $s->id }}" @selected(old('catalogue_service_id', $article->catalogue_service_id)==$s->id)>{{ $s->nom }}</option>@endforeach</select></div></div>
    <button class="btn btn-primary">Enregistrer</button><a href="{{ route('stock.articles') }}" class="btn btn-outline-secondary">Retour</a>
</form>
@if(Auth::user()->hasPermission('Gestion du Stock', 'desactiver'))<hr><form method="post" action="{{ route('stock.articles.desactivate', $article) }}" onsubmit="return confirm('Désactiver cet article ?');">@csrf<button class="btn btn-outline-danger btn-sm">Désactiver</button></form>@endif
</div></div>
@endsection
