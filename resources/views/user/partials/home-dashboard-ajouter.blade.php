{{-- Modal Ajouter : choix multitâche (Visite / RDV / Rappel) puis formulaire dédié --}}
<div class="modal fade" id="homeDashboardAjouterModal" tabindex="-1" aria-labelledby="homeDashboardAjouterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content home-mt-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="homeDashboardAjouterModalLabel">
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i>Nouvel élément
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body pt-2">
                {{-- Sélecteur type (style multitâche iOS) --}}
                <div id="homeMtPicker">
                    <p class="text-muted small mb-3">Choisissez ce que vous souhaitez ajouter</p>
                    <div class="home-mt-grid">
                        <button type="button" class="home-mt-tile" id="homeMtBtnVisite" data-home-mt-target="visite">
                            <span class="home-mt-icon" style="background:rgba(52,199,89,.15);color:#34C759;"><i class="bi bi-geo-alt"></i></span>
                            <span class="home-mt-label">Visite</span>
                            <span class="home-mt-hint">Pour aujourd’hui</span>
                        </button>
                        <button type="button" class="home-mt-tile" id="homeMtBtnRdv" data-home-mt-target="rdv">
                            <span class="home-mt-icon" style="background:rgba(0,122,255,.12);color:#007AFF;"><i class="bi bi-calendar-event"></i></span>
                            <span class="home-mt-label">RDV</span>
                            <span class="home-mt-hint">Planifier à venir</span>
                        </button>
                        <button type="button" class="home-mt-tile" id="homeMtBtnRappel" data-home-mt-target="rappel">
                            <span class="home-mt-icon" style="background:rgba(88,86,214,.12);color:#5856D6;"><i class="bi bi-bell"></i></span>
                            <span class="home-mt-label">Rappel</span>
                            <span class="home-mt-hint">Note personnelle</span>
                        </button>
                    </div>
                </div>

                {{-- Formulaire Visite (date = aujourd’hui) --}}
                <div id="homeMtFormVisite" class="d-none mt-3">
                    <button type="button" class="btn btn-link btn-sm text-decoration-none px-0 mb-2 home-mt-back"><i class="bi bi-arrow-left"></i> Retour</button>
                    @if($canAjouterRdv ?? false)
                        <form action="{{ route('rendez-vous.store') }}" method="POST" id="homeFormVisite">
                            @csrf
                            <input type="hidden" name="redirect_to" value="{{ $agenda_redirect_to ?? 'home' }}">
                            <input type="hidden" name="date_rdv" value="{{ now()->format('Y-m-d') }}">
                            <input type="hidden" name="statut" value="confirme">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                                    <select name="client_id" class="form-select" required>
                                        <option value="">Choisir…</option>
                                        @foreach($clients ?? [] as $client)
                                            <option value="{{ $client->id }}">{{ $client->nom }}@if($client->prenom) — {{ $client->prenom }}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Heure <span class="text-danger">*</span></label>
                                    <select name="heure_rdv" class="form-select home-visite-heure" required>
                                        <option value="">Créneau</option>
                                        @for($h = 8; $h <= 20; $h++)
                                            <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00</option>
                                            @if($h < 20)
                                                <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:30">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:30</option>
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Date</label>
                                    <input type="text" class="form-control" value="Aujourd’hui ({{ now()->format('d/m/Y') }})" disabled>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Notes</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="Objet de la visite…"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Prestations</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($services ?? [] as $service)
                                            <label class="form-check-label border rounded px-2 py-1 small" style="cursor:pointer;">
                                                <input type="checkbox" name="services[]" value="{{ $service->id }}" class="form-check-input me-1 home-svc-check">{{ $service->nom }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Packs</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($packs ?? [] as $pack)
                                            <label class="form-check-label border rounded px-2 py-1 small" style="cursor:pointer;">
                                                <input type="checkbox" name="packs[]" value="{{ $pack->id }}" class="form-check-input me-1 home-pck-check">{{ $pack->nom }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-success rounded-pill px-4">Enregistrer la visite</button>
                            </div>
                        </form>
                    @else
                        <p class="text-muted mb-0">Permission insuffisante pour créer une visite.</p>
                    @endif
                </div>

                {{-- Formulaire RDV --}}
                <div id="homeMtFormRdv" class="d-none mt-3">
                    <button type="button" class="btn btn-link btn-sm text-decoration-none px-0 mb-2 home-mt-back"><i class="bi bi-arrow-left"></i> Retour</button>
                    @if($canAjouterRdv ?? false)
                        <form action="{{ route('rendez-vous.store') }}" method="POST" id="homeFormRdv">
                            @csrf
                            <input type="hidden" name="redirect_to" value="{{ $agenda_redirect_to ?? 'home' }}">
                            <input type="hidden" name="statut" value="en_attente">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                                    <select name="client_id" class="form-select" required>
                                        <option value="">Choisir…</option>
                                        @foreach($clients ?? [] as $client)
                                            <option value="{{ $client->id }}">{{ $client->nom }}@if($client->prenom) — {{ $client->prenom }}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date_rdv" id="home_rdv_date" class="form-control home-rdv-date" min="{{ now()->format('Y-m-d') }}" value="{{ now()->addDay()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Heure <span class="text-danger">*</span></label>
                                    <select name="heure_rdv" class="form-select" required>
                                        <option value="">Créneau</option>
                                        @for($h = 8; $h <= 20; $h++)
                                            <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00</option>
                                            @if($h < 20)
                                                <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:30">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:30</option>
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Notes</label>
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Prestations</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($services ?? [] as $service)
                                            <label class="form-check-label border rounded px-2 py-1 small" style="cursor:pointer;">
                                                <input type="checkbox" name="services[]" value="{{ $service->id }}" class="form-check-input me-1 home-svc-check-rdv">{{ $service->nom }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Packs</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($packs ?? [] as $pack)
                                            <label class="form-check-label border rounded px-2 py-1 small" style="cursor:pointer;">
                                                <input type="checkbox" name="packs[]" value="{{ $pack->id }}" class="form-check-input me-1 home-pck-check-rdv">{{ $pack->nom }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Planifier le RDV</button>
                            </div>
                        </form>
                    @else
                        <p class="text-muted mb-0">Permission insuffisante pour créer un rendez-vous.</p>
                    @endif
                </div>

                {{-- Formulaire Rappel personnel --}}
                <div id="homeMtFormRappel" class="d-none mt-3">
                    <button type="button" class="btn btn-link btn-sm text-decoration-none px-0 mb-2 home-mt-back"><i class="bi bi-arrow-left"></i> Retour</button>
                    <form action="{{ route('rappels-personnels.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                                <input type="text" name="titre" class="form-control" required maxlength="255" placeholder="Ex. Relancer client, envoyer devis…">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date_rappel" class="form-control" min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Heure</label>
                                <input type="time" name="heure_rappel" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Détails optionnels…"></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn rounded-pill px-4 text-white" style="background:#5856D6;">Enregistrer le rappel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('homeDashboardAjouterModal');
    if (!modal) return;

    const picker = document.getElementById('homeMtPicker');
    const fVisite = document.getElementById('homeMtFormVisite');
    const fRdv = document.getElementById('homeMtFormRdv');
    const fRappel = document.getElementById('homeMtFormRappel');
    function showPanel(name) {
        [picker, fVisite, fRdv, fRappel].forEach(function (el) { if (el) el.classList.add('d-none'); });
        if (name === 'picker') {
            picker.classList.remove('d-none');
        } else if (name === 'visite') {
            fVisite.classList.remove('d-none');
        } else if (name === 'rdv') {
            fRdv.classList.remove('d-none');
        } else if (name === 'rappel') {
            fRappel.classList.remove('d-none');
        }
    }

    modal.querySelectorAll('[data-home-mt-target]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const t = btn.getAttribute('data-home-mt-target');
            showPanel(t);
        });
    });

    modal.querySelectorAll('.home-mt-back').forEach(function (btn) {
        btn.addEventListener('click', function () { showPanel('picker'); });
    });

    modal.addEventListener('hidden.bs.modal', function () {
        showPanel('picker');
        document.getElementById('homeFormVisite')?.reset();
        document.getElementById('homeFormRdv')?.reset();
        const fr = modal.querySelector('#homeMtFormRappel form');
        if (fr) fr.reset();
    });

    function needSvcPack(form) {
        const s = form.querySelectorAll('input[name="services[]"]:checked');
        const p = form.querySelectorAll('input[name="packs[]"]:checked');
        return s.length > 0 || p.length > 0;
    }

    document.getElementById('homeFormVisite')?.addEventListener('submit', function (e) {
        if (!needSvcPack(this)) {
            e.preventDefault();
            alert('Sélectionnez au moins une prestation ou un pack.');
        }
    });
    document.getElementById('homeFormRdv')?.addEventListener('submit', function (e) {
        if (!needSvcPack(this)) {
            e.preventDefault();
            alert('Sélectionnez au moins une prestation ou un pack.');
        }
    });
})();
</script>
