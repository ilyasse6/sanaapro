{{--
    Bon de livraison au format AFIF ALU — même rendu que le devis (fiche, impression, PDF et aperçu live).
    Attend : $societe ; $bonLivraison (null dans l'aperçu de création, client + lignes chargés sinon) ;
    optionnel : $numeroApercu, $pdf_render, $logo_src_for_pdf, $logo_img_pdf_dims, $blPrintId.
    Les éléments [data-bv] sont réécrits en direct par le formulaire (create / edit).
    Compatible DomPDF : pas de flex/grid ; bande, décor et pied de page en position fixe (répétés sur chaque page).
--}}
@php
    $bvPdf = !empty($pdf_render);
    $bvBl = $bonLivraison ?? null;
    $bvQte = function ($v) {
        $n = (float) $v;
        if (floor($n) == $n) {
            return str_pad((string) (int) $n, 2, '0', STR_PAD_LEFT);
        }
        return str_replace('.', ',', rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.'));
    };

    $bvLogo = $bvPdf
        ? ($logo_src_for_pdf ?? null)
        : (($societe['logo_url'] ?? null) ?: asset('assets/imgs/afifalu-logo.png'));
    $bvTitre = $societe['entete_titre'] ?? \App\Support\SocieteBranding::DEFAULT_DEVIS_ENTETE_TITRE;
    $bvActivite = $societe['entete_activite'] ?? \App\Support\SocieteBranding::DEFAULT_DEVIS_ENTETE_ACTIVITE;
    $bvPied = $societe['pied_page'] ?? \App\Support\SocieteBranding::DEFAULT_DEVIS_PIED_PAGE;

    $bvClient = $bvBl && $bvBl->client ? trim($bvBl->client->nom.($bvBl->client->prenom ? ' – '.$bvBl->client->prenom : '')) : '';
    $bvMeta = array_filter([
        'Date' => $bvBl ? $bvBl->date_bl->format('d/m/Y') : now()->format('d/m/Y'),
        'BL N°' => $bvBl ? $bvBl->numero : ($numeroApercu ?? ''),
        'Client' => $bvClient,
        'Lieu' => $bvBl ? (string) $bvBl->lieu : '',
        'N° commande' => $bvBl ? (string) $bvBl->numero_commande : '',
        'Contact' => $bvBl ? (string) $bvBl->contact_client : '',
    ], fn ($v) => trim((string) $v) !== '');
    $bvInfos = $bvBl ? trim((string) $bvBl->informations_additionnelles) : '';
    $bvRecu = $bvBl && $bvBl->recu_le ? $bvBl->recu_le->format('d/m/Y') : '';
    $bvLivre = $bvBl && $bvBl->livre_le ? $bvBl->livre_le->format('d/m/Y') : '';
    $bvLignes = $bvBl ? $bvBl->lignes : collect();

    $bvDeco = $bvPdf
        ? 'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="250" height="470" viewBox="0 0 250 470">'
            .'<polygon points="8,0 92,58 132,470 48,470" fill="#c8c8c8" fill-opacity="0.55"/>'
            .'<polygon points="92,28 160,78 196,470 128,470" fill="#b0b0b0" fill-opacity="0.6"/>'
            .'<polygon points="160,74 232,126 250,470 196,470" fill="#d9a57c" fill-opacity="0.65"/>'
            .'<polygon points="216,60 250,84 250,470 238,470" fill="#c8c8c8" fill-opacity="0.55"/>'
            .'</svg>')
        : null;
@endphp
@once
<style>
    .bv-head { width: 100%; border-collapse: collapse; }
    .bv-head td { padding: 0; vertical-align: top; }
    .bv-logo { width: 170px; padding-left: 20px !important; }
    .bv-logo img { max-width: 140px; max-height: 80px; }
    .bv-brand { text-align: center; padding-top: 18px !important; }
    .bv-brand-title { font-size: 25px; font-weight: bold; letter-spacing: 0.3px; line-height: 1.1; }
    .bv-brand-activite { font-size: 14.5px; margin-top: 6px; line-height: 1.3; padding: 0 30px; }

    .bv-doc-title { text-align: center; font-size: 19px; font-weight: bold; letter-spacing: 1px; text-decoration: underline; margin-top: 34px; }
    .bv-meta { font-size: 13.5px; margin: 22px 0 0 10px; border-collapse: collapse; }
    .bv-meta td { padding: 0; height: 27px; vertical-align: middle; }
    .bv-meta td:first-child { width: 100px; }

    .bv-table { width: 100%; border-collapse: collapse; margin-top: 48px; font-family: Calibri, Carlito, Arial, Helvetica, sans-serif; font-size: 11.5px; }
    .bv-table th, .bv-table td { border: 1px solid #000; padding: 3px 5px; }
    .bv-table thead th { font-weight: bold; font-style: italic; font-size: 10.5px; height: 36px; text-align: center; vertical-align: top; }
    .bv-table tbody td { vertical-align: top; height: 26px; }
    .bv-table tr { page-break-inside: avoid; }
    .bv-td-num { text-align: center; font-weight: bold; font-size: 10px; }
    .bv-td-desig { font-style: italic; line-height: 1.35; }
    .bv-td-qte { font-weight: bold; font-style: italic; text-align: center; }
    .bv-td-unite { text-align: center; }
    .bv-row-spacer td { height: 24px; }
    .bv-empty { color: #888; font-style: italic; text-align: center; vertical-align: middle !important; height: 60px; }

    .bv-infos { font-size: 13px; margin: 18px 0 0 4px; line-height: 1.4; }
    .bv-infos b { font-style: italic; }

    .bv-sign { width: 100%; border-collapse: collapse; margin-top: 40px; font-family: Calibri, Carlito, Arial, Helvetica, sans-serif; font-size: 11.5px; page-break-inside: avoid; }
    .bv-sign th, .bv-sign td { border: 1px solid #000; padding: 4px 8px; width: 50%; }
    .bv-sign th { font-weight: bold; font-style: italic; font-size: 10.5px; text-align: center; height: 24px; }
    .bv-sign td { vertical-align: top; height: 92px; }
    .bv-sign-date { margin-bottom: 6px; }
    .bv-sign-label { color: #555; font-style: italic; font-size: 10.5px; }

    .bv-foot-rule { border-top: 3px solid #555; border-bottom: 1px solid #555; height: 5px; margin: 0 4px 8px; }
    .bv-foot-text { font-size: 13.5px; text-align: center; line-height: 1.35; }

@if($bvPdf)
    @@page { margin: 42px 22px 130px 22px; }
    body { margin: 0; color: #000; font-family: Times, "Times New Roman", serif; }
    .bv-band { position: fixed; left: -22px; top: -42px; width: 150px; height: 1123px; background: #f2f2f2; }
    .bv-deco { position: fixed; right: -22px; bottom: -130px; width: 250px; height: 470px; }
    .bv-foot { position: fixed; left: 0; right: 0; bottom: -104px; }
    .bv-band, .bv-deco { z-index: 0; }
    .bv-content { position: relative; z-index: 10; }
@else
    .bv-stage { overflow: hidden; }
    .bv-scale { transform-origin: top left; }
    .bv-page {
        position: relative; width: 794px; min-height: 1123px; overflow: hidden; color: #000;
        background: linear-gradient(90deg, #f2f2f2 0, #f2f2f2 150px, #fff 150px);
        padding: 42px 22px 26px; display: flex; flex-direction: column; box-sizing: border-box;
    }
    .bv-stage .bv-page { box-shadow: 0 6px 24px rgba(0,0,0,0.18); }
    .bv-deco { position: absolute; right: 0; bottom: 0; width: 250px; height: 470px; z-index: 0; pointer-events: none; }
    .bv-page > *:not(.bv-deco) { position: relative; z-index: 1; }
    .bv-foot { margin-top: auto; padding-top: 40px; }
    .bv-page, .bv-page * { font-family: "Times New Roman", Times, serif !important; color: #000; }
    .bv-page .bv-table, .bv-page .bv-table *, .bv-page .bv-sign, .bv-page .bv-sign * { font-family: Calibri, Carlito, "Segoe UI", Arial, sans-serif !important; }
    .bv-page .bv-empty, .bv-page .bv-sign-label { color: #666; }
    .bv-page span { font-weight: inherit; font-style: inherit; }
    .bv-table th, .bv-table td, .bv-sign th, .bv-sign td { background: transparent !important; }
    .bv-print-layout { width: 100%; border-collapse: collapse; }
    .bv-print-layout > thead > tr > td, .bv-print-layout > tbody > tr > td, .bv-print-layout > tfoot > tr > td { padding: 0; border: 0; }
    .bv-print-space, .bv-band-print { display: none; }
    @@media print {
        @@page { size: A4; margin: 0; }
        .bv-stage { overflow: visible; }
        .bv-scale { transform: none !important; width: auto !important; height: auto !important; margin: 0 !important; }
        .bv-stage .bv-page { box-shadow: none; min-height: 0; padding: 0 22px; overflow: visible; display: block; background: none; }
        .bv-page { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        {{-- Les espaces du thead/tfoot se répètent sur chaque feuille : ils réservent la place du haut et du pied de page fixe. --}}
        .bv-print-space { display: block; }
        .bv-print-space-top { height: 42px; }
        .bv-print-space-bottom { height: 130px; }
        .bv-page > .bv-band-print { display: block; position: fixed !important; left: 0; top: 0; bottom: 0; width: 150px; background: #f2f2f2; z-index: 0 !important; }
        .bv-page > .bv-deco { position: fixed; right: 0; bottom: 0; }
        .bv-page > .bv-foot { position: fixed !important; left: 22px; right: 22px; bottom: 26px; margin: 0; padding: 0; }
    }
@endif
</style>
@endonce
@if(!$bvPdf)
@once
<script>
    window.bvFit = function () {
        document.querySelectorAll('.bv-stage').forEach(function (stage) {
            const scale = stage.querySelector('.bv-scale');
            const page = scale.querySelector('.bv-page');
            const s = Math.min(1, stage.clientWidth / 794);
            scale.style.width = '794px';
            scale.style.transform = 'scale(' + s + ')';
            scale.style.height = (page.offsetHeight * s) + 'px';
            scale.style.marginLeft = Math.max(0, (stage.clientWidth - 794 * s) / 2) + 'px';
        });
    };
    window.addEventListener('resize', window.bvFit);
    window.addEventListener('load', window.bvFit);
    document.addEventListener('DOMContentLoaded', window.bvFit);
</script>
@endonce
<div class="bv-stage"><div class="bv-scale">
<div class="bv-page"@if(!empty($blPrintId)) id="{{ $blPrintId }}"@endif>
    <svg class="bv-deco" viewBox="0 0 250 470" preserveAspectRatio="none" aria-hidden="true">
        <defs>
            <linearGradient id="bvG1" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#cfcfcf" stop-opacity=".75"/><stop offset="1" stop-color="#e6e6e6" stop-opacity=".35"/></linearGradient>
            <linearGradient id="bvG2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#b5b5b5" stop-opacity=".8"/><stop offset="1" stop-color="#d4d4d4" stop-opacity=".4"/></linearGradient>
            <linearGradient id="bvG3" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#d9a57c" stop-opacity=".85"/><stop offset="1" stop-color="#ecd2bd" stop-opacity=".45"/></linearGradient>
        </defs>
        <polygon points="8,0 92,58 132,470 48,470" fill="url(#bvG1)"/>
        <polygon points="92,28 160,78 196,470 128,470" fill="url(#bvG2)"/>
        <polygon points="160,74 232,126 250,470 196,470" fill="url(#bvG3)"/>
        <polygon points="216,60 250,84 250,470 238,470" fill="url(#bvG1)"/>
    </svg>
    <div class="bv-band-print"></div>
    <table class="bv-print-layout">
        <thead><tr><td><div class="bv-print-space bv-print-space-top"></div></td></tr></thead>
        <tfoot><tr><td><div class="bv-print-space bv-print-space-bottom"></div></td></tr></tfoot>
        <tbody><tr><td>
@else
    <div class="bv-band"></div>
    <img class="bv-deco" src="{{ $bvDeco }}" alt="">
    <div class="bv-foot">
        <div class="bv-foot-rule"></div>
        <div class="bv-foot-text">{!! nl2br(e($bvPied)) !!}</div>
    </div>
    <div class="bv-content">
@endif

    <table class="bv-head">
        <tr>
            <td class="bv-logo">
                @if(!empty($bvLogo))
                    <img src="{{ $bvLogo }}" alt=""@if($bvPdf && !empty($logo_img_pdf_dims['w'])) width="{{ (int) $logo_img_pdf_dims['w'] }}" height="{{ (int) $logo_img_pdf_dims['h'] }}"@endif>
                @endif
            </td>
            <td class="bv-brand">
                <div class="bv-brand-title">{{ $bvTitre }}</div>
                <div class="bv-brand-activite">{{ $bvActivite }}</div>
            </td>
        </tr>
    </table>

    <div class="bv-doc-title">BON DE LIVRAISON</div>

    <table class="bv-meta">
        <tbody data-bv="meta">
            @foreach($bvMeta as $label => $valeur)
                <tr><td>{{ $label }}</td><td>: {{ $valeur }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <table class="bv-table">
        <colgroup>
            <col style="width:6%"><col style="width:16%"><col style="width:54%"><col style="width:10%"><col style="width:14%">
        </colgroup>
        <thead>
            <tr>
                <th>N°</th>
                <th>RÉF.</th>
                <th>DESIGNATION</th>
                <th>QTE</th>
                <th>UNITE</th>
            </tr>
        </thead>
        <tbody data-bv="lignes">
            @forelse($bvLignes as $i => $ligne)
                <tr>
                    <td class="bv-td-num">{{ $i + 1 }}</td>
                    <td>{{ $ligne->reference_produit }}</td>
                    <td class="bv-td-desig">{!! nl2br(e($ligne->designation)) !!}</td>
                    <td class="bv-td-qte">{{ (float) $ligne->quantite ? $bvQte($ligne->quantite) : '' }}</td>
                    <td class="bv-td-unite">{{ $ligne->unite }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="bv-empty">Aucune ligne</td></tr>
            @endforelse
        </tbody>
        <tbody>
            <tr class="bv-row-spacer"><td></td><td></td><td></td><td></td><td></td></tr>
        </tbody>
    </table>

    <div class="bv-infos" data-bv="infos"@if($bvInfos === '') style="display:none"@endif>
        <b>Observations :</b> <span data-bv="infos-texte">{!! nl2br(e($bvInfos)) !!}</span>
    </div>

    <table class="bv-sign">
        <tr>
            <th>VISA DU CLIENT</th>
            <th>VISA DU FOURNISSEUR</th>
        </tr>
        <tr>
            <td>
                <div class="bv-sign-date">Reçu le : <span data-bv="recu">{{ $bvRecu !== '' ? $bvRecu : '……/……/…………' }}</span></div>
                <div class="bv-sign-label">Signature et cachet</div>
            </td>
            <td>
                <div class="bv-sign-date">Livré le : <span data-bv="livre">{{ $bvLivre !== '' ? $bvLivre : '……/……/…………' }}</span></div>
                <div class="bv-sign-label">Signature et cachet</div>
            </td>
        </tr>
    </table>

@if(!$bvPdf)
        </td></tr></tbody>
    </table>
    <div class="bv-foot">
        <div class="bv-foot-rule"></div>
        <div class="bv-foot-text">{!! nl2br(e($bvPied)) !!}</div>
    </div>
</div>
</div></div>
@else
    </div>
@endif
