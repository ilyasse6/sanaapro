document.addEventListener("DOMContentLoaded", function() {
    // Règles de validation pour les champs utilisateur
    const VALIDATION_RULES = {
        nom: {
            pattern: /^[A-Za-zÀ-ÿ\s'-]{2,50}$/,
            message: "Le nom doit contenir entre 2 et 50 caractères alphabétiques.",
            required: true
        },
        prenom: {
            pattern: /^[A-Za-zÀ-ÿ\s'-]{2,50}$/,
            message: "Le prénom doit contenir entre 2 et 50 caractères alphabétiques.",
            required: true
        },
        email: {
            pattern: /^[a-z]\.[a-z0-9]+@chuis\.ma$/i,
            message: "L'email doit être au format 'première lettre du prénom.nom@chuis.ma'.",
            required: true,
            custom: function(input, form) {
                // Récupération des valeurs de nom et prénom pour vérifier le format
                let prenom = '';
                let nom = '';

                // Récupérer le préfixe d'ID pour savoir si on est dans un formulaire d'édition
                const isEditForm = input.id.startsWith('edit_');
                const prefix = isEditForm ? 'edit_' : '';
                const userId = isEditForm ? input.id.split('_')[2] : '';

                if (isEditForm) {
                    prenom = document.getElementById(`${prefix}prenom_${userId}`).value;
                    nom = document.getElementById(`${prefix}nom_${userId}`).value;
                } else {
                    prenom = document.getElementById('prenom').value;
                    nom = document.getElementById('nom').value;
                }

                // Si le nom ou prénom est vide, on ne fait pas cette validation
                if (!prenom || !nom) return null;

                // Extraction de la première lettre du prénom et du nom pour la comparaison
                const prenomFirstLetter = prenom.trim().charAt(0).toLowerCase();
                const nomFormatted = nom.trim().toLowerCase();

                // Format attendu
                const expectedEmail = `${prenomFirstLetter}.${nomFormatted}@chuis.ma`;
                const inputEmail = input.value.toLowerCase();

                if (inputEmail !== expectedEmail) {
                    return `L'email devrait être: ${expectedEmail}`;
                }
                return null;
            }
        },
        id_etablissement: {
            pattern: /.+/,
            message: "Veuillez sélectionner un établissement.",
            required: false // Etablissement est nullable dans la DB
        },
        id_poste: {
            pattern: /.+/,
            message: "Veuillez sélectionner un poste.",
            required: true
        },
        profiles: {
            pattern: /.+/,
            message: "Veuillez sélectionner au moins un profil.",
            required: true,
            custom: function(input, form) {
                // Vérifier si au moins un profil est sélectionné
                const checkedProfiles = form.querySelectorAll('input[name="profiles[]"]:checked');
                if (checkedProfiles.length === 0) {
                    return "Veuillez sélectionner au moins un profil.";
                }
                return null;
            }
        },
        password: {
            pattern: /^.{8,}$/,
            message: "Le mot de passe doit contenir au moins 8 caractères.",
            required: function(input) {
                // Le mot de passe est requis uniquement sur le formulaire d'ajout
                return !input.id.startsWith('edit_');
            }
        },
        password_confirmation: {
            pattern: /.+/,
            message: "Veuillez confirmer le mot de passe.",
            required: function(input) {
                // La confirmation est requise uniquement si le champ password est rempli
                const formId = input.closest('form').id;
                const passwordId = input.id.replace('_confirmation', '');
                const passwordInput = document.getElementById(passwordId);
                return passwordInput && passwordInput.value.trim() !== '';
            },
            match: function(input) {
                // Récupère l'ID du champ mot de passe correspondant
                const passwordId = input.id.replace('_confirmation', '');
                const passwordInput = document.getElementById(passwordId);

                if (passwordInput && input.value !== passwordInput.value) {
                    return "Les mots de passe ne correspondent pas.";
                }
                return null;
            }
        }
    };

    // États des champs touchés pour chaque formulaire
    const touchedFields = {};
    // État de validité global pour chaque formulaire
    const formValidity = {};

    // Fonction pour obtenir l'élément de feedback
    function getFeedbackElement(input) {
        const feedbackId = input.id + "-feedback";
        let feedback = document.getElementById(feedbackId);

        if (!feedback) {
            feedback = document.createElement("div");
            feedback.id = feedbackId;
            feedback.className = "validation-feedback";

            // Pour les champs dans un input-group, placer le feedback après l'input-group
            const inputGroup = input.closest('.input-group');
            if (inputGroup) {
                inputGroup.parentNode.appendChild(feedback);
            } else {
                input.parentNode.appendChild(feedback);
            }
        }

        return feedback;
    }

    // Fonction spéciale pour gérer le feedback des profils
    function getProfilesFeedbackElement(form) {
        let feedback = form.querySelector('#profiles-feedback');

        if (!feedback) {
            feedback = document.createElement("div");
            feedback.id = "profiles-feedback";
            feedback.className = "validation-feedback text-danger mt-1";

            // Placer le feedback après le conteneur des badges
            const profilesContainer = form.querySelector('.bg-light.rounded');
            if (profilesContainer) {
                profilesContainer.parentNode.appendChild(feedback);
            }
        }

        return feedback;
    }

    // Fonction pour valider les profils sélectionnés
    function validateProfiles(form) {
        const checkedProfiles = form.querySelectorAll('input[name="profiles[]"]:checked');
        const isValid = checkedProfiles.length > 0;
        const feedback = getProfilesFeedbackElement(form);

        if (!isValid) {
            feedback.textContent = "Veuillez sélectionner au moins un profil.";
            feedback.style.display = 'block';
        } else {
            feedback.textContent = "";
            feedback.style.display = 'none';
        }

        return isValid;
    }

    // Fonction pour mettre à jour l'affichage visuel d'un champ
    function updateFieldDisplay(input, isEmpty) {
        const feedback = getFeedbackElement(input);
        const baseFieldName = input.name.replace(/^edit_/, '').replace('[]', '');
        const rule = VALIDATION_RULES[baseFieldName];
        const isRequired = typeof rule?.required === 'function' ? rule.required(input) : rule?.required;
        const formId = input.form.id;

        // Si le champ a été touché, est vide et requis
        if (touchedFields[formId]?.[input.id] && isEmpty && isRequired) {
            // Ajouter classe d'avertissement
            input.classList.add("warning-border");
            input.classList.remove("is-valid", "is-invalid");

            // Ajouter icône d'avertissement
            if (feedback) {
                feedback.textContent = "";
                feedback.className = "validation-feedback text-warning mt-1";
            }

            return false;
        }
        // Validation du contenu si le champ n'est pas vide
        else if (!isEmpty) {
            let isValid = rule ? rule.pattern.test(input.value) : true;
            let message = rule ? rule.message : "";

            // Vérification personnalisée si définie
            if (isValid && rule && rule.custom) {
                const customError = rule.custom(input, input.form);
                if (customError) {
                    isValid = false;
                    message = customError;
                }
            }

            // Vérification de correspondance (pour le mot de passe)
            if (isValid && rule && rule.match) {
                const matchError = rule.match(input);
                if (matchError) {
                    isValid = false;
                    message = matchError;
                }
            }

            input.classList.remove("warning-border");
            input.classList.toggle("is-valid", isValid);
            input.classList.toggle("is-invalid", !isValid);

            if (feedback) {
                if (!isValid) {
                    feedback.textContent = message;
                    feedback.className = "validation-feedback text-danger mt-1";

                    // Ajouter icône d'erreur
                    if (!feedback.querySelector('.error-icon')) {
                        const icon = document.createElement('i');
                        icon.className = 'bi bi-x-circle-fill me-1 error-icon';
                        feedback.prepend(icon);
                    }
                } else {
                    feedback.textContent = "";
                    feedback.className = "validation-feedback";

                    // Retirer toutes les icônes
                    const icon = feedback.querySelector('i');
                    if (icon) icon.remove();
                }
            }

            return isValid;
        }
        // Retirer les classes si le champ n'a pas été touché ou n'est pas requis
        else {
            input.classList.remove("is-valid", "is-invalid", "warning-border");

            if (feedback) {
                feedback.textContent = "";
                feedback.className = "validation-feedback";

                // Retirer toutes les icônes
                const icon = feedback.querySelector('i');
                if (icon) icon.remove();
            }

            return !isRequired;
        }
    }

    // Fonction pour valider un champ individuel
    function validateField(input) {
        const isEmpty = !input.value.trim();
        return updateFieldDisplay(input, isEmpty);
    }

    // Fonction pour marquer un champ comme touché
    function markFieldAsTouched(input) {
        const formId = input.form.id;

        if (!touchedFields[formId]) {
            touchedFields[formId] = {};
        }

        touchedFields[formId][input.id] = true;
    }

    // NOUVELLE FONCTION: Vérifier spécifiquement les profils sélectionnés
    function checkProfilesSelection(form) {
        const checkedProfiles = form.querySelectorAll('input[name="profiles[]"]:checked');
        return checkedProfiles.length > 0;
    }

    // Fonction pour mettre à jour l'état du bouton de soumission
    function updateSubmitButton(form) {
        const formId = form.id;

        // Trouver le bouton de soumission associé au formulaire
        const submitButton = form.closest('.modal-content')?.querySelector('[type="submit"]');
        if (!submitButton) return;

        // MODIFICATION: Vérifier d'abord si au moins un profil est sélectionné
        const hasProfileSelected = checkProfilesSelection(form);

        // Si aucun profil n'est sélectionné, désactiver le bouton immédiatement
        if (!hasProfileSelected) {
            submitButton.disabled = true;
            return;
        }

        // Vérifier si le formulaire est valide
        const isFormValid = formValidity[formId] || false;

        // Mettre à jour l'état du bouton
        submitButton.disabled = !isFormValid;
    }

    // Auto-génération de l'email lorsque nom et prénom sont remplis
    function setupEmailAutoGeneration() {
        // Formulaire d'ajout
        const addForm = document.getElementById("utilisateurForm");
        if (addForm) {
            const nomInput = addForm.querySelector('[name="nom"]');
            const prenomInput = addForm.querySelector('[name="prenom"]');
            const emailInput = addForm.querySelector('[name="email"]');

            if (nomInput && prenomInput && emailInput) {
                const generateEmail = () => {
                    if (nomInput.value && prenomInput.value) {
                        const prenomFirstLetter = prenomInput.value.trim().charAt(0).toLowerCase();
                        const nomFormatted = nomInput.value.trim().toLowerCase();
                        emailInput.value = `${prenomFirstLetter}.${nomFormatted}@chuis.ma`;
                        // Déclencher la validation de l'email
                        if (touchedFields[addForm.id]?.[emailInput.id]) {
                            validateField(emailInput);
                            checkFormValidity(addForm);
                        }
                    }
                };

                nomInput.addEventListener('blur', generateEmail);
                prenomInput.addEventListener('blur', generateEmail);
            }
        }

        // Formulaires d'édition
        document.querySelectorAll("[id^='editUtilisateurForm-']").forEach(form => {
            const formId = form.id.split('-')[1];
            const nomInput = form.querySelector(`[id="edit_nom_${formId}"]`);
            const prenomInput = form.querySelector(`[id="edit_prenom_${formId}"]`);
            const emailInput = form.querySelector(`[id="edit_email_${formId}"]`);

            if (nomInput && prenomInput && emailInput) {
                const generateEmail = () => {
                    if (nomInput.value && prenomInput.value) {
                        const prenomFirstLetter = prenomInput.value.trim().charAt(0)
                            .toLowerCase();
                        const nomFormatted = nomInput.value.trim().toLowerCase();
                        emailInput.value = `${prenomFirstLetter}.${nomFormatted}@chuis.ma`;
                        // Déclencher la validation de l'email
                        if (touchedFields[form.id]?.[emailInput.id]) {
                            validateField(emailInput);
                            checkFormValidity(form);
                        }
                    }
                };

                nomInput.addEventListener('blur', generateEmail);
                prenomInput.addEventListener('blur', generateEmail);
            }
        });
    }

    // Fonction pour vérifier la validité globale du formulaire
    function checkFormValidity(form) {
        const formId = form.id;
        let isValid = true;

        // Vérifier tous les champs du formulaire SAUF les profils
        form.querySelectorAll("input, textarea, select").forEach(input => {
            // Ignorer les champs cachés et les profils
            if (input.type === 'hidden' || input.disabled || input.name === 'profiles[]') return;

            const fieldIsValid = validateField(input);
            isValid = isValid && fieldIsValid;
        });

        // Vérifier les profils séparément
        const profilesValid = validateProfiles(form);
        isValid = isValid && profilesValid;

        // Mettre à jour l'état global du formulaire
        formValidity[formId] = isValid;

        // Mettre à jour l'état du bouton de soumission
        updateSubmitButton(form);

        return isValid;
    }

    // Fonction pour vérifier si tous les champs du formulaire sont valides avant soumission
    function validateAllFields(form) {
        const formId = form.id;

        // Marquer tous les champs comme touchés
        form.querySelectorAll("input, textarea, select").forEach(input => {
            if (input.type !== 'hidden' && !input.disabled && input.name !== 'profiles[]') {
                markFieldAsTouched(input);
            }
        });

        // Vérifier la validité du formulaire
        return checkFormValidity(form);
    }

    // FONCTION MODIFIÉE: Ajouter des écouteurs aux badges de profils
    function setupProfileValidation(form) {
        const profileCheckboxes = form.querySelectorAll('input[name="profiles[]"]');

        profileCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                // Immédiatement vérifier et mettre à jour le bouton
                updateSubmitButton(form);
                // Puis faire la validation complète
                checkFormValidity(form);
            });
        });

        // Gérer aussi les clics sur les badges (si vous utilisez des badges cliquables)
        const profileBadges = form.querySelectorAll('.profile-badge');
        profileBadges.forEach(badge => {
            badge.addEventListener('click', () => {
                // Attendre que le changement de checkbox soit traité
                setTimeout(() => {
                    updateSubmitButton(form);
                    checkFormValidity(form);
                }, 10);
            });
        });
    }

    // Initialiser la validation pour un formulaire
    function initializeFormValidation(form) {
        if (!form) return;

        const formId = form.id;
        touchedFields[formId] = {};
        formValidity[formId] = false;

        // MODIFICATION: Désactiver le bouton de soumission au départ et vérifier les profils
        const submitButton = form.closest('.modal-content')?.querySelector('[type="submit"]');
        if (submitButton) {
            // Toujours désactiver au départ
            submitButton.disabled = true;

            // Vérifier immédiatement s'il y a des profils sélectionnés
            setTimeout(() => {
                updateSubmitButton(form);
            }, 100);
        }

        // Ajouter des écouteurs pour chaque champ
        form.querySelectorAll("input, textarea, select").forEach(input => {
            // Ignorer les checkboxes de profils (gérés séparément)
            if (input.name === 'profiles[]') return;

            // Quand l'utilisateur commence à taper
            input.addEventListener("input", () => {
                markFieldAsTouched(input);
                validateField(input);
                checkFormValidity(form);
            });

            // Quand l'utilisateur quitte le champ
            input.addEventListener("blur", () => {
                markFieldAsTouched(input);
                validateField(input);
                checkFormValidity(form);
            });

            // Pour les select
            if (input.tagName === 'SELECT') {
                input.addEventListener("change", () => {
                    markFieldAsTouched(input);
                    validateField(input);
                    checkFormValidity(form);
                });
            }
        });

        // Configurer la validation des profils
        setupProfileValidation(form);

        // Vérifier la validité initiale du formulaire (pour les formulaires d'édition pré-remplis)
        setTimeout(() => {
            checkFormValidity(form);
        }, 100);

        // Gestion spéciale pour la confirmation de mot de passe
        const passwordInput = form.querySelector("[name='password']");
        const confirmInput = form.querySelector("[name='password_confirmation']");

        if (passwordInput && confirmInput) {
            passwordInput.addEventListener("input", () => {
                if (touchedFields[formId]?.[confirmInput.id]) {
                    validateField(confirmInput);
                    checkFormValidity(form);
                }
            });
        }

        // Intercepter la soumission du formulaire
        form.addEventListener("submit", (e) => {
            if (!validateAllFields(form)) {
                e.preventDefault();
                e.stopPropagation();

                // Focus sur le premier champ invalide
                const firstInvalid = form.querySelector('.is-invalid, .warning-border');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });

        // Gérer le bouton submit externe
        if (submitButton) {
            submitButton.addEventListener("click", (e) => {
                e.preventDefault();
                if (validateAllFields(form)) {
                    form.submit();
                }
            });
        }
    }

    // Initialiser tous les formulaires de la page
    function initializeAllForms() {
        // Formulaire d'ajout
        const addForm = document.getElementById("utilisateurForm");
        if (addForm) {
            initializeFormValidation(addForm);
        }

        // Formulaires de modification
        document.querySelectorAll("[id^='editUtilisateurForm-']").forEach(editForm => {
            initializeFormValidation(editForm);
        });

        // Configurer l'auto-génération des emails
        setupEmailAutoGeneration();
    }

    // Réinitialiser un formulaire à la fermeture d'une modale
    function resetForm(form) {
        if (!form) return;

        const formId = form.id;
        touchedFields[formId] = {};
        formValidity[formId] = false;

        form.reset();

        form.querySelectorAll("input, textarea, select").forEach(input => {
            input.classList.remove("is-invalid", "is-valid", "warning-border");

            const feedback = getFeedbackElement(input);
            if (feedback) {
                feedback.textContent = "";
                feedback.className = "validation-feedback";

                // Retirer toutes les icônes
                const icon = feedback.querySelector('i');
                if (icon) icon.remove();
            }
        });

        // Réinitialiser les badges de profils
        const profileBadges = form.querySelectorAll('.profile-badge');
        profileBadges.forEach(badge => {
            badge.classList.remove('selected');
        });

        // Réinitialiser le feedback des profils
        const profilesFeedback = form.querySelector('#profiles-feedback');
        if (profilesFeedback) {
            profilesFeedback.textContent = "";
            profilesFeedback.style.display = 'none';
        }

        // MODIFICATION: Désactiver le bouton de soumission lors de la réinitialisation
        const submitButton = form.closest('.modal-content')?.querySelector('[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }
    }

    // Réinitialiser les formulaires quand on ferme les modales
    document.querySelectorAll(".modal").forEach(modal => {
        modal.addEventListener("hidden.bs.modal", () => {
            const form = modal.querySelector("form");
            if (form) {
                resetForm(form);
            }
        });
    });

    // Initialiser tous les formulaires
    initializeAllForms();

    // Ajouter des styles CSS pour les bordures d'avertissement et les icônes
    const style = document.createElement('style');
    style.textContent = `
            .warning-border {
                border-color: #ffc107 !important;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23ffc107'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23ffc107' stroke='none'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right calc(0.375em + 0.1875rem) center;
                background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
                padding-right: calc(1.5em + 0.75rem) !important;
            }
            .warning-icon {
                color: #ffc107;
            }
            .error-icon {
                color: #dc3545;
            }
            .text-warning {
                color: #ffc107 !important;
            }
        `;
    document.head.appendChild(style);
});
