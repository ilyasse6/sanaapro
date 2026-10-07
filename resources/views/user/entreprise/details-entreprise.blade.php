<div class="modal fade" id="DetailModal-{{ $entreprise->id }}" tabindex="-1" aria-labelledby="DetailModal"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-design">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-building me-1"></i>
                    <span>Détails de l'entreprise</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <!-- Abréviation -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="detail_abbreviation_{{ $entreprise->id }}" class="form-label">
                                            <i class="bi bi-text-paragraph me-1"></i>
                                            Abréviation :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            id="detail_abbreviation_{{ $entreprise->id }}"
                                            value="{{ $entreprise->abbreviation }}">
                                    </div>
                                </div>

                                <!-- Type -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="detail_type_{{ $entreprise->id }}" class="form-label">
                                            <i class="bi bi-tag me-1"></i>
                                            Type :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            id="detail_type_{{ $entreprise->id }}"
                                            value="{{ $entreprise->type_label }}">
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="detail_description_{{ $entreprise->id }}" class="form-label">
                                            <i class="bi bi-card-text me-1"></i>
                                            Description :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            id="detail_description_{{ $entreprise->id }}"
                                            value="{{ $entreprise->description }}">
                                    </div>
                                </div>

                                <!-- Date de création -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="detail_created_at_{{ $entreprise->id }}" class="form-label">
                                            <i class="bi bi-calendar-plus me-1"></i>
                                            Créé le :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            id="detail_created_at_{{ $entreprise->id }}"
                                            value="{{ $entreprise->created_at->format('d/m/Y H:i') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-circle-fill me-1"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>
