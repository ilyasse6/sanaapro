@extends('user.master')
@section('content')
<div class="section">
    <div class="d-flex justify-content-between mb-3"><h1 class="pagetitle mb-0">Mouvements comptables</h1><a href="{{ route('compta.index') }}" class="btn btn-outline-secondary btn-sm">Synthèse</a></div>
    @include('user.partials.flash')
    @if(Auth::user()->hasPermission('Gestion de la Comptabilité', 'ajouter'))
    <div class="card mb-3"><div class="card-body"><h6>Nouvelle écriture</h6>
        <form method="post" action="{{ route('compta.mouvements.store') }}" class="row g-2 small">@csrf
            <div class="col-md-2"><label class="form-label">Type</label><select name="type" class="form-select form-select-sm">@foreach(['entree','depense','paiement_client','paiement_fournisseur','credit','salaire'] as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Montant</label><input type="number" step="0.01" name="montant" class="form-control form-control-sm" required></div>
            <div class="col-md-2"><label class="form-label">Date</label><input type="date" name="date_operation" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}" required></div>
            <div class="col-md-3"><label class="form-label">Libellé</label><input name="libelle" class="form-control form-control-sm" required></div>
            <div class="col-md-2"><label class="form-label">Mode paiement</label><input name="mode_paiement" class="form-control form-control-sm" placeholder="virement…"></div>
            <div class="col-md-3"><label class="form-label">Client</label><select name="client_id" class="form-select form-select-sm"><option value="">—</option>@foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->nom }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label">Fournisseur</label><select name="fournisseur_id" class="form-select form-select-sm"><option value="">—</option>@foreach($fournisseurs as $f)<option value="{{ $f->id }}">{{ $f->raison_sociale }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label">Facture (paiement client)</label><select name="facture_id" class="form-select form-select-sm"><option value="">—</option>@foreach($factures as $fac)<option value="{{ $fac->id }}">{{ $fac->numero_facture }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label">Employé (salaire)</label><select name="employe_id" class="form-select form-select-sm"><option value="">—</option>@foreach($employes as $e)<option value="{{ $e->id }}">{{ $e->nom }} {{ $e->prenom }}</option>@endforeach</select></div>
            <div class="col-12"><label class="form-label">Notes</label><input name="notes" class="form-control form-control-sm"></div>
            <div class="col-12"><button class="btn btn-sm btn-primary">Enregistrer</button></div>
        </form>
    </div></div>
    @endif
    <div class="card"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Type</th><th>Libellé</th><th>Montant</th><th>Lien</th></tr></thead><tbody>
        @foreach($mouvements as $m)
            <tr><td>{{ $m->date_operation->format('d/m/Y') }}</td><td>{{ $m->type_label }}</td><td>{{ $m->libelle }}</td><td>{{ number_format($m->montant, 2, ',', ' ') }}</td>
                <td class="small">@if($m->facture)<a href="{{ route('factures.show', $m->facture) }}">Facture</a>@endif @if($m->client){{ $m->client->nom }}@endif</td></tr>
        @endforeach
    </tbody></table></div>{{ $mouvements->links() }}</div>
</div>
@endsection
