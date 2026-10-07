document.addEventListener("DOMContentLoaded", function() {
    // Règles de validation pour les champs de l'établissement
    const VALIDATION_RULES = {
        abbreviation: {
            pattern: /^[A-Za-z0-9\s\-_]{2,10}$/,
            message: "L'abréviation doit contenir entre 2 et 10 caractères (lettres, chiffres, espaces, tirets, underscores).",
            required: true
        },
        description: {
            pattern: /^[\s\S]{4,200}$/,
            message: "La description doit contenir entre 4 et 200 caractères.",
            required: true
        },
        type: {
            required: true,
            message: "Veuillez sélectionner un type."
        }
    };

    // États des champs touchés pour chaque formulaire
    const touchedFields = {};
    // État de validité global pour chaque formulaire
    const formValidity = {};

    // Fonction pour obtenir l'élément de feedback
    function getFeedbackElement(input) {
        const feedbackId = input.id + "-feedback";
        return document.getElementById(feedbackId);
    }

    // Fonction pour valider un champ select
    function validateSelectField(select) {
        const fieldName = select.name;
        const rule = VALIDATION_RULES[fieldName];
        const isEmpty = !select.value;
        const feedback = getFeedbackElement(select);
        const formId = select.form.id;

        // Si le champ a été touché et est vide mais requis
        if (touchedFields[formId]?.[select.id] && isEmpty && rule?.required) {
            select.classList.add("warning-border");
            select.classList.remove("is-valid", "is-invalid");

            if (feedback) {
                feedback.textContent = "";
                feedback.className = "validation-feedback text-warning mt-1";
            }
            return false;
        }
        // Si le champ n'est pas vide
        else if (!isEmpty) {
            select.classList.remove("warning-border", "is-invalid");
            select.classList.add("is-valid");

            if (feedback) {
                feedback.textContent = "";
                feedback.className = "validation-feedback";
            }
            return true;
        }
        // Champ vide mais pas encore touché
        else {
            select.classList.remove("is-valid", "is-invalid", "warning-border");
            if (feedback) {
                feedback.textContent = "";
                feedback.className = "validation-feedback";
            }
            return !rule?.required;
        }
    }

    // Fonction pour mettre à jour l'affichage visuel d'un champ
    function updateFieldDisplay(input, isEmpty) {
        const feedback = getFeedbackElement(input);
        const fieldName = input.name;
        const isRequired = VALIDATION_RULES[fieldName]?.required;
        const formId = input.form.id;

        // Si le champ a été touché, est vide et requis
        if (touchedFields[formId]?.[input.id] && isEmpty && isRequired) {
            input.classList.add("warning-border");
            input.classList.remove("is-valid", "is-invalid");

            if (feedback) {
                feedback.textContent = "";
                feedback.className = "validation-feedback text-warning mt-1";
            }
            return false;
        }
        // Validation du contenu si le champ n'est pas vide
        else if (!isEmpty) {
            const rule = VALIDATION_RULES[fieldName];
            let isValid = rule ? rule.pattern.test(input.value) : true;
            let message = rule ? rule.message : "";

            input.classList.remove("warning-border");
            input.classList.toggle("is-valid", isValid);
            input.classList.toggle("is-invalid", !isValid);

            if (feedback) {
                if (!isValid) {
                    feedback.textContent = message;
                    feedback.className = "validation-feedback text-danger mt-1";

                    if (!feedback.querySelector('.error-icon')) {
                        const icon = document.createElement('i');
                        icon.className = 'bi bi-x-circle-fill me-1 error-icon';
                        feedback.prepend(icon);
                    }
                } else {
                    feedback.textContent = "";
                    feedback.className = "validation-feedback";

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

                const icon = feedback.querySelector('i');
                if (icon) icon.remove();
            }
            return !isRequired;
        }
    }

    // Fonction pour valider un champ individuel
    function validateField(input) {
        if (input.tagName.toLowerCase() === 'select') {
            return validateSelectField(input);
        }
        
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

    // Fonction pour mettre à jour l'état du bouton de soumission
    function updateSubmitButton(form) {
        const formId = form.id;
        const submitButton = form.closest('.modal-content')?.querySelector('[type="submit"]');
        if (!submitButton) return;

        const isFormValid = formValidity[formId] || false;
        submitButton.disabled = !isFormValid;
    }

    // Fonction pour vérifier la validité globale du formulaire
    function checkFormValidity(form) {
        const formId = form.id;
        let isValid = true;

        // Vérifier tous les champs
        form.querySelectorAll("input[type='text'], textarea, select").forEach(input => {
            if (VALIDATION_RULES[input.name]) {
                const fieldIsValid = validateField(input);
                isValid = isValid && fieldIsValid;
            }
        });

        formValidity[formId] = isValid;
        updateSubmitButton(form);
        return isValid;
    }

    // Fonction pour vérifier si tous les champs du formulaire sont valides avant soumission
    function validateAllFields(form) {
        const formId = form.id;

        // Marquer tous les champs comme touchés
        form.querySelectorAll("input, textarea, select").forEach(input => {
            markFieldAsTouched(input);
        });

        return checkFormValidity(form);
    }

    // Initialiser la validation pour un formulaire
    function initializeFormValidation(form) {
        if (!form) return;

        const formId = form.id;
        touchedFields[formId] = {};
        formValidity[formId] = false;

        // Désactiver le bouton de soumission au départ
        const submitButton = form.closest('.modal-content')?.querySelector('[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }

        // Ajouter des éléments de feedback si nécessaire
        form.querySelectorAll("input[type='text'], textarea, select").forEach(input => {
            if (VALIDATION_RULES[input.name] && !getFeedbackElement(input)) {
                const feedback = document.createElement('div');
                feedback.id = input.id + '-feedback';
                feedback.className = 'validation-feedback';
                input.parentNode.appendChild(feedback);
            }
        });

        // Ajouter des écouteurs pour chaque champ
        form.querySelectorAll("input[type='text'], textarea, select").forEach(input => {
            // Quand l'utilisateur commence à taper ou change de sélection
            input.addEventListener("input", () => {
                markFieldAsTouched(input);
                validateField(input);
                checkFormValidity(form);
            });

            input.addEventListener("change", () => {
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
        });

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
    }

    // Initialiser tous les formulaires de la page
    function initializeAllForms() {
        // Formulaire d'ajout
        const addForm = document.getElementById("entrepriseForm");
        if (addForm) {
            initializeFormValidation(addForm);

            const addButton = document.querySelector("#AddEntrepriseModal .modal-footer [type='submit']");
            if (addButton) {
                addButton.addEventListener("click", (e) => {
                    e.preventDefault();
                    if (validateAllFields(addForm)) {
                        addForm.submit();
                    }
                });
            }
        }

        // Formulaires de modification
        document.querySelectorAll("[id^='editEntrepriseForm-']").forEach(editForm => {
            initializeFormValidation(editForm);

            const etablissementId = editForm.id.replace("editEntrepriseForm-", "");
            const editButton = document.querySelector(`#EditModal-${etablissementId} .modal-footer [type='submit']`);

            if (editButton) {
                editButton.addEventListener("click", (e) => {
                    e.preventDefault();
                    if (validateAllFields(editForm)) {
                        editForm.submit();
                    }
                });
            }
        });
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

                const icon = feedback.querySelector('i');
                if (icon) icon.remove();
            }
        });

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

    // Ajouter des styles CSS
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
        .validation-feedback {
            display: block;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
    `;
    document.head.appendChild(style);
});