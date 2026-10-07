@extends('user.master')
@section('content')
<div class="section" style="max-width:640px"><h1 class="pagetitle">Nouvel employé</h1>
<div class="card card-body"><form method="post" action="{{ route('employes.store') }}">@csrf
    <div class="mb-2"><label class="form-label">Matricule *</label><input name="matricule" class="form-control" required value="{{ old('matricule') }}"></div>
    <div class="row g-2 mb-2"><div class="col"><label class="form-label">Nom *</label><input name="nom" class="form-control" required value="{{ old('nom') }}"></div><div class="col"><label class="form-label">Prénom *</label><input name="prenom" class="form-control" required value="{{ old('prenom') }}"></div></div>
    <div class="row g-2 mb-2"><div class="col"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div><div class="col"><label class="form-label">Téléphone</label><input name="telephone" class="form-control" value="{{ old('telephone') }}"></div></div>
    <div class="mb-2"><label class="form-label">Poste (libellé)</label><input name="poste_libelle" class="form-control" value="{{ old('poste_libelle') }}"></div>
    <div class="row g-2 mb-2"><div class="col"><label class="form-label">Date embauche</label><input type="date" name="date_embauche" class="form-control" value="{{ old('date_embauche') }}"></div><div class="col"><label class="form-label">Salaire mensuel</label><input type="number" step="0.01" name="salaire_mensuel" class="form-control" value="{{ old('salaire_mensuel') }}"></div></div>
    <div class="mb-3"><label class="form-label">Lier compte utilisateur (optionnel)</label><select name="user_id" class="form-select"><option value="">—</option>@foreach($users as $u)<option value="{{ $u->id }}" @selected(old('user_id')==$u->id)>{{ $u->nom }} {{ $u->prenom }} ({{ $u->email }})</option>@endforeach</select></div>
    <button class="btn btn-primary">Enregistrer</button><a href="{{ route('employes.index') }}" class="btn btn-outline-secondary">Retour</a>
</form></div></div>
@endsection
