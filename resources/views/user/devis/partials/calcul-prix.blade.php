{{-- Panneau « Calcul du prix unitaire » ajouté sous chaque .ligne-devis de #lignesContainer. --}}
<style>
    .calc-pu { margin-top: .85rem; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; overflow: hidden; }
    .calc-pu-head { display: flex; justify-content: space-between; align-items: center; gap: .75rem; padding: .6rem .85rem; background: linear-gradient(90deg, #f1f5f9 0%, #f8fafc 100%); border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; }
    .calc-pu-title { display: flex; align-items: center; gap: .6rem; }
    .calc-pu-title .calc-pu-icon { width: 30px; height: 30px; border-radius: 8px; background: #17324D; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: .95rem; }
    .calc-pu-title strong { display: block; font-size: .82rem; color: #17324D; line-height: 1.1; }
    .calc-pu-title small { color: #64748b; font-size: .7rem; }
    .calc-pu-total { display: flex; align-items: center; gap: .5rem; padding: .3rem .4rem .3rem .75rem; border-radius: 999px; background: #e2e8f0; color: #64748b; font-size: .72rem; transition: background .2s, color .2s; }
    .calc-pu-total strong { background: #fff; color: #94a3b8; border-radius: 999px; padding: .2rem .65rem; font-size: .8rem; min-width: 96px; text-align: center; }
    .calc-pu-total.is-active { background: linear-gradient(135deg, #17324D 0%, #0f2438 100%); color: rgba(255,255,255,.85); }
    .calc-pu-total.is-active strong { color: #17324D; }

    .calc-rows { padding: .35rem; }
    .calc-row { display: grid; grid-template-columns: minmax(190px, 1.25fr) minmax(170px, 1fr) minmax(150px, .9fr); align-items: center; gap: .75rem; padding: .5rem .6rem; border-radius: 9px; border-left: 3px solid transparent; transition: background .15s, border-color .15s; }
    .calc-row + .calc-row { margin-top: 2px; }
    .calc-row:hover { background: #f8fafc; }
    .calc-row.is-on { background: #f0f6ff; border-left-color: #17324D; }

    .calc-label { display: flex; align-items: center; gap: .55rem; margin: 0; cursor: pointer; user-select: none; }
    .calc-label input { position: absolute; opacity: 0; pointer-events: none; }
    .calc-tick { flex: 0 0 20px; width: 20px; height: 20px; border-radius: 6px; border: 2px solid #cbd5e1; background: #fff; display: inline-flex; align-items: center; justify-content: center; color: transparent; font-size: .8rem; transition: all .15s; }
    .calc-label input:focus-visible + .calc-tick { box-shadow: 0 0 0 3px rgba(23,50,77,.2); }
    .calc-row.is-on .calc-tick { background: #17324D; border-color: #17324D; color: #fff; }
    .calc-num { flex: 0 0 22px; width: 22px; height: 22px; border-radius: 50%; background: #e2e8f0; color: #475569; font-size: .7rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; }
    .calc-row.is-on .calc-num { background: #dbeafe; color: #17324D; }
    .calc-name { font-size: .8rem; font-weight: 600; color: #334155; line-height: 1.15; }
    .calc-name i { color: #94a3b8; margin-right: .25rem; }
    .calc-row.is-on .calc-name, .calc-row.is-on .calc-name i { color: #17324D; }
    .calc-formula { display: block; font-size: .68rem; font-weight: 500; color: #94a3b8; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; margin-top: 2px; }

    .calc-inputs { display: flex; align-items: center; gap: .4rem; flex-wrap: wrap; }
    .calc-money { display: flex; align-items: stretch; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; overflow: hidden; max-width: 170px; }
    .calc-money:focus-within { border-color: #17324D; box-shadow: 0 0 0 3px rgba(30,64,175,.1); }
    .calc-money input { border: 0; outline: 0; width: 100%; min-width: 0; padding: .25rem .5rem; font-size: .8rem; background: transparent; }
    .calc-money span { background: #f1f5f9; color: #64748b; font-size: .65rem; padding: 0 .45rem; display: flex; align-items: center; white-space: nowrap; border-left: 1px solid #e2e8f0; }
    .calc-coef { display: inline-flex; align-items: center; gap: .2rem; font-size: .7rem; color: #64748b; }
    .calc-coef input { width: 46px; border: 1px solid #e2e8f0; border-radius: 6px; padding: .15rem .3rem; font-size: .75rem; text-align: center; }
    .calc-chip { border: 1px solid #e2e8f0; background: #fff; color: #475569; border-radius: 6px; font-size: .68rem; font-weight: 600; padding: .1rem .4rem; line-height: 1.4; }
    .calc-chip:hover { border-color: #17324D; color: #17324D; }
    .calc-chip.is-sel { background: #17324D; border-color: #17324D; color: #fff; }
    .calc-poids { display: inline-flex; align-items: center; gap: .3rem; background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; border-radius: 999px; padding: .15rem .55rem; font-size: .72rem; font-weight: 700; white-space: nowrap; }

    .calc-result { text-align: right; line-height: 1.2; }
    .calc-result small { display: block; font-size: .66rem; color: #94a3b8; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .calc-result strong { font-size: .85rem; color: #94a3b8; }
    .calc-row.is-on .calc-result strong { color: #17324D; }
    .calc-result .calc-marge { display: flex; justify-content: flex-end; align-items: center; gap: .35rem; margin-top: 3px; font-size: .7rem; white-space: nowrap; }
    .calc-marge .calc-avant { color: #64748b; font-weight: 600; }
    .calc-marge .calc-apres-tag { background: #dcfce7; color: #15803d; border-radius: 999px; padding: .05rem .45rem; font-weight: 700; }

    .calc-pu-marge { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; font-size: .75rem; color: #475569; }
    .calc-pu-marge b { color: #17324D; }
    .calc-pu-marge .calc-pu-marge-tag { background: #dcfce7; color: #15803d; border-radius: 999px; padding: .1rem .5rem; font-weight: 700; }

    .ligne-pu.ligne-pu-auto { background: #eef6ff; border-color: #93c5fd; color: #17324D; font-weight: 700; cursor: not-allowed; }

    @media (max-width: 767.98px) {
        .calc-row { grid-template-columns: 1fr; gap: .4rem; }
        .calc-result { text-align: left; }
    }
</style>

<script>
(function () {
    const KG_PAR_M2 = {{ \App\Models\DevisLigne::MOTEUR_KG_PAR_M2 }};
    const COEFS_ML = [2, 4, 6, 8];
    const AJOUTS_VR = [0.30, 0.40];
    const PLACEHOLDERS = { mot: 'Prix moteur', ps: 'Prix HT' };
    const AJOUT_VR_DEFAUT = {{ \App\Models\DevisLigne::VOLET_AJOUT_HAUTEUR }};

    const nombre = (v) => { const n = parseFloat(String(v ?? '').replace(',', '.')); return isNaN(n) ? 0 : n; };
    const fr = (n, dec = 2) => n.toLocaleString('fr-FR', { minimumFractionDigits: dec, maximumFractionDigits: dec });
    const dim = (n) => n.toLocaleString('fr-FR', { maximumFractionDigits: 3 });
    const arrondi = (n) => Math.round(n * 100) / 100;

    const CALCULS = [
        { cle: 'm2', titre: 'Mètre carré', icone: 'bi-bounding-box', formule: 'L × H × Prix × marge', unite: 'MAD/m²', marge: true,
          montant: (L, H, c) => L * H * c.prix,
          detail: (L, H, c) => `${dim(L)} × ${dim(H)} × ${fr(c.prix)}` },
        { cle: 'ml', titre: 'Mètre linéaire', icone: 'bi-rulers', formule: '((L × 2) + (H × N)) × Prix × marge', unite: 'MAD/ml', marge: true,
          montant: (L, H, c) => (L * 2 * c.prix) + (H * c.coef * c.prix),
          detail: (L, H, c) => `(${dim(L)}×2 + ${dim(H)}×${dim(c.coef)}) × ${fr(c.prix)}` },
        { cle: 'fc', titre: 'Faux cadre', icone: 'bi-border-outer', formule: '((L × 2) + (H × 2)) × Prix × marge', unite: 'MAD/ml', marge: true,
          montant: (L, H, c) => (L * 2 * c.prix) + (H * 2 * c.prix),
          detail: (L, H, c) => `(${dim(L)}×2 + ${dim(H)}×2) × ${fr(c.prix)}` },
        { cle: 'vr', titre: 'Volet roulant', icone: 'bi-window', formule: 'L × (H + ajout) × Prix', unite: 'MAD/m²',
          montant: (L, H, c) => L * (H + c.ajout) * c.prix,
          detail: (L, H, c) => `${dim(L)} × (${dim(H)}+${dim(c.ajout)}) × ${fr(c.prix)}` },
        { cle: 'mot', titre: 'Moteur', icone: 'bi-gear-wide-connected', formule: `Poids : L × H × ${KG_PAR_M2} (kg)`, unite: 'MAD',
          montant: (L, H, c) => c.prix,
          detail: () => 'Prix saisi (catalogue vendeur)' },
        { cle: 'ps', titre: 'Prix société', icone: 'bi-building', formule: 'Prix HT × marge', unite: 'MAD HT', marge: true,
          montant: (L, H, c) => c.prix,
          detail: (L, H, c) => `${fr(c.prix)} HT` },
    ];

    function ligneHtml(def, i) {
        let extra = '';
        if (def.cle === 'ml') {
            extra = `<span class="calc-coef">H ×
                        <input type="number" class="calc-coef-input" min="0" step="1" value="4" title="Nombre de hauteurs">
                        ${COEFS_ML.map(n => `<button type="button" class="calc-chip" data-coef="${n}">${n}</button>`).join('')}
                    </span>`;
        }
        if (def.cle === 'vr') {
            extra = `<span class="calc-coef">H +
                        <input type="number" class="calc-ajout-input" min="0" step="0.05" value="${AJOUT_VR_DEFAUT.toFixed(2)}" title="Ajout à la hauteur (m)"> m
                        ${AJOUTS_VR.map(n => `<button type="button" class="calc-chip" data-ajout="${n}">${fr(n)}</button>`).join('')}
                    </span>`;
        }
        if (def.cle === 'mot') {
            extra = `<span class="calc-poids" title="L × H × ${KG_PAR_M2}"><i class="bi bi-speedometer2"></i><span class="calc-poids-val">0 kg</span></span>`;
        }
        return `
            <div class="calc-row" data-cle="${def.cle}">
                <label class="calc-label">
                    <input type="checkbox" class="calc-check">
                    <span class="calc-tick"><i class="bi bi-check-lg"></i></span>
                    <span class="calc-num">${i + 1}</span>
                    <span class="calc-name"><i class="bi ${def.icone}"></i>${def.titre}<span class="calc-formula">${def.formule}</span></span>
                </label>
                <div class="calc-inputs">
                    <div class="calc-money">
                        <input type="number" class="calc-prix" min="0" step="0.01" placeholder="${PLACEHOLDERS[def.cle] || 'Prix'}">
                        <span>${def.unite}</span>
                    </div>
                    ${extra}
                </div>
                <div class="calc-result">
                    <small class="calc-detail">—</small><strong class="calc-montant">0,00 MAD</strong>
                    ${def.marge ? `<div class="calc-marge" hidden>Avant marge <span class="calc-avant">0,00</span><span class="calc-apres-tag">+0 %</span></div>` : ''}
                </div>
            </div>`;
    }

    function initLigne(row) {
        if (row.dataset.calculInit) return;
        const designation = row.querySelector('.ligne-designation');
        const pu = row.querySelector('.ligne-pu');
        if (!designation || !pu) return;
        row.dataset.calculInit = '1';

        let hidden = row.querySelector('input[name$="[calcul]"]');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = designation.name.replace(/\[designation\]$/, '[calcul]');
            row.prepend(hidden);
        }

        const panel = document.createElement('div');
        panel.className = 'calc-pu';
        panel.innerHTML = `
            <div class="calc-pu-head">
                <div class="calc-pu-title">
                    <span class="calc-pu-icon"><i class="bi bi-calculator"></i></span>
                    <div><strong>Calcul du prix unitaire</strong><small>Cochez un ou plusieurs calculs : leur somme devient le PU</small></div>
                </div>
                <div class="calc-pu-marge" hidden>
                    <span>Avant marge : <b class="calc-pu-avant">0,00 MAD</b></span>
                    <i class="bi bi-arrow-right"></i>
                    <span>Après marge : <b class="calc-pu-apres">0,00 MAD</b></span>
                    <span class="calc-pu-marge-tag">+0 %</span>
                </div>
                <div class="calc-pu-total"><span class="calc-total-label">PU saisi manuellement</span><strong class="calc-total">—</strong></div>
            </div>
            <div class="calc-rows">${CALCULS.map(ligneHtml).join('')}</div>`;
        row.appendChild(panel);

        let etat = {};
        try { etat = JSON.parse(hidden.value || '{}') || {}; } catch (e) { etat = {}; }
        CALCULS.forEach(def => {
            const r = panel.querySelector(`.calc-row[data-cle="${def.cle}"]`);
            const s = etat[def.cle] || {};
            r.querySelector('.calc-check').checked = !!s.on;
            if (s.prix) r.querySelector('.calc-prix').value = s.prix;
            if (def.cle === 'ml' && s.coef !== undefined) r.querySelector('.calc-coef-input').value = s.coef;
            if (def.cle === 'vr' && s.ajout !== undefined) r.querySelector('.calc-ajout-input').value = s.ajout;
        });

        const largeur = row.querySelector('.ligne-largeur');
        const hauteur = row.querySelector('.ligne-hauteur');
        const margeInput = document.getElementById('marge_pct');

        function maj() {
            const L = nombre(largeur && largeur.value);
            const H = nombre(hauteur && hauteur.value);
            const coefMarge = 1 + nombre(margeInput && margeInput.value) / 100;
            const margeLabel = '+' + dim((coefMarge - 1) * 100) + ' %';
            const sauvegarde = {};
            let total = 0;
            let totalSansMarge = 0;
            let margeCochee = false;
            let coches = 0;

            CALCULS.forEach(def => {
                const r = panel.querySelector(`.calc-row[data-cle="${def.cle}"]`);
                const on = r.querySelector('.calc-check').checked;
                const c = { prix: nombre(r.querySelector('.calc-prix').value) };
                if (def.cle === 'ml') {
                    c.coef = nombre(r.querySelector('.calc-coef-input').value);
                    r.querySelectorAll('.calc-chip').forEach(ch => ch.classList.toggle('is-sel', nombre(ch.dataset.coef) === c.coef));
                }
                if (def.cle === 'vr') {
                    c.ajout = nombre(r.querySelector('.calc-ajout-input').value);
                    r.querySelectorAll('.calc-chip').forEach(ch => ch.classList.toggle('is-sel', Math.abs(nombre(ch.dataset.ajout) - c.ajout) < 0.001));
                }
                if (def.cle === 'mot') {
                    r.querySelector('.calc-poids-val').textContent = fr(L * H * KG_PAR_M2) + ' kg';
                }
                const base = def.montant(L, H, c);
                const brut = base * (def.marge ? coefMarge : 1);
                r.classList.toggle('is-on', on);
                r.querySelector('.calc-detail').textContent = def.detail(L, H, c) + (def.marge && coefMarge !== 1 ? ` × ${dim(coefMarge)}` : '');
                r.querySelector('.calc-montant').textContent = fr(arrondi(brut)) + ' MAD';
                if (def.marge) {
                    const ligneMarge = r.querySelector('.calc-marge');
                    ligneMarge.hidden = coefMarge === 1;
                    ligneMarge.querySelector('.calc-avant').textContent = fr(arrondi(base)) + ' MAD';
                    ligneMarge.querySelector('.calc-apres-tag').textContent = margeLabel;
                }
                if (on) {
                    total += brut;
                    totalSansMarge += base;
                    coches++;
                    if (def.marge) margeCochee = true;
                }
                sauvegarde[def.cle] = Object.assign({ on }, c);
            });

            total = arrondi(total);
            hidden.value = JSON.stringify(sauvegarde);

            const resumeMarge = panel.querySelector('.calc-pu-marge');
            resumeMarge.hidden = !(margeCochee && coefMarge !== 1);
            resumeMarge.querySelector('.calc-pu-avant').textContent = fr(arrondi(totalSansMarge)) + ' MAD';
            resumeMarge.querySelector('.calc-pu-apres').textContent = fr(total) + ' MAD';
            resumeMarge.querySelector('.calc-pu-marge-tag').textContent = margeLabel;

            const pill = panel.querySelector('.calc-pu-total');
            pill.classList.toggle('is-active', coches > 0);
            panel.querySelector('.calc-total-label').textContent = coches > 0 ? `PU calculé (${coches} coché${coches > 1 ? 's' : ''})` : 'PU saisi manuellement';
            panel.querySelector('.calc-total').textContent = coches > 0 ? fr(total) + ' MAD' : '—';

            pu.readOnly = coches > 0;
            pu.classList.toggle('ligne-pu-auto', coches > 0);
            pu.title = coches > 0 ? 'Calculé automatiquement — décochez les calculs pour saisir le prix' : '';
            if (coches > 0 && nombre(pu.value) !== total) {
                pu.value = total.toFixed(2);
                pu.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }

        panel.addEventListener('input', maj);
        panel.addEventListener('change', maj);
        panel.addEventListener('click', (e) => {
            const chip = e.target.closest('.calc-chip');
            if (!chip) return;
            if (chip.dataset.ajout !== undefined) {
                panel.querySelector('.calc-ajout-input').value = nombre(chip.dataset.ajout).toFixed(2);
            } else {
                panel.querySelector('.calc-coef-input').value = chip.dataset.coef;
            }
            maj();
        });
        [largeur, hauteur, margeInput].forEach(el => el && el.addEventListener('input', maj));
        maj();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('lignesContainer');
        if (!container) return;
        container.querySelectorAll('.ligne-devis').forEach(initLigne);
        new MutationObserver(muts => muts.forEach(m => m.addedNodes.forEach(n => {
            if (n.nodeType === 1 && n.classList.contains('ligne-devis')) initLigne(n);
        }))).observe(container, { childList: true });
    });
})();
</script>
