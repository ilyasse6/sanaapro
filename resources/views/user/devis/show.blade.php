@extends('user.master')

@section('content')
@include('user.devis.partials.styles-devis-ae')

<div class="section">
    @if(!$devi->actif)
        <div class="alert alert-secondary border mb-3">
            <i class="bi bi-archive me-2"></i><strong>Devis désactivé.</strong> Il n’apparaît pas dans la liste principale.
            @if(Auth::user()->hasPermission('Gestion des Devis', 'activer'))
                <a href="{{ route('devis.index_desactives') }}" class="alert-link">Liste des devis désactivés</a> — vous pouvez le réactiver depuis cette liste.
            @endif
        </div>
    @endif
    <div class="devis-actions d-flex flex-wrap gap-2">
        <a href="{{ $devi->actif ? route('devis.index') : route('devis.index_desactives') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>{{ $devi->actif ? 'Liste des devis' : 'Devis désactivés' }}</a>
        @if(Auth::user()->hasPermission('Gestion des Devis', 'modifier') && $devi->actif)
            <a href="{{ route('devis.edit', $devi) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Modifier</a>
        @endif
        @if(Auth::user()->hasPermission('Gestion des Devis', 'ajouter') && $devi->actif)
            <a href="{{ route('devis.duplicate', $devi) }}" class="btn btn-outline-secondary"><i class="bi bi-copy me-1"></i>Dupliquer</a>
        @endif
        @if(Auth::user()->hasPermission('Gestion des Factures', 'ajouter') && $devi->actif && !$devi->facture && in_array($devi->statut, ['accepte','envoye']))
            <form action="{{ route('factures.convertir-devis', $devi) }}" method="post" class="d-inline" onsubmit="return confirm('Créer la facture à partir de ce devis ?');">
                @csrf
                <button type="submit" class="btn btn-success"><i class="bi bi-receipt-cutoff me-1"></i>Convertir en facture</button>
            </form>
        @endif
        @if($devi->facture && Auth::user()->hasPermission('Gestion des Factures', 'consulter'))
            <a href="{{ route('factures.show', $devi->facture) }}" class="btn btn-outline-success"><i class="bi bi-receipt me-1"></i>Voir la facture</a>
        @endif
        <a href="{{ route('devis.pdf', $devi) }}" class="btn btn-outline-danger" target="_blank" rel="noopener"><i class="bi bi-file-pdf me-1"></i>PDF</a>
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimer</button>
    </div>

    <div class="devis-ae-statut-screen">
        <span class="badge rounded-pill px-3 py-2
            @if($devi->statut === 'brouillon') text-bg-secondary
            @elseif($devi->statut === 'envoye') text-bg-info
            @elseif($devi->statut === 'accepte') text-bg-success
            @else text-bg-danger
            @endif
        ">{{ $devi->statut_label }}</span>
    </div>

    @include('user.devis.partials.document-afifalu', [
        'devi' => $devi,
        'societe' => $societe,
        'devisPrintId' => 'devisPrint',
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
