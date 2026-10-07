<div class="modal fade" id="ShowModal-{{ $utilisateur->id }}" tabindex="-1" aria-labelledby="ShowModalLabel-{{ $utilisateur->id }}" aria-hidden="true">
    <div class="modal-dialog mx-auto modal-xl">
        <div class="modal-content card-design">
            <div class="modal-header">
                <h5 class="modal-title" id="ShowModalLabel-{{ $utilisateur->id }}">
                    <i class="bi bi-eye-fill me-1"></i>
                    <span>Détails de l'utilisateur</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <!-- Nom -->
                                <div class="col-xl-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-person-fill me-1"></i> Nom :
                                        </label>
                                        <input type="text" class="form-control custom-input" value="{{ $utilisateur->nom }}" readonly>
                                    </div>
                                </div>
                                <!-- Prénom -->
                                <div class="col-xl-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-person-fill me-1"></i> Prénom :
                                        </label>
                                        <input type="text" class="form-control custom-input" value="{{ $utilisateur->prenom }}" readonly>
                                    </div>
                                </div>
                                <!-- Email -->
                                <div class="col-xl-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-envelope-fill me-1"></i> Email :
                                        </label>
                                        <input type="email" class="form-control custom-input" value="{{ $utilisateur->email }}" readonly>
                                    </div>
                                </div>
                                <!-- Téléphone -->
                                <div class="col-xl-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-telephone-fill me-1"></i> Téléphone :
                                        </label>
                                        <input type="text" class="form-control custom-input" value="{{ $utilisateur->telephone ?? 'N/A' }}" readonly>
                                    </div>
                                </div>
                                <!-- Entreprise -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-building-fill me-1"></i> Entreprise :
                                        </label>
                                        <input type="text" class="form-control custom-input" value="{{ $utilisateur->entreprise->abbreviation ?? 'N/A' }}" readonly>
                                    </div>
                                </div>
                                <!-- Poste -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-briefcase-fill me-1"></i> Poste :
                                        </label>
                                        <input type="text" class="form-control custom-input" value="{{ $utilisateur->poste_occupe->intitule ?? 'N/A' }}" readonly>
                                    </div>
                                </div>
                                <!-- Date de création -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-calendar-plus-fill me-1"></i> Date de création :
                                        </label>
                                        <input type="text" class="form-control custom-input" value="{{ $utilisateur->created_at->format('d/m/Y H:i') }}" readonly>
                                    </div>
                                </div>
                                <!-- Profils -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-person-badge-fill me-1"></i> Profils :
                                        </label>
                                        <div class="p-3 bg-light rounded" style="max-height: 150px; overflow-y: auto;">
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($utilisateur->profiles as $profile)
                                                    <div class="profile-badge selected position-relative" style="background-color: #e7f5ff; border: 1px solid #4dabf7; color: #1971c2; padding: 8px 12px; border-radius: 20px;">
                                                        <i class="bi bi-person-check me-2"></i>
                                                        {{ $profile->nom }}
                                                        <span class="badge-icon" style="color: #2b8a3e; margin-left: 5px;">
                                                            <i class="bi bi-check-circle-fill"></i>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
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
