<div class="modal fade" id="AddEntrepriseModal" tabindex="-1" aria-labelledby="AddEntrepriseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-add-entreprise">
        <div class="modal-content card-design">
            <div class="modal-header py-3 px-4 border-0">
                <h5 class="modal-title mb-0" id="AddEntrepriseModalLabel">
                    <i class="bi bi-building me-1"></i>
                    <span>Ajouter une entreprise</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3 px-4">
                <form class="row justify-content-center" action="{{ route('entreprises.store') }}" method="POST"
                    id="entrepriseForm">
                    @csrf
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <!-- Description -->
                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <label for="description" class="form-label mb-1 fw-medium">
                                            <i class="bi bi-card-text me-1"></i>
                                            Description :
                                        </label>
                                        <textarea class="form-control custom-input modal-entreprise-desc" id="description" name="description" rows="1"
                                            placeholder="Entrez une description de l'entreprise" required>{{ old('description') }}</textarea>
                                        <div id="description-feedback" class="validation-feedback"></div>
                                        @error('description')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Abréviation -->
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="abbreviation" class="form-label mb-1 fw-medium">
                                            <i class="bi bi-text-paragraph me-1"></i>
                                            Abréviation :
                                        </label>
                                        <input type="text" class="form-control custom-input" id="abbreviation"
                                            name="abbreviation" value="{{ old('abbreviation') }}"
                                            placeholder="Entrez l'abréviation de l'entreprise" required>
                                        <div id="abbreviation-feedback" class="validation-feedback"></div>
                                        @error('abbreviation')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Type -->
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="type" class="form-label mb-1 fw-medium">
                                            <i class="bi bi-tag me-1"></i>
                                            Type :
                                        </label>
                                        <select class="form-control custom-input" id="type" name="type"
                                            required>
                                            <option value="">Sélectionnez un type</option>
                                            @foreach (\App\Models\Entreprise::TYPE_LABELS as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ old('type') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div id="type-feedback" class="validation-feedback"></div>
                                        @error('type')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 pt-2">
                                    <p class="text-muted mb-0 modal-entreprise-hint">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Créez le compte du directeur ensuite dans <strong>Gestion des utilisateurs</strong>, avec le profil <strong>Directeur</strong> et cette entreprise.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-center gap-2 flex-wrap border-0 py-3 px-4">
                <button type="submit" class="btn btn-primary" id="submitButton"
                    onclick="document.getElementById('entrepriseForm').submit()">
                    <i class="bi bi-plus-circle me-1"></i>
                    Ajouter l'entreprise
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-circle-fill me-1"></i>Fermer
                </button>
            </div>
        </div>
    </div>
</div>
<style>
    #AddEntrepriseModal .modal-add-entreprise {
        max-width: min(600px, calc(100vw - 2rem));
        margin-left: auto;
        margin-right: auto;
    }
    #AddEntrepriseModal .modal-content {
        overflow: hidden;
    }
    #AddEntrepriseModal .modal-entreprise-desc {
        min-height: 2.25rem;
        max-height: 5rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        line-height: 1.45;
        font-size: 1rem;
        resize: vertical;
    }
    #AddEntrepriseModal .modal-title {
        font-size: 1.2rem;
    }
    #AddEntrepriseModal .form-label {
        font-size: 0.95rem;
    }
    #AddEntrepriseModal .form-control.custom-input,
    #AddEntrepriseModal select.form-control {
        padding: 0.5rem 0.75rem;
        min-height: calc(1.6em + 1rem + 2px);
        font-size: 1rem;
    }
    #AddEntrepriseModal .modal-entreprise-hint {
        font-size: 0.875rem;
        line-height: 1.45;
    }
    #AddEntrepriseModal #description-feedback,
    #AddEntrepriseModal #abbreviation-feedback,
    #AddEntrepriseModal #type-feedback {
        margin-top: 0.25rem;
        min-height: 0;
        line-height: 1.25;
        font-size: 0.875rem;
    }
</style>
