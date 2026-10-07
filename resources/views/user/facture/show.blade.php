@extends('user.master')

@section('content')
@php
    $societe = $facture->branding_affiche;
@endphp
@include('user.devis.partials.styles-devis-ae')

<div class="section">
    @if(! $facture->actif)
        <div class="alert alert-secondary border mb-3">
            <i class="bi bi-archive me-2"></i><strong>Facture désactivée.</strong> Elle n’apparaît pas dans la liste principale.
            @if(Auth::user()->hasPermission('Gestion des Factures', 'activer'))
                <a href="{{ route('factures.index_desactives') }}" class="alert-link">Factures désactivées</a> — vous pouvez la réactiver depuis cette liste.
            @endif
        </div>
    @endif
    <div class="facture-actions devis-actions d-flex flex-wrap gap-2">
        <a href="{{ $facture->actif ? route('factures.index') : route('factures.index_desactives') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>{{ $facture->actif ? 'Liste des factures' : 'Factures désactivées' }}</a>
        <a href="{{ route('factures.pdf', $facture) }}" class="btn btn-outline-dark" target="_blank"><i class="bi bi-file-pdf me-1"></i>Télécharger PDF</a>
        @if(Auth::user()->hasPermission('Gestion des Factures', 'modifier') && $facture->actif)
            <a href="{{ route('factures.edit', $facture) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Modifier</a>
        @endif
        @if(Auth::user()->hasPermission('Gestion des Factures', 'ajouter') && $facture->actif)
            <a href="{{ route('factures.duplicate', $facture) }}" class="btn btn-outline-secondary"><i class="bi bi-copy me-1"></i>Dupliquer</a>
        @endif
        @if(Auth::user()->hasPermission('Gestion des Factures', 'modifier') && $facture->actif)
            <form action="{{ route('factures.update-statut', $facture) }}" method="post" class="d-flex align-items-center gap-2 flex-wrap">
                @csrf
                <select name="statut" class="form-select form-select-sm" style="width:auto;min-width:11rem">
                    @foreach(['brouillon','emise','envoyee','payee_partielle','payee','annulee','avoir'] as $st)
                        <option value="{{ $st }}" @selected($facture->statut === $st)>{{ $st }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Mettre à jour statut</button>
            </form>
        @endif
        @if($facture->devis_id && Auth::user()->hasPermission('Gestion des Devis', 'consulter'))
            <a href="{{ route('devis.show', $facture->devis_id) }}" class="btn btn-outline-secondary"><i class="bi bi-file-earmark-text me-1"></i>Voir le devis</a>
        @endif
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimer / PDF</button>
    </div>

    <div class="facture-ae-statut-screen devis-ae-statut-screen">
        <span class="badge rounded-pill px-3 py-2
            @if($facture->statut === 'brouillon') text-bg-secondary
            @elseif(in_array($facture->statut, ['emise','envoyee'])) text-bg-info
            @elseif($facture->statut === 'payee_partielle') text-bg-warning text-dark
            @elseif($facture->statut === 'payee') text-bg-success
            @elseif(in_array($facture->statut, ['annulee','avoir'])) text-bg-danger
            @else text-bg-secondary
            @endif
        ">{{ $facture->statut_label }}</span>
    </div>

    @include('user.partials.flash')

    @include('user.facture.partials.document-afifalu', [
        'facture' => $facture,
        'societe' => $societe,
        'facturePrintId' => 'facturePrint',
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
