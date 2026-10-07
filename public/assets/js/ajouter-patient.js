// ajouter-patient.js
document.addEventListener("DOMContentLoaded", function() {
    // Règles de validation pour les champs
    const VALIDATION_RULES = {
        nom: {
            pattern: /^[A-Za-zÀ-ÿ\s'-]{3,50}$/,
            message: "Le nom doit contenir entre 3 et 50 caractères alphabétiques.",
            required: true
        },
        prenom: {
            pattern: /^[A-Za-zÀ-ÿ\s'-]{3,50}$/,
            message: "Le prénom doit contenir entre 3 et 50 caractères alphabétiques.",
            required: true
        },
        cin: {
            pattern: /^[A-Z0-9]{6,10}$/,
            message: "Le CIN doit contenir entre 6 et 10 caractères alphanumériques.",
            required: true
        },
        daten: {
            validate: function(input) {
                const birthDate = new Date(input.value);
                const today = new Date();
                const minDate = new Date();
                minDate.setFullYear(today.getFullYear() - 90);
                const maxDate = new Date();
                maxDate.setFullYear(today.getFullYear() - 15);

                return birthDate >= minDate && birthDate <= maxDate;
            },
            message: "L'âge doit être entre 15 et 90 ans.",
            required: true
        },
        poids: {
            validate: function(input) {
                const value = parseFloat(input.value);
                return value >= 1 && value <= 250;
            },
            message: "Le poids doit être entre 1 et 250 kg.",
            required: false
        },
        taille: {
            validate: function(input) {
                const value = parseFloat(input.value);
                return value >= 30 && value <= 250;
            },
            message: "La taille doit être entre 30 et 250 cm.",
            required: false
        },
        diametre: {
            validate: function(input) {
                const value = parseFloat(input.value);
                return value >= 2 && value <= 40;
            },
            message: "Le diamètre doit être entre 2 et 40 cm.",
            required: false
        },
        tel1: {
            pattern: /^[6-8]\d{8}$/,
            message: "Le téléphone doit commencer par 6,7 ou 8 et contenir 9 chiffres.",
            required: true
        },
        tel2: {
            pattern: /^([6-8]\d{8})?$/,
            message: "Le téléphone doit commencer par 6,7 ou 8 et contenir 9 chiffres.",
            required: false
        }
    };

    // États des champs touchés
    const touchedFields = {};
    // État de validité global
    let formIsValid = false;

    // Fonction pour obtenir l'élément de feedback
    function getFeedbackElement(input) {
        const feedbackId = input.id + "-feedback";
        let feedback = document.getElementById(feedbackId);

        if (!feedback) {
            feedback = document.createElement("div");
            feedback.id = feedbackId;
            feedback.className = "invalid-feedback";
            input.parentNode.appendChild(feedback);
        }

        return feedback;
    }

    // Fonction pour calculer l'IMC et déterminer le statut
    function calculateIMCStatus(poids, taille, sexe, age) {
        if (!poids || !taille || taille <= 0) return "Inconnu";

        const tailleEnM = taille / 100;
        const imc = poids / (tailleEnM * tailleEnM);

        // Déterminer le statut selon l'âge et le sexe
        if (age < 18) {
            // Normes pour enfants (simplifiées)
            if (imc < 16) return "Insuffisance pondérale";
            if (imc >= 16 && imc < 25) return "Normal";
            return "Surpoids";
        } else {
            // Normes pour adultes
            if (sexe === "Femme") {
                if (imc < 18.5) return "Insuffisance pondérale";
                if (imc >= 18.5 && imc < 25) return "Normal";
                if (imc >= 25 && imc < 30) return "Surpoids";
                return "Obésité";
            } else {
                if (imc < 20) return "Insuffisance pondérale";
                if (imc >= 20 && imc < 25) return "Normal";
                if (imc >= 25 && imc < 30) return "Surpoids";
                return "Obésité";
            }
        }
    }

    // Fonction pour mettre à jour l'affichage de l'IMC
    function updateIMCDisplay() {
        const poids = parseFloat(document.getElementById('poids').value);
        const taille = parseFloat(document.getElementById('taille').value);
        const sexe = document.querySelector('input[name="sexe"]:checked')?.value;
        const age = parseInt(document.getElementById('age').value) || 0;

        if (poids && taille) {
            const status = calculateIMCStatus(poids, taille, sexe, age);
            document.getElementById('imc').value = (poids / ((taille/100) * (taille/100))).toFixed(2);

            const imcFeedback = document.getElementById('imc-feedback');
            if (imcFeedback) {
                imcFeedback.textContent = `Statut: ${status}`;
                imcFeedback.className = "form-text";

                if (status === "Normal") {
                    imcFeedback.classList.add("text-success");
                } else {
                    imcFeedback.classList.add("text-warning");
                }
            }
        }
    }

    // Fonction pour mettre à jour l'affichage visuel d'un champ
    function updateFieldDisplay(input, isEmpty) {
        const feedback = getFeedbackElement(input);
        const fieldName = input.name;
        const rule = VALIDATION_RULES[fieldName];
        const isRequired = rule?.required || false;

        // Si le champ a été touché, est vide et requis
        if (touchedFields[input.id] && isEmpty && isRequired) {
            input.classList.add("is-invalid");
            input.classList.remove("is-valid");
            feedback.textContent = "Ce champ est requis.";
            return false;
        }
        // Validation du contenu si le champ n'est pas vide
        else if (!isEmpty) {
            let isValid;
            if (rule?.validate) {
                isValid = rule.validate(input);
            } else if (rule?.pattern) {
                isValid = rule.pattern.test(input.value);
            } else {
                isValid = true;
            }

            input.classList.toggle("is-valid", isValid);
            input.classList.toggle("is-invalid", !isValid);
            feedback.textContent = isValid ? "" : rule.message;
            return isValid;
        }
        // Retirer les classes si le champ n'a pas été touché ou n'est pas requis
        else {
            input.classList.remove("is-valid", "is-invalid");
            feedback.textContent = "";
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
        touchedFields[input.id] = true;
    }

    // Fonction pour vérifier la validité globale du formulaire
    function checkFormValidity() {
        let isValid = true;
        const form = document.querySelector('form');

        // Vérifier tous les champs du formulaire
        form.querySelectorAll("input, select, textarea").forEach(input => {
            // Ignorer les champs cachés et désactivés
            if (input.type === 'hidden' || input.disabled) return;

            const fieldIsValid = validateField(input);
            isValid = isValid && fieldIsValid;
        });

        // Mettre à jour l'état global du formulaire
        formIsValid = isValid;

        // Mettre à jour l'état du bouton de soumission
        updateSubmitButton();

        return isValid;
    }

    // Fonction pour mettre à jour l'état du bouton de soumission
    function updateSubmitButton() {
        const submitButton = document.getElementById('possible');
        const impossibleButton = document.getElementById('impossible');

        if (formIsValid) {
            submitButton.style.display = '';
            impossibleButton.style.display = 'none';
        } else {
            submitButton.style.display = 'none';
            impossibleButton.style.display = '';
        }
    }

    // Fonction pour réinitialiser le formulaire
    function resetForm() {
        const form = document.querySelector('form');
        form.reset();

        // Réinitialiser le groupe sanguin par défaut
        document.getElementById('groupesang').value = "O+";

        // Réinitialiser les états de validation
        Object.keys(touchedFields).forEach(key => delete touchedFields[key]);
        formIsValid = false;

        // Réinitialiser l'affichage des champs
        form.querySelectorAll("input, select, textarea").forEach(input => {
            input.classList.remove("is-valid", "is-invalid");

            const feedback = getFeedbackElement(input);
            if (feedback) {
                feedback.textContent = "";
            }
        });

        // Mettre à jour le bouton de soumission
        updateSubmitButton();
    }

    // Initialiser la validation
    function initializeValidation() {
        const form = document.querySelector('form');

        // Ajouter des écouteurs pour chaque champ
        form.querySelectorAll("input, select, textarea").forEach(input => {
            // Ignorer les champs cachés et les boutons
            if (input.type === 'hidden' || input.type === 'submit' || input.type === 'reset') return;

            // Quand l'utilisateur commence à taper
            input.addEventListener("input", function() {
                markFieldAsTouched(this);

                // Mettre à jour l'IMC si poids ou taille change
                if (this.id === 'poids' || this.id === 'taille') {
                    updateIMCDisplay();
                }

                validateField(this);
                checkFormValidity();
            });

            // Quand l'utilisateur quitte le champ
            input.addEventListener("blur", function() {
                markFieldAsTouched(this);
                validateField(this);
                checkFormValidity();
            });
        });

        // Gérer le changement de sexe pour l'IMC
        document.querySelectorAll('input[name="sexe"]').forEach(radio => {
            radio.addEventListener("change", function() {
                if (document.getElementById('poids').value && document.getElementById('taille').value) {
                    updateIMCDisplay();
                }
            });
        });

        // Intercepter la soumission du formulaire
        form.addEventListener("submit", function(e) {
            if (!checkFormValidity()) {
                e.preventDefault();
                e.stopPropagation();

                // Focus sur le premier champ invalide
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });

        // Gérer le bouton de réinitialisation
        document.querySelector('a.btn-warning').addEventListener("click", function(e) {
            e.preventDefault();
            resetForm();
        });

        // Initialiser l'affichage de l'IMC si des valeurs existent
        if (document.getElementById('poids').value && document.getElementById('taille').value) {
            updateIMCDisplay();
        }

        // Vérifier la validité initiale
        checkFormValidity();
    }

    // Initialiser la validation lorsque le DOM est chargé
    initializeValidation();
});
