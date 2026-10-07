<!-- Modal Ajouter Utilisateur -->
<div class="modal fade custom-modal-height" id="AddModal" tabindex="-1" aria-labelledby="AddModal" aria-hidden="true">
  <div class="modal-dialog mx-auto modal-xl">
    <div class="modal-content card-design">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-person-plus-fill me-1"></i>
          <span>Ajouter un utilisateur</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form class="row justify-content-center" action="{{ route('utilisateurs.store') }}" method="POST" id="utilisateurForm" novalidate>
          @csrf
          <div class="col-lg-12">
            <div class="fade-in-left">
              <div class="row g-3">
                <!-- Nom -->
                <div class="col-xl-4 col-md-6">
                  <div class="form-group">
                    <label for="nom" class="form-label"><i class="bi bi-person-fill me-1"></i>Nom :</label>
                    <input type="text" class="form-control custom-input" id="nom" name="nom" value="{{ old('nom') }}" placeholder="Entrez le nom" required>
                    <span class="text-danger mt-1 d-block error-span" id="nom-error">@error('nom'){{ $message }}@enderror</span>
                  </div>
                </div>

                <!-- Prénom -->
                <div class="col-xl-4 col-md-6">
                  <div class="form-group">
                    <label for="prenom" class="form-label"><i class="bi bi-person-fill me-1"></i>Prénom :</label>
                    <input type="text" class="form-control custom-input" id="prenom" name="prenom" value="{{ old('prenom') }}" placeholder="Entrez le prénom" required>
                    <span class="text-danger mt-1 d-block error-span" id="prenom-error">@error('prenom'){{ $message }}@enderror</span>
                  </div>
                </div>
                
                <!-- Email -->
                <div class="col-xl-4 col-md-6">
                  <div class="form-group position-relative">
                    <label for="email" class="form-label"><i class="bi bi-envelope-fill me-1"></i>Email :</label>
                    <input type="text" class="form-control custom-input" id="email" name="email" value="{{ old('email') }}" placeholder="Entrez l'email" required autocomplete="email" inputmode="email">
                    <span class="position-absolute top-50 end-0 translate-middle-y me-3" id="email-exists-icon" style="display:none; pointer-events:none; z-index:2;">
                      <i class="bi bi-exclamation-circle-fill text-danger"></i>
                    </span>
                    <span class="text-danger mt-1 d-block error-span" id="email-error">@error('email'){{ $message }}@enderror</span>
                    <span class="text-danger mt-1 d-block error-span" id="email-exists-error"></span>
                  </div>
                </div>

                <!-- Téléphone -->
                <div class="col-xl-4 col-md-6">
                  <div class="form-group">
                    <label for="telephone" class="form-label"><i class="bi bi-telephone-fill me-1"></i>Téléphone :</label>
                    <input type="tel" class="form-control custom-input" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="Ex : 0612345678" required maxlength="14" inputmode="numeric" autocomplete="tel">
                    <span class="text-danger mt-1 d-block error-span" id="telephone-error">@error('telephone'){{ $message }}@enderror</span>
                  </div>
                </div>

                <!-- Poste -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="id_poste" class="form-label"><i class="bi bi-briefcase-fill me-1"></i>Poste :</label>
                    <select class="form-select custom-input" id="id_poste" name="id_poste" required>
                      <option value="">Sélectionner le poste</option>
                      @foreach ($postes as $poste)
                      <option value="{{ $poste->id }}" {{ old('id_poste') == $poste->id ? 'selected' : '' }}>{{ $poste->intitule }}</option>
                      @endforeach
                    </select>
                    <span class="text-danger mt-1 d-block error-span" id="id_poste-error">@error('id_poste'){{ $message }}@enderror</span>
                  </div>
                </div>

                <!-- Mot de passe -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="password" class="form-label"><i class="bi bi-lock-fill me-1"></i>Mot de passe :</label>
                    <div class="input-group">
                      <input type="password" class="form-control custom-input" id="password" name="password" placeholder="Entrez le mot de passe" required minlength="8">
                      <button class="btn btn-outline-secondary" type="button" data-password-toggle="password"><i class="bi bi-eye-fill"></i></button>
                    </div>
                    <span class="text-danger mt-1 d-block error-span" id="password-error">@error('password'){{ $message }}@enderror</span>
                  </div>
                </div>

                <!-- Confirmation mot de passe -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="password_confirmation" class="form-label"><i class="bi bi-lock-fill me-1"></i>Confirmer :</label>
                    <div class="input-group">
                      <input type="password" class="form-control custom-input" id="password_confirmation" name="password_confirmation" placeholder="Confirmer le mot de passe" required minlength="8">
                      <button class="btn btn-outline-secondary" type="button" data-password-toggle="password_confirmation"><i class="bi bi-eye-fill"></i></button>
                    </div>
                    <span class="text-danger mt-1 d-block error-span" id="password_confirmation-error"></span>
                  </div>
                </div>

                <!-- Etablissement -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="id_entreprise" class="form-label"><i class="bi bi-building-fill me-1"></i>Entreprise :</label>
                    <select class="form-select custom-input" id="id_entreprise" name="id_entreprise">
                      <option value="">Sélectionner une entreprise</option>
                      @foreach ($entreprises as $etab)
                      <option value="{{ $etab->id }}" {{ old('id_entreprise') == $etab->id ? 'selected' : '' }}>{{ $etab->abbreviation }}</option>
                      @endforeach
                    </select>
                    <span class="text-danger mt-1 d-block error-span" id="id_entreprise-error">@error('id_entreprise'){{ $message }}@enderror</span>
                  </div>
                </div>


                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"><i class="bi bi-person-badge-fill me-1"></i>Profils :</label>
                    <input type="text" id="profileSearchInput" class="form-control mb-2" placeholder="Rechercher un profil...">
                    <div class="badge-container-fixed">
                      <div class="d-flex flex-wrap gap-2" id="profile-badges-container">
                        @foreach ($profiles as $profile)
                        <div class="profile-badge position-relative" onclick="toggleBadge(this, {{ $profile->id }})">
                          <input type="checkbox" name="profiles[]" value="{{ $profile->id }}" class="d-none" {{ in_array($profile->id, old('profiles', [])) ? 'checked' : '' }}>
                          <i class="bi bi-person-check me-2"></i>{{ $profile->nom }}
                          <span class="badge-icon"><i class="bi bi-check-circle-fill"></i></span>
                        </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <!-- NOUVEAU : Bouton pour réinitialiser -->
        <button type="button" class="btn btn-warning" id="resetFormButton">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Vider les Champs
        </button>
        <button type="submit" class="btn btn-primary" id="addUserSubmitBtn" form="utilisateurForm">
          <i class="bi bi-plus-circle me-1"></i>Ajouter l'utilisateur
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ri-close-circle-fill me-1"></i>Fermer</button>
      </div>
    </div>
  </div>
</div>

<style>
  .profile-badge {
    border: 1px solid #ccc; padding: 6px 12px; border-radius: 20px; background-color: #f8f9fa; cursor: pointer; display: inline-flex; align-items: center;
  }
  .profile-badge.selected {
    background-color: #cfe2ff; border-color: #0d6efd; color: #084298;
  }
  .badge-icon {
    margin-left: 6px;
  }
  .badge-container-fixed {
    max-height: 220px; min-height: 220px; overflow-y: auto; background-color: #f8f9fa; padding: 1rem; border-radius: 8px;
  }
  .custom-modal-height .modal-dialog {
    max-height: 85vh; /* Prevent modal exceeding viewport */
  }
  /* Helper to hide validation messages on reset */
  .error-span { transition: opacity 0.3s ease-in-out; }
</style>

<script>
 document.addEventListener("DOMContentLoaded", function() {
    // Règles de validation pour les champs
    const VALIDATION_RULES = {
        nom: {
            pattern: /^.{2,255}$/,
            message: "Le nom doit contenir au moins 2 caractères.",
            required: true
        },
        prenom: {
            pattern: /^.{2,255}$/,
            message: "Le prénom doit contenir au moins 2 caractères.",
            required: true
        },
        email: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: "Veuillez saisir une adresse e-mail valide.",
            required: true,
        },
        telephone: {
            pattern: /^0[67]\d{8}$/,
            message: "Téléphone : 10 chiffres, commençant par 06 ou 07.",
            required: true,
            normalize: function (field) {
                const digits = field.value.replace(/\D/g, '');
                if (field.value !== digits) {
                    field.value = digits;
                }
            }
        },
        id_poste: {
            pattern: /.+/,
            message: "Veuillez sélectionner un poste.",
            required: true
        },
        profiles: {
            custom: function(form) {
                const checkedProfiles = form.querySelectorAll('input[name="profiles[]"]:checked');
                return checkedProfiles.length > 0 ? null : "Veuillez sélectionner au moins un profil.";
            }
        },
        password: {
            pattern: /^.{8,}$/,
            message: "Le mot de passe doit contenir au moins 8 caractères.",
            required: true // Pour le formulaire d'ajout
        },
        password_confirmation: {
            match: function(input) {
                const passwordInput = input.form.querySelector('#password');
                return (passwordInput && input.value === passwordInput.value) ? null : "Les mots de passe ne correspondent pas.";
            },
            required: true
        }
    };

    const addModalEl = document.getElementById('AddModal');
    const userForm = document.getElementById('utilisateurForm');
    if (!userForm) return; // Si le formulaire n'est pas sur la page, arrêter

    // --- Sélection des éléments du formulaire ---
    const etabSelect = document.getElementById('id_entreprise');
    const profileSearchInput = document.getElementById('profileSearchInput');
    const resetBtn = document.getElementById('resetFormButton');
    const emailInput = document.getElementById('email');
    const emailExistsError = document.getElementById('email-exists-error');
    const emailExistsIcon = document.getElementById('email-exists-icon');

    let emailExists = false;

    // --- Fonctions Utilitaires ---

    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    userForm.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-password-toggle]');
        if (!btn || !userForm.contains(btn)) return;
        e.preventDefault();
        togglePasswordVisibility(btn.getAttribute('data-password-toggle'));
    });

    window.toggleBadge = function (badge) {
        const checkbox = badge.querySelector('input[type="checkbox"]');
        if (!checkbox) return;
        checkbox.checked = !checkbox.checked;
        badge.classList.toggle('selected', checkbox.checked);
        validateForm();
    };

    // --- Logique de validation ---

    function setFieldError(field, message) {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        const errorSpan = document.getElementById(`${field.name}-error`);
        if (errorSpan) {
            errorSpan.textContent = message;
        }
    }

    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        const errorSpan = document.getElementById(`${field.name}-error`);
        if (errorSpan) {
            errorSpan.textContent = '';
        }
    }

    function validateField(field) {
        const rule = VALIDATION_RULES[field.name.replace('[]', '')];
        if (!rule) return true;

        if (typeof rule.normalize === 'function') {
            rule.normalize(field);
        }

        let isValid = true;
        let errorMessage = '';

        if (rule.required && !field.value.trim()) {
            isValid = false;
            errorMessage = rule.message;
        } else if (rule.pattern && !rule.pattern.test(field.value.trim())) {
            isValid = false;
            errorMessage = rule.message;
        } else if (rule.match) {
            const matchError = rule.match(field);
            if (matchError) {
                isValid = false;
                errorMessage = matchError;
            }
        }

        if (isValid) {
            clearFieldError(field);
        } else {
            setFieldError(field, errorMessage);
        }
        return isValid;
    }

    function validateForm() {
        let isFormValid = true;
        const fields = userForm.querySelectorAll('input[required], select[required]');
        fields.forEach(field => {
            if (!validateField(field)) {
                isFormValid = false;
            }
        });

        // Validation personnalisée pour les profils
        const profilesRule = VALIDATION_RULES.profiles;
        const profileError = profilesRule.custom(userForm);
        const profileContainer = document.getElementById('profile-badges-container');
        if (profileError) {
            isFormValid = false;
            profileContainer.classList.add('is-invalid');
        } else {
            profileContainer.classList.remove('is-invalid');
        }

        if (emailExists) {
           isFormValid = false;
        }

        return isFormValid;
    }

    // --- Fonctions de gestion du formulaire ---

    function resetForm() {
        userForm.reset();

        document.querySelectorAll('.is-invalid, .is-valid').forEach(el => el.classList.remove('is-invalid', 'is-valid'));
        document.querySelectorAll('.error-span').forEach(span => span.textContent = '');

        document.querySelectorAll('.profile-badge').forEach(badge => {
            badge.classList.remove('selected');
            const checkbox = badge.querySelector('input');
            if(checkbox) checkbox.checked = false;
        });

        emailExists = false;
        emailInput?.classList.remove('is-invalid');
        if (emailExistsIcon) emailExistsIcon.style.display = 'none';
        emailExistsError.textContent = '';

        validateForm();
    }

    function initializeFormState() {
        document.querySelectorAll('input[name="profiles[]"]:checked').forEach(input => {
            input.closest('.profile-badge')?.classList.add('selected');
        });

        validateForm(); // Valider l'état initial
    }

    // --- Écouteurs d'événements ---

    etabSelect?.addEventListener('change', validateForm);

    resetBtn?.addEventListener('click', resetForm);
    addModalEl?.addEventListener('hidden.bs.modal', resetForm);
    addModalEl?.addEventListener('shown.bs.modal', function () {
        requestAnimationFrame(validateForm);
        setTimeout(validateForm, 50);
        setTimeout(validateForm, 350);
    });

    userForm.addEventListener('focusin', validateForm);

    userForm.addEventListener('submit', function (e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });

    userForm.querySelectorAll('input, select').forEach(field => {
       field.addEventListener('input', validateForm);
       field.addEventListener('change', validateForm);
    });

    profileSearchInput?.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('.profile-badge').forEach(badge => {
            badge.style.display = badge.textContent.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    emailInput?.addEventListener('input', function () {
        emailExists = false;
        emailExistsError.textContent = '';
        if (emailExistsIcon) emailExistsIcon.style.display = 'none';
        emailInput?.classList.remove('is-invalid');
    });

    emailInput?.addEventListener('blur', function() {
        if (!emailInput) return;
        const email = emailInput.value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailExists = false;
            validateForm();
            return;
        }

        fetch("{{ route('utilisateurs.checkEmail') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ email: email })
            })
            .then(async (response) => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    emailExists = false;
                    emailExistsError.textContent = '';
                    validateForm();
                    return;
                }
                if (data.exists) {
                    emailExists = true;
                    emailExistsError.textContent = "Cet email existe déjà.";
                    emailInput.classList.add('is-invalid');
                    if (emailExistsIcon) emailExistsIcon.style.display = '';
                } else {
                    emailExists = false;
                    emailExistsError.textContent = "";
                    if (emailExistsIcon) emailExistsIcon.style.display = 'none';
                }
                validateForm();
            })
            .catch(() => {
                emailExists = false;
                emailExistsError.textContent = "Erreur lors de la vérification.";
                if (emailExistsIcon) emailExistsIcon.style.display = '';
                validateForm();
            });
    });

    // --- Initialisation ---
    // Si le formulaire est affiché avec des erreurs serveur (old data), initialiser son état.
    // Sinon, le réinitialiser pour s'assurer qu'il est propre.
    @if (!$errors->isEmpty())
        initializeFormState();
    @else
        resetForm();
    @endif
});
</script>