<div class="modal fade" id="DetailModal-{{ $poste->id }}" tabindex="-1" aria-labelledby="DetailModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-design">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-briefcase me-1"></i>
                    <span>Détails du poste</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <!-- Intitulé -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="detail_intitule_{{ $poste->id }}" class="form-label">
                                            <i class="bi bi-card-heading me-1"></i>
                                            Intitulé :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            id="detail_intitule_{{ $poste->id }}" value="{{ $poste->intitule }}">
                                    </div>
                                </div>
                                <!-- Statut -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="detail_status_{{ $poste->id }}" class="form-label">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Statut :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            id="detail_status_{{ $poste->id }}"
                                            value="{{ $poste->actif ? 'Actif' : 'Inactif' }}">
                                    </div>
                                </div>
                                <!-- Date de création -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            Date de création :
                                        </label>
                                        <input type="text" class="form-control custom-input"
                                            value="{{ $poste->created_at->format('d/m/Y H:i') }}" readonly>
                                    </div>
                                </div>
                                <!-- Description -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="detail_description_{{ $poste->id }}" class="form-label">
                                            <i class="bi bi-text-paragraph me-1"></i>
                                            Description :
                                        </label>
                                        <textarea class="form-control custom-input" readonly id="detail_description_{{ $poste->id }}" rows="3">{{ $poste->description }}</textarea>
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
