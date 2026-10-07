@extends('user.master')

@section('content')
@include('user.devis.partials.styles-devis-ae')
<style>
    .facture-lot-wrap { margin-bottom: 2rem; }
    .facture-lot-wrap:not(:last-child) { page-break-after: always; break-after: page; }
    @@media print { .facture-lot-wrap { margin-bottom: 0; } }
</style>

<div class="section">
    <div class="facture-lot-actions facture-actions devis-actions d-flex flex-wrap gap-2 mb-3">
        <a href="{{ $retourListe }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour à la liste</a>
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimer</button>
    </div>

    @foreach($items as $item)
        <div class="facture-lot-wrap">
            @include('user.facture.partials.document-afifalu', [
                'facture' => $item['facture'],
                'societe' => $item['societe'],
                'facturePrintId' => 'facturePrint'.$loop->iteration,
            ])
        </div>
    @endforeach
</div>

<script>
window.addEventListener('load', function () {
    setTimeout(function () { window.print(); }, 400);
});
</script>
@endsection
