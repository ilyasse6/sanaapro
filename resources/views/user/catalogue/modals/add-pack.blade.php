<div class="modal fade" id="modalAddPack" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-catalogue">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Ajouter un pack / ensemble</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('catalogue.store.pack') }}" method="post" class="d-flex flex-column overflow-hidden">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control modern-input" required maxlength="255" value="{{ old('nom') }}" placeholder="Ex : Ensemble baie vitrée + fenêtres">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control modern-input" rows="2" placeholder="Description du pack">{{ old('description') }}</textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Prix pack (MAD) <span class="text-danger">*</span></label>
                            <input type="number" name="prix" class="form-control modern-input" required min="0" step="0.01" value="{{ old('prix', 0) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Réduction (%)</label>
                            <input type="number" name="reduction" class="form-control modern-input" min="0" max="100" step="0.01" value="{{ old('reduction', 0) }}">
                        </div>
                    </div>
                    @include('user.catalogue.partials.services-picker', ['selection' => array_map('intval', old('services', []))])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
