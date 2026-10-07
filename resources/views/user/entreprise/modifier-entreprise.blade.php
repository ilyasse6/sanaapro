<div class="modal fade" id="EditModal-{{ $entreprise->id }}" tabindex="-1" aria-labelledby="EditModal"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-design">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-building me-1"></i>
                    <span>Modifier l'entreprise</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row justify-content-center"
                    action="{{ route('entreprises.update', $entreprise->id) }}" method="POST"
                    id="editEntrepriseForm-{{ $entreprise->id }}">
                    @csrf
                    @method('PUT')
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <!-- Description -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="edit_description_{{ $entreprise->id }}" class="form-label">
                                            <i class="bi bi-card-text me-1"></i>
                                            Description :
                                        </label>
                                        <textarea class="form-control custom-input" id="edit_description_{{ $entreprise->id }}" name="description"
                                            rows="3" placeholder="Entrez la description" required>{{ old('description', $entreprise->description) }}</textarea>
                                        <div id="edit_description_{{ $entreprise->id }}-feedback"
                                            class="validation-feedback"></div>
                                        @if (session('failed_modal') == "EditModal-{$entreprise->id}")
                                            @error('description')
                                                <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                            @enderror
                                        @endif
                                    </div>
                                </div>
                                <!-- Abréviation -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_abbreviation_{{ $entreprise->id }}" class="form-label">
                                            <i class="bi bi-text-paragraph me-1"></i>
                                            Abréviation :
                                        </label>
                                        <input type="text" class="form-control custom-input"
                                            id="edit_abbreviation_{{ $entreprise->id }}" name="abbreviation"
                                            value="{{ old('abbreviation', $entreprise->abbreviation) }}"
                                            placeholder="Entrez l'abréviation" required>
                                        <div id="edit_abbreviation_{{ $entreprise->id }}-feedback"
                                            class="validation-feedback"></div>
                                        @if (session('failed_modal') == "EditModal-{$entreprise->id}")
                                            @error('abbreviation')
                                                <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                            @enderror
                                        @endif
                                    </div>
                                </div>
                                <!-- Type -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_type_{{ $entreprise->id }}" class="form-label">
                                            <i class="bi bi-tag me-1"></i>
                                            Type :
                                        </label>
                                        <select class="form-control custom-input"
                                            id="edit_type_{{ $entreprise->id }}" name="type" required>
                                            <option value="">Sélectionnez un type</option>
                                            @foreach (\App\Models\Entreprise::TYPE_LABELS as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ old('type', $entreprise->type) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div id="edit_type_{{ $entreprise->id }}-feedback"
                                            class="validation-feedback"></div>
                                        @if (session('failed_modal') == "EditModal-{$entreprise->id}")
                                            @error('type')
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
                    onclick="document.getElementById('editEntrepriseForm-{{ $entreprise->id }}').submit();">
                    <i class="bi bi-save me-1"></i>
                    Enregistrer les modifications
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-circle-fill me-1"></i>Fermer
                </button>
            </div>
        </div>
    </div>
</div>
