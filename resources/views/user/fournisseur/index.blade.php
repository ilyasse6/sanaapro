@extends('user.master')
@section('content')
<div class="section">
    <div class="d-flex justify-content-between mb-3"><h1 class="pagetitle mb-0">Fournisseurs</h1>
        @if(Auth::user()->hasPermission('Gestion des Fournisseurs', 'ajouter'))<a href="{{ route('fournisseurs.create') }}" class="btn btn-primary btn-sm">Ajouter</a>@endif</div>
    @include('user.partials.flash')
    <div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Raison sociale</th><th>ICE</th><th>Contact</th><th></th></tr></thead><tbody>
        @foreach($fournisseurs as $f)
            <tr><td>{{ $f->raison_sociale }}</td><td>{{ $f->ice ?? '—' }}</td><td>{{ $f->telephone ?? $f->email ?? '—' }}</td>
                <td class="text-end">@if(Auth::user()->hasPermission('Gestion des Fournisseurs', 'modifier'))<a href="{{ route('fournisseurs.edit', $f) }}" class="btn btn-sm btn-outline-primary">Éditer</a>@endif</td></tr>
        @endforeach
    </tbody></table></div>{{ $fournisseurs->links() }}</div>
</div>
@endsection
