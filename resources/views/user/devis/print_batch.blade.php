@extends('user.master')

@section('content')
@include('user.devis.partials.styles-devis-ae')
<style>
    .devis-lot-wrap { margin-bottom: 2rem; }
    .devis-lot-wrap:not(:last-child) { page-break-after: always; break-after: page; }
</style>

<div class="section">
    <div class="devis-lot-actions devis-actions d-flex flex-wrap gap-2 mb-3">
        <a href="{{ $retourListe }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour à la liste</a>
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimer</button>
    </div>

    @foreach($items as $item)
        <div class="devis-lot-wrap">
            @include('user.devis.partials.document-afifalu', $item)
        </div>
    @endforeach
</div>

<script>
window.addEventListener('load', function () {
    setTimeout(function () { window.print(); }, 400);
});
</script>
@endsection
