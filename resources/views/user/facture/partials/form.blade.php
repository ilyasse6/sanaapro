{{--
    Formulaire facture « une ligne » : désignation + total TTC, aperçu en direct au format AFIF ALU.
    Attend : $clients, $societe ; $facture (null en création) ; $action, $method ; optionnel : $preselectedClientId, $numeroApercu.
--}}
@php
    $facture = $facture ?? null;
    $statuts = [
        'brouillon' => 'Brouillon',
        'emise' => 'Émise',
        'envoyee' => 'Envoyée',
        'payee_partielle' => 'Payée partiellement',
        'payee' => 'Payée',
        'annulee' => 'Annulée',
        'avoir' => 'Avoir',
    ];
    $preselectedClientId = $preselectedClientId ?? null;
    $iceClient = $facture?->client?->ice ?? ($preselectedClientId ? $clients->firstWhere('id', $preselectedClientId)?->ice : '');
@endphp
<style>
    .card-facture { border-radius: 16px; box-shadow: 0 10px 40px rgba(30,64,175,0.08); border: none; }
    .modern-input { border-radius: 8px; border: 1px solid #e2e8f0; }
    .modern-input:focus { border-color: #17324D; box-shadow: 0 0 0 3px rgba(30,64,175,0.1); outline: none; }
    .fac-ttc-input { font-size: 1.25rem; font-weight: 700; color: #17324D; }
    .fac-calc { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 12px; padding: .85rem 1rem; }
    .fac-calc .fac-lettres { font-style: italic; font-weight: 600; color: #17324D; }
    .fac-apercu { position: sticky; top: 1rem; }
    .fac-apercu .fx-stage { background: #e9ecef; border-radius: 12px; padding: 14px 0; }
</style>

<form action="{{ $action }}" method="post" id="formFacture">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="row g-4">
        <div class="col-xl-5">
            <div class="card card-facture mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">Client et facture</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            @include('user.partials.client-select-libre', [
                                'selectedClientId' => $facture?->client_id ?? $preselectedClientId,
                                'avecOptionVide' => $facture === null,
                            ])
                        </div>
                        <div class="col-12">
                            <label class="form-label">ICE du client</label>
                            <input type="text" name="client_ice" id="client_ice" class="form-control modern-input" maxlength="50" value="{{ old('client_ice', $iceClient) }}" placeholder="Ex : 001553860000026">
                            <div class="form-text">Enregistré sur la fiche du client.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date_emission" class="form-control modern-input" value="{{ old('date_emission', $facture ? $facture->date_emission->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date d'échéance</label>
                            <input type="date" name="date_echeance" class="form-control modern-input" value="{{ old('date_echeance', $facture ? $facture->date_echeance?->format('Y-m-d') : date('Y-m-d', strtotime('+30 days'))) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Statut</label>
                            <select name="statut" class="form-select modern-input" @if($facture) required @endif>
                                @foreach($statuts as $stVal => $stLabel)
                                    <option value="{{ $stVal }}" @selected(old('statut', $facture?->statut ?? 'brouillon') === $stVal)>{{ $stLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-facture mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">Ligne de la facture</h6>
                    <div class="mb-3">
                        <label class="form-label">Désignation <span class="text-danger">*</span></label>
                        <textarea name="designation" class="form-control modern-input" rows="3" maxlength="5000" required>{{ old('designation', $facture ? $facture->designation_unique : \App\Models\Facture::DESIGNATION_DEFAUT) }}</textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label">Total T.T.C <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="total_ttc" id="total_ttc" class="form-control modern-input fac-ttc-input" min="0" step="0.01" required
                                    value="{{ old('total_ttc', $facture ? number_format($facture->montant_ttc, 2, '.', '') : '') }}" placeholder="0.00">
                                <span class="input-group-text">DH</span>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Taux T.V.A</label>
                            <div class="input-group">
                                <input type="number" name="taux_tva" id="taux_tva" class="form-control modern-input" min="0" max="100" step="0.01" value="{{ old('taux_tva', $facture ? (float) $facture->taux_tva : 20) }}">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="fac-calc">
                        <div class="d-flex justify-content-between small mb-1"><span>Mt H.T</span><strong id="calcHt">0.00</strong></div>
                        <div class="d-flex justify-content-between small mb-1"><span>Mt T.V.A</span><strong id="calcTva">0.00</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Mt T.T.C</span><strong id="calcTtc">0.00 DH</strong></div>
                        <div class="small text-muted">Arrêtée à la somme de :</div>
                        <div class="fac-lettres" id="calcLettres"></div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 12px;"><i class="bi bi-check-circle me-2"></i>Enregistrer la facture</button>
                <a href="{{ $facture ? route('factures.show', $facture) : route('factures.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="fac-apercu">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 fw-bold" style="color:#17324D"><i class="bi bi-eye-fill me-2"></i>Aperçu de la facture</h6>
                    <span class="badge text-bg-success bg-opacity-75">● Live</span>
                </div>
                @include('user.facture.partials.document-afifalu', [
                    'facture' => $facture,
                    'societe' => $societe,
                    'numeroApercu' => $numeroApercu ?? null,
                ])
            </div>
        </div>
    </div>
</form>

<script>
{{-- Même algorithme que App\Support\MontantEnLettres --}}
window.montantEnLettres = function (montant) {
    const U = ['zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix',
        'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
    const D = { 2: 'vingt', 3: 'trente', 4: 'quarante', 5: 'cinquante', 6: 'soixante', 7: 'soixante', 8: 'quatre-vingt', 9: 'quatre-vingt' };

    function moinsDeCent(n, pluriel) {
        if (n < 20) return U[n];
        const d = Math.floor(n / 10), u = n % 10;
        if (d === 7 || d === 9) return D[d] + (d === 7 && u === 1 ? ' et ' : '-') + U[10 + u];
        if (u === 0) return D[d] + (d === 8 && pluriel ? 's' : '');
        return D[d] + (u === 1 && d !== 8 ? ' et ' : '-') + U[u];
    }
    function moinsDeMille(n, pluriel) {
        const c = Math.floor(n / 100), r = n % 100;
        let t = '';
        if (c > 0) {
            t = c === 1 ? 'cent' : U[c] + ' cent';
            if (r === 0 && c > 1 && pluriel) t += 's';
        }
        if (r > 0) t += (t ? ' ' : '') + moinsDeCent(r, pluriel);
        return t;
    }
    function nombre(n) {
        if (n === 0) return 'zéro';
        const parts = [];
        [[1e9, 'milliard'], [1e6, 'million']].forEach(([v, nom]) => {
            const q = Math.floor(n / v);
            if (q > 0) { parts.push(moinsDeMille(q, true) + ' ' + nom + (q > 1 ? 's' : '')); n %= v; }
        });
        const m = Math.floor(n / 1000);
        if (m > 0) { parts.push(m === 1 ? 'mille' : moinsDeMille(m, false) + ' mille'); n %= 1000; }
        if (n > 0) parts.push(moinsDeMille(n, true));
        return parts.join(' ');
    }

    const totalCentimes = Math.round(Math.abs(montant) * 100);
    const entier = Math.floor(totalCentimes / 100), centimes = totalCentimes % 100;
    let t = nombre(entier) + ' ' + (entier >= 1e6 && entier % 1e6 === 0 ? 'de ' : '') + (entier > 1 ? 'dirhams' : 'dirham');
    if (centimes > 0) t += ' et ' + nombre(centimes) + ' ' + (centimes > 1 ? 'centimes' : 'centime');
    return t.charAt(0).toUpperCase() + t.slice(1) + '.';
};

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formFacture');
    const icesClients = @json($clients->pluck('ice', 'id'));
    const iceInput = document.getElementById('client_ice');
    const num = (v) => { const n = parseFloat(String(v ?? '').replace(',', '.')); return isNaN(n) ? 0 : n; };
    const fmt = (n) => n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    const fx = (k) => form.querySelector('[data-fx="' + k + '"]');

    function render() {
        const ttc = Math.round(num(form.total_ttc.value) * 100) / 100;
        const tauxBrut = form.taux_tva.value;
        const taux = tauxBrut === '' ? 20 : num(tauxBrut);
        const ht = Math.round(ttc / (1 + taux / 100) * 100) / 100;
        const tva = Math.round((ttc - ht) * 100) / 100;
        const lettres = window.montantEnLettres(ttc);

        document.getElementById('calcHt').textContent = fmt(ht);
        document.getElementById('calcTva').textContent = fmt(tva);
        document.getElementById('calcTtc').textContent = fmt(ttc) + ' DH';
        document.getElementById('calcLettres').textContent = lettres;

        fx('ttc-ligne').textContent = fmt(ttc);
        fx('ttc').textContent = fmt(ttc);
        fx('ht').textContent = fmt(ht);
        fx('tva').textContent = fmt(tva);
        fx('taux').textContent = String(taux).replace(/\.0+$/, '');
        fx('lettres').textContent = lettres;

        const desig = fx('designation');
        desig.textContent = form.designation.value;
        desig.innerHTML = desig.innerHTML.replace(/\n/g, '<br>');

        const d = form.date_emission.value ? new Date(form.date_emission.value + 'T00:00:00') : null;
        fx('date').textContent = d ? d.toLocaleDateString('fr-FR') : '';
        fx('client').textContent = window.clientSelectionLabel(form).replace(/\s+–\s+/, ' ').toUpperCase();
        fx('ice').textContent = iceInput.value.trim();
        fx('ice-wrap').style.visibility = iceInput.value.trim() ? 'visible' : 'hidden';
        if (window.fxFit) window.fxFit();
    }

    const select = form.querySelector('select[name="client_id"]');
    select.addEventListener('change', function () {
        if (select.value && !select.disabled) iceInput.value = icesClients[select.value] || '';
    });
    form.addEventListener('input', render);
    form.addEventListener('change', render);
    render();
});
</script>
