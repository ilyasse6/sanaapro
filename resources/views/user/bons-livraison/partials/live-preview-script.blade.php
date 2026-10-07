@php
    $blClientsJson = $clients->mapWithKeys(fn ($c) => [
        (string) $c->id => [
            'nom' => trim($c->nom.' '.($c->prenom ?? '')),
            'sous' => collect([$c->adresse, trim(($c->code_postal ?? '').' '.($c->ville ?? ''))])->filter()->implode(' · '),
        ],
    ]);
@endphp
<script>
(function () {
    const form = document.getElementById('form-bl');
    const tbody = document.getElementById('bl-lignes-tbody');
    const clients = @json($blClientsJson);
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

    function refreshBlLivePreview() {
        if (!form) return;

        const clientId = form.querySelector('[name="client_id"]')?.value;
        const elClient = document.getElementById('simBlClient');
        const elDetail = document.getElementById('simBlClientDetail');
        if (elClient) {
            if (clientId && clients[clientId]) {
                elClient.innerHTML = esc(clients[clientId].nom);
                elDetail.textContent = clients[clientId].sous || '';
            } else {
                elClient.innerHTML = '<span class="simulateur-placeholder">— Choisir un client —</span>';
                elDetail.textContent = '';
            }
        }

        const d = form.querySelector('[name="date_bl"]')?.value;
        const elDate = document.getElementById('simBlDate');
        if (elDate) elDate.textContent = fmtDate(d);

        const lieu = form.querySelector('[name="lieu"]')?.value?.trim();
        const elLieu = document.getElementById('simBlLieu');
        if (elLieu) {
            elLieu.textContent = lieu || '—';
            elLieu.title = lieu || '';
        }

        const setTxt = function (id, name) {
            const el = document.getElementById(id);
            const v = form.querySelector('[name="' + name + '"]')?.value?.trim();
            if (el) el.textContent = v || '—';
        };
        setTxt('simBlCmd', 'numero_commande');
        setTxt('simBlRefCli', 'reference_client');
        setTxt('simBlContact', 'contact_client');
        setTxt('simBlEmis', 'emis_par');

        const st = form.querySelector('[name="statut"]')?.value;
        const badge = document.getElementById('simBlStatut');
        if (badge) {
            badge.textContent = statutLabels[st] || st || '—';
            badge.className = 'badge badge-statut ' + (st === 'valide' ? 'bg-success' : st === 'annule' ? 'bg-danger' : 'bg-secondary');
        }

        const infos = form.querySelector('[name="informations_additionnelles"]')?.value?.trim();
        const wrap = document.getElementById('simBlInfosWrap');
        const elInfos = document.getElementById('simBlInfos');
        if (wrap && elInfos) {
            if (infos) {
                elInfos.textContent = infos;
                wrap.style.display = '';
            } else {
                wrap.style.display = 'none';
            }
        }

        const simBody = document.getElementById('simBlLignes');
        if (!simBody || !tbody) return;

        const rows = tbody.querySelectorAll('tr');
        const lines = [];
        rows.forEach(function (tr) {
            const ref = tr.querySelector('[name*="[reference_produit]"]')?.value?.trim() || '';
            const des = tr.querySelector('[name*="[designation]"]')?.value?.trim() || '';
            const q = tr.querySelector('[name*="[quantite]"]')?.value;
            const u = tr.querySelector('[name*="[unite]"]')?.value?.trim() || '';
            if (des || ref || q) {
                let qStr = '—';
                if (q !== undefined && q !== '' && !isNaN(parseFloat(q))) {
                    qStr = String(q).replace('.', ',');
                }
                lines.push({ ref: ref || '—', des: des || '—', q: qStr, u: u || '—' });
            }
        });

        if (lines.length === 0) {
            simBody.innerHTML = '<tr><td colspan="4" class="text-center simulateur-placeholder py-2">Aucune ligne</td></tr>';
            return;
        }
        simBody.innerHTML = lines.map(function (l) {
            return '<tr><td>' + esc(l.ref) + '</td><td>' + esc(l.des) + '</td><td class="q">' + esc(l.q) + '</td><td>' + esc(l.u) + '</td></tr>';
        }).join('');
    }

    window.refreshBlLivePreview = refreshBlLivePreview;

    form?.addEventListener('input', refreshBlLivePreview);
    form?.addEventListener('change', refreshBlLivePreview);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', refreshBlLivePreview);
    } else {
        refreshBlLivePreview();
    }
})();
</script>
