<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $facture->numero_facture }}</title>
    <style>
        @page { margin: 0; }
        html, body { margin: 0; padding: 0; }
    </style>
</head>
<body>
@include('user.facture.partials.document-afifalu', [
    'facture' => $facture,
    'societe' => $societe,
    'logo_src_for_pdf' => $logo_src_for_pdf ?? null,
    'logo_img_pdf_dims' => $logo_img_pdf_dims ?? null,
    'pdf_render' => true,
])
</body>
</html>
