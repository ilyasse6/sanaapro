@php
    $m = $marche ?? null;
    $v = function (string $key) use ($m) {
        return old($key, $m?->$key);
    };
    $dtLimitePils = old('date_limite_plis', $m && $m->date_limite_plis ? $m->date_limite_plis->format('Y-m-d\TH:i') : '');
    $hasPortailData = filled($v('reference_consultation')) || filled($v('objet')) || filled($v('source_url'));
@endphp

<div class="marche-portail-collapse mb-4">
    <button class="btn btn-link text-decoration-none w-100 text-start p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePortailFields" aria-expanded="{{ $hasPortailData ? 'true' : 'false' }}" aria-controls="collapsePortailFields" id="btn-toggle-portail">
        <div class="d-flex align-items-center justify-content-between gap-2 py-2 px-3 rounded-3" style="background: rgba(30,64,175,0.06); border: 1px solid rgba(30,64,175,0.12);">
            <span class="fw-semibold" style="color: var(--prowise-blue-dark, #0f2438);">
                <i class="bi bi-building me-2"></i>Détails portail (marchespublics.gov.ma)
                <span class="text-muted fw-normal small ms-1">— optionnel, rempli par l’import</span>
            </span>
            <i class="bi bi-chevron-down" id="icon-portail-chevron"></i>
        </div>
    </button>
    <div class="collapse {{ $hasPortailData ? 'show' : '' }}" id="collapsePortailFields">
        <fieldset class="marche-fieldset border rounded-3 p-3 mt-2">
            <p class="text-muted small mb-3">Ces champs correspondent à la fiche officielle. L’import les remplit automatiquement.</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Référence (portail)</label>
                    <input type="text" name="reference_consultation" class="form-control marche-input" value="{{ $v('reference_consultation') }}" maxlength="120" placeholder="ex. 06/2026/DRSPSRO">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date et heure limite de remise des plis</label>
                    <input type="datetime-local" name="date_limite_plis" class="form-control marche-input" value="{{ $dtLimitePils }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Objet</label>
                    <textarea name="objet" class="form-control marche-textarea" rows="3" placeholder="Objet tel qu’affiché sur le portail">{{ $v('objet') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Type d’annonce</label>
                    <input type="text" name="type_annonce" class="form-control marche-input" value="{{ $v('type_annonce') }}" maxlength="255">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Catégorie principale</label>
                    <input type="text" name="categorie_principale" class="form-control marche-input" value="{{ $v('categorie_principale') }}" maxlength="120">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Procédure</label>
                    <textarea name="procedure_text" class="form-control marche-textarea" rows="2" placeholder="Appel d’offres ouvert | Sur offre de prix">{{ $v('procedure_text') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Allotissement</label>
                    <input type="text" name="allotissement" class="form-control marche-input" value="{{ $v('allotissement') }}" maxlength="120" placeholder="ex. 5 Lots">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Prix d’acquisition des plans (MAD)</label>
                    <input type="number" step="0.01" name="prix_acquisition_plans" class="form-control marche-input" value="{{ $v('prix_acquisition_plans') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Lieu d’exécution</label>
                    <textarea name="lieu_execution" class="form-control marche-textarea" rows="2">{{ $v('lieu_execution') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Domaines d’activité</label>
                    <textarea name="domaines_activite" class="form-control marche-textarea" rows="2">{{ $v('domaines_activite') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Adresse de retrait des dossiers</label>
                    <textarea name="adresse_retrait_dossiers" class="form-control marche-textarea" rows="2">{{ $v('adresse_retrait_dossiers') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Adresse de dépôt des offres</label>
                    <textarea name="adresse_depot_offres" class="form-control marche-textarea" rows="2">{{ $v('adresse_depot_offres') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Lieu d’ouverture des plis</label>
                    <textarea name="lieu_ouverture_plis" class="form-control marche-textarea" rows="2">{{ $v('lieu_ouverture_plis') }}</textarea>
                </div>
                <div class="col-12"><h6 class="small text-uppercase mt-2 mb-0" style="color: var(--prowise-blue-dark, #0f2438); letter-spacing: 0.04em;">Contact administratif</h6></div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nom / intitulé</label>
                    <input type="text" name="contact_nom" class="form-control marche-input" value="{{ $v('contact_nom') }}" maxlength="255">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">E-mail</label>
                    <input type="email" name="contact_email" class="form-control marche-input" value="{{ $v('contact_email') }}" maxlength="255">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Téléphone</label>
                    <input type="text" name="contact_telephone" class="form-control marche-input" value="{{ $v('contact_telephone') }}" maxlength="80">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fax</label>
                    <input type="text" name="contact_fax" class="form-control marche-input" value="{{ $v('contact_fax') }}" maxlength="80">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Lien source (portail)</label>
                    <input type="url" name="source_url" class="form-control marche-input" value="{{ $v('source_url') }}" placeholder="https://www.marchespublics.gov.ma/...">
                </div>
            </div>
        </fieldset>
    </div>
</div>
