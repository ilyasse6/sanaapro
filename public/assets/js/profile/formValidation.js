document.addEventListener("DOMContentLoaded", function () {
    // Règles de validation pour les champs
    const VALIDATION_RULES = {
        nom: {
            pattern: /^[A-Za-zÀ-ÿ\s'-]{3,100}$/,
            message:
                "Le nom doit contenir entre 3 et 100 caractères alphabétiques.",
            required: true,
        },
        description: {
            pattern: /^[A-Za-zÀ-ÿ\s'-]{10,1000}$/,
            message:
                "La description doit contenir entre 10 et 1000 caractères.",
            required: true,
        },
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
            const inputGroup = input.closest(".input-group");
            if (inputGroup) {
                inputGroup.parentNode.appendChild(feedback);
            } else {
                input.parentNode.appendChild(feedback);
            }
        }

        return feedback;
    }

    // Fonction pour mettre à jour l'affichage visuel d'un champ
    function updateFieldDisplay(input, isEmpty) {
        const feedback = getFeedbackElement(input);
        const baseFieldName = input.name.replace(/^edit_/, "");
        const rule = VALIDATION_RULES[baseFieldName];
        const isRequired =
            typeof rule?.required === "function"
                ? rule.required(input)
                : rule?.required;
        const formId = input.form.id;

        // Si le champ a été touché, est vide et requis
        if (touchedFields[formId]?.[input.id] && isEmpty && isRequired) {
            // Ajouter classe d'avertissement
            input.classList.add("warning-border");
            input.classList.remove("is-valid", "is-invalid");

            // Ajouter icône d'avertissement
            if (feedback) {
                //feedback.textContent = "Ce champ est requis.";
                feedback.textContent = "";
                feedback.className = "validation-feedback text-warning mt-1";
            }

            return false;
        }
        // Validation du contenu si le champ n'est pas vide
        else if (!isEmpty) {
            let isValid = rule ? rule.pattern.test(input.value) : true;
            let message = rule ? rule.message : "";

            input.classList.remove("warning-border");
            input.classList.toggle("is-valid", isValid);
            input.classList.toggle("is-invalid", !isValid);

            if (feedback) {
                if (!isValid) {
                    feedback.textContent = message;
                    feedback.className = "validation-feedback text-danger mt-1";

                    // Ajouter icône d'erreur
                    if (!feedback.querySelector(".error-icon")) {
                        const icon = document.createElement("i");
                        icon.className = "bi bi-x-circle-fill me-1 error-icon";
                        feedback.prepend(icon);
                    }
                } else {
                    feedback.textContent = "";
                    feedback.className = "validation-feedback";

                    // Retirer toutes les icônes
                    const icon = feedback.querySelector("i");
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
                const icon = feedback.querySelector("i");
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

    // Fonction pour mettre à jour l'état du bouton de soumission
    function updateSubmitButton(form) {
        const formId = form.id;

        // Trouver le bouton de soumission associé au formulaire
        const submitButton = form
            .closest(".modal-content")
            ?.querySelector('[type="submit"]');
        if (!submitButton) return;

        // Vérifier si le formulaire est valide
        const isFormValid = formValidity[formId] || false;

        // Mettre à jour l'état du bouton
        submitButton.disabled = !isFormValid;
    }

    // Fonction pour vérifier la validité globale du formulaire
    function checkFormValidity(form) {
        const formId = form.id;
        let isValid = true;

        // Vérifier tous les champs du formulaire
        form.querySelectorAll("input, textarea").forEach((input) => {
            // Ignorer les champs cachés
            if (input.type === "hidden" || input.disabled) return;

            const fieldIsValid = validateField(input);
            isValid = isValid && fieldIsValid;
        });

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
        form.querySelectorAll("input, textarea").forEach((input) => {
            if (input.type !== "hidden" && !input.disabled) {
                markFieldAsTouched(input);
            }
        });

        // Vérifier la validité du formulaire
        return checkFormValidity(form);
    }

    // Initialiser la validation pour un formulaire
    function initializeFormValidation(form) {
        if (!form) return;

        const formId = form.id;
        touchedFields[formId] = {};
        formValidity[formId] = false;

        // Désactiver le bouton de soumission au départ
        const submitButton = form
            .closest(".modal-content")
            ?.querySelector('[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }

        // Ajouter des écouteurs pour chaque champ
        form.querySelectorAll("input, textarea").forEach((input) => {
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

            // Pour les textarea
            if (input.tagName === "TEXTAREA") {
                input.addEventListener("change", () => {
                    markFieldAsTouched(input);
                    validateField(input);
                    checkFormValidity(form);
                });
            }
        });

        // Intercepter la soumission du formulaire
        form.addEventListener("submit", (e) => {
            if (!validateAllFields(form)) {
                e.preventDefault();
                e.stopPropagation();

                // Focus sur le premier champ invalide
                const firstInvalid = form.querySelector(
                    ".is-invalid, .warning-border"
                );
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });

        // Gérer le bouton submit externe
        if (submitButton) {
            submitButton.addEventListener("click", () => {
                if (validateAllFields(form)) {
                    form.submit();
                }
            });
        }
    }

    // Initialiser tous les formulaires de la page
    function initializeAllForms() {
        // Formulaire d'ajout
        const addForm = document
            .getElementById("AddModal")
            ?.querySelector("form");
        if (addForm) {
            initializeFormValidation(addForm);
        }

        // Formulaires de modification
        document.querySelectorAll("[id^='EditModal-']").forEach((modal) => {
            const editForm = modal.querySelector("form");
            if (editForm) {
                initializeFormValidation(editForm);
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

        form.querySelectorAll("input, textarea").forEach((input) => {
            input.classList.remove("is-invalid", "is-valid", "warning-border");

            const feedback = getFeedbackElement(input);
            if (feedback) {
                feedback.textContent = "";
                feedback.className = "validation-feedback";

                // Retirer toutes les icônes
                const icon = feedback.querySelector("i");
                if (icon) icon.remove();
            }
        });

        // Désactiver le bouton de soumission
        const submitButton = form
            .closest(".modal-content")
            ?.querySelector('[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }
    }

    // Réinitialiser les formulaires quand on ferme les modales
    document.querySelectorAll(".modal").forEach((modal) => {
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
    const style = document.createElement("style");
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
