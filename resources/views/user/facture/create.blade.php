@extends('user.master')

@section('content')
<style>
    .devis-form-hero { background: linear-gradient(135deg, #17324D 0%, #0f2438 100%); color: white; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; }
</style>

<div class="section">
    <div class="devis-form-hero">
        <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Nouvelle facture</h4>
        <p class="mb-0 opacity-90 small">Une ligne : désignation et total TTC. Le HT, la TVA et le montant en lettres sont calculés automatiquement.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('user.facture.partials.form', [
        'facture' => null,
        'action' => route('factures.store'),
        'method' => 'POST',
    ])
</div>
@endsection
