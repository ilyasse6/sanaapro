<div class="modal fade" id="EditModal-{{ $poste->id }}" tabindex="-1" aria-labelledby="EditModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-design">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-briefcase me-1"></i>
                    <span>Modifier le poste</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row justify-content-center" action="{{ route('postes.update', $poste->id) }}"
                    method="POST" id="editPosteForm-{{ $poste->id }}">
                    @csrf
                    @method('PUT')
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <!-- Intitulé -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="edit_intitule_{{ $poste->id }}" class="form-label">
                                            <i class="bi bi-card-heading me-1"></i>
                                            Intitulé :
                                        </label>
                                        <input type="text" class="form-control custom-input"
                                            id="edit_intitule_{{ $poste->id }}" name="intitule"
                                            value="{{ old('intitule', $poste->intitule) }}" required>
                                        @if (session('failed_modal') == "EditModal-{$poste->id}")
                                            @error('intitule')
                                                <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                            @enderror
                                        @endif
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="edit_description_{{ $poste->id }}" class="form-label">
                                            <i class="bi bi-text-paragraph me-1"></i>
                                            Description :
                                        </label>
                                        <textarea class="form-control custom-input" id="edit_description_{{ $poste->id }}" name="description" rows="3">{{ old('description', $poste->description) }}</textarea>
                                        @if (session('failed_modal') == "EditModal-{$poste->id}")
                                            @error('description')
                                                <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                            @enderror
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"
                    onclick="document.getElementById('editPosteForm-{{ $poste->id }}').submit()">
                    <i class="bi bi-save me-1"></i>
                    Enregistrer
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-circle-fill me-1"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>
