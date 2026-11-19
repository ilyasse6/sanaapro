@extends('user.master')
@section('content')
@include('user.credits.partials.styles')
@php
    $dh = fn ($n) => number_format((float) $n, 2, ',', ' ').' DH';
@endphp
<div class="section">
    <div class="cr-hero d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="mb-1"><i class="bi bi-wallet2 me-2"></i>Crédits clients</h1>
            <p class="mb-0 opacity-90">Ce que chaque client doit encore : factures, règlements, retards et chèques en attente.</p>
        </div>
        <a href="{{ route('cheques.index') }}" class="btn btn-light"><i class="bi bi-credit-card-2-front me-1"></i>Suivi des chèques</a>
    </div>

    @include('user.partials.flash')

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="cr-stat danger">
                <div class="lbl">Crédit total</div>
                <div class="val">{{ $dh($stats['credit_total']) }}</div>
                <div class="sub">{{ $stats['nb_debiteurs'] }} client(s) débiteur(s)</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="cr-stat warning">
                <div class="lbl">Montant échu</div>
                <div class="val">{{ $dh($stats['montant_echu']) }}</div>
                <div class="sub">{{ $stats['nb_retard'] }} client(s) en retard</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="cr-stat info">
                <div class="lbl">Chèques non encaissés</div>
                <div class="val">{{ $dh($stats['cheques_en_cours']) }}</div>
                <div class="sub">{{ $stats['nb_cheques_en_cours'] }} chèque(s) en portefeuille ou remis</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="cr-stat success">
                <div class="lbl">Reste réellement à encaisser</div>
                <div class="val">{{ $dh($stats['credit_total'] + $stats['cheques_en_cours']) }}</div>
                <div class="sub">Crédit + chèques en attente</div>
            </div>
        </div>
    </div>

    <div class="cr-card mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="cr-filtres d-flex flex-wrap gap-2">
                @foreach(['debiteurs' => 'Débiteurs', 'retard' => 'En retard', 'tous' => 'Tous les clients'] as $cle => $lib)
                    <a href="{{ route('credits.index', array_filter(['filtre' => $cle, 'q' => $recherche])) }}"
                       class="btn btn-sm {{ $filtre === $cle ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $lib }}</a>
                @endforeach
            </div>
            <form method="get" class="d-flex gap-2">
                <input type="hidden" name="filtre" value="{{ $filtre }}">
                <input type="search" name="q" value="{{ $recherche }}" class="form-control form-control-sm" placeholder="Nom, prénom ou téléphone" style="min-width: 240px">
                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>

    <div class="cr-card">
        <div class="table-responsive">
            <table class="table cr-table mb-0">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th class="text-end">Facturé TTC</th>
                        <th class="text-end">Réglé</th>
                        <th class="text-end">Solde dû</th>
                        <th class="text-end">Échu</th>
                        <th class="text-end">Chèques en attente</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lignes as $l)
                        @php $c = $l['client']; @endphp
                        <tr>
                            <td>
                                <a href="{{ route('credits.show', $c) }}" class="fw-semibold text-decoration-none">{{ trim($c->nom.' '.$c->prenom) }}</a>
                                <div class="small text-muted">
                                    {{ collect([$c->telephone, $l['nb_factures_ouvertes'] ? $l['nb_factures_ouvertes'].' facture(s) ouverte(s)' : null])->filter()->implode(' · ') }}
                                    @if($l['credit_initial'] > 0) · crédit initial {{ $dh($l['credit_initial']) }}@endif
                                </div>
                            </td>
                            <td class="cr-montant">{{ $dh($l['facture'] - $l['avoirs']) }}</td>
                            <td class="cr-montant">{{ $dh($l['regle']) }}</td>
                            <td class="cr-montant {{ $l['solde'] > 0.005 ? 'cr-solde-pos' : ($l['solde'] < -0.005 ? 'cr-solde-neg' : '') }}">
                                {{ $dh($l['solde']) }}
                                @if($l['solde'] < -0.005)<div class="small fw-normal">avance client</div>@endif
                            </td>
                            <td class="cr-montant">
                                @if($l['montant_echu'] > 0)
                                    <span class="text-danger fw-semibold">{{ $dh($l['montant_echu']) }}</span>
                                    <div><span class="badge text-bg-danger">{{ (int) $l['plus_ancienne_echeance']->diffInDays(today()) }} j de retard</span></div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="cr-montant">
                                @if($l['nb_cheques_en_cours'])
                                    {{ $dh($l['cheques_en_cours']) }}
                                    <div class="small text-muted">{{ $l['nb_cheques_en_cours'] }} chèque(s)</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('credits.show', $c) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Détail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-emoji-smile fs-3 d-block mb-2"></i>
                            {{ $filtre === 'debiteurs' ? 'Aucun client ne vous doit d’argent.' : 'Aucun client trouvé.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
