<div class="modal fade" id="AddModal" tabindex="-1" aria-labelledby="AddModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-design">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-lock me-1"></i>
                    <span>Ajouter une permission</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row justify-content-center" action="{{ route('permissions.store') }}" method="POST"
                    id="permissionForm">
                    @csrf
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <!-- Nom -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="nom" class="form-label">
                                            <i class="bi bi-card-heading me-1"></i>
                                            Nom :
                                        </label>
                                        <input type="text" class="form-control custom-input" id="nom"
                                            name="nom" value="{{ old('nom') }}"
                                            placeholder="Entrez le nom de la permission" required>

                                        @error('nom')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Action -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="action" class="form-label">
                                            <i class="bi bi-gear me-1"></i>
                                            Action :
                                        </label>
                                        <select class="form-control custom-input" required id="action" name="action">
                                            <option value="">Sélectionnez une action</option>
                                            <option value="ajouter" {{ old('action') == 'ajouter' ? 'selected' : '' }}>
                                                Ajouter</option>
                                            <option value="modifier"
                                                {{ old('action') == 'modifier' ? 'selected' : '' }}>Modifier</option>
                                            <option value="consulter"
                                                {{ old('action') == 'consulter' ? 'selected' : '' }}>Consulter</option>
                                            <option value="consultermien"
                                                {{ old('action') == 'consultermien' ? 'selected' : '' }}>Consultermien</option>
                                            <option value="desactiver"
                                                {{ old('action') == 'desactiver' ? 'selected' : '' }}>Désactiver
                                            </option>
                                            <option value="activer" {{ old('action') == 'activer' ? 'selected' : '' }}>
                                                Activer</option>
                                            <option value="inactif" {{ old('action') == 'inactif' ? 'selected' : '' }}>
                                                Inactif</option>
                                        </select>

                                        @error('action')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Module -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_module" class="form-label">
                                            <i class="bi bi-collection me-1"></i>
                                            Module :
                                        </label>
                                        <select class="form-control custom-input" id="id_module" name="id_module"
                                            required>
                                            <option value="">Sélectionnez un module</option>
                                            @foreach ($modules as $module)
                                                <option value="{{ $module->id }}"
                                                    {{ old('id_module') == $module->id ? 'selected' : '' }}>
                                                    {{ $module->nom }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('id_module')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                    <i class="ri-close-circle-fill me-1"></i>Fermer
                </button>
                <button type="submit" class="btn btn-primary" id="submitButton"
                    onclick="document.getElementById('permissionForm').submit()">
                    <i class="bi bi-plus-circle me-1"></i>
                    Ajouter la permission
                </button>
            </div>
        </div>
    </div>
</div>
