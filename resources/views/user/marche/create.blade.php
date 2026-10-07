@extends('user.master')
@section('content')
@include('user.marche.partials.styles')
@php
    $statuts = [
        'brouillon' => 'Brouillon',
        'en_cours' => 'En cours',
        'attribue' => 'Attribué',
        'execution' => 'Exécution',
        'cloture' => 'Clôturé',
        'annule' => 'Annulé',
    ];
@endphp

<div class="section">
    <div class="marche-hero">
        <div class="marche-hero-content d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-plus-circle me-2"></i>Nouveau marché</h1>
                <p>1) Importez l’URL du portail · 2) Vérifiez les infos essentielles · 3) Enregistrez. Lots, cautions et documents s’ajoutent ensuite sur la fiche.</p>
            </div>
            <a href="{{ route('marches.index') }}" class="btn btn-marche btn-marche-outline">
                <i class="bi bi-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    @include('user.partials.flash')

    <div class="marche-import-card">
        <div class="card-marche-title mb-2">
            <i class="bi bi-cloud-download"></i> Import automatique
        </div>
        <p class="text-muted small mb-3">
            Collez l’URL de la page <strong>Détails consultation</strong> sur
            <a href="https://www.marchespublics.gov.ma/" target="_blank" rel="noopener">marchespublics.gov.ma</a>,
            puis cliquez sur « Remplir le formulaire ».
        </p>
        <div class="row g-2 align-items-end">
            <div class="col">
                <label class="form-label small mb-1 fw-semibold">Lien de la consultation</label>
                <input type="url" id="url_import_consultation" class="form-control marche-input" placeholder="https://www.marchespublics.gov.ma/index.php?page=entreprise.EntrepriseDetailsConsultation&..." autocomplete="off">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-marche btn-marche-primary" id="btn-import-mp">
                    <span class="label-import"><i class="bi bi-arrow-down-circle me-1"></i>Remplir le formulaire</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
        <div id="import-mp-alert" class="alert alert-danger small mt-3 d-none mb-0" role="alert"></div>
    </div>

    <div class="card-marche">
        <div class="card-marche-body">
            <div class="card-marche-title">
                <i class="bi bi-list-check"></i> Infos essentielles
            </div>
            <p class="text-muted small mb-3">Ce qui suffit pour suivre le marché au quotidien. Le reste (détails portail) est optionnel.</p>
            <form method="post" action="{{ route('marches.store') }}" id="form-marche-create">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Intitulé *</label>
                        <input type="text" name="intitule" class="form-control marche-input @error('intitule') is-invalid @enderror" value="{{ old('intitule') }}" required id="field_intitule" placeholder="Ex. AO fournitures — DR Santé…">
                        @error('intitule')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-8">
                        <label class="form-label fw-semibold" for="field_maitre_ouvrage">Maître d’ouvrage</label>
                        <textarea name="acheteur_public" id="field_maitre_ouvrage" class="form-control marche-textarea" rows="2" placeholder="Organisme / acheteur public">{{ old('acheteur_public') }}</textarea>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label fw-semibold">Montant estimé (MAD)</label>
                        <input type="number" step="0.01" name="montant_estime" class="form-control marche-input" value="{{ old('montant_estime') }}" placeholder="0,00">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Statut</label>
                        <select name="statut" class="form-select marche-select">
                            @foreach($statuts as $s => $label)
                                <option value="{{ $s }}" @selected(old('statut','en_cours')==$s)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ouverture</label>
                        <input type="date" name="date_ouverture" class="form-control marche-input" value="{{ old('date_ouverture') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Échéance suivi interne</label>
                        <input type="date" name="date_limite" id="field_date_limite" class="form-control marche-input" value="{{ old('date_limite') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes internes</label>
                        <textarea name="notes" class="form-control marche-textarea" rows="2" placeholder="Rappels, responsable, remarques…">{{ old('notes') }}</textarea>
                    </div>
                </div>

                @include('user.marche.partials.form-portail-fields', ['marche' => null])

                <div class="d-flex flex-wrap gap-2 pt-1 border-top pt-3">
                    <button type="submit" class="btn btn-marche btn-marche-primary">
                        <i class="bi bi-check-lg me-1"></i>Créer le marché
                    </button>
                    <a href="{{ route('marches.index') }}" class="btn btn-outline-secondary rounded-pill px-3">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
(function () {
    const btn = document.getElementById('btn-import-mp');
    const urlInput = document.getElementById('url_import_consultation');
    const alertBox = document.getElementById('import-mp-alert');
    const form = document.getElementById('form-marche-create');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function setField(name, val) {
        if (val === undefined || val === null) return;
        const el = form.querySelector('[name="' + name + '"]');
        if (el) el.value = val;
    }

    btn?.addEventListener('click', async function () {
        const url = urlInput.value.trim();
        if (!url) {
            alertBox.textContent = 'Veuillez coller l’URL de la consultation.';
            alertBox.classList.remove('d-none');
            return;
        }
        alertBox.classList.add('d-none');
        btn.disabled = true;
        btn.querySelector('.label-import')?.classList.add('d-none');
        btn.querySelector('.spinner-border')?.classList.remove('d-none');

        try {
            const res = await fetch(@json(route('marches.import-consultation')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ url: url })
            });
            const json = await res.json();
            if (!json.ok) {
                alertBox.textContent = json.error || 'Import impossible.';
                alertBox.classList.remove('d-none');
                return;
            }
            const d = json.data || {};
            setField('intitule', d.intitule);
            setField('objet', d.objet);
            setField('date_limite', d.date_limite || '');
            setField('date_limite_plis', d.date_limite_plis || '');
            setField('reference_consultation', d.reference_consultation);
            setField('acheteur_public', d.acheteur_public);
            setField('type_annonce', d.type_annonce);
            setField('procedure_text', d.procedure_text);
            setField('categorie_principale', d.categorie_principale);
            setField('allotissement', d.allotissement);
            setField('lieu_execution', d.lieu_execution);
            setField('domaines_activite', d.domaines_activite);
            setField('adresse_retrait_dossiers', d.adresse_retrait_dossiers);
            setField('adresse_depot_offres', d.adresse_depot_offres);
            setField('lieu_ouverture_plis', d.lieu_ouverture_plis);
            if (d.prix_acquisition_plans !== undefined && d.prix_acquisition_plans !== null)
                setField('prix_acquisition_plans', d.prix_acquisition_plans);
            setField('contact_nom', d.contact_nom);
            setField('contact_email', d.contact_email);
            setField('contact_telephone', d.contact_telephone);
            setField('contact_fax', d.contact_fax);
            setField('source_url', d.source_url || url);
            const statutEl = form.querySelector('[name="statut"]');
            if (statutEl && (!statutEl.value || statutEl.value === 'brouillon')) statutEl.value = 'en_cours';
            const portail = document.getElementById('collapsePortailFields');
            if (portail && typeof bootstrap !== 'undefined') {
                bootstrap.Collapse.getOrCreateInstance(portail, { toggle: false }).show();
            }
        } catch (e) {
            alertBox.textContent = 'Erreur réseau : ' + (e.message || '');
            alertBox.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.querySelector('.label-import')?.classList.remove('d-none');
            btn.querySelector('.spinner-border')?.classList.add('d-none');
        }
    });
})();
</script>
@endpush
@endsection
