<div class="modal fade" id="AddModal" tabindex="-1" aria-labelledby="AddModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-design">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-briefcase-fill me-1"></i>
                    <span>Ajouter un poste</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row justify-content-center" action="{{ route('postes.store') }}" method="POST"
                    id="posteForm">
                    @csrf
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <!-- Intitulé -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="intitule" class="form-label">
                                            <i class="bi bi-card-heading me-1"></i>
                                            Intitulé :
                                        </label>
                                        <input type="text" class="form-control custom-input" id="intitule"
                                            name="intitule" value="{{ old('intitule') }}"
                                            placeholder="Entrez l'intitulé du poste" required>

                                        @error('intitule')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description" class="form-label">
                                            <i class="bi bi-text-paragraph me-1"></i>
                                            Description :
                                        </label>
                                        <textarea class="form-control custom-input" id="description" name="description" rows="3"
                                            placeholder="Entrez la description du poste">{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                    <i class="ri-close-circle-fill me-1"></i>Fermer
                </button>
                <button type="submit" class="btn btn-primary" id="submitButton"
                    onclick="document.getElementById('posteForm').submit()">
                    <i class="bi bi-plus-circle me-1"></i>
                    Ajouter le poste
                </button>
            </div>
        </div>
    </div>
</div>
