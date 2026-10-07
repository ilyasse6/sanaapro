{{-- Marge de bénéfice du devis : multiplie les calculs Mètre carré, Mètre linéaire et Faux cadre. Attend : $valeur --}}
<style>
    .marge-box { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; background: #fff; border: 2px solid #17324D; border-radius: 16px; padding: .9rem 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 10px 40px rgba(30,64,175,0.08); }
    .marge-box-title { display: flex; align-items: center; gap: .75rem; }
    .marge-box-title .marge-icon { width: 40px; height: 40px; border-radius: 10px; background: #17324D; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.15rem; }
    .marge-box-title strong { display: block; color: #17324D; }
    .marge-box-title small { color: #64748b; }
    .marge-box-input { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
    .marge-box-input .input-group { width: 130px; }
    .marge-box-input input { font-weight: 700; font-size: 1.1rem; color: #17324D; text-align: center; }
    .marge-coef { background: #e0f2fe; color: #17324D; border-radius: 999px; padding: .3rem .75rem; font-weight: 700; font-size: .85rem; white-space: nowrap; }
</style>
<div class="marge-box">
    <div class="marge-box-title">
        <span class="marge-icon"><i class="bi bi-graph-up-arrow"></i></span>
        <div>
            <strong>Marge de bénéfice</strong>
            <small>Appliquée aux calculs Mètre carré, Mètre linéaire et Faux cadre de chaque ligne</small>
        </div>
    </div>
    <div class="marge-box-input">
        <div class="input-group">
            <input type="number" name="marge_pct" id="marge_pct" class="form-control modern-input" min="0" max="1000" step="0.01" value="{{ old('marge_pct', (float) $valeur) }}">
            <span class="input-group-text">%</span>
        </div>
        @foreach([20, 25] as $m)
            <button type="button" class="btn btn-sm btn-outline-primary js-marge-chip" data-marge="{{ $m }}">{{ $m }} %</button>
        @endforeach
        <span class="marge-coef" id="margeCoef">× 1</span>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('marge_pct');
    const coef = document.getElementById('margeCoef');
    const maj = () => {
        const m = parseFloat(String(input.value).replace(',', '.')) || 0;
        coef.textContent = '× ' + (1 + m / 100).toLocaleString('fr-FR', { maximumFractionDigits: 4 });
    };
    document.querySelectorAll('.js-marge-chip').forEach(btn => btn.addEventListener('click', function () {
        input.value = this.dataset.marge;
        input.dispatchEvent(new Event('input', { bubbles: true }));
    }));
    input.addEventListener('input', maj);
    maj();
});
</script>
