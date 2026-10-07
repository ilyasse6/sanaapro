<div class="modal fade" id="EditModal-{{ $utilisateur->id }}" tabindex="-1" aria-labelledby="EditModal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content card-design">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-1"></i>
                    <span>Modifier l'utilisateur</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row justify-content-center" action="{{ route('utilisateurs.update', $utilisateur->id) }}"
                    method="POST" id="editUtilisateurForm-{{ $utilisateur->id }}">
                    @csrf
                    @method('PUT')
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <!-- Nom -->
                                <div class="col-xl-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="edit_nom_{{ $utilisateur->id }}" class="form-label">
                                            <i class="bi bi-person-fill me-1"></i>
                                            Nom :
                                        </label>
                                        <input type="text" class="form-control custom-input"
                                            id="edit_nom_{{ $utilisateur->id }}" name="nom"
                                            value="{{ old('nom', $utilisateur->nom) }}" placeholder="Entrez le nom"
                                            required>
                                        @error('nom')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                        <div id="edit_nom_{{ $utilisateur->id }}-feedback"
                                            class="validation-feedback text-danger mt-1"></div>
                                    </div>
                                </div>
                                <!-- Prénom -->
                                <div class="col-xl-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="edit_prenom_{{ $utilisateur->id }}" class="form-label">
                                            <i class="bi bi-person-fill me-1"></i>
                                            Prénom :
                                        </label>
                                        <input type="text" class="form-control custom-input"
                                            id="edit_prenom_{{ $utilisateur->id }}" name="prenom"
                                            value="{{ old('prenom', $utilisateur->prenom) }}"
                                            placeholder="Entrez le prénom" required>
                                        @error('prenom')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                        <div id="edit_prenom_{{ $utilisateur->id }}-feedback"
                                            class="validation-feedback text-danger mt-1"></div>
                                    </div>
                                </div>
                                <!-- Email -->
                                <div class="col-xl-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="edit_email_{{ $utilisateur->id }}" class="form-label">
                                            <i class="bi bi-envelope-fill me-1"></i>
                                            Email :
                                        </label>
                                        <input type="email" class="form-control custom-input"
                                            id="edit_email_{{ $utilisateur->id }}" name="email"
                                            value="{{ old('email', $utilisateur->email) }}"
                                            placeholder="Entrez l'adresse email" required>
                                        @error('email')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                        <div id="edit_email_{{ $utilisateur->id }}-feedback"
                                            class="validation-feedback text-danger mt-1"></div>
                                    </div>
                                </div>
                                <!-- Téléphone -->
                                <div class="col-xl-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="edit_telephone_{{ $utilisateur->id }}" class="form-label">
                                            <i class="bi bi-telephone-fill me-1"></i>
                                            Téléphone :
                                        </label>
                                        <input type="tel" class="form-control custom-input"
                                            id="edit_telephone_{{ $utilisateur->id }}" name="telephone"
                                            value="{{ old('telephone', $utilisateur->telephone) }}"
                                            placeholder="06 ou 07 + 8 chiffres (ex. 0612345678)">
                                        @error('telephone')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Etablissement -->
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group">
                                        <label for="edit_etablissement_{{ $utilisateur->id }}" class="form-label">
                                            <i class="bi bi-building-fill me-1"></i>
                                            Entreprise :
                                        </label>
                                        <select class="form-select custom-input"
                                            id="edit_etablissement_{{ $utilisateur->id }}" name="id_entreprise">
                                            <option value="">Sélectionner une entreprise</option>
                                            @foreach ($entreprises as $entreprise)
                                                <option value="{{ $entreprise->id }}"
                                                    {{ $utilisateur->id_entreprise == $entreprise->id ? 'selected' : '' }}>
                                                    {{ $entreprise->abbreviation }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_entreprise')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                        <div id="edit_etablissement_{{ $utilisateur->id }}-feedback"
                                            class="validation-feedback text-danger mt-1"></div>
                                    </div>
                                </div>
                                <!-- Poste -->
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group">
                                        <label for="edit_poste_{{ $utilisateur->id }}" class="form-label">
                                            <i class="bi bi-briefcase-fill me-1"></i>Poste :
                                        </label>
                                        <select class="form-select custom-input" id="edit_poste_{{ $utilisateur->id }}"
                                            name="id_poste" required>
                                            <option value="">Sélectionner le poste</option>
                                            @foreach ($postes as $poste)
                                                <option value="{{ $poste->id }}"
                                                    {{ old('id_poste', $utilisateur->id_poste) == $poste->id ? 'selected' : '' }}>
                                                    {{ $poste->intitule }}
                                                </option>
                                            @endforeach
                                            @if ($utilisateur->id_poste && ! $postes->pluck('id')->contains($utilisateur->id_poste))
                                                <option value="{{ $utilisateur->id_poste }}" selected>
                                                    {{ $utilisateur->poste_occupe->intitule ?? 'Poste actuel' }}
                                                </option>
                                            @endif
                                        </select>
                                        @error('id_poste')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                        <div id="edit_poste_{{ $utilisateur->id }}-feedback"
                                            class="validation-feedback text-danger mt-1"></div>
                                    </div>
                                </div>

                                <!-- Mot de passe -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_password_{{ $utilisateur->id }}" class="form-label">
                                            <i class="bi bi-lock-fill me-1"></i>
                                            Nouveau mot de passe :
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control custom-input"
                                                id="edit_password_{{ $utilisateur->id }}" name="password"
                                                placeholder="Minimum 8 caractères (optionnel)" minlength="8">
                                            <button class="btn btn-outline-secondary" type="button"
                                                data-target="edit_password_{{ $utilisateur->id }}"
                                                onclick="togglePassword('edit_password_{{ $utilisateur->id }}')">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                        <div id="edit_password_{{ $utilisateur->id }}-feedback"
                                            class="validation-feedback text-danger mt-1"></div>
                                    </div>
                                </div>

                                <!-- Confirmation du mot de passe -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_password_confirmation_{{ $utilisateur->id }}"
                                            class="form-label">
                                            <i class="bi bi-lock-fill me-1"></i>
                                            Confirmer le nouveau mot de passe :
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control custom-input"
                                                id="edit_password_confirmation_{{ $utilisateur->id }}"
                                                name="password_confirmation"
                                                placeholder="Minimum 8 caractères (optionnel)" minlength="8">
                                            <button class="btn btn-outline-secondary" type="button"
                                                data-target="edit_password_confirmation_{{ $utilisateur->id }}"
                                                onclick="togglePassword('edit_password_confirmation_{{ $utilisateur->id }}')">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </div>
                                        <div id="edit_password_confirmation_{{ $utilisateur->id }}-feedback"
                                            class="validation-feedback text-danger mt-1"></div>
                                    </div>
                                </div>

                                <!-- Profile -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-person-badge-fill me-1"></i>Profils :
                                        </label>
                                        <!-- Search input for profiles -->
                                        <div class="mb-3">
                                            <input type="text" 
                                                class="form-control custom-input" 
                                                id="profileSearch_{{ $utilisateur->id }}"
                                                placeholder="Rechercher un profil..."
                                                onkeyup="filterProfiles({{ $utilisateur->id }})">
                                        </div>
                                        <div class="p-3 bg-light rounded">
                                            <div class="profiles-container" style="max-height: 150px; overflow-y: auto;">
                                                <div class="d-flex flex-wrap gap-2" id="profilesList_{{ $utilisateur->id }}">
                                                    @foreach ($profiles as $profile)
                                                    <div class="profile-badge position-relative"
                                                        onclick="toggleBadge(this, {{ $utilisateur->id }})"
                                                        data-selected="false"
                                                        data-profile-name="{{ strtolower($profile->nom) }}">
                                                        <input type="checkbox" name="profiles[]"
                                                            value="{{ $profile->id }}"
                                                            id="edit_profile_badge_{{ $utilisateur->id }}_{{ $profile->id }}"
                                                            class="d-none">
                                                        <i class="bi bi-person-check me-2"></i>
                                                        {{ $profile->nom }}
                                                        <span class="badge-icon">
                                                            <i class="bi bi-check-circle-fill"></i>
                                                        </span>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @error('profiles')
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
                <button type="submit" class="btn btn-primary" id="editSubmitBtn-{{ $utilisateur->id }}"
                    onclick="document.getElementById('editUtilisateurForm-{{ $utilisateur->id }}').submit();">
                    <i class="bi bi-save me-1"></i>
                    Enregistrer les modifications
                </button>

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-circle-fill me-1"></i>Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styles pour les badges de profil */
    .profiles-container {
        scrollbar-width: thin;
        scrollbar-color: #adb5bd #f8f9fa;
    }

    .profiles-container::-webkit-scrollbar {
        width: 8px;
    }

    .profiles-container::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 10px;
    }

    .profiles-container::-webkit-scrollbar-thumb {
        background-color: #adb5bd;
        border-radius: 10px;
    }

    .profile-badge {
        padding: 8px 12px;
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .profile-badge:hover {
        background-color: #f8f9fa;
    }

    .profile-badge.selected,
    .profile-badge[data-selected="true"] {
        background-color: #e7f5ff !important;
        border-color: #4dabf7 !important;
        color: #1971c2 !important;
    }

    .badge-icon {
        margin-left: 5px;
        opacity: 0;
        transition: opacity 0.2s;
        color: #2b8a3e;
    }

    .profile-badge.selected .badge-icon,
    .profile-badge[data-selected="true"] .badge-icon {
        opacity: 1;
    }

    /* Validation styles */
    #profilesList_{{ $utilisateur->id }}.is-invalid {
        border: 2px solid #dc3545 !important;
        border-radius: 8px;
        background-color: #fff5f5;
    }

    #profilesList_{{ $utilisateur->id }}.is-invalid::after {
        content: "Ce champ est requis";
        color: #dc3545;
        font-size: 0.875rem;
        display: block;
        margin-top: 0.25rem;
    }
</style>

<script>
    // Function to toggle profile badge
    function toggleBadge(badge, userId) {
        const checkbox = badge.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
        
        // Update visual state
        if (checkbox.checked) {
            badge.classList.add('selected');
            badge.setAttribute('data-selected', 'true');
        } else {
            badge.classList.remove('selected');
            badge.setAttribute('data-selected', 'false');
        }
        
        // Validate form after profile change
        validateEditForm(userId);
    }

    // Function to filter profiles by name
    function filterProfiles(utilisateurId) {
        const searchInput = document.getElementById('profileSearch_' + utilisateurId);
        const searchTerm = searchInput.value.toLowerCase();
        const profilesList = document.getElementById('profilesList_' + utilisateurId);
        const profileBadges = profilesList.querySelectorAll('.profile-badge');

        profileBadges.forEach(badge => {
            const profileName = badge.getAttribute('data-profile-name');
            if (profileName.includes(searchTerm)) {
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        });
    }

    // Function to validate edit form
    function validateEditForm(userId) {
        let isValid = true;
        
        // Validate profiles - at least one must be selected
        const profileCheckboxes = document.querySelectorAll('#profilesList_' + userId + ' input[name="profiles[]"]:checked');
        const profilesContainer = document.getElementById('profilesList_' + userId);
        if (profileCheckboxes.length === 0) {
            profilesContainer.classList.add('is-invalid');
            isValid = false;
        } else {
            profilesContainer.classList.remove('is-invalid');
        }

        // Disable/enable submit button
        const submitButton = document.getElementById('editSubmitBtn-' + userId);
        if (submitButton) {
            submitButton.disabled = !isValid;
        }
        
        return isValid;
    }

    function initializeEditFormState(userId) {
        validateEditForm(userId);
    }

    window.userProfiles_{{ $utilisateur->id }} = @json($utilisateur->profiles->pluck('id'));

    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($utilisateurs))
            @foreach($utilisateurs as $utilisateur)
                $('#EditModal-{{ $utilisateur->id }}').on('shown.bs.modal', function () {
                    initializeEditFormState({{ $utilisateur->id }});
                    (window.userProfiles_{{ $utilisateur->id }} || []).forEach(function(profileId) {
                        var badge = document.querySelector(
                            '#profilesList_{{ $utilisateur->id }} .profile-badge input[value="' + profileId + '"]'
                        );
                        if (badge && !badge.checked) {
                            toggleBadge(badge.closest('.profile-badge'), {{ $utilisateur->id }});
                        }
                    });
                });

                const etabSelect{{ $utilisateur->id }} = document.getElementById('edit_etablissement_{{ $utilisateur->id }}');
                if (etabSelect{{ $utilisateur->id }}) {
                    etabSelect{{ $utilisateur->id }}.addEventListener('change', function() {
                        validateEditForm({{ $utilisateur->id }});
                    });
                }
            @endforeach
        @endif
    });
</script>
