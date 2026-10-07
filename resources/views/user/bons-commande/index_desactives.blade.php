@extends('user.master')
@section('content')
<div class="section">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="pagetitle mb-0"><i class="bi bi-archive me-2"></i>Bons de commande archivés</h1>
        </div>
        <a href="{{ route('bons-commande.index') }}" class="btn btn-outline-primary btn-sm">Liste active</a>
    </div>
    @include('user.partials.flash')

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N°</th>
                        <th>Date</th>
                        <th>Fournisseur</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bons as $bc)
                        <tr>
                            <td><a href="{{ route('bons-commande.show', $bc) }}">{{ $bc->numero }}</a></td>
                            <td>{{ $bc->date_bc->format('d/m/Y') }}</td>
                            <td>{{ $bc->fournisseur->raison_sociale }}</td>
                            <td class="text-end">
                                @if(Auth::user()->hasPermission('Gestion des Bons de commande', 'activer'))
                                    <form action="{{ route('bons-commande.activate', $bc->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Réactiver</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Aucun document archivé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bons->hasPages())
            <div class="card-body border-top">{{ $bons->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
