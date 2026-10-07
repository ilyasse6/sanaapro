@extends('user.master')

@section('content')
<style>
    .devis-form-hero { background: linear-gradient(135deg, #17324D 0%, #0f2438 100%); color: white; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; }
</style>

<div class="section">
    <div class="devis-form-hero">
        <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Modifier la facture {{ $facture->numero_facture }}</h4>
        <p class="mb-0 opacity-90 small">Client : {{ $facture->client->nom }}@if($facture->client->prenom) – {{ $facture->client->prenom }}@endif</p>
    </div>

    @include('user.partials.flash')

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($facture->devis_id && $facture->devis)
        <div class="alert alert-info small py-2">
            <i class="bi bi-link-45deg me-1"></i>Liée au devis
            <a href="{{ route('devis.show', $facture->devis_id) }}" class="alert-link">{{ $facture->devis->numero_devis }}</a>
            — le lien est conservé à l’enregistrement.
        </div>
    @endif

    @if(! $facture->aTotalTtcSaisi() && $facture->lignes->count() > 1)
        <div class="alert alert-warning small py-2">
            <i class="bi bi-exclamation-triangle me-1"></i>Cette facture avait {{ $facture->lignes->count() }} lignes : à l’enregistrement, elles seront regroupées en une seule ligne (désignations ci-dessous, total TTC inchangé).
        </div>
    @endif

    @include('user.facture.partials.form', [
        'action' => route('factures.update', $facture),
        'method' => 'PUT',
    ])
</div>
@endsection
