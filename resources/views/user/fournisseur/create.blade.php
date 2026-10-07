@extends('user.master')
@section('content')
<div class="section" style="max-width:640px">
    <h1 class="pagetitle">Nouveau fournisseur</h1>
    <div class="card card-body"><form method="post" action="{{ route('fournisseurs.store') }}">@csrf
        <div class="mb-2"><label class="form-label">Raison sociale *</label><input name="raison_sociale" class="form-control" value="{{ old('raison_sociale') }}" required></div>
        <div class="mb-2"><label class="form-label">ICE</label><input name="ice" class="form-control" value="{{ old('ice') }}"></div>
        <div class="row g-2 mb-2"><div class="col"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div><div class="col"><label class="form-label">Tél.</label><input name="telephone" class="form-control" value="{{ old('telephone') }}"></div></div>
        <div class="mb-2"><label class="form-label">Adresse</label><input name="adresse" class="form-control" value="{{ old('adresse') }}"></div>
        <div class="row g-2 mb-2"><div class="col"><label class="form-label">Ville</label><input name="ville" class="form-control" value="{{ old('ville') }}"></div><div class="col"><label class="form-label">CP</label><input name="code_postal" class="form-control" value="{{ old('code_postal') }}"></div></div>
        <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control">{{ old('notes') }}</textarea></div>
        <button class="btn btn-primary">Enregistrer</button><a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">Retour</a>
    </form></div>
</div>
@endsection
