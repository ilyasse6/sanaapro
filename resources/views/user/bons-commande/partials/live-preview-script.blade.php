@php
    $bcFournisseursJson = $fournisseurs->mapWithKeys(fn ($f) => [
        (string) $f->id => [
            'nom' => $f->raison_sociale,
            'sous' => collect([$f->adresse, trim(($f->code_postal ?? '').' '.($f->ville ?? ''))])->filter()->implode(' · '),
        ],
    ]);
@endphp
<script>
(function () {
    const form = document.getElementById('form-bc');
    const tbody = document.getElementById('bc-lignes-tbody');
    const fournisseurs = @json($bcFournisseursJson);
    const statutLabels = { brouillon: 'Brouillon', valide: 'Validé', annule: 'Annulé' };

    function fmtDate(iso) {
        if (!iso) return '—';
        const p = String(iso).split('-');
        if (p.length !== 3) return iso;
        return p[2] + '/' + p[1] + '/' + p[0];
    }

    function esc(s) {
        if (s == null || s === '') return '';
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function parseNum(v) {
        if (v === undefined || v === '' || v === null) return NaN;
        const n = parseFloat(String(v).replace(',', '.'));
        return isNaN(n) ? NaN : n;
    }

    function fmtMoney(n) {
        return n.toFixed(2).replace('.', ',');
    }

    function refreshBcLivePreview() {
        if (!form) return;

        const fid = form.querySelector('[name="fournisseur_id"]')?.value;
        const elNom = document.getElementById('simBcFournisseur');
        const elDetail = document.getElementById('simBcFournisseurDetail');
        if (elNom) {
            if (fid && fournisseurs[fid]) {
                elNom.innerHTML = esc(fournisseurs[fid].nom);
                elDetail.textContent = fournisseurs[fid].sous || '';
            } else {
                elNom.innerHTML = '<span class="simulateur-placeholder">— Choisir un fournisseur —</span>';
                elDetail.textContent = '';
            }
        }

        const d = form.querySelector('[name="date_bc"]')?.value;
        const elDate = document.getElementById('simBcDate');
        if (elDate) elDate.textContent = fmtDate(d);

        const ds = form.querySelector('[name="date_livraison_souhaitee"]')?.value;
        const elDs = document.getElementById('simBcLivSouh');
        if (elDs) {
            elDs.textContent = ds ? fmtDate(ds) : '—';
            elDs.title = ds || '';
        }

        const setTxt = function (id, name) {
            const el = document.getElementById(id);
            const v = form.querySelector('[name="' + name + '"]')?.value?.trim();
            if (el) el.textContent = v || '—';
        };
        setTxt('simBcRefInt', 'reference_interne');
        setTxt('simBcRefF', 'reference_fournisseur');
        setTxt('simBcContact', 'contact_fournisseur');
        setTxt('simBcEmis', 'emis_par');
        setTxt('simBcLieu', 'lieu_livraison');

        const st = form.querySelector('[name="statut"]')?.value;
        const badge = document.getElementById('simBcStatut');
        if (badge) {
            badge.textContent = statutLabels[st] || st || '—';
            badge.className = 'badge badge-statut ' + (st === 'valide' ? 'bg-success' : st === 'annule' ? 'bg-danger' : 'bg-secondary');
        }

        const infos = form.querySelector('[name="informations_additionnelles"]')?.value?.trim();
        const wrap = document.getElementById('simBcInfosWrap');
        const elInfos = document.getElementById('simBcInfos');
        if (wrap && elInfos) {
            if (infos) {
                elInfos.textContent = infos;
                wrap.style.display = '';
            } else {
                wrap.style.display = 'none';
            }
        }

        const simBody = document.getElementById('simBcLignes');
        const totalWrap = document.getElementById('simBcTotalWrap');
        const totalEl = document.getElementById('simBcTotal');
        if (!simBody || !tbody) return;

        const rows = tbody.querySelectorAll('tr');
        let sumHt = 0;
        const lines = [];
        rows.forEach(function (tr) {
            const ref = tr.querySelector('[name*="[reference_produit]"]')?.value?.trim() || '';
            const des = tr.querySelector('[name*="[designation]"]')?.value?.trim() || '';
            const q = tr.querySelector('[name*="[quantite]"]')?.value;
            const u = tr.querySelector('[name*="[unite]"]')?.value?.trim() || '';
            const puRaw = tr.querySelector('[name*="[prix_unitaire_ht]"]')?.value;
            const pu = parseNum(puRaw);
            const qn = parseNum(q);
            if (des || ref || q) {
                let qStr = '—';
                if (q !== undefined && q !== '' && !isNaN(parseFloat(q))) {
                    qStr = String(q).replace('.', ',');
                }
                let puStr = '—';
                if (!isNaN(pu) && pu >= 0) {
                    puStr = fmtMoney(pu);
                    if (!isNaN(qn) && qn > 0) {
                        sumHt += qn * pu;
                    }
                }
                lines.push({ ref: ref || '—', des: des || '—', q: qStr, u: u || '—', pu: puStr });
            }
        });

        if (lines.length === 0) {
            simBody.innerHTML = '<tr><td colspan="5" class="text-center simulateur-placeholder py-2">Aucune ligne</td></tr>';
            if (totalWrap) totalWrap.style.display = 'none';
            return;
        }
        simBody.innerHTML = lines.map(function (l) {
            return '<tr><td>' + esc(l.ref) + '</td><td>' + esc(l.des) + '</td><td class="q">' + esc(l.q) + '</td><td>' + esc(l.u) + '</td><td class="p">' + esc(l.pu) + '</td></tr>';
        }).join('');
        if (totalWrap && totalEl) {
            if (sumHt > 0) {
                totalEl.textContent = fmtMoney(sumHt);
                totalWrap.style.display = '';
            } else {
                totalWrap.style.display = 'none';
            }
        }
    }

    window.refreshBcLivePreview = refreshBcLivePreview;

    form?.addEventListener('input', refreshBcLivePreview);
    form?.addEventListener('change', refreshBcLivePreview);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', refreshBcLivePreview);
    } else {
        refreshBcLivePreview();
    }
})();
</script>
