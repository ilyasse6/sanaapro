<div class="modal fade modal-modern" id="AddModal" tabindex="-1" aria-labelledby="AddModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-building-add me-2"></i>
                    <span>Nouveau client</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row justify-content-center" action="{{ route('clients.store') }}" method="POST" id="clientForm">
                    @csrf
                    <div class="col-lg-12">
                        <div class="row">
                            <!-- Raison sociale / Nom entreprise -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-building me-2 text-primary"></i>
                                        Raison sociale <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control modern-input" id="nom" name="nom"
                                        value="{{ old('nom') }}" placeholder="Nom de l'entreprise ou du client" required>
                                    <div class="error-message" id="error-nom"></div>
                                    @error('nom')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact / Personne à contacter -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prenom" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-person-fill me-2 text-primary"></i>
                                        Contact <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control modern-input" id="prenom" name="prenom"
                                        value="{{ old('prenom') }}" placeholder="Personne à contacter" required>
                                    <div class="error-message" id="error-prenom"></div>
                                    @error('prenom')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-envelope me-2 text-primary"></i>
                                        Email
                                    </label>
                                    <input type="email" class="form-control modern-input" id="email" name="email"
                                        value="{{ old('email') }}" placeholder="email@exemple.com">
                                    <div class="error-message" id="error-email"></div>
                                    @error('email')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Téléphone -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-telephone me-2 text-primary"></i>
                                        Téléphone
                                    </label>
                                    <input type="text" class="form-control modern-input" id="telephone" name="telephone"
                                        value="{{ old('telephone') }}" placeholder="06 00 00 00 00">
                                    <div class="error-message" id="error-telephone"></div>
                                    @error('telephone')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Adresse -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adresse" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-geo-alt me-2 text-primary"></i>
                                        Adresse
                                    </label>
                                    <input type="text" class="form-control modern-input" id="adresse" name="adresse"
                                        value="{{ old('adresse') }}" placeholder="Adresse complète">
                                    <div class="error-message" id="error-adresse"></div>
                                    @error('adresse')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Ville -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ville" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-pin-map me-2 text-primary"></i>
                                        Ville
                                    </label>
                                    <input type="text" class="form-control modern-input" id="ville" name="ville"
                                        value="{{ old('ville') }}" placeholder="Ville">
                                    <div class="error-message" id="error-ville"></div>
                                    @error('ville')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Code postal -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code_postal" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-mailbox me-2 text-primary"></i>
                                        Code postal
                                    </label>
                                    <input type="text" class="form-control modern-input" id="code_postal" name="code_postal"
                                        value="{{ old('code_postal') }}" placeholder="20000">
                                    <div class="error-message" id="error-code_postal"></div>
                                    @error('code_postal')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- ICE -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ice" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-upc me-2 text-primary"></i>
                                        ICE
                                    </label>
                                    <input type="text" class="form-control modern-input" id="ice" name="ice"
                                        value="{{ old('ice') }}" placeholder="Identifiant Commun Entreprise">
                                    <div class="error-message" id="error-ice"></div>
                                    @error('ice')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- RC -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rc" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-journal-text me-2 text-primary"></i>
                                        Registre de commerce (RC)
                                    </label>
                                    <input type="text" class="form-control modern-input" id="rc" name="rc"
                                        value="{{ old('rc') }}" placeholder="N° RC">
                                    <div class="error-message" id="error-rc"></div>
                                    @error('rc')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Secteur d'activité -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="secteur_activite" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-briefcase me-2 text-primary"></i>
                                        Secteur d'activité
                                    </label>
                                    <select class="form-control modern-input" id="secteur_activite" name="secteur_activite">
                                        <option value="">— Choisir —</option>
                                        <option value="Médical" {{ old('secteur_activite') == 'Médical' ? 'selected' : '' }}>Médical</option>
                                        <option value="Cuisine / Restauration" {{ old('secteur_activite') == 'Cuisine / Restauration' ? 'selected' : '' }}>Cuisine / Restauration</option>
                                        <option value="Bureautique" {{ old('secteur_activite') == 'Bureautique' ? 'selected' : '' }}>Bureautique</option>
                                        <option value="Industriel" {{ old('secteur_activite') == 'Industriel' ? 'selected' : '' }}>Industriel</option>
                                        <option value="Commerce" {{ old('secteur_activite') == 'Commerce' ? 'selected' : '' }}>Commerce</option>
                                        <option value="Autre" {{ old('secteur_activite') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    <div class="error-message" id="error-secteur_activite"></div>
                                    @error('secteur_activite')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Date de création (utilise date_naissance en BDD) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_naissance" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-calendar-event me-2 text-primary"></i>
                                        Date de création <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control modern-input" id="date_naissance" name="date_naissance"
                                        value="{{ old('date_naissance', date('Y-m-d')) }}" required>
                                    <div class="error-message" id="error-date_naissance"></div>
                                    @error('date_naissance')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes" class="form-label fw-semibold mb-2">
                                        <i class="bi bi-sticky me-2 text-primary"></i>
                                        Notes
                                    </label>
                                    <textarea class="form-control modern-input" id="notes" name="notes" rows="2"
                                        placeholder="Notes commerciales, besoins spécifiques...">{{ old('notes') }}</textarea>
                                    <div class="error-message" id="error-notes"></div>
                                    @error('notes')
                                        <span class="text-danger mt-1 d-block small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">
                    <i class="ri-close-circle-fill me-1"></i>Annuler
                </button>
                <button type="button" class="btn btn-modern btn-modern-primary" id="submitClientButton">
                    <i class="bi bi-plus-circle me-1"></i>
                    Ajouter le client
                </button>
            </div>
        </div>
    </div>
</div>
