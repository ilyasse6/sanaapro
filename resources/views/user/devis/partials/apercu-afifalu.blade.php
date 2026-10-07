{{-- Attend : $apercu (logo_url, titre, activite, pied_page, images, numero) --}}
<style>
    .apx-card { background: #fff; border-radius: 16px; box-shadow: 0 10px 40px rgba(23,50,77,0.08); padding: 1rem 1rem 1.25rem; }
    .apx-card-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 0.75rem; }
    .apx-card-head h6 { margin: 0; font-weight: 700; color: #17324D; }
    .apx-live { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #198754; background: rgba(25,135,84,0.1); padding: 0.2rem 0.55rem; border-radius: 6px; }
    .apx-stage { background: #e9ecef; border-radius: 12px; padding: 18px; overflow: hidden; }
    .apx-scale { margin: 0 auto; transform-origin: top left; }

    .apx-page {
        position: relative;
        width: 794px;
        min-height: 1123px;
        background: linear-gradient(90deg, #f2f2f2 0, #f2f2f2 150px, #fff 150px);
        box-shadow: 0 6px 24px rgba(0,0,0,0.18);
        padding: 42px 22px 26px;
        display: flex;
        flex-direction: column;
        color: #000;
        overflow: hidden;
    }
    .apx-page .apx-brand, .apx-page .apx-brand *, .apx-page .apx-meta, .apx-page .apx-meta *, .apx-page .apx-foot-text { font-family: "Times New Roman", Times, serif !important; }
    .apx-page .apx-table, .apx-page .apx-table * { font-family: Calibri, Carlito, "Segoe UI", Arial, sans-serif !important; }
    .apx-deco { position: absolute; right: 0; bottom: 0; width: 250px; height: 470px; z-index: 0; pointer-events: none; }
    .apx-page > *:not(.apx-deco) { position: relative; z-index: 1; }

    .apx-head { display: flex; align-items: flex-start; }
    .apx-logo { width: 170px; flex-shrink: 0; padding-left: 20px; }
    .apx-logo img { max-width: 140px; max-height: 80px; object-fit: contain; }
    .apx-brand { flex: 1; text-align: center; font-family: "Times New Roman", Times, serif; padding-top: 18px; }
    .apx-brand-title { font-size: 25px; font-weight: 700; letter-spacing: 0.3px; line-height: 1.1; }
    .apx-brand-activite { font-size: 14.5px; margin-top: 6px; line-height: 1.3; padding: 0 30px; }

    .apx-meta { font-family: "Times New Roman", Times, serif; font-size: 13.5px; margin: 42px 0 0 10px; line-height: 2; }
    .apx-meta span { display: inline-block; min-width: 72px; }

    .apx-table { width: 100%; border-collapse: collapse; margin-top: 96px; font-family: Calibri, Carlito, "Segoe UI", Arial, sans-serif; font-size: 11.5px; background: transparent; }
    .apx-table th, .apx-table td { border: 1px solid #000; padding: 3px 5px; background: transparent; }
    .apx-table thead th { font-weight: 700; font-style: italic; font-size: 10.5px; height: 36px; }
    .apx-table thead th.apx-th-type { text-align: left; vertical-align: top; font-style: normal; }
    .apx-table thead th.apx-th-top { text-align: center; vertical-align: top; }
    .apx-table thead th.apx-th-bottom { text-align: center; vertical-align: bottom; padding-bottom: 5px; }
    .apx-table tbody td { vertical-align: top; }
    .apx-td-type { position: relative; text-align: center; height: 40px; padding: 2px 4px 10px !important; }
    .apx-td-type img { max-width: 62%; max-height: 54px; object-fit: contain; display: inline-block; }
    .apx-td-num { position: absolute; left: 4px; bottom: 1px; font-weight: 700; font-size: 10px; }
    .apx-td-desig { font-style: italic; line-height: 1.35; }
    .apx-td-qte { font-weight: 700; font-style: italic; vertical-align: middle !important; text-align: center; }
    .apx-td-money { text-align: right; white-space: nowrap; }
    .apx-row-spacer td { height: 24px; }
    .apx-row-total td { height: 42px; vertical-align: top !important; }
    .apx-total-label { font-weight: 700; font-style: italic; font-size: 15px; text-align: center; }
    .apx-total-value { font-weight: 700; font-style: italic; font-size: 17px; text-align: right; white-space: nowrap; }
    .apx-empty { color: #888; font-style: italic; text-align: center; vertical-align: middle !important; height: 60px; }

    .apx-foot { margin-top: auto; padding-top: 40px; }
    .apx-foot-rule { border-top: 3px solid #555; border-bottom: 1px solid #555; height: 5px; margin: 0 4px 8px; }
    .apx-foot-text { font-family: "Times New Roman", Times, serif; font-size: 13.5px; text-align: center; line-height: 1.35; white-space: pre-line; }
</style>

<div class="apx-card" id="apercuDevisCard">
    <div class="apx-card-head">
        <h6><i class="bi bi-eye-fill me-2"></i>Aperçu du devis</h6>
        <span class="apx-live">● Live</span>
    </div>
    <div class="apx-stage" id="apxStage">
        <div class="apx-scale" id="apxScale">
            <div class="apx-page" id="apxPage">
                <svg class="apx-deco" viewBox="0 0 250 470" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="apxG1" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#cfcfcf" stop-opacity=".75"/><stop offset="1" stop-color="#e6e6e6" stop-opacity=".35"/></linearGradient>
                        <linearGradient id="apxG2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#b5b5b5" stop-opacity=".8"/><stop offset="1" stop-color="#d4d4d4" stop-opacity=".4"/></linearGradient>
                        <linearGradient id="apxG3" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#d9a57c" stop-opacity=".85"/><stop offset="1" stop-color="#ecd2bd" stop-opacity=".45"/></linearGradient>
                    </defs>
                    <polygon points="8,0 92,58 132,470 48,470" fill="url(#apxG1)"/>
                    <polygon points="92,28 160,78 196,470 128,470" fill="url(#apxG2)"/>
                    <polygon points="160,74 232,126 250,470 196,470" fill="url(#apxG3)"/>
                    <polygon points="216,60 250,84 250,470 238,470" fill="url(#apxG1)"/>
                </svg>

                <div class="apx-head">
                    <div class="apx-logo">
                        @if(!empty($apercu['logo_url']))
                            <img src="{{ $apercu['logo_url'] }}" alt="Logo">
                        @endif
                    </div>
                    <div class="apx-brand">
                        <div class="apx-brand-title">{{ $apercu['titre'] }}</div>
                        <div class="apx-brand-activite">{{ $apercu['activite'] }}</div>
                    </div>
                </div>

                <div class="apx-meta">
                    <div><span>Date</span>:<b id="apxDate" style="font-weight:400"></b></div>
                    <div><span>Devis N°</span>: {{ $apercu['numero'] }}</div>
                    <div id="apxClientWrap" style="display:none"><span>Client</span>: <b id="apxClient" style="font-weight:400"></b></div>
                    <div id="apxObjetWrap" style="display:none"><span>Objet</span>: <b id="apxObjet" style="font-weight:400"></b></div>
                </div>

                <table class="apx-table">
                    <colgroup>
                        <col style="width:19%"><col style="width:36%"><col style="width:6.5%"><col style="width:7.5%"><col style="width:4.5%"><col style="width:12.3%"><col style="width:14.2%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="apx-th-type">TYPE</th>
                            <th class="apx-th-top">DESIGNATION</th>
                            <th class="apx-th-bottom">LAR</th>
                            <th class="apx-th-bottom">HAUT</th>
                            <th class="apx-th-bottom">QTE</th>
                            <th class="apx-th-top">PU HT</th>
                            <th class="apx-th-top">PRIX TOTAL HT</th>
                        </tr>
                    </thead>
                    <tbody id="apxLignes"></tbody>
                    <tbody>
                        <tr class="apx-row-spacer"><td colspan="2"></td><td colspan="2"></td><td colspan="3"></td></tr>
                        <tr class="apx-row-total">
                            <td colspan="2" class="apx-total-label">MONTANT TOTAL HT</td>
                            <td colspan="2"></td>
                            <td colspan="3" class="apx-total-value" id="apxTotalHt"></td>
                        </tr>
                        <tr class="apx-row-total">
                            <td colspan="2" class="apx-total-label" id="apxTvaLabel">TVA 20%</td>
                            <td colspan="2"></td>
                            <td colspan="3" class="apx-total-value" id="apxTotalTva"></td>
                        </tr>
                        <tr class="apx-row-total">
                            <td colspan="2" class="apx-total-label">MONTANT TOTAL TTC</td>
                            <td colspan="2"></td>
                            <td colspan="3" class="apx-total-value" id="apxTotalTtc"></td>
                        </tr>
                    </tbody>
                </table>

                <div class="apx-foot">
                    <div class="apx-foot-rule"></div>
                    <div class="apx-foot-text">{{ $apercu['pied_page'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formDevis');
    const container = document.getElementById('lignesContainer');
    const images = @json($apercu['images'] ?? [], JSON_UNESCAPED_SLASHES);
    const stage = document.getElementById('apxStage');
    const scaleBox = document.getElementById('apxScale');
    const page = document.getElementById('apxPage');
    if (!form || !container || !page) return;

    const num = (v) => { const n = parseFloat(String(v ?? '').replace(',', '.')); return isNaN(n) ? 0 : n; };
    const dim = (v) => { const s = String(v ?? '').trim(); if (s === '') return ''; const n = num(s); return String(n).replace('.', ','); };
    const qte = (n) => Number.isInteger(n) ? String(n).padStart(2, '0') : String(n).replace('.', ',');
    const money = (n) => n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' DH';
    const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const val = (row, sel) => { const el = row.querySelector(sel); return el ? el.value : ''; };

    function render() {
        const dateInput = form.querySelector('[name="date_emission"]');
        const d = dateInput && dateInput.value ? new Date(dateInput.value + 'T00:00:00') : null;
        document.getElementById('apxDate').textContent = d ? d.toLocaleDateString('fr-FR') : '';

        const client = window.clientSelectionLabel(form);
        document.getElementById('apxClientWrap').style.display = client ? '' : 'none';
        document.getElementById('apxClient').textContent = client;

        const objet = (form.querySelector('[name="objet"]') || {}).value || '';
        document.getElementById('apxObjetWrap').style.display = objet.trim() ? '' : 'none';
        document.getElementById('apxObjet').textContent = objet.trim();

        const rows = container.querySelectorAll('.ligne-devis');
        const tbody = document.getElementById('apxLignes');
        let totalLignes = 0;
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="apx-empty">Aucune ligne</td></tr>';
        } else {
            tbody.innerHTML = Array.from(rows).map((row, i) => {
                const q = num(val(row, '.ligne-quantite'));
                const pu = num(val(row, '.ligne-pu'));
                const remise = num(val(row, '.ligne-remise'));
                const montant = q * pu * (1 - remise / 100);
                totalLignes += montant;
                const serviceId = val(row, 'input[name$="[catalogue_service_id]"]');
                const img = serviceId && images[serviceId] ? `<img src="${esc(images[serviceId])}" alt="">` : '';
                const desig = val(row, '.ligne-designation').trim();
                return `<tr>
                    <td class="apx-td-type">${img}<span class="apx-td-num">${i + 1}</span></td>
                    <td class="apx-td-desig">${esc(desig).replace(/\n/g, '<br>')}</td>
                    <td>${esc(dim(val(row, '.ligne-largeur')))}</td>
                    <td>${esc(dim(val(row, '.ligne-hauteur')))}</td>
                    <td class="apx-td-qte">${q ? qte(q) : ''}</td>
                    <td class="apx-td-money">${pu ? money(pu) : ''}</td>
                    <td class="apx-td-money">${montant ? money(montant) : ''}</td>
                </tr>`;
            }).join('');
        }

        const remiseGlobale = num((document.getElementById('remise_globale_pct') || {}).value);
        const tauxInput = document.getElementById('taux_tva');
        const taux = tauxInput && tauxInput.value !== '' ? num(tauxInput.value) : 20;
        const ht = totalLignes * (1 - remiseGlobale / 100);
        const tva = ht * taux / 100;
        document.getElementById('apxTvaLabel').textContent = 'TVA ' + String(taux).replace('.', ',') + '%';
        document.getElementById('apxTotalHt').textContent = money(ht);
        document.getElementById('apxTotalTva').textContent = money(tva);
        document.getElementById('apxTotalTtc').textContent = money(ht + tva);
        fit();
    }

    function fit() {
        const avail = stage.clientWidth - 36;
        const scale = Math.min(1, avail / 794);
        scaleBox.style.width = '794px';
        scaleBox.style.transform = `scale(${scale})`;
        scaleBox.style.height = (page.offsetHeight * scale) + 'px';
        scaleBox.style.marginLeft = Math.max(0, (avail - 794 * scale) / 2) + 'px';
    }

    form.addEventListener('input', render);
    form.addEventListener('change', render);
    new MutationObserver(render).observe(container, { childList: true, subtree: true });
    new ResizeObserver(fit).observe(stage);
    render();
});
</script>
