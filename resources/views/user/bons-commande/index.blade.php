@extends('user.master')
@section('content')
<div class="section">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="pagetitle mb-0"><i class="bi bi-bag-check me-2 text-primary"></i>Bons de commande</h1>
            <p class="text-muted small mb-0">Liste des documents actifs</p>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->hasPermission('Gestion des Bons de commande', 'consulter'))
                <a href="{{ route('bons-commande.index_desactives') }}" class="btn btn-outline-secondary btn-sm">Archivés</a>
            @endif
            @if(Auth::user()->hasPermission('Gestion des Bons de commande', 'ajouter'))
                <a href="{{ route('bons-commande.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau</a>
            @endif
        </div>
    </div>
    @include('user.partials.flash')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small mb-0">N°</label>
                    <input type="text" name="numero" class="form-control form-control-sm" value="{{ request('numero') }}" placeholder="BC-…">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0">Fournisseur</label>
                    <select name="fournisseur_id" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        @foreach($fournisseurs as $f)
                            <option value="{{ $f->id }}" @selected(request('fournisseur_id') == $f->id)>{{ $f->raison_sociale }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0">Statut</label>
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        @foreach(['brouillon' => 'Brouillon', 'valide' => 'Validé', 'annule' => 'Annulé'] as $s => $lab)
                            <option value="{{ $s }}" @selected(request('statut') === $s)>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0">Du</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0">Au</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N°</th>
                        <th>Date</th>
                        <th>Fournisseur</th>
                        <th>Lieu livraison</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bons as $bc)
                        <tr>
                            <td><a href="{{ route('bons-commande.show', $bc) }}">{{ $bc->numero }}</a></td>
                            <td>{{ $bc->date_bc->format('d/m/Y') }}</td>
                            <td>{{ $bc->fournisseur->raison_sociale }}</td>
                            <td class="text-muted small">{{ $bc->lieu_livraison ?: '—' }}</td>
                            <td><span class="badge bg-secondary">{{ $bc->statut_label }}</span></td>
                            <td class="text-end text-nowrap">
                                @if(Auth::user()->hasPermission('Gestion des Bons de commande', 'consulter'))
                                    <a href="{{ route('bons-commande.pdf', $bc) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                @endif
                                @if(Auth::user()->hasPermission('Gestion des Bons de commande', 'modifier') && $bc->actif)
                                    <a href="{{ route('bons-commande.edit', $bc) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                @endif
                                @if(Auth::user()->hasPermission('Gestion des Bons de commande', 'desactiver') && $bc->actif)
                                    <form action="{{ route('bons-commande.desactivate', $bc->id) }}" method="post" class="d-inline" onsubmit="return confirm('Désactiver ce bon ?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-archive"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Aucun bon de commande.</td></tr>
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
