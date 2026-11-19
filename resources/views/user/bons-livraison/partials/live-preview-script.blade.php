@php
    $blClientsJson = $clients->mapWithKeys(fn ($c) => [
        (string) $c->id => trim($c->nom.($c->prenom ? ' – '.$c->prenom : '')),
    ]);
@endphp
<script>
(function () {
    const form = document.getElementById('form-bl');
    const tbody = document.getElementById('bl-lignes-tbody');
    const doc = document.querySelector('.bv-page');
    const clients = @json($blClientsJson);
    const numero = @json($numeroPreview);
    const vide = '……/……/…………';

    function fmtDate(iso) {
        const p = String(iso || '').split('-');
        return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : '';
    }

    function esc(s) {
        const d = document.createElement('div');
        d.textContent = s == null ? '' : String(s);
        return d.innerHTML;
    }

    function qte(v) {
        const n = parseFloat(String(v).replace(',', '.'));
        if (isNaN(n) || !n) return '';
        if (Number.isInteger(n)) return String(n).padStart(2, '0');
        return String(Math.round(n * 1000) / 1000).replace('.', ',');
    }

    function champ(name) {
        return (form.querySelector('[name="' + name + '"]')?.value || '').trim();
    }

    function hook(name) {
        return doc.querySelector('[data-bv="' + name + '"]');
    }

    function refreshBlLivePreview() {
        if (!form || !doc) return;

        const meta = [
            ['Date', fmtDate(champ('date_bl'))],
            ['BL N°', numero],
            ['Client', clients[champ('client_id')] || ''],
            ['Lieu', champ('lieu')],
            ['N° commande', champ('numero_commande')],
            ['Contact', champ('contact_client')],
        ].filter(function (m) { return m[1] !== ''; });
        hook('meta').innerHTML = meta.map(function (m) {
            return '<tr><td>' + esc(m[0]) + '</td><td>: ' + esc(m[1]) + '</td></tr>';
        }).join('');

        const lignes = [];
        (tbody ? tbody.querySelectorAll('tr') : []).forEach(function (tr) {
            const ref = tr.querySelector('[name*="[reference_produit]"]')?.value?.trim() || '';
            const des = tr.querySelector('[name*="[designation]"]')?.value?.trim() || '';
            const q = tr.querySelector('[name*="[quantite]"]')?.value || '';
            const u = tr.querySelector('[name*="[unite]"]')?.value?.trim() || '';
            if (ref || des) lignes.push({ ref: ref, des: des, q: q, u: u });
        });
        hook('lignes').innerHTML = lignes.length === 0
            ? '<tr><td colspan="5" class="bv-empty">Aucune ligne</td></tr>'
            : lignes.map(function (l, i) {
                return '<tr>'
                    + '<td class="bv-td-num">' + (i + 1) + '</td>'
                    + '<td>' + esc(l.ref) + '</td>'
                    + '<td class="bv-td-desig">' + esc(l.des).replace(/\n/g, '<br>') + '</td>'
                    + '<td class="bv-td-qte">' + qte(l.q) + '</td>'
                    + '<td class="bv-td-unite">' + esc(l.u) + '</td>'
                    + '</tr>';
            }).join('');

        const infos = champ('informations_additionnelles');
        hook('infos').style.display = infos ? '' : 'none';
        hook('infos-texte').innerHTML = esc(infos).replace(/\n/g, '<br>');

        hook('recu').textContent = fmtDate(champ('recu_le')) || vide;
        hook('livre').textContent = fmtDate(champ('livre_le')) || vide;

        window.bvFit?.();
    }

    window.refreshBlLivePreview = refreshBlLivePreview;

    form?.addEventListener('input', refreshBlLivePreview);
    form?.addEventListener('change', refreshBlLivePreview);

    const stage = document.querySelector('.bv-stage');
    if (stage && window.ResizeObserver) new ResizeObserver(function () { window.bvFit?.(); }).observe(stage);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', refreshBlLivePreview);
    } else {
        refreshBlLivePreview();
    }
})();
</script>
