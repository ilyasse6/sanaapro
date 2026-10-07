@extends('user.master')
@section('content')
<div class="section">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="pagetitle mb-0"><i class="bi bi-archive me-2"></i>Bons de livraison archivés</h1>
        </div>
        <a href="{{ route('bons-livraison.index') }}" class="btn btn-outline-primary btn-sm">Liste active</a>
    </div>
    @include('user.partials.flash')

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N°</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bons as $bl)
                        <tr>
                            <td><a href="{{ route('bons-livraison.show', $bl) }}">{{ $bl->numero }}</a></td>
                            <td>{{ $bl->date_bl->format('d/m/Y') }}</td>
                            <td>{{ $bl->client->nom }}</td>
                            <td class="text-end">
                                @if(Auth::user()->hasPermission('Gestion des Bons de livraison', 'activer'))
                                    <form action="{{ route('bons-livraison.activate', $bl->id) }}" method="post" class="d-inline">
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
