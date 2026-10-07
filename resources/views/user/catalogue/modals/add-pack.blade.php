<div class="modal fade" id="modalAddPack" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-catalogue">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Ajouter un pack / ensemble</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('catalogue.store.pack') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control modern-input" required maxlength="255" value="{{ old('nom') }}" placeholder="Ex : Pack Cabinet médical de base">
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
                    <div class="mb-0">
                        <label class="form-label">Prestations incluses (optionnel)</label>
                        <select name="services[]" class="form-select modern-input" multiple size="6">
                            @foreach($services as $s)
                                @if($s->actif)
                                    <option value="{{ $s->id }}" {{ in_array($s->id, old('services', [])) ? 'selected' : '' }}>{{ $s->nom }} – {{ number_format($s->prix_apres_reduction, 2, ',', ' ') }} MAD</option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted">Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs.</small>
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
