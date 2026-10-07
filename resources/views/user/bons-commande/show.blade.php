@extends('user.master')
@section('content')
@php
    $a = $societe['accent_color'] ?? \App\Support\SocieteBranding::DEFAULT_ACCENT_COLOR;
    $bonCommande->loadMissing('lignes');
@endphp
<div class="section" style="max-width: 960px">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h1 class="pagetitle mb-0">{{ $bonCommande->numero }}</h1>
            <p class="text-muted small mb-0">Bon de commande · {{ $bonCommande->date_bc->format('d/m/Y') }} · {{ $bonCommande->statut_label }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if(Auth::user()->hasPermission('Gestion des Bons de commande', 'consulter'))
                <a href="{{ route('bons-commande.pdf', $bonCommande) }}" class="btn btn-outline-danger" target="_blank"><i class="bi bi-file-pdf me-1"></i>PDF</a>
            @endif
            @if(Auth::user()->hasPermission('Gestion des Bons de commande', 'modifier') && $bonCommande->actif)
                <a href="{{ route('bons-commande.edit', $bonCommande) }}" class="btn btn-primary">Modifier</a>
            @endif
            @if(Auth::user()->hasPermission('Gestion des Bons de commande', 'ajouter') && $bonCommande->actif)
                <a href="{{ route('bons-commande.duplicate', $bonCommande) }}" class="btn btn-outline-secondary">Dupliquer</a>
            @endif
            @if(Auth::user()->hasPermission('Gestion des Bons de commande', 'desactiver') && $bonCommande->actif)
                <form action="{{ route('bons-commande.desactivate', $bonCommande->id) }}" method="post" class="d-inline" onsubmit="return confirm('Archiver ce bon ?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning">Archiver</button>
                </form>
            @endif
            <a href="{{ route('bons-commande.index') }}" class="btn btn-link">Retour</a>
        </div>
    </div>
    @include('user.partials.flash')

    <div class="card border-0 shadow-sm mb-4" style="border-top: 4px solid {{ $a }} !important;">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted small">Acheteur</h6>
                    <strong>{{ $societe['nom'] }}</strong>
                    <div class="small text-muted">{!! nl2br(e($societe['adresse'])) !!}</div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted small">Fournisseur</h6>
                    <strong>{{ $bonCommande->fournisseur->raison_sociale }}</strong>
                    @if($bonCommande->fournisseur->adresse)<div class="small">{!! nl2br(e($bonCommande->fournisseur->adresse)) !!}</div>@endif
                </div>
            </div>
            <hr>
            <div class="row small g-2">
                <div class="col-md-4"><strong>Lieu livraison</strong> {{ $bonCommande->lieu_livraison ?: '—' }}</div>
                <div class="col-md-4"><strong>Réf. interne</strong> {{ $bonCommande->reference_interne ?: '—' }}</div>
                <div class="col-md-4"><strong>Réf. fournisseur</strong> {{ $bonCommande->reference_fournisseur ?: '—' }}</div>
                <div class="col-md-4"><strong>Contact</strong> {{ $bonCommande->contact_fournisseur ?: '—' }}</div>
                <div class="col-md-4"><strong>Émis par</strong> {{ $bonCommande->emis_par ?: '—' }}</div>
                <div class="col-md-4"><strong>Livraison souhaitée</strong> {{ $bonCommande->date_livraison_souhaitee?->format('d/m/Y') ?: '—' }}</div>
            </div>
            @if($bonCommande->informations_additionnelles)
                <div class="mt-3 p-3 rounded bg-light small">
                    <strong>Informations additionnelles</strong>
                    <div class="mt-1" style="white-space: pre-wrap;">{{ $bonCommande->informations_additionnelles }}</div>
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
                            <th class="text-end">PU HT</th>
                            <th class="text-end">Total HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bonCommande->lignes as $l)
                            <tr>
                                <td>{{ $l->reference_produit ?: '—' }}</td>
                                <td>{{ $l->designation }}</td>
                                <td class="text-end">{{ rtrim(rtrim(number_format((float) $l->quantite, 3, ',', ' '), '0'), ',') }}</td>
                                <td>{{ $l->unite }}</td>
                                <td class="text-end">@if($l->prix_unitaire_ht !== null){{ number_format((float) $l->prix_unitaire_ht, 2, ',', ' ') }}@else—@endif</td>
                                <td class="text-end">@if($l->prix_unitaire_ht !== null){{ number_format($l->montant_ht, 2, ',', ' ') }}@else—@endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                    @if($bonCommande->total_ht > 0)
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="5" class="text-end">Total HT</td>
                                <td class="text-end">{{ number_format($bonCommande->total_ht, 2, ',', ' ') }} MAD</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
