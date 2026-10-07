<div class="modal fade" id="DetailModal-{{ $permission->id }}" tabindex="-1" aria-labelledby="DetailModal"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-design">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-lock me-1"></i>
                    <span>Détails de la permission</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <!-- Nom -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="detail_nom_{{ $permission->id }}" class="form-label">
                                            <i class="bi bi-card-heading me-1"></i>
                                            Nom :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            id="detail_nom_{{ $permission->id }}" value="{{ $permission->nom }}">
                                    </div>
                                </div>

                                <!-- Action -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="detail_action_{{ $permission->id }}" class="form-label">
                                            <i class="bi bi-gear me-1"></i>
                                            Action :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            id="detail_action_{{ $permission->id }}"
                                            value="{{ $permission->action ?? 'Non spécifiée' }}">
                                    </div>
                                </div>
                                <!-- Module -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="detail_module_{{ $permission->id }}" class="form-label">
                                            <i class="bi bi-collection me-1"></i>
                                            Module :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            id="detail_module_{{ $permission->id }}"
                                            value="{{ $permission->module->nom }}">
                                    </div>
                                </div>
                                <!-- Statut -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="detail_status_{{ $permission->id }}" class="form-label">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Statut :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            id="detail_status_{{ $permission->id }}"
                                            value="{{ $permission->actif ? 'Actif' : 'Inactif' }}">
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
                                            value="{{ $permission->created_at->format('d/m/Y H:i') }}" readonly>
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
