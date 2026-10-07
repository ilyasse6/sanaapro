@php
    $f = $bonCommande->fournisseur;
    $totalHt = $bonCommande->total_ht;
    $hasPrix = $bonCommande->lignes->contains(fn ($l) => $l->prix_unitaire_ht !== null);
@endphp
<div class="bc-doc">
    <h1 class="bc-title">Bon de commande</h1>

    <div class="bc-co-name">{{ $societe['nom'] }}</div>
    <div class="bc-co-lines">
        @if($societe['adresse'] !== ''){!! nl2br(e($societe['adresse'])) !!}<br>@endif
        @if($societe['telephone'] !== '')Téléphone : {{ $societe['telephone'] }}@endif
    </div>

    <div class="bc-row">
        <div class="bc-box">
            <table>
                <tr><td>Bon de commande N°</td><td>{{ $bonCommande->numero }}</td></tr>
                <tr><td>Date</td><td>{{ $bonCommande->date_bc->format('d/m/Y') }}</td></tr>
                <tr><td>Lieu de livraison</td><td>{{ $bonCommande->lieu_livraison ?: '—' }}</td></tr>
                <tr><td>Réf. interne</td><td>{{ $bonCommande->reference_interne ?: '—' }}</td></tr>
                <tr><td>Réf. fournisseur</td><td>{{ $bonCommande->reference_fournisseur ?: '—' }}</td></tr>
                <tr><td>Contact fournisseur</td><td>{{ $bonCommande->contact_fournisseur ?: '—' }}</td></tr>
                <tr><td>Émis par</td><td>{{ $bonCommande->emis_par ?: '—' }}</td></tr>
                <tr><td>Livraison souhaitée</td><td>@if($bonCommande->date_livraison_souhaitee){{ $bonCommande->date_livraison_souhaitee->format('d/m/Y') }}@else—@endif</td></tr>
            </table>
        </div>
        <div class="bc-box bc-box-right">
            <div class="bc-dest-h">Fournisseur</div>
            <div class="bc-dest-name">{{ $f->raison_sociale }}</div>
            <div class="bc-dest-lines">
                @if($f->adresse){!! nl2br(e($f->adresse)) !!}<br>@endif
                @if($f->code_postal || $f->ville){{ trim($f->code_postal.' '.$f->ville) }}<br>@endif
                @if($f->ice)ICE : {{ $f->ice }}<br>@endif
                @if($f->telephone)Tél. {{ $f->telephone }}@endif
            </div>
        </div>
    </div>

    <div class="bc-add-h">Informations additionnelles</div>
    <div class="bc-add-txt">{{ $bonCommande->informations_additionnelles ?: '—' }}</div>

    <table class="bc-table">
        <thead>
            <tr>
                <th style="width:14%">Réf. produit</th>
                <th>Description</th>
                <th class="num" style="width:11%">Qté</th>
                <th style="width:9%">Unité</th>
                @if($hasPrix)
                    <th class="num" style="width:11%">PU HT</th>
                    <th class="num" style="width:11%">Total HT</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($bonCommande->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->reference_produit ?: '—' }}</td>
                    <td>{!! nl2br(e($ligne->designation)) !!}</td>
                    <td class="num">{{ rtrim(rtrim(number_format((float) $ligne->quantite, 3, ',', ' '), '0'), ',') }}</td>
                    <td>{{ $ligne->unite }}</td>
                    @if($hasPrix)
                        <td class="num">@if($ligne->prix_unitaire_ht !== null){{ number_format((float) $ligne->prix_unitaire_ht, 2, ',', ' ') }}@else—@endif</td>
                        <td class="num">@if($ligne->prix_unitaire_ht !== null){{ number_format($ligne->montant_ht, 2, ',', ' ') }}@else—@endif</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($hasPrix && $totalHt > 0)
        <div class="bc-total-row">Total HT : {{ number_format($totalHt, 2, ',', ' ') }} MAD</div>
    @endif

    <div class="bc-sign-row">
        <div class="bc-sign-col">
            <div class="bc-sign-h">Signature acheteur</div>
            <div class="bc-sign-line">Signature et cachet :</div>
        </div>
        <div class="bc-sign-col-r">
            <div class="bc-sign-h">Bon pour accord fournisseur</div>
            <div class="bc-sign-line">Date et signature :</div>
        </div>
    </div>

    <div class="bc-footer">
        <div class="bc-footer-col">
            <h4>Siège social</h4>
            @if($societe['adresse'] !== '')<div>{!! nl2br(e($societe['adresse'])) !!}</div>@endif
            @if($societe['mention_tva'] !== '')<div class="mt-1">{{ $societe['mention_tva'] }}</div>@endif
        </div>
        <div class="bc-footer-col">
            <h4>Coordonnées</h4>
            @if($societe['telephone'])<div>Tél. {{ $societe['telephone'] }}</div>@endif
            @if($societe['email'])<div>{{ $societe['email'] }}</div>@endif
        </div>
        <div class="bc-footer-col">
            <h4>Paiement</h4>
            <div>Conditions et modalités selon accord avec le fournisseur.</div>
        </div>
    </div>
    <div style="clear:both"></div>
    <div class="bc-bar"></div>
</div>
