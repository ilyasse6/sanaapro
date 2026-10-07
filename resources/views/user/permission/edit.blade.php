<div class="modal fade" id="EditModal-{{ $permission->id }}" tabindex="-1" aria-labelledby="EditModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-design">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-lock me-1"></i>
                    <span>Modifier la permission</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row justify-content-center" action="{{ route('permissions.update', $permission->id) }}"
                    method="POST" id="editPermissionForm-{{ $permission->id }}">
                    @csrf
                    @method('PUT')
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <!-- Nom -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="edit_nom_{{ $permission->id }}" class="form-label">
                                            <i class="bi bi-card-heading me-1"></i>
                                            Nom :
                                        </label>
                                        <input type="text" class="form-control custom-input"
                                            id="edit_nom_{{ $permission->id }}" name="nom"
                                            value="{{ old('nom', $permission->nom) }}" required>
                                        @if (session('failed_modal') == "EditModal-{$permission->id}")
                                            @error('nom')
                                                <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                            @enderror
                                        @endif
                                    </div>
                                </div>

                                <!-- Action -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="edit_action_{{ $permission->id }}" class="form-label">
                                            <i class="bi bi-gear me-1"></i>
                                            Action :
                                        </label>
                                        <select class="form-control custom-input" id="edit_action_{{ $permission->id }}"
                                            name="action">
                                            <option value="">Sélectionnez une action</option>
                                            <option value="ajouter"
                                                {{ old('action', $permission->action) == 'ajouter' ? 'selected' : '' }}>
                                                Ajouter</option>
                                            <option value="modifier"
                                                {{ old('action', $permission->action) == 'modifier' ? 'selected' : '' }}>
                                                Modifier</option>
                                            <option value="consulter"
                                                {{ old('action', $permission->action) == 'consulter' ? 'selected' : '' }}>
                                                Consulter</option>
                                            <option value="consultermien"
                                                {{ old('action', $permission->action) == 'consultermien' ? 'selected' : '' }}>
                                                Consulter</option>
                                            <option value="desactiver"
                                                {{ old('action', $permission->action) == 'desactiver' ? 'selected' : '' }}>
                                                Désactiver</option>
                                            <option value="activer"
                                                {{ old('action', $permission->action) == 'activer' ? 'selected' : '' }}>
                                                Activer</option>
                                            <option value="inactif"
                                                {{ old('action', $permission->action) == 'inactif' ? 'selected' : '' }}>
                                                Inactif</option>
                                        </select>
                                        @if (session('failed_modal') == "EditModal-{$permission->id}")
                                            @error('action')
                                                <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                            @enderror
                                        @endif
                                    </div>
                                </div>

                                <!-- Module -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="edit_id_module_{{ $permission->id }}" class="form-label">
                                            <i class="bi bi-collection me-1"></i>
                                            Module :
                                        </label>
                                        <select class="form-control custom-input"
                                            id="edit_id_module_{{ $permission->id }}" name="id_module" required>
                                            @foreach ($modules as $module)
                                                <option value="{{ $module->id }}"
                                                    {{ old('id_module', $permission->id_module) == $module->id ? 'selected' : '' }}>
                                                    {{ $module->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if (session('failed_modal') == "EditModal-{$permission->id}")
                                            @error('id_module')
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
                    onclick="document.getElementById('editPermissionForm-{{ $permission->id }}').submit()">
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
