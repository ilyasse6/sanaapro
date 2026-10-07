@extends('user.master')
@section('content')
<div class="section">
    <div class="d-flex justify-content-between mb-3"><h1 class="pagetitle mb-0">Employés</h1>
        @if(Auth::user()->hasPermission('Gestion des Employés & Congés', 'ajouter'))<a href="{{ route('employes.create') }}" class="btn btn-primary btn-sm">Ajouter</a>@endif</div>
    @include('user.partials.flash')
    <div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Matricule</th><th>Nom</th><th>Poste</th><th>Embauche</th><th>Salaire</th><th></th></tr></thead><tbody>
        @foreach($employes as $e)
            <tr><td>{{ $e->matricule }}</td><td>{{ $e->nom }} {{ $e->prenom }}</td><td>{{ $e->poste_libelle ?? '—' }}</td><td>{{ $e->date_embauche?->format('d/m/Y') ?? '—' }}</td><td>@if($e->salaire_mensuel){{ number_format($e->salaire_mensuel, 2, ',', ' ') }}@else — @endif</td>
                <td class="text-end"><a href="{{ route('employes.show', $e) }}" class="btn btn-sm btn-outline-primary">Fiche</a></td></tr>
        @endforeach
    </tbody></table></div>{{ $employes->links() }}</div>
</div>
@endsection
