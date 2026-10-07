{{-- Attend : $facture, $societe, $remiseGlobaleHt ; optionnel : $facturePrintId --}}
@php
    $docAccentColor = $societe['accent_color'] ?? \App\Support\SocieteBranding::DEFAULT_ACCENT_COLOR;
    $docTextColor = $societe['text_color'] ?? \App\Support\SocieteBranding::DEFAULT_TEXT_COLOR;
    $forDomPdf = isset($pdf_render) && $pdf_render;
    $logoImgSrc = $forDomPdf
        ? ($logo_src_for_pdf ?? null)
        : (($logo_src_for_pdf ?? null) ?? ($societe['logo_url'] ?? null));
@endphp
<div class="devis-ae-doc{{ !empty($pdf_render) ? ' devis-ae-pdf' : '' }}"@if(!$forDomPdf) style="--devis-ae-blue: {{ e($docAccentColor) }}; --devis-ae-text: {{ e($docTextColor) }}; --devis-ae-border: 1px solid var(--devis-ae-blue);"@endif @if(!empty($facturePrintId)) id="{{ $facturePrintId }}"@endif>
    <div class="devis-ae-top">
        <div class="devis-ae-co-left">
            <div class="devis-ae-logo-row">
                @if(!empty($logoImgSrc))
                    <img src="{{ $logoImgSrc }}" alt="" class="devis-ae-logo"@if($forDomPdf && isset($logo_img_pdf_dims) && is_array($logo_img_pdf_dims) && isset($logo_img_pdf_dims['w'], $logo_img_pdf_dims['h'])) width="{{ (int) $logo_img_pdf_dims['w'] }}" height="{{ (int) $logo_img_pdf_dims['h'] }}"@endif>
                @else
                    <div class="devis-ae-logo-ph">Logo<br><span class="devis-ae-ph-hint">Menu Paramètres</span></div>
                @endif
            </div>
            @if($societe['nom'] !== '')
                <div class="devis-ae-co-name">{{ $societe['nom'] }}</div>
            @endif
            <div class="devis-ae-co-lines text-muted small">
                @if($societe['adresse'] !== '')
                    {!! nl2br(e($societe['adresse'])) !!}<br>
                @endif
                @if($societe['telephone'] !== '')Tél. {{ $societe['telephone'] }}<br>@endif
                @if($societe['email'] !== ''){{ $societe['email'] }}@endif
            </div>
        </div>
        <div class="devis-ae-boxes">
            <div class="devis-ae-box">
                <div class="devis-ae-box-h">Informations de la FACTURE :</div>
                <div class="devis-ae-box-b">
                    <div><strong>N° facture :</strong> {{ $facture->numero_facture }}</div>
                    <div><strong>Date d'émission :</strong> {{ $facture->date_emission->format('d/m/Y') }}</div>
                    @if($facture->date_echeance)
                        <div><strong>Date d'échéance :</strong> {{ $facture->date_echeance->format('d/m/Y') }}</div>
                    @endif
                    @if($facture->devis)
                        <div><strong>Devis d'origine :</strong> {{ $facture->devis->numero_devis }}</div>
                    @endif
                </div>
            </div>
            <div class="devis-ae-box">
                <div class="devis-ae-box-h">Informations client :</div>
                <div class="devis-ae-box-b">
                    <div><strong>{{ $facture->client->nom }}@if($facture->client->prenom) {{ $facture->client->prenom }}@endif</strong></div>
                    @if($facture->client->adresse)<div>{!! nl2br(e($facture->client->adresse)) !!}</div>@endif
                    @if($facture->client->code_postal || $facture->client->ville)
                        <div>{{ trim($facture->client->code_postal.' '.$facture->client->ville) }}</div>
                    @endif
                    @if($facture->client->telephone)<div>Tél. {{ $facture->client->telephone }}</div>@endif
                    @if($facture->client->email)<div>{{ $facture->client->email }}</div>@endif
                    @if($facture->client->ice)<div>ICE : {{ $facture->client->ice }}</div>@endif
                </div>
            </div>
        </div>
    </div>

    <h1 class="devis-ae-title">FACTURE N° {{ $facture->numero_facture }}</h1>
    @if($facture->objet)
        <p class="devis-ae-sub mb-0 devis-ae-objet-titre">{{ $facture->objet }}</p>
    @else
        <p class="devis-ae-sub">[Objet de la facture (facultatif)]</p>
    @endif

    <table class="devis-ae-table">
        <thead>
            <tr>
                <th style="width:46%">Description des produits ou services</th>
                <th class="num" style="width:12%">Quantité</th>
                <th class="num" style="width:19%">Prix unitaire HT</th>
                <th class="num" style="width:19%">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->lignes as $ligne)
                <tr>
                    <td class="desc">{!! nl2br(e($ligne->designation)) !!}</td>
                    <td class="num">{{ rtrim(rtrim(number_format((float) $ligne->quantite, 3, ',', ' '), '0'), ',') }} @if($ligne->unite)<span class="text-muted" style="font-size:9px">{{ $ligne->unite }}</span>@endif</td>
                    <td class="num">{{ number_format($ligne->prix_unitaire_ht, 2, ',', ' ') }} MAD</td>
                    <td class="num">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} MAD</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="devis-ae-totaux">
        <table>
            @if($facture->remise_globale_pct > 0)
                <tr>
                    <td>Prix total HT</td>
                    <td>{{ number_format($facture->montant_lignes_ht, 2, ',', ' ') }} MAD</td>
                </tr>
                <tr>
                    <td>Remise HT</td>
                    <td>- {{ number_format($remiseGlobaleHt, 2, ',', ' ') }} MAD</td>
                </tr>
            @endif
            <tr>
                <td><strong>Total HT</strong></td>
                <td><strong>{{ number_format($facture->montant_ht, 2, ',', ' ') }} MAD</strong></td>
            </tr>
            @if($facture->taux_tva > 0)
                <tr>
                    <td>TVA {{ number_format($facture->taux_tva, 2, ',', ' ') }} %</td>
                    <td>{{ number_format($facture->montant_tva, 2, ',', ' ') }} MAD</td>
                </tr>
            @endif
            <tr class="devis-ae-totaux-tr-payer">
                <td>Prix total à payer</td>
                <td>
                    @if($facture->taux_tva > 0)
                        {{ number_format($facture->montant_ttc, 2, ',', ' ') }} MAD TTC
                    @else
                        {{ number_format($facture->montant_ht, 2, ',', ' ') }} MAD
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @if($societe['mention_tva'] !== '')
        <p class="devis-ae-mention-tva">{{ $societe['mention_tva'] }}</p>
    @endif

    <div class="devis-ae-comments">
        <h3>Commentaires</h3>
        <div class="small text-muted" style="white-space:pre-wrap;">
            @if($facture->conditions_paiement)
                <strong>Conditions de paiement :</strong> {{ $facture->conditions_paiement }}

            @endif
            @if($facture->notes)
                <strong>Notes :</strong> {{ $facture->notes }}
            @endif
            @if(!$facture->conditions_paiement && !$facture->notes)
                —
            @endif
        </div>
    </div>

    <div class="devis-ae-sign">
        <p>Date et signature précédée de la mention « Bon pour accord » :</p>
        <div class="row-sig">
            <div><strong>Date</strong><br><span class="text-muted">………………</span></div>
            <div><strong>Signature</strong><br><span class="text-muted">………………</span></div>
        </div>
    </div>
</div>
