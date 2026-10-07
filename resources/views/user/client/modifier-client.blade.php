<div class="modal fade modal-modern" id="EditModal-{{ $client->id }}" tabindex="-1" aria-labelledby="EditModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>
                    <span>Modifier le client</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row justify-content-center" action="{{ route('clients.update', $client->id) }}" method="POST" id="editClientForm-{{ $client->id }}">
                    @csrf
                    @method('PUT')
                    <div class="col-lg-12">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_nom_{{ $client->id }}" class="form-label"><i class="bi bi-building me-1"></i>Raison sociale <span class="text-danger">*</span></label>
                                <input type="text" class="form-control modern-input" id="edit_nom_{{ $client->id }}" name="nom"
                                    value="{{ old('nom', $client->nom) }}" required>
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('nom') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="edit_prenom_{{ $client->id }}" class="form-label"><i class="bi bi-person-fill me-1"></i>Contact <span class="text-danger">*</span></label>
                                <input type="text" class="form-control modern-input" id="edit_prenom_{{ $client->id }}" name="prenom"
                                    value="{{ old('prenom', $client->prenom) }}" required>
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('prenom') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="edit_email_{{ $client->id }}" class="form-label"><i class="bi bi-envelope me-1"></i>Email</label>
                                <input type="email" class="form-control modern-input" id="edit_email_{{ $client->id }}" name="email"
                                    value="{{ old('email', $client->email) }}">
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('email') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="edit_telephone_{{ $client->id }}" class="form-label"><i class="bi bi-telephone me-1"></i>Téléphone</label>
                                <input type="text" class="form-control modern-input" id="edit_telephone_{{ $client->id }}" name="telephone"
                                    value="{{ old('telephone', $client->telephone) }}">
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('telephone') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                            <div class="col-md-12">
                                <label for="edit_adresse_{{ $client->id }}" class="form-label"><i class="bi bi-geo-alt me-1"></i>Adresse</label>
                                <input type="text" class="form-control modern-input" id="edit_adresse_{{ $client->id }}" name="adresse"
                                    value="{{ old('adresse', $client->adresse) }}">
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('adresse') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="edit_ville_{{ $client->id }}" class="form-label"><i class="bi bi-pin-map me-1"></i>Ville</label>
                                <input type="text" class="form-control modern-input" id="edit_ville_{{ $client->id }}" name="ville"
                                    value="{{ old('ville', $client->ville) }}">
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('ville') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="edit_code_postal_{{ $client->id }}" class="form-label"><i class="bi bi-mailbox me-1"></i>Code postal</label>
                                <input type="text" class="form-control modern-input" id="edit_code_postal_{{ $client->id }}" name="code_postal"
                                    value="{{ old('code_postal', $client->code_postal) }}">
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('code_postal') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="edit_ice_{{ $client->id }}" class="form-label"><i class="bi bi-upc me-1"></i>ICE</label>
                                <input type="text" class="form-control modern-input" id="edit_ice_{{ $client->id }}" name="ice"
                                    value="{{ old('ice', $client->ice) }}">
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('ice') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="edit_rc_{{ $client->id }}" class="form-label"><i class="bi bi-journal-text me-1"></i>RC</label>
                                <input type="text" class="form-control modern-input" id="edit_rc_{{ $client->id }}" name="rc"
                                    value="{{ old('rc', $client->rc) }}">
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('rc') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="edit_secteur_activite_{{ $client->id }}" class="form-label"><i class="bi bi-briefcase me-1"></i>Secteur d'activité</label>
                                <select class="form-control modern-input" id="edit_secteur_activite_{{ $client->id }}" name="secteur_activite">
                                    <option value="">— Choisir —</option>
                                    <option value="Médical" {{ old('secteur_activite', $client->secteur_activite) == 'Médical' ? 'selected' : '' }}>Médical</option>
                                    <option value="Cuisine / Restauration" {{ old('secteur_activite', $client->secteur_activite) == 'Cuisine / Restauration' ? 'selected' : '' }}>Cuisine / Restauration</option>
                                    <option value="Bureautique" {{ old('secteur_activite', $client->secteur_activite) == 'Bureautique' ? 'selected' : '' }}>Bureautique</option>
                                    <option value="Industriel" {{ old('secteur_activite', $client->secteur_activite) == 'Industriel' ? 'selected' : '' }}>Industriel</option>
                                    <option value="Commerce" {{ old('secteur_activite', $client->secteur_activite) == 'Commerce' ? 'selected' : '' }}>Commerce</option>
                                    <option value="Autre" {{ old('secteur_activite', $client->secteur_activite) == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('secteur_activite') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                            <div class="col-md-6">
                                <label for="edit_date_naissance_{{ $client->id }}" class="form-label"><i class="bi bi-calendar-event me-1"></i>Date de création <span class="text-danger">*</span></label>
                                <input type="date" class="form-control modern-input" id="edit_date_naissance_{{ $client->id }}" name="date_naissance"
                                    value="{{ old('date_naissance', $client->date_naissance ? $client->date_naissance->format('Y-m-d') : date('Y-m-d')) }}" required>
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('date_naissance') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                            <div class="col-md-12">
                                <label for="edit_notes_{{ $client->id }}" class="form-label"><i class="bi bi-sticky me-1"></i>Notes</label>
                                <textarea class="form-control modern-input" id="edit_notes_{{ $client->id }}" name="notes" rows="2">{{ old('notes', $client->notes) }}</textarea>
                                @if (session('failed_modal') == "EditModal-{$client->id}") @error('notes') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal"><i class="ri-close-circle-fill me-1"></i>Annuler</button>
                <button type="button" class="btn btn-modern btn-modern-primary" id="submitEditClientButton-{{ $client->id }}"><i class="bi bi-save me-1"></i>Enregistrer</button>
            </div>
        </div>
    </div>
</div>
