@extends('user.master')
@section('content')
@php
    $lignesOld = old('lignes');
    if (!is_array($lignesOld) || count($lignesOld) === 0) {
        $lignesOld = [['reference_produit' => '', 'designation' => '', 'quantite' => '1', 'unite' => 'pièce']];
    }
    $numeroPreviewBl = 'BL-'.date('Y-m').'-…';
@endphp
@include('user.bons-livraison.partials.live-preview-styles')
<style>
    .bl-hero { background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); border-radius: 16px; padding: 1.5rem; color: #fff; margin-bottom: 1.5rem; }
    .bl-card { border-radius: 14px; box-shadow: 0 8px 32px rgba(8,145,178,0.1); border: 1px solid #e0f2fe; }
    .bl-ligne-row { background: #f8fafc; border-radius: 10px; padding: 0.75rem; margin-bottom: 0.5rem; border: 1px solid #e2e8f0; }
</style>
<div class="section" style="max-width: 1320px">
    <div class="bl-hero d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0"><i class="bi bi-truck me-2"></i>Nouveau bon de livraison</h4>
            <p class="mb-0 small opacity-90">Saisie à gauche, <strong>aperçu live</strong> à droite (comme pour les devis).</p>
        </div>
        <a href="{{ route('bons-livraison.index') }}" class="btn btn-light btn-sm">Retour liste</a>
    </div>
    @include('user.partials.flash')
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('bons-livraison.store') }}" method="post" id="form-bl">
        @csrf
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <div class="card bl-card mb-4">
                    <div class="card-body">
                        <h6 class="text-primary mb-3"><i class="bi bi-info-circle me-1"></i>En-tête</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Client <span class="text-danger">*</span></label>
                                <select name="client_id" class="form-select" required>
                                    <option value="">— Choisir —</option>
                                    @foreach($clients as $c)
                                        <option value="{{ $c->id }}" @selected((string) old('client_id', $preselectedClientId ?? '') === (string) $c->id)>{{ $c->nom }}@if($c->prenom) – {{ $c->prenom }}@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date_bl" class="form-control" value="{{ old('date_bl', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Statut</label>
                                <select name="statut" class="form-select">
                                    @foreach(['brouillon' => 'Brouillon', 'valide' => 'Validé', 'annule' => 'Annulé'] as $v => $lab)
                                        <option value="{{ $v }}" @selected(old('statut', 'brouillon') === $v)>{{ $lab }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lieu de livraison</label>
                                <input type="text" name="lieu" class="form-control" value="{{ old('lieu') }}" placeholder="Ville, site…">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">N° commande</label>
                                <input type="text" name="numero_commande" class="form-control" value="{{ old('numero_commande') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">N° client (réf. externe)</label>
                                <input type="text" name="reference_client" class="form-control" value="{{ old('reference_client') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact client</label>
                                <input type="text" name="contact_client" class="form-control" value="{{ old('contact_client') }}" placeholder="Nom du contact chez le client">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Émis par</label>
                                <input type="text" name="emis_par" class="form-control" value="{{ old('emis_par', trim((Auth::user()->prenom ?? '').' '.(Auth::user()->nom ?? ''))) }}" placeholder="Nom du responsable expédition">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reçu le (facultatif)</label>
                                <input type="date" name="recu_le" class="form-control" value="{{ old('recu_le') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Livré le (facultatif)</label>
                                <input type="date" name="livre_le" class="form-control" value="{{ old('livre_le') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Informations additionnelles</label>
                                <textarea name="informations_additionnelles" class="form-control" rows="2" placeholder="Mentions, consignes, SAV…">{{ old('informations_additionnelles') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bl-card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-primary"><i class="bi bi-list-ul me-1"></i>Lignes</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="btn-bl-add-ligne" title="Ajouter une ligne"><i class="bi bi-plus-lg"></i></button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle" id="table-bl-lignes">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:12%">Réf. produit</th>
                                        <th>Description <span class="text-danger">*</span></th>
                                        <th style="width:12%">Qté <span class="text-danger">*</span></th>
                                        <th style="width:10%">Unité</th>
                                        <th style="width:3rem"></th>
                                    </tr>
                                </thead>
                                <tbody id="bl-lignes-tbody">
                                    @foreach($lignesOld as $i => $lig)
                                        <tr class="bl-ligne-row">
                                            <td><input type="text" name="lignes[{{ $i }}][reference_produit]" class="form-control form-control-sm" value="{{ $lig['reference_produit'] ?? '' }}"></td>
                                            <td><input type="text" name="lignes[{{ $i }}][designation]" class="form-control form-control-sm" value="{{ $lig['designation'] ?? '' }}" required></td>
                                            <td><input type="number" step="0.001" min="0.001" name="lignes[{{ $i }}][quantite]" class="form-control form-control-sm" value="{{ $lig['quantite'] ?? '1' }}" required></td>
                                            <td><input type="text" name="lignes[{{ $i }}][unite]" class="form-control form-control-sm" value="{{ $lig['unite'] ?? 'pièce' }}"></td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-link text-danger p-0 btn-bl-remove-ligne" title="Supprimer"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Enregistrer</button>
                <a href="{{ route('bons-livraison.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>

            @include('user.bons-livraison.partials.live-preview-panel', ['numeroPreview' => $numeroPreviewBl])
        </div>
    </form>
</div>
@push('scripts')
@include('user.bons-livraison.partials.live-preview-script')
<script>
(function () {
    const tbody = document.getElementById('bl-lignes-tbody');
    const btnAdd = document.getElementById('btn-bl-add-ligne');
    let idx = tbody ? tbody.querySelectorAll('tr').length : 0;

    function addRow(data) {
        data = data || { reference_produit: '', designation: '', quantite: '1', unite: 'pièce' };
        const tr = document.createElement('tr');
        tr.className = 'bl-ligne-row';
        tr.innerHTML =
            '<td><input type="text" name="lignes[' + idx + '][reference_produit]" class="form-control form-control-sm" value="' + (data.reference_produit || '').replace(/"/g, '&quot;') + '"></td>' +
            '<td><input type="text" name="lignes[' + idx + '][designation]" class="form-control form-control-sm" value="' + (data.designation || '').replace(/"/g, '&quot;') + '" required></td>' +
            '<td><input type="number" step="0.001" min="0.001" name="lignes[' + idx + '][quantite]" class="form-control form-control-sm" value="' + (data.quantite || '1') + '" required></td>' +
            '<td><input type="text" name="lignes[' + idx + '][unite]" class="form-control form-control-sm" value="' + (data.unite || 'pièce').replace(/"/g, '&quot;') + '"></td>' +
            '<td class="text-end"><button type="button" class="btn btn-link text-danger p-0 btn-bl-remove-ligne" title="Supprimer"><i class="bi bi-trash"></i></button></td>';
        tbody.appendChild(tr);
        idx++;
        window.refreshBlLivePreview?.();
    }

    btnAdd?.addEventListener('click', function () { addRow({}); });
    tbody?.addEventListener('click', function (e) {
        if (e.target.closest('.btn-bl-remove-ligne')) {
            const tr = e.target.closest('tr');
            if (tbody.querySelectorAll('tr').length <= 1) return;
            tr?.remove();
            window.refreshBlLivePreview?.();
        }
    });
})();
</script>
@endpush
@endsection
