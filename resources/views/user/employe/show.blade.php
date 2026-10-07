@extends('user.master')
@section('content')
<div class="section">
    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
        <a href="{{ route('employes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h1 class="pagetitle mb-0">{{ $employe->nom }} {{ $employe->prenom }}</h1>
        <span class="badge bg-secondary">{{ $employe->matricule }}</span>
        @if(Auth::user()->hasPermission('Gestion des Employés & Congés', 'modifier'))
            <a href="{{ route('employes.edit', $employe) }}" class="btn btn-sm btn-primary ms-auto">Modifier</a>
        @endif
    </div>
    @include('user.partials.flash')
    <div class="row g-3">
        <div class="col-md-4"><div class="card"><div class="card-body">
            <h6>Coordonnées</h6>
            <p class="mb-1 small">{{ $employe->email ?? '—' }}</p>
            <p class="mb-1 small">{{ $employe->telephone ?? '—' }}</p>
            <p class="mb-0 small"><strong>Poste :</strong> {{ $employe->poste_libelle ?? '—' }}</p>
            @if($employe->salaire_mensuel)<p class="mt-2 mb-0"><strong>Salaire :</strong> {{ number_format($employe->salaire_mensuel, 2, ',', ' ') }} MAD</p>@endif
        </div></div></div>
        <div class="col-md-8"><div class="card"><div class="card-body">
            <h6>Congés</h6>
            <table class="table table-sm"><thead><tr><th>Début</th><th>Fin</th><th>Type</th><th>Statut</th><th></th></tr></thead><tbody>
                @foreach($employe->conges as $c)
                    <tr>
                        <td>{{ $c->date_debut->format('d/m/Y') }}</td><td>{{ $c->date_fin->format('d/m/Y') }}</td><td>{{ $c->type_conge }}</td><td>{{ $c->statut }}</td>
                        <td>@if(Auth::user()->hasPermission('Gestion des Employés & Congés', 'modifier') && $c->statut==='en_attente')
                            <form action="{{ route('employes.conges.statut', $c) }}" method="post" class="d-inline">@csrf
                                <input type="hidden" name="statut" value="approuve"><button class="btn btn-sm btn-success">OK</button>
                            </form>
                            <form action="{{ route('employes.conges.statut', $c) }}" method="post" class="d-inline">@csrf
                                <input type="hidden" name="statut" value="refuse"><button class="btn btn-sm btn-outline-danger">Refus</button>
                            </form>
                        @endif</td>
                    </tr>
                @endforeach
            </tbody></table>
            @if(Auth::user()->hasPermission('Gestion des Employés & Congés', 'modifier'))
                <form method="post" action="{{ route('employes.conges.store', $employe) }}" class="row g-2 small">@csrf
                    <div class="col-md-3"><input type="date" name="date_debut" class="form-control form-control-sm" required></div>
                    <div class="col-md-3"><input type="date" name="date_fin" class="form-control form-control-sm" required></div>
                    <div class="col-md-3"><select name="type_conge" class="form-select form-select-sm">@foreach(['annuel','maladie','sans_solde','exceptionnel'] as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach</select></div>
                    <div class="col-md-3"><button class="btn btn-sm btn-primary w-100">Demander</button></div>
                    <div class="col-12"><input name="motif" class="form-control form-control-sm" placeholder="Motif"></div>
                </form>
            @endif
        </div></div></div>
    </div>
</div>
@endsection
