@extends('user.master')
@section('content')
<div class="section" style="max-width:640px">
    <h1 class="pagetitle">Modifier fournisseur</h1>
    <div class="card card-body"><form method="post" action="{{ route('fournisseurs.update', $fournisseur) }}">@csrf @method('PUT')
        <div class="mb-2"><label class="form-label">Raison sociale *</label><input name="raison_sociale" class="form-control" value="{{ old('raison_sociale', $fournisseur->raison_sociale) }}" required></div>
        <div class="mb-2"><label class="form-label">ICE</label><input name="ice" class="form-control" value="{{ old('ice', $fournisseur->ice) }}"></div>
        <div class="row g-2 mb-2"><div class="col"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $fournisseur->email) }}"></div><div class="col"><label class="form-label">Tél.</label><input name="telephone" class="form-control" value="{{ old('telephone', $fournisseur->telephone) }}"></div></div>
        <div class="mb-2"><label class="form-label">Adresse</label><input name="adresse" class="form-control" value="{{ old('adresse', $fournisseur->adresse) }}"></div>
        <div class="row g-2 mb-2"><div class="col"><label class="form-label">Ville</label><input name="ville" class="form-control" value="{{ old('ville', $fournisseur->ville) }}"></div><div class="col"><label class="form-label">CP</label><input name="code_postal" class="form-control" value="{{ old('code_postal', $fournisseur->code_postal) }}"></div></div>
        <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control">{{ old('notes', $fournisseur->notes) }}</textarea></div>
        <button class="btn btn-primary">Mettre à jour</button><a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">Retour</a>
    </form>
    @if(Auth::user()->hasPermission('Gestion des Fournisseurs', 'desactiver'))
        <hr><form method="post" action="{{ route('fournisseurs.desactivate', $fournisseur) }}" onsubmit="return confirm('Désactiver ?');">@csrf<button class="btn btn-outline-danger btn-sm">Désactiver</button></form>
    @endif
    </div>
</div>
@endsection
