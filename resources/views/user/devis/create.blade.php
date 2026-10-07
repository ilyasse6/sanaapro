@extends('user.master')

@section('content')
<style>
    .devis-form-hero { background: linear-gradient(135deg, #17324D 0%, #0f2438 100%); color: white; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .card-devis { border-radius: 16px; box-shadow: 0 10px 40px rgba(30,64,175,0.08); border: none; }
    .ligne-devis { background: #f8fafc; border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; border: 1px solid #e2e8f0; }
    .modern-input { border-radius: 8px; border: 1px solid #e2e8f0; }
    .modern-input:focus { border-color: #17324D; box-shadow: 0 0 0 3px rgba(30,64,175,0.1); outline: none; }
    .btn-add-line { border-radius: 10px; font-weight: 600; }
    .totaux-box { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 12px; padding: 1rem; border: 1px solid #bae6fd; }
    /* Simulateur / Aperçu live */
    .simulateur-wrapper { position: sticky; top: 1rem; }
    .simulateur-devis {
        background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(30, 64, 175, 0.12), 0 0 0 1px rgba(30, 64, 175, 0.06);
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transform: perspective(800px) rotateY(-1deg);
        transition: box-shadow 0.3s ease;
    }
    .simulateur-devis:hover { box-shadow: 0 12px 40px rgba(30, 64, 175, 0.15); }
    .simulateur-header {
        background: linear-gradient(135deg, #17324D 0%, #0f2438 100%);
        color: white;
        padding: 1rem 1.25rem;
        position: relative;
    }
    .simulateur-header .live-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.75rem;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: rgba(255,255,255,0.25);
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        animation: pulse-live 2s ease-in-out infinite;
    }
    @keyframes pulse-live { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
    .simulateur-body { padding: 1rem; font-size: 0.8rem; max-height: 420px; overflow-y: auto; }
    .simulateur-body .num-devis { font-weight: 700; color: #17324D; font-size: 0.95rem; }
    .simulateur-body .label-sim { color: #64748b; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 0.15rem; }
    .simulateur-body .value-sim { font-weight: 500; color: #1e293b; }
    .simulateur-table { font-size: 0.7rem; margin: 0.75rem 0; }
    .simulateur-table th { background: #f1f5f9; padding: 0.4rem 0.35rem; border: 1px solid #e2e8f0; }
    .simulateur-table td { padding: 0.35rem; border: 1px solid #e2e8f0; }
    .simulateur-totaux { background: #f8fafc; border-radius: 8px; padding: 0.6rem; margin-top: 0.75rem; }
    .simulateur-totaux .total-ttc { font-size: 1rem; font-weight: 700; color: #17324D; }
    .simulateur-placeholder { color: #94a3b8; font-style: italic; }
</style>

<div class="section">
    <div class="devis-form-hero">
        <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Nouveau devis</h4>
        <p class="mb-0 opacity-90 small">Remplissez les informations et les lignes de prestations ou produits.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('devis.store') }}" method="post" id="formDevis">
        @csrf
        @include('user.devis.partials.marge', ['valeur' => 0])
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-devis mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-4">Informations générales</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                @include('user.partials.client-select-libre', ['selectedClientId' => $preselectedClientId ?? null])
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date d'émission <span class="text-danger">*</span></label>
                                <input type="date" name="date_emission" class="form-control modern-input" value="{{ old('date_emission', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date de validité <span class="text-danger">*</span></label>
                                <input type="date" name="date_validite" class="form-control modern-input" value="{{ old('date_validite', date('Y-m-d', strtotime('+30 days'))) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Statut</label>
                                <select name="statut" class="form-select modern-input">
                                    <option value="brouillon" {{ old('statut', 'brouillon') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                    <option value="envoye" {{ old('statut') === 'envoye' ? 'selected' : '' }}>Envoyé</option>
                                    <option value="accepte" {{ old('statut') === 'accepte' ? 'selected' : '' }}>Accepté</option>
                                    <option value="refuse" {{ old('statut') === 'refuse' ? 'selected' : '' }}>Refusé</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Objet du devis</label>
                                <input type="text" name="objet" class="form-control modern-input" placeholder="Ex : Équipement médical, cuisine professionnelle…" value="{{ old('objet') }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Conditions de paiement</label>
                                <input type="text" name="conditions_paiement" class="form-control modern-input" placeholder="Ex : 30% à la commande, solde à la livraison" value="{{ old('conditions_paiement') }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control modern-input" rows="2" placeholder="Remarques internes ou pour le client">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-devis mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Remise & TVA</h6>
                        <div class="mb-3">
                            <label class="form-label">Remise globale (%)</label>
                            <input type="number" name="remise_globale_pct" id="remise_globale_pct" class="form-control modern-input" value="{{ old('remise_globale_pct', 0) }}" min="0" max="100" step="0.01">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Taux TVA (%)</label>
                            <input type="number" name="taux_tva" id="taux_tva" class="form-control modern-input" value="{{ old('taux_tva', 20) }}" min="0" max="100" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="totaux-box mb-4">
                    <div class="d-flex justify-content-between mb-2"><span>Total HT</span><strong id="totalHt">0,00 MAD</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>TVA</span><strong id="totalTva">0,00 MAD</strong></div>
                    <div class="d-flex justify-content-between fs-5"><span>Total TTC</span><strong id="totalTtc" class="text-primary">0,00 MAD</strong></div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 12px;"><i class="bi bi-check-circle me-2"></i>Enregistrer le devis</button>
                    <a href="{{ route('devis.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </div>
        <div class="card card-devis mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="card-title mb-0">Lignes du devis (prestations / produits)</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-add-line" id="btnAddFromCatalogue"><i class="bi bi-box-seam me-1"></i>Depuis le catalogue</button>
                        <button type="button" class="btn btn-primary btn-sm btn-add-line" id="btnAddLigne"><i class="bi bi-plus-lg me-1"></i>Ligne libre</button>
                    </div>
                </div>
                <div id="lignesContainer">
                    <!-- Lignes ajoutées par JS -->
                </div>
                <p class="text-muted small mb-0">Au moins une ligne est requise. Ajoutez des prestations depuis le catalogue ou saisissez une ligne libre.</p>
            </div>
        </div>
        <div class="mb-4">
            @include('user.devis.partials.apercu-afifalu')
        </div>
    </form>
</div>

<!-- Modal catalogue -->
<div class="modal fade" id="modalCatalogue" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter depuis le catalogue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabPrestations">Prestations / Produits</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabPacks">Packs</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tabPrestations">
                        @foreach($services as $s)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span>{{ $s->nom }} – {{ number_format($s->prix_apres_reduction, 2, ',', ' ') }} MAD</span>
                                <button type="button" class="btn btn-sm btn-outline-primary add-from-catalogue" data-designation="{{ $s->nom }}" data-description="{{ $s->description }}" data-prix="{{ $s->prix_apres_reduction }}" data-type="prestation" data-id="{{ $s->id }}">Ajouter</button>
                            </div>
                        @endforeach
                        @if($services->isEmpty())
                            <p class="text-muted">Aucune prestation dans le catalogue.</p>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="tabPacks">
                        @foreach($packs as $p)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span>{{ $p->nom }} – {{ number_format($p->prix_apres_reduction, 2, ',', ' ') }} MAD</span>
                                <button type="button" class="btn btn-sm btn-outline-primary add-from-catalogue" data-designation="{{ $p->nom }}" data-prix="{{ $p->prix_apres_reduction }}" data-type="pack" data-id="{{ $p->id }}">Ajouter</button>
                            </div>
                        @endforeach
                        @if($packs->isEmpty())
                            <p class="text-muted">Aucun pack dans le catalogue.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let ligneIndex = 0;
    const container = document.getElementById('lignesContainer');
    const form = document.getElementById('formDevis');

    function addLigne(data = {}) {
        const id = ligneIndex++;
        const designation = data.designation || '';
        const quantite = data.quantite ?? 1;
        const unite = data.unite || 'pièce';
        const prix = data.prix ?? 0;
        const remise = data.remise ?? 0;
        const largeur = data.largeur ?? '';
        const hauteur = data.hauteur ?? '';
        const typeEntree = data.type_entree || 'libre';
        const serviceId = data.catalogue_service_id || '';
        const packId = data.catalogue_pack_id || '';

        const div = document.createElement('div');
        div.className = 'ligne-devis';
        div.dataset.index = id;
        div.innerHTML = `
            <input type="hidden" name="lignes[${id}][type_entree]" value="${typeEntree}">
            <input type="hidden" name="lignes[${id}][catalogue_service_id]" value="${serviceId}">
            <input type="hidden" name="lignes[${id}][catalogue_pack_id]" value="${packId}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Désignation</label>
                    <input type="text" name="lignes[${id}][designation]" class="form-control form-control-sm modern-input ligne-designation" value="${designation}" required>
                </div>
                <div class="col-md-1">
                    <label class="form-label small">Larg. (m)</label>
                    <input type="number" name="lignes[${id}][largeur]" class="form-control form-control-sm modern-input ligne-largeur" value="${largeur}" min="0" step="0.001">
                </div>
                <div class="col-md-1">
                    <label class="form-label small">Haut. (m)</label>
                    <input type="number" name="lignes[${id}][hauteur]" class="form-control form-control-sm modern-input ligne-hauteur" value="${hauteur}" min="0" step="0.001">
                </div>
                <div class="col-md-1">
                    <label class="form-label small">Qté</label>
                    <input type="number" name="lignes[${id}][quantite]" class="form-control form-control-sm modern-input ligne-quantite" value="${quantite}" min="0.001" step="0.001" required>
                </div>
                <div class="col-md-1">
                    <label class="form-label small">Unité</label>
                    <input type="text" name="lignes[${id}][unite]" class="form-control form-control-sm modern-input" value="${unite}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Prix unit. HT</label>
                    <input type="number" name="lignes[${id}][prix_unitaire_ht]" class="form-control form-control-sm modern-input ligne-pu" value="${prix}" min="0" step="0.01" required>
                </div>
                <div class="col-md-1">
                    <label class="form-label small">Remise %</label>
                    <input type="number" name="lignes[${id}][remise_pct]" class="form-control form-control-sm modern-input ligne-remise" value="${remise}" min="0" max="100" step="0.01">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-ligne" title="Supprimer la ligne"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        `;
        container.appendChild(div);

        div.querySelector('.remove-ligne').addEventListener('click', function() {
            div.remove();
            updateTotaux();
        });
        div.querySelectorAll('.ligne-quantite, .ligne-pu, .ligne-remise, .ligne-designation').forEach(el => {
            el.addEventListener('input', updateTotaux);
        });
        updateTotaux();
    }

    function updateTotaux() {
        let totalHt = 0;
        container.querySelectorAll('.ligne-devis').forEach(row => {
            const qte = parseFloat(row.querySelector('.ligne-quantite').value) || 0;
            const pu = parseFloat(row.querySelector('.ligne-pu').value) || 0;
            const remise = parseFloat(row.querySelector('.ligne-remise').value) || 0;
            const mt = qte * pu * (1 - remise / 100);
            totalHt += mt;
        });
        const remiseGlobale = parseFloat(document.getElementById('remise_globale_pct').value) || 0;
        totalHt = totalHt * (1 - remiseGlobale / 100);
        const tauxTva = parseFloat(document.getElementById('taux_tva').value) || 20;
        const tva = totalHt * (tauxTva / 100);
        const ttc = totalHt + tva;
        document.getElementById('totalHt').textContent = totalHt.toFixed(2).replace('.', ',') + ' MAD';
        document.getElementById('totalTva').textContent = tva.toFixed(2).replace('.', ',') + ' MAD';
        document.getElementById('totalTtc').textContent = ttc.toFixed(2).replace('.', ',') + ' MAD';
        updateSimulateur(totalHt, tva, ttc);
    }

    function formatDateFr(str) {
        if (!str) return '—';
        const d = new Date(str + 'T00:00:00');
        return d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    function updateSimulateur(totalHtArg, tvaArg, ttcArg) {
        if (!document.getElementById('simClient')) return;
        let totalHt = totalHtArg;
        let tva = tvaArg;
        let ttc = ttcArg;
        if (totalHt === undefined) {
            let ht = 0;
            container.querySelectorAll('.ligne-devis').forEach(row => {
                const qte = parseFloat(row.querySelector('.ligne-quantite').value) || 0;
                const pu = parseFloat(row.querySelector('.ligne-pu').value) || 0;
                const remise = parseFloat(row.querySelector('.ligne-remise').value) || 0;
                ht += qte * pu * (1 - remise / 100);
            });
            const remiseGlobale = parseFloat(document.getElementById('remise_globale_pct').value) || 0;
            totalHt = ht * (1 - remiseGlobale / 100);
            const tauxTva = parseFloat(document.getElementById('taux_tva').value) || 20;
            tva = totalHt * (tauxTva / 100);
            ttc = totalHt + tva;
        }

        const clientText = window.clientSelectionLabel(form);
        const simClient = document.getElementById('simClient');
        if (clientText) { simClient.textContent = clientText.replace(/·/g, ' · '); } else { simClient.innerHTML = '<span class="simulateur-placeholder">— Choisir un client —</span>'; }

        const dateEmission = form.querySelector('input[name="date_emission"]');
        const dateValidite = form.querySelector('input[name="date_validite"]');
        document.getElementById('simDateEmission').textContent = dateEmission ? formatDateFr(dateEmission.value) : '—';
        document.getElementById('simDateValidite').textContent = dateValidite ? formatDateFr(dateValidite.value) : '—';

        const objetInput = form.querySelector('input[name="objet"]');
        const objetVal = objetInput ? objetInput.value.trim() : '';
        const objetWrap = document.getElementById('simObjetWrap');
        const simObjet = document.getElementById('simObjet');
        if (objetVal) {
            objetWrap.style.display = 'block';
            simObjet.textContent = objetVal;
        } else {
            objetWrap.style.display = 'none';
        }

        const tbody = document.getElementById('simLignes');
        const rows = container.querySelectorAll('.ligne-devis');
        if (rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center simulateur-placeholder">Aucune ligne</td></tr>';
        } else {
            tbody.innerHTML = '';
            rows.forEach(row => {
                const designation = (row.querySelector('.ligne-designation') && row.querySelector('.ligne-designation').value) || '—';
                const qte = parseFloat(row.querySelector('.ligne-quantite').value) || 0;
                const pu = parseFloat(row.querySelector('.ligne-pu').value) || 0;
                const remise = parseFloat(row.querySelector('.ligne-remise').value) || 0;
                const mt = (qte * pu * (1 - remise / 100)).toFixed(2).replace('.', ',');
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${designation.substring(0, 28)}${designation.length > 28 ? '…' : ''}</td><td class="text-center">${qte}</td><td class="text-end">${mt} MAD</td>`;
                tbody.appendChild(tr);
            });
        }

        document.getElementById('simTotalHt').textContent = (totalHt !== undefined ? totalHt : 0).toFixed(2).replace('.', ',') + ' MAD';
        document.getElementById('simTotalTva').textContent = (tva !== undefined ? tva : 0).toFixed(2).replace('.', ',') + ' MAD';
        document.getElementById('simTotalTtc').textContent = (ttc !== undefined ? ttc : 0).toFixed(2).replace('.', ',') + ' MAD';

        const conditions = form.querySelector('input[name="conditions_paiement"]');
        const notes = form.querySelector('textarea[name="notes"]');
        const condVal = conditions ? conditions.value.trim() : '';
        const notesVal = notes ? notes.value.trim() : '';
        const condWrap = document.getElementById('simConditionsWrap');
        const notesWrap = document.getElementById('simNotesWrap');
        if (condVal) {
            condWrap.style.display = 'block';
            document.getElementById('simConditions').textContent = condVal;
        } else {
            condWrap.style.display = 'none';
        }
        if (notesVal) {
            notesWrap.style.display = 'block';
            document.getElementById('simNotes').textContent = notesVal;
        } else {
            notesWrap.style.display = 'none';
        }
    }

    document.getElementById('remise_globale_pct').addEventListener('input', updateTotaux);
    document.getElementById('taux_tva').addEventListener('input', updateTotaux);

    form.querySelector('select[name="client_id"]').addEventListener('change', () => updateSimulateur());
    form.querySelector('input[name="client_libre"]').addEventListener('input', () => updateSimulateur());
    form.querySelector('input[name="date_emission"]').addEventListener('change', () => updateSimulateur());
    form.querySelector('input[name="date_validite"]').addEventListener('change', () => updateSimulateur());
    form.querySelector('input[name="objet"]').addEventListener('input', () => updateSimulateur());
    form.querySelector('input[name="conditions_paiement"]').addEventListener('input', () => updateSimulateur());
    form.querySelector('textarea[name="notes"]').addEventListener('input', () => updateSimulateur());

    document.getElementById('btnAddLigne').addEventListener('click', function() {
        addLigne({});
    });

    document.getElementById('btnAddFromCatalogue').addEventListener('click', function() {
        new bootstrap.Modal(document.getElementById('modalCatalogue')).show();
    });

    document.querySelectorAll('.add-from-catalogue').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            const dims = (this.dataset.description || '').match(/L\s*([\d.,]+)\s*m\s*[×x]\s*H\s*([\d.,]+)/i);
            addLigne({
                designation: this.dataset.designation,
                largeur: dims ? dims[1].replace(',', '.') : '',
                hauteur: dims ? dims[2].replace(',', '.') : '',
                prix: parseFloat(this.dataset.prix),
                quantite: 1,
                unite: 'pièce',
                type_entree: type,
                catalogue_service_id: type === 'prestation' ? this.dataset.id : '',
                catalogue_pack_id: type === 'pack' ? this.dataset.id : '',
            });
            bootstrap.Modal.getInstance(document.getElementById('modalCatalogue')).hide();
        });
    });

    form.addEventListener('submit', function() {
        const indices = [];
        container.querySelectorAll('.ligne-devis').forEach((row, i) => {
            indices.push(i);
            row.querySelectorAll('input, select').forEach(inp => {
                const name = inp.name;
                if (name && name.includes('[' + row.dataset.index + ']')) {
                    inp.name = name.replace('[' + row.dataset.index + ']', '[' + i + ']');
                }
            });
        });
    });

    if (container.querySelectorAll('.ligne-devis').length === 0) {
        addLigne({});
    }
    updateSimulateur();
});
</script>
@include('user.devis.partials.calcul-prix')
@endsection
