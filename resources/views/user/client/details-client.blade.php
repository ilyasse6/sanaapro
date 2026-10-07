<div class="modal fade modal-modern" id="DetailModal-{{ $client->id }}" tabindex="-1" aria-labelledby="DetailModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-building me-2"></i>
                    <span>Détails du client</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-building me-1"></i>Raison sociale :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->nom }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-person-fill me-1"></i>Contact :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->prenom }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-envelope me-1"></i>Email :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->email ?? '-' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-telephone me-1"></i>Téléphone :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->telephone ?? '-' }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label"><i class="bi bi-geo-alt me-1"></i>Adresse :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->adresse ?? '-' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-pin-map me-1"></i>Ville :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->ville ?? '-' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-mailbox me-1"></i>Code postal :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->code_postal ?? '-' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-upc me-1"></i>ICE :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->ice ?? '-' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-journal-text me-1"></i>RC :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->rc ?? '-' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-briefcase me-1"></i>Secteur d'activité :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->secteur_activite ?? '-' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-info-circle me-1"></i>Statut :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->actif ? 'Actif' : 'Inactif' }}">
                                </div>
                                @if(!empty($client->notes))
                                <div class="col-md-12">
                                    <label class="form-label"><i class="bi bi-sticky me-1"></i>Notes :</label>
                                    <textarea class="form-control modern-input" readonly rows="2">{{ $client->notes }}</textarea>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-calendar-event me-1"></i>Date de création :</label>
                                    <input type="text" class="form-control modern-input" readonly value="{{ $client->created_at->format('d/m/Y H:i') }}">
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
