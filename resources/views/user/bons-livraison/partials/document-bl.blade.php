<div class="bl-doc">
    <h1 class="bl-title">Bon de livraison</h1>

    <div class="bl-co-name">{{ $societe['nom'] }}</div>
    <div class="bl-co-lines">
        @if($societe['adresse'] !== ''){!! nl2br(e($societe['adresse'])) !!}<br>@endif
        @if($societe['telephone'] !== '')Téléphone : {{ $societe['telephone'] }}@endif
    </div>

    <div class="bl-row">
        <div class="bl-box">
            <table>
                <tr><td>Bon de livraison N°</td><td>{{ $bonLivraison->numero }}</td></tr>
                <tr><td>Date</td><td>{{ $bonLivraison->date_bl->format('d/m/Y') }}</td></tr>
                <tr><td>Lieu</td><td>{{ $bonLivraison->lieu ?: '—' }}</td></tr>
                <tr><td>N° commande</td><td>{{ $bonLivraison->numero_commande ?: '—' }}</td></tr>
                <tr><td>N° client</td><td>{{ $bonLivraison->reference_client ?: '—' }}</td></tr>
                <tr><td>Contact client</td><td>{{ $bonLivraison->contact_client ?: '—' }}</td></tr>
                <tr><td>Émis par</td><td>{{ $bonLivraison->emis_par ?: '—' }}</td></tr>
            </table>
        </div>
        <div class="bl-box bl-box-right">
            <div class="bl-dest-h">Destinataire</div>
            <div class="bl-dest-name">{{ $bonLivraison->client->nom }}@if($bonLivraison->client->prenom) {{ $bonLivraison->client->prenom }}@endif</div>
            <div class="bl-dest-lines">
                @if($bonLivraison->client->adresse){!! nl2br(e($bonLivraison->client->adresse)) !!}<br>@endif
                @if($bonLivraison->client->code_postal || $bonLivraison->client->ville){{ trim($bonLivraison->client->code_postal.' '.$bonLivraison->client->ville) }}<br>@endif
                @if($bonLivraison->client->telephone)Tél. {{ $bonLivraison->client->telephone }}@endif
            </div>
        </div>
    </div>

    <div class="bl-add-h">Informations additionnelles</div>
    <div class="bl-add-txt">{{ $bonLivraison->informations_additionnelles ?: '—' }}</div>

    <table class="bl-table">
        <thead>
            <tr>
                <th style="width:18%">Réf. produit</th>
                <th>Description</th>
                <th class="num" style="width:14%">Quantités</th>
                <th style="width:12%">Unité</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bonLivraison->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->reference_produit ?: '—' }}</td>
                    <td>{!! nl2br(e($ligne->designation)) !!}</td>
                    <td class="num">{{ rtrim(rtrim(number_format((float) $ligne->quantite, 3, ',', ' '), '0'), ',') }}</td>
                    <td>{{ $ligne->unite }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="bl-sign-row">
        <div class="bl-sign-col">
            <div class="bl-sign-h">Visa du client</div>
            <div>Reçu le : @if($bonLivraison->recu_le){{ $bonLivraison->recu_le->format('d/m/Y') }}@else………………@endif</div>
            <div class="bl-sign-line">Signature :</div>
        </div>
        <div class="bl-sign-col-r">
            <div class="bl-sign-h">Visa du fournisseur</div>
            <div>Livré le : @if($bonLivraison->livre_le){{ $bonLivraison->livre_le->format('d/m/Y') }}@else………………@endif</div>
            <div class="bl-sign-line">Signature :</div>
        </div>
    </div>

    <div class="bl-footer">
        <div class="bl-footer-col">
            <h4>Siège social</h4>
            @if($societe['adresse'] !== '')<div>{!! nl2br(e($societe['adresse'])) !!}</div>@endif
            @if($societe['mention_tva'] !== '')<div class="mt-1">{{ $societe['mention_tva'] }}</div>@endif
        </div>
        <div class="bl-footer-col">
            <h4>Coordonnées</h4>
            @if($societe['telephone'])<div>Tél. {{ $societe['telephone'] }}</div>@endif
            @if($societe['email'])<div>{{ $societe['email'] }}</div>@endif
        </div>
        <div class="bl-footer-col">
            <h4>Détails bancaires</h4>
            <div>Renseignez vos RIB / IBAN dans les paramètres société si besoin.</div>
        </div>
    </div>
    <div style="clear:both"></div>
    <div class="bl-bar"></div>
</div>
