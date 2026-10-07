<div class="modal fade" id="modalEditService-{{ $service->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-catalogue">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Modifier la prestation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('catalogue.update.service', $service->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control modern-input" required maxlength="255" value="{{ old('nom', $service->nom) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control modern-input" rows="2">{{ old('description', $service->description) }}</textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Prix (MAD) <span class="text-danger">*</span></label>
                            <input type="number" name="prix" class="form-control modern-input" required min="0" step="0.01" value="{{ old('prix', $service->prix) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Réduction (%)</label>
                            <input type="number" name="reduction" class="form-control modern-input" min="0" max="100" step="0.01" value="{{ old('reduction', $service->reduction) }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
