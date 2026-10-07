<div class="modal fade" id="AddModal" tabindex="-1" aria-labelledby="AddModal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content card-design">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-1"></i>
                    <span>Ajouter un profil</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row justify-content-center" action="{{ route('profil.store') }}" method="POST"
                    id="profilForm">
                    @csrf
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="nom" class="form-label">
                                            <i class="bi bi-card-heading me-1"></i>
                                            Nom :
                                        </label>
                                        <input type="text" class="form-control custom-input" id="nom"
                                            name="nom" value="{{ old('nom') }}"
                                            placeholder="Entrez le nom du profil" required>
                                        @error('nom')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-shield-check me-1"></i>
                                            Permissions :
                                        </label>
                                        <div class="permission-tree" id="permissionTree">
                                            <div class="tree-node level-1">
                                                <div class="region-header">
                                                    <div class="header-content">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="all_permissions"
                                                                onchange="toggleAllPermissions(this)">
                                                            <label class="form-check-label fw-bold text-primary"
                                                                for="all_permissions">
                                                                <i class="bi bi-globe me-1"></i>
                                                                Toutes les permissions (toutes les entreprises)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="my-2">

                                            <div class="row g-2">
                                                @foreach ($entreprises as $entreprise)
                                                    <div class="col-12">
                                                        <div class="border rounded p-2">
                                                            <div class="tree-node level-1">
                                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                                    <button type="button" class="tree-toggle btn btn-sm btn-light"
                                                                        onclick="toggleNode('entreprise_{{ $entreprise->id }}')">
                                                                        <i class="bi bi-chevron-right"
                                                                            id="icon_entreprise_{{ $entreprise->id }}"></i>
                                                                    </button>
                                                                    <div class="form-check mb-0">
                                                                        <input class="form-check-input entreprise-checkbox"
                                                                            type="checkbox"
                                                                            id="entreprise_{{ $entreprise->id }}"
                                                                            data-entreprise-id="{{ $entreprise->id }}"
                                                                            onchange="toggleEntreprisePermissions({{ $entreprise->id }}, this)">
                                                                        <label class="form-check-label fw-semibold"
                                                                            for="entreprise_{{ $entreprise->id }}">
                                                                            <i class="bi bi-hospital me-1"></i>
                                                                            {{ $entreprise->abbreviation }}
                                                                            <span class="text-muted small">({{ $entreprise->type }})</span>
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div class="tree-children ps-3"
                                                                    id="entreprise_{{ $entreprise->id }}_children">
                                                                    @foreach ($modules as $module)
                                                                        <div class="tree-node level-2">
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <button type="button" class="tree-toggle btn btn-sm btn-light"
                                                                                    onclick="toggleNode('module_{{ $entreprise->id }}_{{ $module->id }}')">
                                                                                    <i class="bi bi-chevron-right"
                                                                                        id="icon_module_{{ $entreprise->id }}_{{ $module->id }}"></i>
                                                                                </button>
                                                                                <div class="form-check mb-0">
                                                                                    <input
                                                                                        class="form-check-input module-checkbox"
                                                                                        type="checkbox"
                                                                                        id="module_{{ $entreprise->id }}_{{ $module->id }}"
                                                                                        data-module-id="{{ $module->id }}"
                                                                                        data-entreprise-id="{{ $entreprise->id }}"
                                                                                        onchange="toggleModulePermissions({{ $entreprise->id }}, {{ $module->id }}, this)">
                                                                                    <label class="form-check-label fw-semibold"
                                                                                        for="module_{{ $entreprise->id }}_{{ $module->id }}">
                                                                                        <span>{{ $module->nom }}</span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>

                                                                            <div class="tree-children ps-3"
                                                                                id="module_{{ $entreprise->id }}_{{ $module->id }}_children">
                                                                                @foreach ($module->permissions as $permission)
                                                                                    <div class="form-check">
                                                                                        <input
                                                                                            class="form-check-input permission-checkbox"
                                                                                            type="checkbox"
                                                                                            id="permission_{{ $permission->id }}_{{ $entreprise->id }}"
                                                                                            value="{{ $permission->id }}"
                                                                                            data-permission-id="{{ $permission->id }}"
                                                                                            data-module-id="{{ $module->id }}"
                                                                                            data-entreprise-id="{{ $entreprise->id }}"
                                                                                            onchange="updateParentCheckboxes(this)">
                                                                                        <label class="form-check-label"
                                                                                            for="permission_{{ $permission->id }}_{{ $entreprise->id }}">
                                                                                            <i class="bi bi-key me-1"></i>
                                                                                            <span>{{ $permission->action }}</span>
                                                                                        </label>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
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
                    onclick="document.getElementById('profilForm').submit()">
                    <i class="bi bi-plus-circle me-1"></i>
                    Ajouter le profil
                </button>
            </div>
        </div>
    </div>
</div>
