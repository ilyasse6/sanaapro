<div class="modal fade" id="chequeActionModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content" id="chequeActionForm">
            @csrf
            <input type="hidden" name="action">
            <div class="modal-header">
                <h5 class="modal-title" data-chq="titre">Chèque</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3 fw-semibold" data-chq="cheque"></p>
                <div class="mb-3" data-chq="bloc-date">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ today()->format('Y-m-d') }}">
                </div>
                <div class="mb-3" data-chq="bloc-motif">
                    <label class="form-label">Motif du rejet</label>
                    <input type="text" name="motif" class="form-control" placeholder="Provision insuffisante, signature non conforme…">
                </div>
                <div class="alert alert-warning small mb-0" data-chq="avertissement"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Confirmer</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('chequeActionModal');
    if (!modal) return;
    const form = document.getElementById('chequeActionForm');
    const part = (n) => modal.querySelector('[data-chq="' + n + '"]');
    const avertissements = {
        impaye: 'Le règlement lié à ce chèque sera annulé : le montant redevient dû par le client.',
        restituer: 'Le chèque est rendu au client ; s’il était encore imputé, le montant redevient dû.',
        representer: 'Le chèque est de nouveau compté comme règlement du client.',
    };
    modal.addEventListener('show.bs.modal', function (e) {
        const b = e.relatedTarget;
        const action = b.dataset.action;
        form.action = b.dataset.url;
        form.elements.action.value = action;
        part('titre').textContent = b.dataset.titre;
        part('cheque').textContent = 'Chèque ' + b.dataset.cheque;
        part('bloc-date').style.display = action === 'restituer' ? 'none' : '';
        part('bloc-motif').style.display = action === 'impaye' ? '' : 'none';
        part('avertissement').style.display = avertissements[action] ? '' : 'none';
        part('avertissement').textContent = avertissements[action] || '';
        form.querySelector('[type=submit]').className = 'btn ' + (action === 'impaye' ? 'btn-danger' : 'btn-primary');
    });
});
</script>
