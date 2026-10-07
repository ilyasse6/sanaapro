@extends('user.master')
@section('content')
<div class="section" style="max-width:640px"><h1 class="pagetitle">Modifier {{ $employe->matricule }}</h1>
<div class="card card-body"><form method="post" action="{{ route('employes.update', $employe) }}">@csrf @method('PUT')
    <div class="mb-2"><label class="form-label">Matricule *</label><input name="matricule" class="form-control" required value="{{ old('matricule', $employe->matricule) }}"></div>
    <div class="row g-2 mb-2"><div class="col"><label class="form-label">Nom *</label><input name="nom" class="form-control" required value="{{ old('nom', $employe->nom) }}"></div><div class="col"><label class="form-label">Prénom *</label><input name="prenom" class="form-control" required value="{{ old('prenom', $employe->prenom) }}"></div></div>
    <div class="row g-2 mb-2"><div class="col"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $employe->email) }}"></div><div class="col"><label class="form-label">Téléphone</label><input name="telephone" class="form-control" value="{{ old('telephone', $employe->telephone) }}"></div></div>
    <div class="mb-2"><label class="form-label">Poste</label><input name="poste_libelle" class="form-control" value="{{ old('poste_libelle', $employe->poste_libelle) }}"></div>
    <div class="row g-2 mb-2"><div class="col"><label class="form-label">Date embauche</label><input type="date" name="date_embauche" class="form-control" value="{{ old('date_embauche', $employe->date_embauche?->format('Y-m-d')) }}"></div><div class="col"><label class="form-label">Salaire</label><input type="number" step="0.01" name="salaire_mensuel" class="form-control" value="{{ old('salaire_mensuel', $employe->salaire_mensuel) }}"></div></div>
    <div class="mb-3"><label class="form-label">Compte utilisateur</label><select name="user_id" class="form-select"><option value="">—</option>@foreach($users as $u)<option value="{{ $u->id }}" @selected(old('user_id', $employe->user_id)==$u->id)>{{ $u->nom }} {{ $u->prenom }}</option>@endforeach</select></div>
    <button class="btn btn-primary">Mettre à jour</button><a href="{{ route('employes.show', $employe) }}" class="btn btn-outline-secondary">Retour</a>
</form>
@if(Auth::user()->hasPermission('Gestion des Employés & Congés', 'desactiver'))<hr><form method="post" action="{{ route('employes.desactivate', $employe) }}" onsubmit="return confirm('Archiver ?');">@csrf<button class="btn btn-outline-danger btn-sm">Archiver</button></form>@endif
</div></div>
@endsection
