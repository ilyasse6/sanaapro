@extends('user.master')
@section('content')
<style>
    .bl-show-statut { margin: 1rem 0; }
    @media print {
        .bl-show-actions, .bl-show-statut, .sidebar, .header, #header, nav, .btn, footer, .alert { display: none !important; }
        body { background: #fff !important; }
        #main { margin: 0 !important; padding: 0 !important; }
    }
</style>
<div class="section">
    <div class="bl-show-actions d-flex flex-wrap gap-2">
        <a href="{{ $bonLivraison->actif ? route('bons-livraison.index') : route('bons-livraison.index_desactives') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Liste des bons</a>
        @if(Auth::user()->hasPermission('Gestion des Bons de livraison', 'modifier') && $bonLivraison->actif)
            <a href="{{ route('bons-livraison.edit', $bonLivraison) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Modifier</a>
        @endif
        @if(Auth::user()->hasPermission('Gestion des Bons de livraison', 'ajouter') && $bonLivraison->actif)
            <a href="{{ route('bons-livraison.duplicate', $bonLivraison) }}" class="btn btn-outline-secondary"><i class="bi bi-copy me-1"></i>Dupliquer</a>
        @endif
        @if(Auth::user()->hasPermission('Gestion des Bons de livraison', 'desactiver') && $bonLivraison->actif)
            <form action="{{ route('bons-livraison.desactivate', $bonLivraison->id) }}" method="post" class="d-inline" onsubmit="return confirm('Archiver ce bon ?');">
                @csrf
                <button type="submit" class="btn btn-outline-warning"><i class="bi bi-archive me-1"></i>Archiver</button>
            </form>
        @endif
        <a href="{{ route('bons-livraison.pdf', $bonLivraison) }}" class="btn btn-outline-danger" target="_blank" rel="noopener"><i class="bi bi-file-pdf me-1"></i>PDF</a>
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimer</button>
    </div>
    @include('user.partials.flash')

    <div class="bl-show-statut">
        <span class="badge rounded-pill px-3 py-2
            @if($bonLivraison->statut === 'valide') text-bg-success
            @elseif($bonLivraison->statut === 'annule') text-bg-danger
            @else text-bg-secondary
            @endif
        ">{{ $bonLivraison->statut_label }}</span>
    </div>

    @include('user.bons-livraison.partials.document-afifalu', [
        'bonLivraison' => $bonLivraison,
        'societe' => $societe,
        'blPrintId' => 'blPrint',
    ])
</div>
@if(request()->boolean('print'))
<script>
window.addEventListener('load', function () {
    setTimeout(function () { window.print(); }, 350);
});
</script>
@endif
@endsection
