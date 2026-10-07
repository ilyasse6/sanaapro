{{--
    Devis au format AFIF ALU — même rendu que l'aperçu live (create / edit).
    Attend : $devi (lignes.catalogueService, client chargés), $societe ; optionnel : $pdf_render, $logo_src_for_pdf, $logo_img_pdf_dims, $devisPrintId.
    Compatible DomPDF : pas de flex/grid ; bande, décor et pied de page en position fixe (répétés sur chaque page).
--}}
@php
    $dvPdf = !empty($pdf_render);
    $dvMoney = fn ($n) => number_format((float) $n, 2, ',', ' ').' DH';
    $dvDim = function ($v) {
        if ($v === null || $v === '') {
            return '';
        }
        return str_replace('.', ',', rtrim(rtrim(number_format((float) $v, 3, '.', ''), '0'), '.'));
    };
    $dvQte = function ($v) use ($dvDim) {
        $n = (float) $v;
        return floor($n) == $n ? str_pad((string) (int) $n, 2, '0', STR_PAD_LEFT) : $dvDim($n);
    };

    $dvLogo = $dvPdf
        ? ($logo_src_for_pdf ?? null)
        : (($societe['logo_url'] ?? null) ?: asset('assets/imgs/afifalu-logo.png'));
    $dvTitre = $societe['entete_titre'] ?? \App\Support\SocieteBranding::DEFAULT_DEVIS_ENTETE_TITRE;
    $dvActivite = $societe['entete_activite'] ?? \App\Support\SocieteBranding::DEFAULT_DEVIS_ENTETE_ACTIVITE;
    $dvPied = $societe['pied_page'] ?? \App\Support\SocieteBranding::DEFAULT_DEVIS_PIED_PAGE;

    $dvClient = $devi->client ? trim($devi->client->nom.($devi->client->prenom ? ' – '.$devi->client->prenom : '')) : '';
    $dvObjet = trim((string) $devi->objet);
    $dvTaux = rtrim(rtrim(number_format((float) $devi->taux_tva, 2, ',', ''), '0'), ',');

    $dvImage = function ($ligne) use ($dvPdf) {
        $chemin = $ligne->catalogueService->image ?? null;
        if (! $chemin) {
            return null;
        }
        if (! $dvPdf) {
            return asset($chemin);
        }
        $fichier = public_path($chemin);
        return is_file($fichier) ? 'file://'.$fichier : null;
    };

    $dvDeco = $dvPdf
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
    .dv-head { width: 100%; border-collapse: collapse; }
    .dv-head td { padding: 0; vertical-align: top; }
    .dv-logo { width: 170px; padding-left: 20px !important; }
    .dv-logo img { max-width: 140px; max-height: 80px; }
    .dv-brand { text-align: center; padding-top: 18px !important; }
    .dv-brand-title { font-size: 25px; font-weight: bold; letter-spacing: 0.3px; line-height: 1.1; }
    .dv-brand-activite { font-size: 14.5px; margin-top: 6px; line-height: 1.3; padding: 0 30px; }

    .dv-meta { font-size: 13.5px; margin: 42px 0 0 10px; line-height: 2; }
    .dv-meta span { display: inline-block; width: 72px; }

    .dv-table { width: 100%; border-collapse: collapse; margin-top: 96px; font-family: Calibri, Carlito, Arial, Helvetica, sans-serif; font-size: 11.5px; }
    .dv-table th, .dv-table td { border: 1px solid #000; padding: 3px 5px; }
    .dv-table thead th { font-weight: bold; font-style: italic; font-size: 10.5px; height: 36px; }
    .dv-table thead th.dv-th-type { text-align: left; vertical-align: top; font-style: normal; }
    .dv-table thead th.dv-th-top { text-align: center; vertical-align: top; }
    .dv-table thead th.dv-th-bottom { text-align: center; vertical-align: bottom; padding-bottom: 5px; }
    .dv-table tbody td { vertical-align: top; }
    .dv-table tr { page-break-inside: avoid; }
    .dv-td-type { position: relative; text-align: center; height: 40px; padding: 2px 4px 10px !important; }
    .dv-td-type img { max-width: 62%; max-height: 54px; }
    .dv-td-num { position: absolute; left: 4px; bottom: 1px; font-weight: bold; font-size: 10px; }
    .dv-td-desig { font-style: italic; line-height: 1.35; }
    .dv-td-qte { font-weight: bold; font-style: italic; vertical-align: middle !important; text-align: center; }
    .dv-td-money { text-align: right; white-space: nowrap; }
    .dv-row-spacer td { height: 24px; }
    .dv-row-total td { height: 42px; vertical-align: top !important; }
    .dv-total-label { font-weight: bold; font-style: italic; font-size: 15px; text-align: center; }
    .dv-total-value { font-weight: bold; font-style: italic; font-size: 17px; text-align: right; white-space: nowrap; }
    .dv-empty { color: #888; font-style: italic; text-align: center; vertical-align: middle !important; height: 60px; }

    .dv-foot-rule { border-top: 3px solid #555; border-bottom: 1px solid #555; height: 5px; margin: 0 4px 8px; }
    .dv-foot-text { font-size: 13.5px; text-align: center; line-height: 1.35; }

@if($dvPdf)
    @@page { margin: 42px 22px 130px 22px; }
    body { margin: 0; color: #000; font-family: Times, "Times New Roman", serif; }
    .dv-band { position: fixed; left: -22px; top: -42px; width: 150px; height: 1123px; background: #f2f2f2; }
    .dv-deco { position: fixed; right: -22px; bottom: -130px; width: 250px; height: 470px; }
    .dv-foot { position: fixed; left: 0; right: 0; bottom: -104px; }
    .dv-band, .dv-deco { z-index: 0; }
    .dv-content { position: relative; z-index: 10; }
@else
    .dv-stage { overflow: hidden; }
    .dv-scale { transform-origin: top left; }
    .dv-page {
        position: relative; width: 794px; min-height: 1123px; overflow: hidden; color: #000;
        background: linear-gradient(90deg, #f2f2f2 0, #f2f2f2 150px, #fff 150px);
        padding: 42px 22px 26px; display: flex; flex-direction: column; box-sizing: border-box;
    }
    .dv-stage .dv-page { box-shadow: 0 6px 24px rgba(0,0,0,0.18); }
    .dv-deco { position: absolute; right: 0; bottom: 0; width: 250px; height: 470px; z-index: 0; pointer-events: none; }
    .dv-page > *:not(.dv-deco) { position: relative; z-index: 1; }
    .dv-foot { margin-top: auto; padding-top: 40px; }
    .dv-page, .dv-page * { font-family: "Times New Roman", Times, serif !important; color: #000; }
    .dv-page .dv-table, .dv-page .dv-table * { font-family: Calibri, Carlito, "Segoe UI", Arial, sans-serif !important; }
    .dv-page .dv-empty { color: #888; }
    .dv-page span { font-weight: inherit; font-style: inherit; }
    .dv-page .dv-td-num { font-weight: bold !important; font-size: 10px !important; }
    .dv-table th, .dv-table td { background: transparent !important; }
    @@media print {
        @@page { size: A4; margin: 0; }
        .dv-stage { overflow: visible; }
        .dv-scale { transform: none !important; width: auto !important; height: auto !important; margin: 0 !important; }
        .dv-stage .dv-page { box-shadow: none; min-height: 1122px; }
        .dv-page { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
@endif
</style>
@endonce
@if(!$dvPdf)
@once
<script>
    window.dvFit = function () {
        document.querySelectorAll('.dv-stage').forEach(function (stage) {
            const scale = stage.querySelector('.dv-scale');
            const page = scale.querySelector('.dv-page');
            const s = Math.min(1, stage.clientWidth / 794);
            scale.style.width = '794px';
            scale.style.transform = 'scale(' + s + ')';
            scale.style.height = (page.offsetHeight * s) + 'px';
            scale.style.marginLeft = Math.max(0, (stage.clientWidth - 794 * s) / 2) + 'px';
        });
    };
    window.addEventListener('resize', window.dvFit);
    window.addEventListener('load', window.dvFit);
    document.addEventListener('DOMContentLoaded', window.dvFit);
</script>
@endonce
<div class="dv-stage"><div class="dv-scale">
<div class="dv-page"@if(!empty($devisPrintId)) id="{{ $devisPrintId }}"@endif>
    <svg class="dv-deco" viewBox="0 0 250 470" preserveAspectRatio="none" aria-hidden="true">
        <defs>
            <linearGradient id="dvG1" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#cfcfcf" stop-opacity=".75"/><stop offset="1" stop-color="#e6e6e6" stop-opacity=".35"/></linearGradient>
            <linearGradient id="dvG2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#b5b5b5" stop-opacity=".8"/><stop offset="1" stop-color="#d4d4d4" stop-opacity=".4"/></linearGradient>
            <linearGradient id="dvG3" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#d9a57c" stop-opacity=".85"/><stop offset="1" stop-color="#ecd2bd" stop-opacity=".45"/></linearGradient>
        </defs>
        <polygon points="8,0 92,58 132,470 48,470" fill="url(#dvG1)"/>
        <polygon points="92,28 160,78 196,470 128,470" fill="url(#dvG2)"/>
        <polygon points="160,74 232,126 250,470 196,470" fill="url(#dvG3)"/>
        <polygon points="216,60 250,84 250,470 238,470" fill="url(#dvG1)"/>
    </svg>
@else
    <div class="dv-band"></div>
    <img class="dv-deco" src="{{ $dvDeco }}" alt="">
    <div class="dv-foot">
        <div class="dv-foot-rule"></div>
        <div class="dv-foot-text">{!! nl2br(e($dvPied)) !!}</div>
    </div>
    <div class="dv-content">
@endif

    <table class="dv-head">
        <tr>
            <td class="dv-logo">
                @if(!empty($dvLogo))
                    <img src="{{ $dvLogo }}" alt=""@if($dvPdf && !empty($logo_img_pdf_dims['w'])) width="{{ (int) $logo_img_pdf_dims['w'] }}" height="{{ (int) $logo_img_pdf_dims['h'] }}"@endif>
                @endif
            </td>
            <td class="dv-brand">
                <div class="dv-brand-title">{{ $dvTitre }}</div>
                <div class="dv-brand-activite">{{ $dvActivite }}</div>
            </td>
        </tr>
    </table>

    <div class="dv-meta">
        <div><span>Date</span>: {{ $devi->date_emission->format('d/m/Y') }}</div>
        <div><span>Devis N°</span>: {{ $devi->numero_devis }}</div>
        @if($dvClient !== '')
            <div><span>Client</span>: {{ $dvClient }}</div>
        @endif
        @if($dvObjet !== '')
            <div><span>Objet</span>: {{ $dvObjet }}</div>
        @endif
    </div>

    <table class="dv-table">
        <colgroup>
            <col style="width:19%"><col style="width:36%"><col style="width:6.5%"><col style="width:7.5%"><col style="width:4.5%"><col style="width:12.3%"><col style="width:14.2%">
        </colgroup>
        <thead>
            <tr>
                <th class="dv-th-type">TYPE</th>
                <th class="dv-th-top">DESIGNATION</th>
                <th class="dv-th-bottom">LAR</th>
                <th class="dv-th-bottom">HAUT</th>
                <th class="dv-th-bottom">QTE</th>
                <th class="dv-th-top">PU HT</th>
                <th class="dv-th-top">PRIX TOTAL HT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($devi->lignes as $i => $ligne)
                @php $dvImg = $dvImage($ligne); @endphp
                <tr>
                    <td class="dv-td-type">@if($dvImg)<img src="{{ $dvImg }}" alt="">@endif<span class="dv-td-num">{{ $i + 1 }}</span></td>
                    <td class="dv-td-desig">{!! nl2br(e($ligne->designation)) !!}</td>
                    <td>{{ $dvDim($ligne->largeur) }}</td>
                    <td>{{ $dvDim($ligne->hauteur) }}</td>
                    <td class="dv-td-qte">{{ (float) $ligne->quantite ? $dvQte($ligne->quantite) : '' }}</td>
                    <td class="dv-td-money">{{ (float) $ligne->prix_unitaire_ht ? $dvMoney($ligne->prix_unitaire_ht) : '' }}</td>
                    <td class="dv-td-money">{{ (float) $ligne->montant_ht ? $dvMoney($ligne->montant_ht) : '' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="dv-empty">Aucune ligne</td></tr>
            @endforelse
            <tr class="dv-row-spacer"><td colspan="2"></td><td colspan="2"></td><td colspan="3"></td></tr>
            <tr class="dv-row-total">
                <td colspan="2" class="dv-total-label">MONTANT TOTAL HT</td>
                <td colspan="2"></td>
                <td colspan="3" class="dv-total-value">{{ $dvMoney($devi->montant_ht) }}</td>
            </tr>
            <tr class="dv-row-total">
                <td colspan="2" class="dv-total-label">TVA {{ $dvTaux }}%</td>
                <td colspan="2"></td>
                <td colspan="3" class="dv-total-value">{{ $dvMoney($devi->montant_tva) }}</td>
            </tr>
            <tr class="dv-row-total">
                <td colspan="2" class="dv-total-label">MONTANT TOTAL TTC</td>
                <td colspan="2"></td>
                <td colspan="3" class="dv-total-value">{{ $dvMoney($devi->montant_ttc) }}</td>
            </tr>
        </tbody>
    </table>

@if(!$dvPdf)
    <div class="dv-foot">
        <div class="dv-foot-rule"></div>
        <div class="dv-foot-text">{!! nl2br(e($dvPied)) !!}</div>
    </div>
</div>
</div></div>
@else
    </div>
@endif
