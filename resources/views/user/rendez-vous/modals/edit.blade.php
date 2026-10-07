<!-- Modal Modifier la visite -->
<div class="modal fade modal-modern" id="EditRendezVousModal-{{ $rdv->id }}" tabindex="-1" aria-labelledby="EditRendezVousModalLabel-{{ $rdv->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditRendezVousModalLabel-{{ $rdv->id }}">
                    <i class="bi bi-pencil me-2"></i>
                    Modifier la visite
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('rendez-vous.update', $rdv->id) }}" method="POST" id="formEditRendezVous-{{ $rdv->id }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-building me-2 text-primary"></i>
                                Client (raison sociale / contact) <span class="text-danger">*</span>
                            </label>
                            <select class="form-control modern-input" name="client_id" required>
                                @foreach($clients ?? [] as $client)
                                    <option value="{{ $client->id }}" {{ $rdv->client_id == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom }}@if($client->prenom) – {{ $client->prenom }}@endif
                                        @if($client->telephone)
                                            · {{ $client->telephone }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-calendar3 me-2 text-primary"></i>
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control modern-input" name="date_rdv"
                                value="{{ \Carbon\Carbon::parse($rdv->date_rdv)->format('Y-m-d') }}"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-clock me-2 text-primary"></i>
                                Heure <span class="text-danger">*</span>
                            </label>
                            @php
                                $heureRdv = is_string($rdv->heure_rdv) ? substr($rdv->heure_rdv, 0, 5) : \Carbon\Carbon::parse($rdv->heure_rdv)->format('H:i');
                                $heureParts = explode(':', $heureRdv);
                                $h = (int) $heureParts[0];
                                $m = (int) ($heureParts[1] ?? 0);
                                if ($m < 15) {
                                    $m = 0;
                                } elseif ($m < 45) {
                                    $m = 30;
                                } else {
                                    $h++;
                                    $m = 0;
                                }
                                $heureRdv = str_pad($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
                            @endphp
                            <select class="form-control modern-input" name="heure_rdv" id="rdv_heure_edit_{{ $rdv->id }}" required>
                                <option value="">Choisir un créneau</option>
                                @for($h = 8; $h <= 20; $h++)
                                    <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00" {{ $heureRdv == str_pad($h, 2, '0', STR_PAD_LEFT) . ':00' ? 'selected' : '' }}>{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00</option>
                                    @if($h < 20)
                                        <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:30" {{ $heureRdv == str_pad($h, 2, '0', STR_PAD_LEFT) . ':30' ? 'selected' : '' }}>{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:30</option>
                                    @endif
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-info-circle me-2 text-primary"></i>
                                Statut <span class="text-danger">*</span>
                            </label>
                            <select class="form-control modern-input" name="statut" required>
                                <option value="en_attente" {{ $rdv->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="confirme" {{ $rdv->statut == 'confirme' ? 'selected' : '' }}>Confirmé</option>
                                <option value="en_cours" {{ $rdv->statut == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="termine" {{ $rdv->statut == 'termine' ? 'selected' : '' }}>Terminé</option>
                                <option value="annule" {{ $rdv->statut == 'annule' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-chat-left-text me-2 text-primary"></i>
                                Notes
                            </label>
                            <textarea class="form-control modern-input" name="notes" rows="3" placeholder="Objet de la visite, équipements ou prestations concernés…">{{ $rdv->notes }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-box-seam-fill me-2 text-primary"></i>
                                Prestations / Produits
                            </label>
                            <div class="services-packs-selection" role="group" aria-label="Prestations">
                                @forelse($services ?? [] as $service)
                                    <label class="selection-item mb-0">
                                        <input type="checkbox" name="services[]" value="{{ $service->id }}"
                                            {{ $rdv->services->contains($service->id) ? 'checked' : '' }}
                                            class="service-checkbox visually-hidden">
                                        <span class="selection-bubble">
                                            <i class="bi bi-circle rdv-sel-icon-off" aria-hidden="true"></i>
                                            <i class="bi bi-check-circle-fill rdv-sel-icon-on" aria-hidden="true"></i>
                                            <span>{{ $service->nom }}</span>
                                            <span style="opacity: 0.85; font-size: 0.85rem;">- {{ number_format($service->prix_apres_reduction, 2) }} MAD</span>
                                        </span>
                                    </label>
                                @empty
                                    <p class="text-muted small mb-0">Aucune prestation dans le catalogue.</p>
                                @endforelse
                            </div>
                            <small class="text-muted d-block mt-2">Au moins une prestation ou un pack doit rester sélectionné.</small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-box-seam me-2 text-primary"></i>
                                Packs / Ensembles
                            </label>
                            <div class="services-packs-selection" role="group" aria-label="Packs">
                                @forelse($packs ?? [] as $pack)
                                    <label class="selection-item mb-0">
                                        <input type="checkbox" name="packs[]" value="{{ $pack->id }}"
                                            {{ $rdv->packs->contains($pack->id) ? 'checked' : '' }}
                                            class="pack-checkbox visually-hidden">
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
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modern btn-modern-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-modern btn-modern-primary">
                        <i class="bi bi-check-circle me-1"></i> Enregistrer la visite
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const modalId = 'EditRendezVousModal-{{ $rdv->id }}';
    const formId = 'formEditRendezVous-{{ $rdv->id }}';

    function updateTimeSlotsEdit(modalEl) {
        const dateInput = modalEl.querySelector('input[name="date_rdv"]');
        const timeSelect = document.getElementById('rdv_heure_edit_{{ $rdv->id }}');
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

            if (option.selected) {
                option.disabled = false;
                option.style.color = '';
                return;
            }

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
    }

    function bindEditRdvModal() {
        const editModal = document.getElementById(modalId);
        const form = document.getElementById(formId);
        if (!editModal || !form) return;

        const dateInput = editModal.querySelector('input[name="date_rdv"]');
        if (dateInput) {
            dateInput.addEventListener('change', function () {
                updateTimeSlotsEdit(editModal);
            });
        }

        editModal.addEventListener('shown.bs.modal', function () {
            updateTimeSlotsEdit(editModal);
        });

        form.addEventListener('submit', function (e) {
            const services = editModal.querySelectorAll('.service-checkbox:checked');
            const packs = editModal.querySelectorAll('.pack-checkbox:checked');
            if (services.length === 0 && packs.length === 0) {
                e.preventDefault();
                alert('Veuillez conserver au moins une prestation ou un pack.');
            }
        });

        updateTimeSlotsEdit(editModal);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindEditRdvModal);
    } else {
        bindEditRdvModal();
    }
})();
</script>
