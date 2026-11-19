{{-- Bénéfice du devis = total après marge − total avant marge (remises comprises). Usage interne : n'apparaît pas sur le document client. --}}
<style>
    .benefice-box { margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #cbd5e1; font-size: .9rem; }
    .benefice-box .benefice-ligne { display: flex; justify-content: space-between; align-items: center; margin-bottom: .35rem; color: #475569; }
    .benefice-box .benefice-ligne strong { color: #17324D; white-space: nowrap; }
    .benefice-box .benefice-total { display: flex; justify-content: space-between; align-items: center; margin-top: .5rem; padding: .55rem .75rem; border-radius: 10px; background: #dcfce7; color: #15803d; font-weight: 700; }
    .benefice-box .benefice-total.is-zero { background: #f1f5f9; color: #64748b; }
    .benefice-box .benefice-pct { font-size: .72rem; font-weight: 700; background: #fff; border-radius: 999px; padding: .1rem .45rem; margin-left: .35rem; }
</style>
<div class="benefice-box" id="beneficeBox">
    <div class="benefice-ligne"><span>Total avant marge</span><strong id="totalAvantMarge">0,00 MAD</strong></div>
    <div class="benefice-ligne"><span>Total après marge</span><strong id="totalApresMarge">0,00 MAD</strong></div>
    <div class="benefice-total is-zero" id="beneficeTotal">
        <span><i class="bi bi-graph-up-arrow me-1"></i>Bénéfice<span class="benefice-pct" id="beneficePct">0 %</span></span>
        <span id="beneficeMontant">0,00 MAD</span>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('lignesContainer');
    if (!container) return;
    const n = v => parseFloat(String(v ?? '').replace(',', '.')) || 0;
    const fr = v => v.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MAD';
    let enAttente = false;

    function calculer() {
        enAttente = false;
        let avant = 0;
        let apres = 0;
        container.querySelectorAll('.ligne-devis').forEach(row => {
            const qte = n(row.querySelector('.ligne-quantite')?.value);
            const pu = n(row.querySelector('.ligne-pu')?.value);
            const remise = n(row.querySelector('.ligne-remise')?.value);
            const puAvant = row.dataset.puAvant !== undefined && row.dataset.puAvant !== '' ? n(row.dataset.puAvant) : pu;
            const coef = qte * (1 - remise / 100);
            apres += pu * coef;
            avant += puAvant * coef;
        });
        const remiseGlobale = 1 - n(document.getElementById('remise_globale_pct')?.value) / 100;
        avant *= remiseGlobale;
        apres *= remiseGlobale;
        const benefice = apres - avant;

        document.getElementById('totalAvantMarge').textContent = fr(avant);
        document.getElementById('totalApresMarge').textContent = fr(apres);
        document.getElementById('beneficeMontant').textContent = fr(benefice);
        document.getElementById('beneficePct').textContent = (avant > 0 ? (benefice / avant * 100) : 0).toLocaleString('fr-FR', { maximumFractionDigits: 1 }) + ' %';
        document.getElementById('beneficeTotal').classList.toggle('is-zero', Math.abs(benefice) < 0.005);
    }

    const planifier = () => {
        if (enAttente) return;
        enAttente = true;
        requestAnimationFrame(calculer);
    };
    document.addEventListener('input', planifier);
    document.addEventListener('change', planifier);
    document.addEventListener('click', planifier);
    new MutationObserver(planifier).observe(container, { childList: true, subtree: true });
    planifier();
});
</script>
