<!-- Modal Planifier une visite -->
<div class="modal fade modal-modern" id="AddRendezVousModal" tabindex="-1" aria-labelledby="AddRendezVousModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AddRendezVousModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>
                    Planifier une visite
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('rendez-vous.store') }}" method="POST" id="formAddRendezVous">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Sélection du client -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-building me-2 text-primary"></i>
                                Client (raison sociale / contact) <span class="text-danger">*</span>
                            </label>
                            <select class="form-control modern-input" name="client_id" id="rdv_client_id" required>
                                <option value="">Choisir un client (entreprise / contact)</option>
                                @foreach($clients ?? [] as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->nom }}@if($client->prenom) – {{ $client->prenom }}@endif
                                        @if($client->telephone)
                                            · {{ $client->telephone }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="error-message" id="error-rdv_client_id"></div>
                        </div>

                        <!-- Date et heure -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-calendar3 me-2 text-primary"></i>
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control modern-input" name="date_rdv" id="rdv_date"
                                value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                            <div class="error-message" id="error-rdv_date"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-clock me-2 text-primary"></i>
                                Heure <span class="text-danger">*</span>
                            </label>
                            <select class="form-control modern-input" name="heure_rdv" id="rdv_heure" required>
                                <option value="">Choisir un créneau</option>
                                @for($h = 8; $h <= 20; $h++)
                                    <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00</option>
                                    @if($h < 20)
                                        <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:30">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:30</option>
                                    @endif
                                @endfor
                            </select>
                            <div class="error-message" id="error-rdv_heure"></div>
                        </div>

                        <!-- Statut -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-info-circle me-2 text-primary"></i>
                                Statut
                            </label>
                            <select class="form-control modern-input" name="statut" id="rdv_statut">
                                <option value="en_attente">En attente</option>
                                <option value="confirme">Confirmé</option>
                                <option value="en_cours">En cours</option>
                                <option value="termine">Terminé</option>
                                <option value="annule">Annulé</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-chat-left-text me-2 text-primary"></i>
                                Notes
                            </label>
                            <textarea class="form-control modern-input" name="notes" id="rdv_notes"
                                rows="3" placeholder="Objet de la visite, équipements ou prestations concernés, remarques…"></textarea>
                        </div>

                        <!-- Prestations / Produits -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-box-seam-fill me-2 text-primary"></i>
                                Prestations / Produits
                            </label>
                            <div class="services-packs-selection" role="group" aria-label="Prestations">
                                @forelse($services ?? [] as $service)
                                    <label class="selection-item mb-0">
                                        <input type="checkbox" name="services[]" value="{{ $service->id }}" class="service-checkbox visually-hidden">
                                        <span class="selection-bubble">
                                            <i class="bi bi-circle rdv-sel-icon-off" aria-hidden="true"></i>
                                            <i class="bi bi-check-circle-fill rdv-sel-icon-on" aria-hidden="true"></i>
                                            <span>{{ $service->nom }}</span>
                                            <span style="opacity: 0.85; font-size: 0.85rem;">- {{ number_format($service->prix_apres_reduction, 2) }} MAD</span>
                                        </span>
                                    </label>
                                @empty
                                    <p class="text-muted small mb-0">Aucune prestation dans le catalogue. Ajoutez-en dans « Catalogues ».</p>
                                @endforelse
                            </div>
                            <small class="text-muted d-block mt-2">Cliquez sur une ou plusieurs cartes pour sélectionner. Au moins une prestation ou un pack est requis.</small>
                        </div>

                        <!-- Packs / Ensembles -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-box-seam me-2 text-primary"></i>
                                Packs / Ensembles
                            </label>
                            <div class="services-packs-selection" role="group" aria-label="Packs">
                                @forelse($packs ?? [] as $pack)
                                    <label class="selection-item mb-0">
                                        <input type="checkbox" name="packs[]" value="{{ $pack->id }}" class="pack-checkbox visually-hidden">
                                        <span class="selection-bubble">
                                            <i class="bi bi-circle rdv-sel-icon-off" aria-hidden="true"></i>
                                            <i class="bi bi-check-circle-fill rdv-sel-icon-on" aria-hidden="true"></i>
                                            <span>{{ $pack->nom }}</span>
                                            <span style="opacity: 0.85; font-size: 0.85rem;">- {{ number_format($pack->prix_apres_reduction, 2) }} MAD</span>
                                        </span>
                                    </label>
                                @empty
                                    <p class="text-muted small mb-0">Aucun pack dans le catalogue.</p>
                                @endforelse
                            </div>
                            <small class="text-muted d-block mt-2">Sélectionnez les packs proposés pour cette visite.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modern btn-modern-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-modern btn-modern-primary">
                        <i class="bi bi-check-circle me-1"></i> Planifier la visite
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    function updateTimeSlots() {
        const dateInput = document.getElementById('rdv_date');
        const timeSelect = document.getElementById('rdv_heure');
        if (!dateInput || !timeSelect) return;

        const selectedDate = new Date(dateInput.value + 'T00:00:00');
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const isToday = selectedDate.getTime() === today.getTime();
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();

        Array.from(timeSelect.options).forEach(function (option, index) {
            if (index === 0) return;

            option.disabled = false;
            option.style.color = '';

            if (isToday && option.value) {
                const parts = option.value.split(':');
                const hour = parseInt(parts[0], 10);
                const minute = parseInt(parts[1], 10);
                const optionTime = hour * 60 + minute;
                const currentTime = currentHour * 60 + currentMinute;

                if (optionTime <= currentTime) {
                    option.disabled = true;
                    option.style.color = '#adb5bd';
                }
            }
        });

        if (timeSelect.value && timeSelect.options[timeSelect.selectedIndex] && timeSelect.options[timeSelect.selectedIndex].disabled) {
            timeSelect.value = '';
        }
    }

    function bindAddRdvModal() {
        const addModal = document.getElementById('AddRendezVousModal');
        const dateInput = document.getElementById('rdv_date');
        if (!addModal || !dateInput) return;

        dateInput.addEventListener('change', updateTimeSlots);

        addModal.addEventListener('shown.bs.modal', function () {
            updateTimeSlots();
        });

        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);

        document.getElementById('formAddRendezVous')?.addEventListener('submit', function (e) {
            const services = addModal.querySelectorAll('.service-checkbox:checked');
            const packs = addModal.querySelectorAll('.pack-checkbox:checked');
            if (services.length === 0 && packs.length === 0) {
                e.preventDefault();
                alert('Veuillez sélectionner au moins une prestation ou un pack.');
            }
        });

        updateTimeSlots();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindAddRdvModal);
    } else {
        bindAddRdvModal();
    }
})();
</script>
