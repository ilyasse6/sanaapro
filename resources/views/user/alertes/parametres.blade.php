@extends('user.master')
@section('content')
@php
    $accentForm = old('document_accent_color', $branding['accent_color'] ?? \App\Support\SocieteBranding::DEFAULT_ACCENT_COLOR);
    if (! is_string($accentForm) || ! preg_match('/^#([0-9A-Fa-f]{6}|[0-9A-Fa-f]{3})$/', $accentForm)) {
        $accentForm = \App\Support\SocieteBranding::DEFAULT_ACCENT_COLOR;
    }
    $textColorForm = old('document_text_color', $branding['text_color'] ?? \App\Support\SocieteBranding::DEFAULT_TEXT_COLOR);
    if (! is_string($textColorForm) || ! preg_match('/^#([0-9A-Fa-f]{6}|[0-9A-Fa-f]{3})$/', $textColorForm)) {
        $textColorForm = \App\Support\SocieteBranding::DEFAULT_TEXT_COLOR;
    }
@endphp

<style>
    .settings-page {
        --settings-blue: #17324D;
        --settings-navy: #0f2438;
        --settings-gold: #198754;
        --settings-muted: #64748b;
        width: 100%;
        max-width: none;
        min-height: 100vh;
        box-sizing: border-box;
    }
    .settings-toolbar {
        display: flex;
        align-items: end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .75rem;
        margin-bottom: .8rem;
    }
    .settings-company {
        min-width: min(100%, 330px);
        max-width: 440px;
    }
    .settings-company label { color: var(--settings-muted); font-size: .7rem; font-weight: 700; margin-bottom: .25rem; }
    .settings-company .form-select {
        border: 1px solid #dfe6f1;
        background-color: #fff;
        border-radius: 11px;
        color: var(--settings-navy);
        font-weight: 600;
    }
    .settings-shell {
        display: block;
        width: 100%;
    }
    .settings-nav {
        display: inline-flex;
        flex-direction: row;
        gap: .35rem;
        background: #fff;
        border: 1px solid rgba(30, 64, 175, .09);
        border-radius: 13px;
        padding: .4rem;
        box-shadow: 0 10px 30px rgba(30, 64, 175, .07);
        position: static;
    }
    .settings-nav .nav-link {
        width: auto;
        border: 0;
        border-radius: 9px;
        color: var(--settings-muted);
        text-align: left;
        font-weight: 600;
        padding: .58rem .85rem;
        display: flex;
        align-items: center;
        gap: .5rem;
        white-space: nowrap;
    }
    .settings-nav .nav-link i { font-size: 1.05rem; }
    .settings-nav .nav-link.active {
        color: #fff !important;
        background: linear-gradient(135deg, var(--settings-blue), var(--settings-navy));
        box-shadow: 0 6px 16px rgba(30, 64, 175, .25);
    }
    .settings-panel {
        width: 100%;
        background: #fff;
        border: 1px solid rgba(30, 64, 175, .09);
        border-radius: 16px;
        padding: 1.35rem;
        box-shadow: 0 10px 30px rgba(30, 64, 175, .07);
        min-height: 520px;
    }
    .settings-title {
        color: var(--settings-navy);
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: .2rem;
    }
    .settings-subtitle { color: var(--settings-muted); font-size: .85rem; margin-bottom: 1.25rem; }
    .settings-section-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        color: var(--settings-navy);
        font-size: .9rem;
        font-weight: 700;
        margin: 0 0 .85rem;
    }
    .settings-section-title i { color: #146c43; }
    .settings-block {
        height: 100%;
        background: #fafbff;
        border: 1px solid #e8edf7;
        border-radius: 13px;
        padding: 1rem;
    }
    .settings-logo-preview {
        min-height: 88px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        background: #fff;
        padding: .75rem;
        margin-bottom: .75rem;
    }
    .settings-logo-preview img { max-width: 220px; max-height: 68px; object-fit: contain; }
    .settings-logo-empty { color: #94a3b8; text-align: center; font-size: .8rem; }
    .settings-page .form-label { color: #334155; font-size: .82rem; font-weight: 600; margin-bottom: .35rem; }
    .settings-page .form-control,
    .settings-page .form-select {
        border-color: #dfe6f1;
        border-radius: 10px;
        padding: .58rem .75rem;
    }
    .settings-page .form-control:focus,
    .settings-page .form-select:focus {
        border-color: var(--settings-blue);
        box-shadow: 0 0 0 3px rgba(30, 64, 175, .11);
    }
    .settings-color-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .7rem .8rem;
        background: #fff;
        border: 1px solid #e8edf7;
        border-radius: 10px;
    }
    .settings-color-row + .settings-color-row { margin-top: .65rem; }
    .settings-color-dot { width: 32px; height: 32px; border-radius: 8px; border: 2px solid #fff; box-shadow: 0 0 0 1px #dbe2ee; }
    .settings-page .form-control-color { width: 42px; height: 36px; padding: .2rem; }
    .settings-save {
        border: 0;
        border-radius: 11px;
        padding: .62rem 1.2rem;
        color: #fff;
        font-weight: 600;
        background: linear-gradient(135deg, var(--settings-blue), var(--settings-navy));
        box-shadow: 0 6px 16px rgba(30, 64, 175, .23);
    }
    .settings-save:hover { color: #fff; transform: translateY(-1px); }
    .alert-setting-card {
        border: 1px solid #e8edf7;
        border-radius: 13px;
        padding: 1rem;
        height: 100%;
        background: #fafbff;
    }
    .alert-setting-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: rgba(30, 64, 175, .09);
        color: var(--settings-blue);
        font-size: 1.15rem;
        margin-bottom: .8rem;
    }
    .settings-switch {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border: 1px solid #e8edf7;
        border-radius: 13px;
        padding: 1rem;
        background: #fafbff;
    }
    .settings-switch .form-check-input { width: 2.8rem; height: 1.45rem; cursor: pointer; }
    @media (max-width: 991.98px) {
        .settings-nav {
            display: flex;
            width: 100%;
            overflow-x: auto;
        }
        .settings-nav .nav-link { flex: 1 0 auto; justify-content: center; }
        .settings-panel { min-height: 0; }
    }
</style>

<div class="section settings-page">
    <div class="settings-shell">
        <div class="settings-toolbar">
            <nav class="nav nav-pills settings-nav" role="tablist" aria-label="Sections des paramètres">
                <button class="nav-link active" id="identity-tab" data-bs-toggle="pill" data-bs-target="#identity-panel" type="button" role="tab" aria-controls="identity-panel" aria-selected="true">
                    <i class="bi bi-building"></i>Identité & documents
                </button>
                <button class="nav-link" id="alerts-tab" data-bs-toggle="pill" data-bs-target="#alerts-panel" type="button" role="tab" aria-controls="alerts-panel" aria-selected="false">
                    <i class="bi bi-bell"></i>Alertes
                </button>
            </nav>
            @if(isset($entreprisesListe) && $entreprisesListe->count() > 0)
                <form method="get" action="{{ route('parametres') }}" class="settings-company">
                    <label for="entrepriseParametres">ENTREPRISE À CONFIGURER</label>
                    <select id="entrepriseParametres" name="entreprise_parametres_id" class="form-select" onchange="this.form.submit()">
                        @foreach($entreprisesListe as $ent)
                            <option value="{{ $ent->id }}" @selected((int)($entrepriseParametresId ?? 0) === (int)$ent->id)>
                                {{ $ent->abbreviation }} — {{ Str::limit($ent->description, 40) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        <div class="tab-content">
            <section class="tab-pane fade show active settings-panel" id="identity-panel" role="tabpanel" aria-labelledby="identity-tab" tabindex="0">
                <h2 class="settings-title">Identité et apparence</h2>
                <p class="settings-subtitle">Ces informations apparaissent dans le menu, les devis et les factures.</p>

                <form method="post" action="{{ route('alertes.parametres.branding') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-xl-8 col-md-6">
                            <div class="settings-block">
                                <h3 class="settings-section-title"><i class="bi bi-image"></i>Logo</h3>
                                <p class="small text-muted mb-2">Le même logo est utilisé dans le menu et sur les devis.</p>
                                <div class="settings-logo-preview">
                                    @if(!empty($branding['logo_url']))
                                        <img src="{{ $branding['logo_url'] }}" alt="Logo SanaaPro">
                                    @else
                                        <div class="settings-logo-empty"><i class="bi bi-image d-block fs-3 mb-1"></i>Aucun logo configuré</div>
                                    @endif
                                </div>
                                @if(!empty($branding['logo_url']))
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="rmLogo">
                                        <label class="form-check-label small" for="rmLogo">Supprimer le logo actuel</label>
                                    </div>
                                @endif
                                <label class="form-label" for="documentLogo">Nouveau logo</label>
                                <input id="documentLogo" type="file" name="logo" class="form-control form-control-sm" accept="image/*,.svg">
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="settings-block">
                                <h3 class="settings-section-title"><i class="bi bi-palette"></i>Couleurs des documents</h3>
                                <div class="settings-color-row">
                                    <div>
                                        <div class="form-label mb-0">Couleur d’accent</div>
                                        <small class="text-muted">Titres et éléments visuels</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="settings-color-dot" id="accentPreview" style="background:{{ $accentForm }}"></span>
                                        <input type="color" name="document_accent_color" id="document_accent_color" class="form-control form-control-color" value="{{ strlen($accentForm) === 7 ? $accentForm : \App\Support\SocieteBranding::DEFAULT_ACCENT_COLOR }}" title="Couleur d’accent">
                                    </div>
                                </div>
                                <div class="settings-color-row">
                                    <div>
                                        <div class="form-label mb-0">Couleur du texte</div>
                                        <small class="text-muted">Texte principal</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="settings-color-dot" id="textColorPreview" style="background:{{ $textColorForm }}"></span>
                                        <input type="color" name="document_text_color" id="document_text_color" class="form-control form-control-color" value="{{ strlen($textColorForm) === 7 ? $textColorForm : \App\Support\SocieteBranding::DEFAULT_TEXT_COLOR }}" title="Couleur du texte">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="settings-block mb-3">
                        <h3 class="settings-section-title"><i class="bi bi-card-text"></i>Coordonnées affichées sur les documents</h3>
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label class="form-label">Nom commercial / raison sociale</label>
                                <input type="text" name="societe_nom_commercial" class="form-control" value="{{ old('societe_nom_commercial', $branding['nom']) }}" placeholder="Nom de votre entreprise">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="societe_telephone" class="form-control" value="{{ old('societe_telephone', $branding['telephone']) }}" placeholder="06XXXXXXXX">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="societe_email" class="form-control" value="{{ old('societe_email', $branding['email']) }}" placeholder="contact@entreprise.ma">
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Adresse</label>
                                <textarea name="societe_adresse" class="form-control" rows="2" placeholder="Adresse, ville…">{{ old('societe_adresse', $branding['adresse']) }}</textarea>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Mention TVA en pied de devis</label>
                                <textarea name="devis_mention_tva" class="form-control" rows="2" placeholder="Mention légale ou fiscale">{{ old('devis_mention_tva', $branding['mention_tva']) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="settings-block mb-3">
                        <h3 class="settings-section-title"><i class="bi bi-file-earmark-text"></i>En-tête et pied de page du devis</h3>
                        <div class="row g-3">
                            <div class="col-lg-5">
                                <label class="form-label">Titre de l’en-tête</label>
                                <input type="text" name="devis_entete_titre" class="form-control" value="{{ old('devis_entete_titre', \App\Models\AppSetting::get('devis_entete_titre', \App\Support\SocieteBranding::DEFAULT_DEVIS_ENTETE_TITRE)) }}" placeholder="STE AFIF ALU S.A.R.L">
                            </div>
                            <div class="col-lg-7">
                                <label class="form-label">Activité (sous le titre)</label>
                                <input type="text" name="devis_entete_activite" class="form-control" value="{{ old('devis_entete_activite', \App\Models\AppSetting::get('devis_entete_activite', \App\Support\SocieteBranding::DEFAULT_DEVIS_ENTETE_ACTIVITE)) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Pied de page (siège social, patente, R.C, I.F, C.N.S.S…)</label>
                                <textarea name="devis_pied_page" class="form-control" rows="2">{{ old('devis_pied_page', \App\Models\AppSetting::get('devis_pied_page', \App\Support\SocieteBranding::DEFAULT_DEVIS_PIED_PAGE)) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn settings-save"><i class="bi bi-check2-circle me-1"></i>Enregistrer l’identité</button>
                    </div>
                </form>
            </section>

            <section class="tab-pane fade settings-panel" id="alerts-panel" role="tabpanel" aria-labelledby="alerts-tab" tabindex="0">
                <h2 class="settings-title">Configuration des alertes</h2>
                <p class="settings-subtitle">Définissez quand l’application doit vous prévenir des échéances importantes.</p>

                <form method="post" action="{{ route('alertes.parametres.save') }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-lg-6">
                            <div class="alert-setting-card">
                                <div class="alert-setting-icon"><i class="bi bi-shield-check"></i></div>
                                <label class="form-label" for="alerteCaution">Alerte avant échéance des cautions</label>
                                <div class="input-group">
                                    <input id="alerteCaution" type="number" name="alerte_caution_jours" class="form-control" min="1" max="365" value="{{ old('alerte_caution_jours', $joursCaution) }}" required>
                                    <span class="input-group-text">jours avant</span>
                                </div>
                                <small class="text-muted d-block mt-2">Une alerte sera générée avant la date de fin de chaque caution active.</small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="alert-setting-card">
                                <div class="alert-setting-icon"><i class="bi bi-receipt"></i></div>
                                <label class="form-label" for="alerteFacture">Alerte des factures impayées</label>
                                <div class="input-group">
                                    <input id="alerteFacture" type="number" name="alerte_facture_impayee_jours" class="form-control" min="1" max="365" value="{{ old('alerte_facture_impayee_jours', $joursFacture) }}" required>
                                    <span class="input-group-text">jours</span>
                                </div>
                                <small class="text-muted d-block mt-2">Signale les factures toujours impayées après le délai configuré.</small>
                            </div>
                        </div>
                    </div>

                    <div class="settings-switch mb-4">
                        <div>
                            <div class="fw-semibold" style="color:var(--settings-navy)">Alertes de stock faible</div>
                            <small class="text-muted">Être averti lorsqu’un article passe sous son seuil minimum.</small>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="alerte_stock_actif" value="1" id="stockA" @checked(old('alerte_stock_actif', $stockActif) == '1')>
                            <label class="visually-hidden" for="stockA">Activer les alertes de stock faible</label>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between gap-2">
                        <a href="{{ route('alertes.index') }}" class="btn btn-outline-secondary rounded-3"><i class="bi bi-arrow-left me-1"></i>Voir les alertes</a>
                        <button type="submit" class="btn settings-save"><i class="bi bi-check2-circle me-1"></i>Enregistrer les alertes</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bindings = [
        ['document_accent_color', 'accentPreview'],
        ['document_text_color', 'textColorPreview']
    ];

    bindings.forEach(function (binding) {
        const input = document.getElementById(binding[0]);
        const preview = document.getElementById(binding[1]);
        input?.addEventListener('input', function () {
            if (preview) preview.style.background = input.value;
        });
    });
});
</script>
@endpush
