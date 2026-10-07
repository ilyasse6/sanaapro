<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $bonLivraison->numero }}</title>
    @include('user.bons-livraison.partials.styles-bl-dompdf')
</head>
<body>
@php
    $pdf_render = true;
@endphp
<div style="margin-bottom: 12px;">
    @if(!empty($logo_src_for_pdf))
        <img src="{{ $logo_src_for_pdf }}" alt="" class="bl-logo" @if(isset($logo_img_pdf_dims['w'], $logo_img_pdf_dims['h'])) width="{{ (int) $logo_img_pdf_dims['w'] }}" height="{{ (int) $logo_img_pdf_dims['h'] }}" @endif>
    @endif
</div>
@include('user.bons-livraison.partials.document-bl', [
    'bonLivraison' => $bonLivraison,
    'societe' => $societe,
    'pdf_render' => true,
    'logo_src_for_pdf' => $logo_src_for_pdf ?? null,
    'logo_img_pdf_dims' => $logo_img_pdf_dims ?? null,
])
</body>
</html>
