document.addEventListener("DOMContentLoaded", function () {
    // Gestion de l'ajout de client via AJAX
    const clientForm = document.getElementById('clientForm');
    const submitClientButton = document.getElementById('submitClientButton');

    if (submitClientButton && clientForm) {
        submitClientButton.addEventListener('click', function(e) {
            e.preventDefault();
            handleClientFormSubmit(clientForm, 'store');
        });
    }

    // Gestion de la modification de client via AJAX
    const editForms = document.querySelectorAll('[id^="editClientForm-"]');
    editForms.forEach(form => {
        const formId = form.id;
        const clientId = formId.replace('editClientForm-', '');
        const submitButton = document.getElementById(`submitEditClientButton-${clientId}`);
        
        if (submitButton) {
            submitButton.addEventListener('click', function(e) {
                e.preventDefault();
                handleClientFormSubmit(form, 'update', clientId);
            });
        }
    });
});

function handleClientFormSubmit(form, action, clientId = null) {
    // Valider le formulaire avant de soumettre
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const url = form.action;
    const method = form.method;

    // Trouver le bon bouton de soumission
    const submitButton = document.getElementById(action === 'store' ? 'submitClientButton' : `submitEditClientButton-${clientId}`);
    if (!submitButton) {
        console.error('Bouton de soumission non trouvé');
        return;
    }

    // Afficher un loader
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>En cours...';

    // Effacer les erreurs précédentes
    form.querySelectorAll('.error-message').forEach(el => el.innerHTML = '');
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                           document.querySelector('input[name="_token"]')?.value
        }
    })
    .then(response => {
        if (response.status === 422) {
            return response.json().then(data => {
                throw new Error(JSON.stringify(data));
            });
        } else if (response.status === 403) {
            throw new Error('Vous n\'avez pas la permission d\'effectuer cette action');
        } else if (response.status === 401) {
            throw new Error('Votre session a expiré. Veuillez vous reconnecter');
        } else if (response.status === 500) {
            return response.json().then(data => {
                const errorMsg = data.message || data.error || 'Erreur interne du serveur';
                const error = new Error(errorMsg);
                error.data = data;
                throw error;
            }).catch((err) => {
                // Si la réponse n'est pas du JSON valide
                return response.text().then(text => {
                    const error = new Error('Erreur interne du serveur');
                    error.details = text;
                    throw error;
                });
            });
        } else if (response.ok) {
            return response.json();
        } else {
            throw new Error(`Erreur HTTP ${response.status}`);
        }
    })
    .then(data => {
        if (data.success) {
            // Afficher un message de succès
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: data.message,
                confirmButtonText: 'OK',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                // Fermer le modal
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                if (modal) {
                    modal.hide();
                }
                // Recharger la page pour afficher les nouvelles données
                window.location.reload();
            });
        } else {
            throw new Error(data.message || 'Une erreur est survenue');
        }
    })
    .catch(error => {
        let errorMessage = 'Une erreur est survenue';
        let errors = {};

        try {
            const errorData = JSON.parse(error.message);
            if (errorData.errors) {
                errors = errorData.errors;
                errorMessage = errorData.message || 'Erreur de validation';
            } else {
                errorMessage = errorData.message || error.message;
            }
        } catch (e) {
            errorMessage = error.message;
        }

        // Afficher les erreurs de validation
        if (Object.keys(errors).length > 0) {
            // Afficher les erreurs sous chaque champ
            Object.keys(errors).forEach(key => {
                const errorField = form.querySelector(`#error-${key}`);
                const inputField = form.querySelector(`[name="${key}"]`);
                if (errorField) {
                    errorField.innerHTML = `<span class="text-danger small">${errors[key][0]}</span>`;
                }
                if (inputField) {
                    inputField.classList.add('is-invalid');
                }
            });

            // Afficher aussi une alerte globale
            let errorHtml = '<ul class="text-start">';
            Object.keys(errors).forEach(key => {
                errors[key].forEach(msg => {
                    errorHtml += `<li>${msg}</li>`;
                });
            });
            errorHtml += '</ul>';

            Swal.fire({
                icon: 'error',
                title: 'Erreur de validation',
                html: errorHtml,
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'modal-modern'
                }
            });
        } else {
            // Afficher le message d'erreur avec plus de détails si disponibles
            let errorDetails = errorMessage;
            if (error.data && error.data.error) {
                errorDetails += '\n\nDétails: ' + error.data.error;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: errorDetails,
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'modal-modern'
                },
                footer: '<small class="text-muted">Vérifiez que la table clients existe dans la base de données</small>'
            });
        }
    })
    .finally(() => {
        // Restaurer le bouton
        const submitButton = document.getElementById(action === 'store' ? 'submitClientButton' : `submitEditClientButton-${clientId}`);
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = action === 'store' 
                ? '<i class="bi bi-plus-circle me-1"></i>Ajouter le client'
                : '<i class="bi bi-save me-1"></i>Enregistrer';
        }
    });
}

// Fonction pour désactiver un client via AJAX
function desactivateClient(clientId) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: "Cette action désactivera le client",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            var baseUrl = document.querySelector('meta[name="app-url"]')?.getAttribute('content') || (window.location.origin + window.location.pathname.replace(/\/clients.*$/, ''));
            var url = baseUrl.replace(/\/$/, '') + '/clients/' + clientId + '/desactivate';
            var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                         document.querySelector('input[name="_token"]')?.value;
            if (!token) {
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Jeton de sécurité manquant. Rechargez la page.' });
                return;
            }
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(function(response) {
                return response.text().then(function(text) {
                    try {
                        return { ok: response.ok, data: JSON.parse(text) };
                    } catch (e) {
                        return { ok: false, data: { success: false, message: 'Réponse invalide (#' + response.status + ')' } };
                    }
                });
            })
            .then(function(result) {
                if (result.ok && result.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: result.data.message,
                        confirmButtonText: 'OK',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(function() { window.location.reload(); });
                } else {
                    var msg = (result.data && result.data.message) || 'Une erreur est survenue lors de la désactivation';
                    if (result.data && result.data.error) { msg += ' — ' + result.data.error; }
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: msg,
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(function(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de contacter le serveur. Vérifiez votre connexion.',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

// Fonction pour activer un client via AJAX
function activateClient(clientId) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: "Cette action activera le client",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Oui, activer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            const baseUrl = document.querySelector('meta[name="app-url"]')?.getAttribute('content') || (window.location.origin + window.location.pathname.replace(/\/clients.*$/, ''));
            const url = (baseUrl.replace(/\/$/, '') + '/clients/' + clientId + '/activate');
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                         document.querySelector('input[name="_token"]')?.value;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: data.message,
                        confirmButtonText: 'OK',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.message || 'Une erreur est survenue',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de l\'activation',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

