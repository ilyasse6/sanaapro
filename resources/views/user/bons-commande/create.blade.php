@extends('user.master')
@section('content')
@php
    $lignesOld = old('lignes');
    if (!is_array($lignesOld) || count($lignesOld) === 0) {
        $lignesOld = [['reference_produit' => '', 'designation' => '', 'quantite' => '1', 'unite' => 'pièce', 'prix_unitaire_ht' => '']];
    }
    $numeroPreviewBc = 'BC-'.date('Y-m').'-…';
@endphp
@include('user.bons-commande.partials.live-preview-styles')
<style>
    .bc-hero { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); border-radius: 16px; padding: 1.5rem; color: #fff; margin-bottom: 1.5rem; }
    .bc-card { border-radius: 14px; box-shadow: 0 8px 32px rgba(124,58,237,0.1); border: 1px solid #ede9fe; }
    .bc-ligne-row { background: #faf5ff; border-radius: 10px; padding: 0.75rem; margin-bottom: 0.5rem; border: 1px solid #e9d5ff; }
</style>
<div class="section" style="max-width: 1320px">
    <div class="bc-hero d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0"><i class="bi bi-bag-plus me-2"></i>Nouveau bon de commande</h4>
            <p class="mb-0 small opacity-90">Saisie à gauche, <strong>aperçu live</strong> à droite.</p>
        </div>
        <a href="{{ route('bons-commande.index') }}" class="btn btn-light btn-sm">Retour liste</a>
    </div>
    @include('user.partials.flash')
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('bons-commande.store') }}" method="post" id="form-bc">
        @csrf
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <div class="card bc-card mb-4">
                    <div class="card-body">
                        <h6 class="text-primary mb-3"><i class="bi bi-info-circle me-1"></i>En-tête</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fournisseur <span class="text-danger">*</span></label>
                                <select name="fournisseur_id" class="form-select" required>
                                    <option value="">— Choisir —</option>
                                    @foreach($fournisseurs as $f)
                                        <option value="{{ $f->id }}" @selected((string) old('fournisseur_id', $preselectedFournisseurId ?? '') === (string) $f->id)>{{ $f->raison_sociale }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date_bc" class="form-control" value="{{ old('date_bc', date('Y-m-d')) }}" required>
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
                                <input type="text" name="lieu_livraison" class="form-control" value="{{ old('lieu_livraison') }}" placeholder="Adresse ou site…">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Réf. interne</label>
                                <input type="text" name="reference_interne" class="form-control" value="{{ old('reference_interne') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Réf. fournisseur</label>
                                <input type="text" name="reference_fournisseur" class="form-control" value="{{ old('reference_fournisseur') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact fournisseur</label>
                                <input type="text" name="contact_fournisseur" class="form-control" value="{{ old('contact_fournisseur') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Livraison souhaitée</label>
                                <input type="date" name="date_livraison_souhaitee" class="form-control" value="{{ old('date_livraison_souhaitee') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Émis par</label>
                                <input type="text" name="emis_par" class="form-control" value="{{ old('emis_par', trim((Auth::user()->prenom ?? '').' '.(Auth::user()->nom ?? ''))) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Informations additionnelles</label>
                                <textarea name="informations_additionnelles" class="form-control" rows="2" placeholder="Conditions, mentions…">{{ old('informations_additionnelles') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bc-card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-primary"><i class="bi bi-list-ul me-1"></i>Lignes</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="btn-bc-add-ligne" title="Ajouter une ligne"><i class="bi bi-plus-lg"></i></button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle" id="table-bc-lignes">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:11%">Réf. produit</th>
                                        <th>Description <span class="text-danger">*</span></th>
                                        <th style="width:10%">Qté <span class="text-danger">*</span></th>
                                        <th style="width:9%">Unité</th>
                                        <th style="width:10%">PU HT</th>
                                        <th style="width:3rem"></th>
                                    </tr>
                                </thead>
                                <tbody id="bc-lignes-tbody">
                                    @foreach($lignesOld as $i => $lig)
                                        <tr class="bc-ligne-row">
                                            <td><input type="text" name="lignes[{{ $i }}][reference_produit]" class="form-control form-control-sm" value="{{ $lig['reference_produit'] ?? '' }}"></td>
                                            <td><input type="text" name="lignes[{{ $i }}][designation]" class="form-control form-control-sm" value="{{ $lig['designation'] ?? '' }}" required></td>
                                            <td><input type="number" step="0.001" min="0.001" name="lignes[{{ $i }}][quantite]" class="form-control form-control-sm" value="{{ $lig['quantite'] ?? '1' }}" required></td>
                                            <td><input type="text" name="lignes[{{ $i }}][unite]" class="form-control form-control-sm" value="{{ $lig['unite'] ?? 'pièce' }}"></td>
                                            <td><input type="number" step="0.01" min="0" name="lignes[{{ $i }}][prix_unitaire_ht]" class="form-control form-control-sm" value="{{ $lig['prix_unitaire_ht'] ?? '' }}" placeholder="opt."></td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-link text-danger p-0 btn-bc-remove-ligne" title="Supprimer"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Enregistrer</button>
                <a href="{{ route('bons-commande.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>

            @include('user.bons-commande.partials.live-preview-panel', ['numeroPreview' => $numeroPreviewBc])
        </div>
    </form>
</div>
@push('scripts')
@include('user.bons-commande.partials.live-preview-script')
<script>
(function () {
    const tbody = document.getElementById('bc-lignes-tbody');
    const btnAdd = document.getElementById('btn-bc-add-ligne');
    let idx = tbody ? tbody.querySelectorAll('tr').length : 0;

    function addRow(data) {
        data = data || { reference_produit: '', designation: '', quantite: '1', unite: 'pièce', prix_unitaire_ht: '' };
        const tr = document.createElement('tr');
        tr.className = 'bc-ligne-row';
        const pu = (data.prix_unitaire_ht !== undefined && data.prix_unitaire_ht !== null) ? String(data.prix_unitaire_ht).replace(/"/g, '&quot;') : '';
        tr.innerHTML =
            '<td><input type="text" name="lignes[' + idx + '][reference_produit]" class="form-control form-control-sm" value="' + (data.reference_produit || '').replace(/"/g, '&quot;') + '"></td>' +
            '<td><input type="text" name="lignes[' + idx + '][designation]" class="form-control form-control-sm" value="' + (data.designation || '').replace(/"/g, '&quot;') + '" required></td>' +
            '<td><input type="number" step="0.001" min="0.001" name="lignes[' + idx + '][quantite]" class="form-control form-control-sm" value="' + (data.quantite || '1') + '" required></td>' +
            '<td><input type="text" name="lignes[' + idx + '][unite]" class="form-control form-control-sm" value="' + (data.unite || 'pièce').replace(/"/g, '&quot;') + '"></td>' +
            '<td><input type="number" step="0.01" min="0" name="lignes[' + idx + '][prix_unitaire_ht]" class="form-control form-control-sm" value="' + pu + '" placeholder="opt."></td>' +
            '<td class="text-end"><button type="button" class="btn btn-link text-danger p-0 btn-bc-remove-ligne" title="Supprimer"><i class="bi bi-trash"></i></button></td>';
        tbody.appendChild(tr);
        idx++;
        window.refreshBcLivePreview?.();
    }

    btnAdd?.addEventListener('click', function () { addRow({}); });
    tbody?.addEventListener('click', function (e) {
        if (e.target.closest('.btn-bc-remove-ligne')) {
            const tr = e.target.closest('tr');
            if (tbody.querySelectorAll('tr').length <= 1) return;
            tr?.remove();
            window.refreshBcLivePreview?.();
        }
    });
})();
</script>
@endpush
@endsection
