<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $bonLivraison->numero }}</title>
</head>
<body>
@include('user.bons-livraison.partials.document-afifalu', [
    'bonLivraison' => $bonLivraison,
    'societe' => $societe,
    'logo_src_for_pdf' => $logo_src_for_pdf ?? null,
    'logo_img_pdf_dims' => $logo_img_pdf_dims ?? null,
    'pdf_render' => true,
])
</body>
</html>
