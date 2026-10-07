@extends('user.master')
@section('content')
@php
    $a = $societe['accent_color'] ?? \App\Support\SocieteBranding::DEFAULT_ACCENT_COLOR;
@endphp
<div class="section" style="max-width: 960px">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h1 class="pagetitle mb-0">{{ $bonLivraison->numero }}</h1>
            <p class="text-muted small mb-0">Bon de livraison · {{ $bonLivraison->date_bl->format('d/m/Y') }} · {{ $bonLivraison->statut_label }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if(Auth::user()->hasPermission('Gestion des Bons de livraison', 'consulter'))
                <a href="{{ route('bons-livraison.pdf', $bonLivraison) }}" class="btn btn-outline-danger" target="_blank"><i class="bi bi-file-pdf me-1"></i>PDF</a>
            @endif
            @if(Auth::user()->hasPermission('Gestion des Bons de livraison', 'modifier') && $bonLivraison->actif)
                <a href="{{ route('bons-livraison.edit', $bonLivraison) }}" class="btn btn-primary">Modifier</a>
            @endif
            @if(Auth::user()->hasPermission('Gestion des Bons de livraison', 'ajouter') && $bonLivraison->actif)
                <a href="{{ route('bons-livraison.duplicate', $bonLivraison) }}" class="btn btn-outline-secondary">Dupliquer</a>
            @endif
            @if(Auth::user()->hasPermission('Gestion des Bons de livraison', 'desactiver') && $bonLivraison->actif)
                <form action="{{ route('bons-livraison.desactivate', $bonLivraison->id) }}" method="post" class="d-inline" onsubmit="return confirm('Archiver ce bon ?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning">Archiver</button>
                </form>
            @endif
            <a href="{{ route('bons-livraison.index') }}" class="btn btn-link">Retour</a>
        </div>
    </div>
    @include('user.partials.flash')

    <div class="card border-0 shadow-sm mb-4" style="border-top: 4px solid {{ $a }} !important;">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted small">Expéditeur</h6>
                    <strong>{{ $societe['nom'] }}</strong>
                    <div class="small text-muted">{!! nl2br(e($societe['adresse'])) !!}</div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted small">Destinataire</h6>
                    <strong>{{ $bonLivraison->client->nom }}@if($bonLivraison->client->prenom) {{ $bonLivraison->client->prenom }}@endif</strong>
                    @if($bonLivraison->client->adresse)<div class="small">{!! nl2br(e($bonLivraison->client->adresse)) !!}</div>@endif
                </div>
            </div>
            <hr>
            <div class="row small g-2">
                <div class="col-md-4"><strong>Lieu</strong> {{ $bonLivraison->lieu ?: '—' }}</div>
                <div class="col-md-4"><strong>N° commande</strong> {{ $bonLivraison->numero_commande ?: '—' }}</div>
                <div class="col-md-4"><strong>N° client</strong> {{ $bonLivraison->reference_client ?: '—' }}</div>
                <div class="col-md-4"><strong>Contact</strong> {{ $bonLivraison->contact_client ?: '—' }}</div>
                <div class="col-md-4"><strong>Émis par</strong> {{ $bonLivraison->emis_par ?: '—' }}</div>
                <div class="col-md-4"><strong>Reçu le</strong> {{ $bonLivraison->recu_le?->format('d/m/Y') ?: '—' }}</div>
                <div class="col-md-4"><strong>Livré le</strong> {{ $bonLivraison->livre_le?->format('d/m/Y') ?: '—' }}</div>
            </div>
            @if($bonLivraison->informations_additionnelles)
                <div class="mt-3 p-3 rounded bg-light small">
                    <strong>Informations additionnelles</strong>
                    <div class="mt-1" style="white-space: pre-wrap;">{{ $bonLivraison->informations_additionnelles }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Réf. produit</th>
                            <th>Description</th>
                            <th class="text-end">Quantité</th>
                            <th>Unité</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bonLivraison->lignes as $l)
                            <tr>
                                <td>{{ $l->reference_produit ?: '—' }}</td>
                                <td>{{ $l->designation }}</td>
                                <td class="text-end">{{ rtrim(rtrim(number_format((float) $l->quantite, 3, ',', ' '), '0'), ',') }}</td>
                                <td>{{ $l->unite }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
