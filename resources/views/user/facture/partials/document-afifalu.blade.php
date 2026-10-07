{{--
    Facture au format AFIF ALU (une ligne : désignation + total TTC, montant en lettres).
    Attend : $societe ; $facture (null dans l'aperçu de création) ; optionnel : $numeroApercu, $pdf_render, $logo_src_for_pdf, $logo_img_pdf_dims, $facturePrintId.
    Les éléments [data-fx] sont mis à jour en direct par le formulaire.
    Compatible DomPDF : pas de flex/grid ni de variables CSS.
--}}
@php
    $fxPdf = !empty($pdf_render);
    $fxFacture = $facture ?? null;
    $fxFmt = fn ($n) => number_format((float) $n, 2, '.', ' ');

    $fxLogo = $fxPdf
        ? ($logo_src_for_pdf ?? null)
        : (($societe['logo_url'] ?? null) ?: asset('assets/imgs/afifalu-logo.png'));
    $fxTitre = $societe['entete_titre'] ?? \App\Support\SocieteBranding::DEFAULT_DEVIS_ENTETE_TITRE;
    $fxActivite = $societe['entete_activite'] ?? \App\Support\SocieteBranding::DEFAULT_DEVIS_ENTETE_ACTIVITE;
    $fxPied = $societe['pied_page'] ?? \App\Support\SocieteBranding::DEFAULT_DEVIS_PIED_PAGE;

    $fxTaux = $fxFacture ? (float) $fxFacture->taux_tva : 20;
    $fxTtc = $fxFacture ? $fxFacture->montant_ttc : 0;
    $fxHt = $fxFacture ? $fxFacture->montant_ht : 0;
    $fxTva = $fxFacture ? $fxFacture->montant_tva : 0;
    $fxClient = $fxFacture && $fxFacture->client ? trim($fxFacture->client->nom.' '.($fxFacture->client->prenom ?? '')) : '';
    $fxIce = $fxFacture && $fxFacture->client ? (string) $fxFacture->client->ice : '';

    $fxDeco = 'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="250" height="470" viewBox="0 0 250 470">'
        .'<polygon points="8,0 92,58 132,470 48,470" fill="#c8c8c8" fill-opacity="0.55"/>'
        .'<polygon points="92,28 160,78 196,470 128,470" fill="#b0b0b0" fill-opacity="0.6"/>'
        .'<polygon points="160,74 232,126 250,470 196,470" fill="#d9a57c" fill-opacity="0.65"/>'
        .'<polygon points="216,60 250,84 250,470 238,470" fill="#c8c8c8" fill-opacity="0.55"/>'
        .'</svg>');
@endphp
@once
<style>
    .fx-page { position: relative; width: 794px; height: 1122px; overflow: hidden; background: #fff; color: #000; font-family: Times, "Times New Roman", serif; }
    .fx-band { position: absolute; left: 0; top: 0; width: 165px; height: 1122px; background: #f2f2f2; }
    .fx-deco { position: absolute; right: 0; bottom: 0; width: 250px; height: 470px; }
    .fx-logo { position: absolute; left: 40px; top: 52px; width: 135px; }
    .fx-logo img { max-width: 135px; max-height: 82px; }
    .fx-brand { position: absolute; left: 190px; right: 0; top: 78px; text-align: center; }
    .fx-brand-titre { font-size: 25px; font-weight: bold; line-height: 1.1; }
    .fx-brand-activite { font-size: 14.5px; line-height: 1.35; margin: 8px auto 0; width: 345px; }

    .fx-main { position: relative; padding-top: 222px; }
    .fx-meta { width: 100%; border-collapse: collapse; }
    .fx-meta td { vertical-align: top; padding: 0; }
    .fx-meta-left { width: 330px; padding-left: 42px !important; font-size: 13px; line-height: 34px; }
    .fx-meta-left b { font-weight: bold; }
    .fx-client { text-align: center; font-family: Calibri, Carlito, Arial, Helvetica, sans-serif; font-weight: bold; font-size: 14.5px; line-height: 34px; padding-right: 70px !important; }

    .fx-table { margin: 46px 0 0 168px; width: 543px; border-collapse: collapse; font-size: 13px; }
    .fx-table th, .fx-table td { border: 1px solid #444; }
    .fx-table thead th { background: #d9d9d9; height: 42px; font-size: 15px; font-weight: bold; padding: 0 8px; }
    .fx-th-desig { text-align: left; width: 68.4%; }
    .fx-th-total { text-align: center; }
    .fx-td-desig { height: 350px; vertical-align: top; padding: 18px 12px !important; line-height: 1.4; }
    .fx-td-total { vertical-align: top; text-align: center; padding: 18px 8px !important; white-space: nowrap; }

    .fx-totaux { margin: 0 0 0 168px; width: 543px; border-collapse: collapse; font-size: 13.5px; text-align: center; }
    .fx-totaux td { border: 1px solid #444; height: 42px; padding: 0 6px; font-weight: bold; vertical-align: middle; white-space: nowrap; }
    .fx-totaux .fx-col { width: 22.8%; }
    .fx-totaux .fx-taux { white-space: normal; }

    .fx-arrete { margin: 82px 0 0 94px; font-size: 13.5px; }
    .fx-lettres { margin: 8px 0 0 94px; padding-right: 60px; font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-style: italic; font-size: 19px; }

    .fx-foot { position: absolute; left: 24px; right: 24px; bottom: 34px; text-align: center; }
    .fx-foot-rule { border-top: 3px solid #666; border-bottom: 1px solid #666; height: 3px; margin-bottom: 14px; }
    .fx-foot-text { font-size: 13px; line-height: 1.45; }

@if(!$fxPdf)
    .fx-page, .fx-page * { font-family: Times, "Times New Roman", serif !important; }
    .fx-page .fx-client, .fx-page .fx-client * { font-family: Calibri, Carlito, Arial, Helvetica, sans-serif !important; font-weight: bold !important; }
    .fx-page .fx-lettres { font-family: Arial, Helvetica, sans-serif !important; font-weight: bold !important; font-style: italic !important; }
    .fx-page .fx-table th, .fx-page .fx-table td, .fx-page .fx-totaux td { color: #000; }
    .fx-page span { font-weight: inherit !important; font-style: inherit !important; font-size: inherit !important; color: inherit !important; }
    .fx-stage { overflow: hidden; }
    .fx-scale { transform-origin: top left; }
    .fx-stage .fx-page { box-shadow: 0 6px 24px rgba(0,0,0,0.18); }
    @@media print {
        @@page { size: A4; margin: 0; }
        .fx-stage { overflow: visible; }
        .fx-scale { transform: none !important; width: auto !important; height: auto !important; margin: 0 !important; }
        .fx-stage .fx-page { box-shadow: none; }
        .fx-page, .fx-band, .fx-table thead th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
@endif
</style>
@endonce
@if(!$fxPdf)
@once
<script>
    window.fxFit = function () {
        document.querySelectorAll('.fx-stage').forEach(function (stage) {
            const scale = stage.querySelector('.fx-scale');
            const page = scale.querySelector('.fx-page');
            const s = Math.min(1, stage.clientWidth / 794);
            scale.style.width = '794px';
            scale.style.transform = 'scale(' + s + ')';
            scale.style.height = (page.offsetHeight * s) + 'px';
            scale.style.marginLeft = Math.max(0, (stage.clientWidth - 794 * s) / 2) + 'px';
        });
    };
    window.addEventListener('resize', window.fxFit);
    document.addEventListener('DOMContentLoaded', window.fxFit);
</script>
@endonce
<div class="fx-stage"><div class="fx-scale">
@endif
<div class="fx-page"@if(!empty($facturePrintId)) id="{{ $facturePrintId }}"@endif>
    <div class="fx-band"></div>
    <img class="fx-deco" src="{{ $fxDeco }}" alt="">

    <div class="fx-logo">
        @if(!empty($fxLogo))
            <img src="{{ $fxLogo }}" alt=""@if($fxPdf && !empty($logo_img_pdf_dims['w'])) width="{{ (int) $logo_img_pdf_dims['w'] }}" height="{{ (int) $logo_img_pdf_dims['h'] }}"@endif>
        @endif
    </div>
    <div class="fx-brand">
        <div class="fx-brand-titre">{{ $fxTitre }}</div>
        <div class="fx-brand-activite">{{ $fxActivite }}</div>
    </div>

    <div class="fx-main">
        <table class="fx-meta">
            <tr>
                <td class="fx-meta-left">
                    Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <span data-fx="date">{{ $fxFacture ? $fxFacture->date_emission->format('d/m/Y') : now()->format('d/m/Y') }}</span><br>
                    Facture: <b>N°</b> <span data-fx="numero">{{ $fxFacture ? $fxFacture->numero_facture : ($numeroApercu ?? '') }}</span>
                </td>
                <td class="fx-client">
                    <span data-fx="client">{{ mb_strtoupper($fxClient) }}</span><br>
                    <span data-fx="ice-wrap"@if($fxIce === '') style="visibility:hidden"@endif>ICE : <span data-fx="ice">{{ $fxIce }}</span></span>
                </td>
            </tr>
        </table>

        <table class="fx-table">
            <thead>
                <tr>
                    <th class="fx-th-desig">Désignation</th>
                    <th class="fx-th-total">Total T.T.C</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fx-td-desig" data-fx="designation">{!! nl2br(e($fxFacture ? $fxFacture->designation_unique : '')) !!}</td>
                    <td class="fx-td-total"><span data-fx="ttc-ligne">{{ $fxFmt($fxTtc) }}</span> DH</td>
                </tr>
            </tbody>
        </table>
        <table class="fx-totaux">
            <tr>
                <td class="fx-col">Mt H.T</td>
                <td class="fx-col fx-taux" rowspan="2">Taux T.V.A :<br><span data-fx="taux">{{ rtrim(rtrim(number_format($fxTaux, 2, '.', ''), '0'), '.') }}</span>%</td>
                <td class="fx-col">Mt T.V.A</td>
                <td>Mt T.T.C</td>
            </tr>
            <tr>
                <td data-fx="ht">{{ $fxFmt($fxHt) }}</td>
                <td data-fx="tva">{{ $fxFmt($fxTva) }}</td>
                <td><span data-fx="ttc">{{ $fxFmt($fxTtc) }}</span> DH</td>
            </tr>
        </table>

        <div class="fx-arrete">ARRETE LA PRESENTE FACTURE A LA SOMME DE :</div>
        <div class="fx-lettres" data-fx="lettres">{{ $fxFacture ? $fxFacture->montant_en_lettres : \App\Support\MontantEnLettres::dirhams(0) }}</div>
    </div>

    <div class="fx-foot">
        <div class="fx-foot-rule"></div>
        <div class="fx-foot-text">{!! nl2br(e($fxPied)) !!}</div>
    </div>
</div>
@if(!$fxPdf)
</div></div>
@endif
