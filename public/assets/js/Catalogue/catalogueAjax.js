document.addEventListener("DOMContentLoaded", function () {
    // Charger les services pour les selects
    loadServicesForSelects();

    // Gestion de l'ajout de service
    const addServiceForm = document.getElementById('addServiceForm');
    if (addServiceForm) {
        addServiceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleServiceFormSubmit(addServiceForm, 'store');
        });
    }

    // Gestion de l'ajout de pack
    const addPackForm = document.getElementById('addPackForm');
    if (addPackForm) {
        addPackForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handlePackFormSubmit(addPackForm, 'store');
        });
    }

    // Gestion de la modification de service
    const editServiceForms = document.querySelectorAll('.edit-service-form');
    editServiceForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const serviceId = form.querySelector('button[type="submit"]').getAttribute('data-service-id');
            handleServiceFormSubmit(form, 'update', serviceId);
        });
    });

    // Gestion de la modification de pack
    const editPackForms = document.querySelectorAll('.edit-pack-form');
    editPackForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const packId = form.querySelector('button[type="submit"]').getAttribute('data-pack-id');
            handlePackFormSubmit(form, 'update', packId);
        });
    });
});

// Charger les services pour les selects
function loadServicesForSelects() {
    return fetch('/catalogue/services/list', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.services) {
            // Remplir le select pour ajouter un pack
            const addPackSelect = document.getElementById('pack_services');
            if (addPackSelect) {
                addPackSelect.innerHTML = '';
                data.services.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = `${service.nom} - ${service.prix} DH`;
                    addPackSelect.appendChild(option);
                });
            }

            // Remplir les selects pour modifier les packs
            const editPackSelects = document.querySelectorAll('.edit-pack-services');
            editPackSelects.forEach(select => {
                const currentValues = Array.from(select.selectedOptions).map(opt => opt.value);
                select.innerHTML = '';
                data.services.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = `${service.nom} - ${service.prix} DH`;
                    if (currentValues.includes(String(service.id))) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            });
        }
        return data;
    })
    .catch(error => {
        console.error('Erreur lors du chargement des services:', error);
        return null;
    });
}

// Charger les services d'un pack pour l'édition
function loadPackServices(packId, selectElement) {
    fetch(`/catalogue/packs/${packId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.service_ids) {
            // Sélectionner les services du pack
            Array.from(selectElement.options).forEach(option => {
                if (data.service_ids.includes(parseInt(option.value))) {
                    option.selected = true;
                }
            });
        }
    })
    .catch(error => {
        console.error('Erreur lors du chargement des services du pack:', error);
    });
}

// Charger les services d'un pack quand le modal s'ouvre
document.addEventListener('DOMContentLoaded', function() {
    // Modal d'ajout de pack
    const addPackModal = document.getElementById('AddPackModal');
    if (addPackModal) {
        addPackModal.addEventListener('show.bs.modal', function() {
            loadServicesForSelects();
        });
    }

    // Modals d'édition de pack
    const editPackModals = document.querySelectorAll('[id^="EditPackModal-"]');
    editPackModals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            const packId = modal.id.replace('EditPackModal-', '');
            const selectElement = document.getElementById(`pack_services_edit-${packId}`);
            if (selectElement) {
                // Recharger les services et sélectionner ceux du pack
                loadServicesForSelects().then(() => {
                    loadPackServices(packId, selectElement);
                });
            }
        });
    });
});

// Gérer la soumission du formulaire de service
function handleServiceFormSubmit(form, action, serviceId = null) {
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const url = action === 'store' 
        ? '/catalogue/services/store' 
        : `/catalogue/services/${serviceId}`;
    const method = action === 'store' ? 'POST' : 'PUT';

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>En cours...';

    // Effacer les erreurs
    form.querySelectorAll('.error-message').forEach(el => el.innerHTML = '');
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        
        // Vérifier si la réponse est du JSON
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Réponse non-JSON reçue:', text.substring(0, 200));
            throw new Error('Le serveur a renvoyé une réponse non-JSON. Vérifiez la console pour plus de détails.');
        }

        const data = await response.json();

        if (response.status === 422) {
            throw new Error(JSON.stringify(data));
        } else if (!response.ok) {
            throw new Error(data.message || 'Une erreur est survenue');
        }
        
        return data;
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        }
    })
    .catch(error => {
        try {
            const errorData = JSON.parse(error.message);
            if (errorData.errors) {
                // Afficher les erreurs de validation
                Object.keys(errorData.errors).forEach(field => {
                    const errorElement = document.getElementById(`error-${field}`) || 
                                       document.getElementById(`error-edit-${field}-${serviceId}`);
                    if (errorElement) {
                        errorElement.innerHTML = errorData.errors[field][0];
                        const inputElement = form.querySelector(`[name="${field}"]`);
                        if (inputElement) {
                            inputElement.classList.add('is-invalid');
                        }
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: errorData.message || 'Une erreur est survenue'
                });
            }
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: error.message || 'Une erreur est survenue. Vérifiez la console pour plus de détails.'
            });
        }
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
}

// Gérer la soumission du formulaire de pack
function handlePackFormSubmit(form, action, packId = null) {
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const url = action === 'store' 
        ? '/catalogue/packs/store' 
        : `/catalogue/packs/${packId}`;
    const method = action === 'store' ? 'POST' : 'PUT';

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>En cours...';

    // Effacer les erreurs
    form.querySelectorAll('.error-message').forEach(el => el.innerHTML = '');
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        
        // Vérifier si la réponse est du JSON
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Réponse non-JSON reçue:', text.substring(0, 200));
            throw new Error('Le serveur a renvoyé une réponse non-JSON. Vérifiez la console pour plus de détails.');
        }

        const data = await response.json();

        if (response.status === 422) {
            throw new Error(JSON.stringify(data));
        } else if (!response.ok) {
            throw new Error(data.message || 'Une erreur est survenue');
        }
        
        return data;
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        }
    })
    .catch(error => {
        try {
            const errorData = JSON.parse(error.message);
            if (errorData.errors) {
                // Afficher les erreurs de validation
                Object.keys(errorData.errors).forEach(field => {
                    const errorElement = document.getElementById(`error-${field}`) || 
                                       document.getElementById(`error-edit-${field}-${packId}`);
                    if (errorElement) {
                        errorElement.innerHTML = errorData.errors[field][0];
                        const inputElement = form.querySelector(`[name="${field}"]`);
                        if (inputElement) {
                            inputElement.classList.add('is-invalid');
                        }
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: errorData.message || 'Une erreur est survenue'
                });
            }
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: error.message || 'Une erreur est survenue. Vérifiez la console pour plus de détails.'
            });
        }
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
}

// Fonction pour désactiver un service ou pack
function desactivateItem(type, id) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: `Voulez-vous vraiment supprimer ce ${type} ?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/catalogue/${type}/${id}/desactivate`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Une erreur est survenue');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Supprimé',
                        text: data.message || `${type} supprimé avec succès`,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.message || 'Une erreur est survenue'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de la suppression'
                });
            });
        }
    });
}

