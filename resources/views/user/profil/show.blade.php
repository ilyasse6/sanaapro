
<style>
    .permission-tree-detail {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        background-color: #f8f9fa;
    }

    .tree-node-detail {
        margin-left: 0;
        padding: 0.25rem 0;
    }

    .tree-toggle-detail {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 0.8rem;
        padding: 0;
        margin-right: 0.5rem;
        cursor: pointer;
        width: 20px;
        text-align: center;
    }

    .tree-children-detail {
        display: none;
    }

    .entreprise-header-detail {
        background-color: #e8f5e8;
        border-left: 4px solid #4caf50;
        padding: 0.5rem;
        margin: 0.15rem 0;
        border-radius: 0.25rem;
        display: flex;
        align-items: center;
    }

    .module-header-detail {
        background-color: #fff3e0;
        border-left: 4px solid #ff9800;
        padding: 0.4rem;
        margin: 0.1rem 0;
        border-radius: 0.25rem;
        display: flex;
        align-items: center;
    }

    .permission-item-detail {
        padding: 0.3rem;
        margin: 0.05rem 0;
        background-color: #e8f5e8;
        border-radius: 0.25rem;
        border-left: 3px solid #28a745;
    }

    .permission-item-detail.inactive {
        background-color: #f8f9fa;
        border-left: 3px solid #6c757d;
        opacity: 0.6;
    }

    .modal-dialog-detail {
        max-width: 90vw;
        width: 90vw;
        max-height: 85vh;
        margin: 2.5vh auto;
        display: flex;
        flex-direction: column;
        position: relative;
        top: 0;
        transform: none;
    }

    .modal-content-detail {
        height: 100%;
        max-height: 85vh;
        display: flex;
        flex-direction: column;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .modal-header-detail {
        flex-shrink: 0;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
        background-color: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 1051;
    }

    .modal-body-detail {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        min-height: 0;
        max-height: calc(85vh - 140px);
    }

    .modal-footer-detail {
        flex-shrink: 0;
        border-top: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
        background-color: #f8f9fa;
    }

    .header-content-detail {
        display: flex;
        align-items: center;
        width: 100%;
    }
</style>
<div class="modal fade" id="DetailModal-{{ $profil->id }}" tabindex="-1" aria-labelledby="DetailModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-detail">
        <div class="modal-content modal-content-detail card-design">
            <div class="modal-header modal-header-detail">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-1"></i>
                    <span>Détails du profil : {{ $profil->nom }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-detail">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="fade-in-left">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-card-heading me-1"></i>
                                            Nom :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            value="{{ $profil->nom }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Statut :
                                        </label>
                                        <input type="text" class="form-control custom-input" readonly
                                            value="{{ $profil->actif ? 'Actif' : 'Inactif' }}">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-text-paragraph me-1"></i>
                                            Description :
                                        </label>
                                        <textarea class="form-control custom-input" readonly rows="2">{{ $profil->description ?? 'Aucune description' }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-shield-check me-1"></i>
                                            Permissions assignées :
                                        </label>
                                        <div class="permission-tree-detail"
                                            id="permissionTreeDetail{{ $profil->id }}">
                                            @php
                                                $profilePermissionEntries = DB::table('profile_permissions')
                                                    ->where('id_profile', $profil->id)
                                                    ->where('actif', 1)
                                                    ->get();

                                                $isSuperAdmin = $profilePermissionEntries
                                                    ->filter(function ($e) {
                                                        return $e->id_permission == null && $e->id_entreprise == null;
                                                    })
                                                    ->isNotEmpty();

                                                $globalPermissionIds = $profilePermissionEntries
                                                    ->filter(function ($e) {
                                                        return $e->id_permission != null && $e->id_entreprise == null;
                                                    })
                                                    ->pluck('id_permission')
                                                    ->map(function ($id) {
                                                        return (int) $id;
                                                    })
                                                    ->unique()
                                                    ->values()
                                                    ->all();

                                                $etabFullIds = $profilePermissionEntries
                                                    ->filter(function ($e) {
                                                        return $e->id_permission == null && $e->id_entreprise != null;
                                                    })
                                                    ->pluck('id_entreprise')
                                                    ->map(function ($id) {
                                                        return (int) $id;
                                                    })
                                                    ->unique()
                                                    ->values()
                                                    ->all();

                                                $permByEtab = [];
                                                foreach ($profilePermissionEntries as $e) {
                                                    if ($e->id_permission == null || $e->id_entreprise == null) {
                                                        continue;
                                                    }
                                                    $eid = (int) $e->id_entreprise;
                                                    if (! isset($permByEtab[$eid])) {
                                                        $permByEtab[$eid] = [];
                                                    }
                                                    $permByEtab[$eid][] = (int) $e->id_permission;
                                                }
                                            @endphp

                                            @if ($profilePermissionEntries->isEmpty())
                                                <div class="text-center text-muted py-4">
                                                    <i class="bi bi-shield-x me-2"></i>
                                                    Aucune permission assignée à ce profil
                                                </div>
                                            @elseif ($isSuperAdmin)
                                                <div class="alert alert-info mb-3">
                                                    <i class="bi bi-shield-fill-check me-2"></i>
                                                    <strong>Super administrateur</strong> — accès complet sur tous les
                                                    établissements.
                                                </div>
                                                <div class="row g-2">
                                                    @foreach ($entreprises as $entreprise)
                                                        <div class="col-12">
                                                            <div class="border rounded p-2 bg-white">
                                                                @php
                                                                    $nodeEtab = 'det_etab_' . $profil->id . '_' . $entreprise->id;
                                                                @endphp
                                                                <div class="tree-node-detail">
                                                                    <div class="entreprise-header-detail">
                                                                        <button type="button" class="tree-toggle-detail"
                                                                            onclick="toggleNodeDetail('{{ $nodeEtab }}')">
                                                                            <i class="bi bi-chevron-right"
                                                                                id="icon_{{ $nodeEtab }}"></i>
                                                                        </button>
                                                                        <div class="header-content-detail">
                                                                            <div class="form-check mb-0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    checked disabled>
                                                                                <label class="form-check-label fw-semibold">
                                                                                    <i class="bi bi-hospital me-1"></i>
                                                                                    {{ $entreprise->abbreviation }}
                                                                                    <span
                                                                                        class="text-muted small">({{ $entreprise->type }})</span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tree-children-detail ps-3"
                                                                        id="{{ $nodeEtab }}_children">
                                                                        @foreach ($modules as $module)
                                                                            @php
                                                                                $nodeMod = 'det_mod_' . $profil->id . '_' . $entreprise->id . '_' . $module->id;
                                                                            @endphp
                                                                            <div class="tree-node-detail">
                                                                                <div class="module-header-detail">
                                                                                    <button type="button"
                                                                                        class="tree-toggle-detail"
                                                                                        onclick="toggleNodeDetail('{{ $nodeMod }}')">
                                                                                        <i class="bi bi-chevron-right"
                                                                                            id="icon_{{ $nodeMod }}"></i>
                                                                                    </button>
                                                                                    <div class="header-content-detail">
                                                                                        <div class="form-check mb-0">
                                                                                            <input class="form-check-input"
                                                                                                type="checkbox" checked
                                                                                                disabled>
                                                                                            <label
                                                                                                class="form-check-label fw-semibold">{{ $module->nom }}</label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="tree-children-detail ps-3"
                                                                                    id="{{ $nodeMod }}_children">
                                                                                    @foreach ($module->permissions as $permission)
                                                                                        <div class="tree-node-detail">
                                                                                            <div
                                                                                                class="permission-item-detail">
                                                                                                <div class="form-check">
                                                                                                    <input
                                                                                                        class="form-check-input"
                                                                                                        type="checkbox"
                                                                                                        checked disabled>
                                                                                                    <label
                                                                                                        class="form-check-label">
                                                                                                        <i
                                                                                                            class="bi bi-key me-1"></i>{{ $permission->action }}
                                                                                                    </label>
                                                                                                </div>
                                                                                            </div>
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
                                            @else
                                                <div class="row g-2">
                                                    @foreach ($entreprises as $entreprise)
                                                        @php
                                                            $eid = (int) $entreprise->id;
                                                            $fullEtab = in_array($eid, $etabFullIds, true);
                                                            $etabPermIds = $permByEtab[$eid] ?? [];
                                                            $nodeEtab = 'det_etab_' . $profil->id . '_' . $entreprise->id;
                                                        @endphp
                                                        <div class="col-12">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="tree-node-detail">
                                                                    <div class="entreprise-header-detail">
                                                                        <button type="button" class="tree-toggle-detail"
                                                                            onclick="toggleNodeDetail('{{ $nodeEtab }}')">
                                                                            <i class="bi bi-chevron-right"
                                                                                id="icon_{{ $nodeEtab }}"></i>
                                                                        </button>
                                                                        <div class="header-content-detail">
                                                                            <div class="form-check mb-0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    {{ $fullEtab ? 'checked' : '' }} disabled>
                                                                                <label class="form-check-label fw-semibold">
                                                                                    <i class="bi bi-hospital me-1"></i>
                                                                                    {{ $entreprise->abbreviation }}
                                                                                    <span
                                                                                        class="text-muted small">({{ $entreprise->type }})</span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tree-children-detail ps-3"
                                                                        id="{{ $nodeEtab }}_children">
                                                                        @foreach ($modules as $module)
                                                                            @php
                                                                                $nodeMod = 'det_mod_' . $profil->id . '_' . $entreprise->id . '_' . $module->id;
                                                                            @endphp
                                                                            <div class="tree-node-detail">
                                                                                <div class="module-header-detail">
                                                                                    <button type="button"
                                                                                        class="tree-toggle-detail"
                                                                                        onclick="toggleNodeDetail('{{ $nodeMod }}')">
                                                                                        <i class="bi bi-chevron-right"
                                                                                            id="icon_{{ $nodeMod }}"></i>
                                                                                    </button>
                                                                                    <div class="header-content-detail">
                                                                                        <div class="form-check mb-0">
                                                                                            @php
                                                                                                $modAll = true;
                                                                                                foreach ($module->permissions as $p) {
                                                                                                    $pid = (int) $p->id;
                                                                                                    $granted =
                                                                                                        $fullEtab ||
                                                                                                        in_array($pid, $globalPermissionIds, true) ||
                                                                                                        in_array($pid, $etabPermIds, true);
                                                                                                    if (! $granted) {
                                                                                                        $modAll = false;
                                                                                                        break;
                                                                                                    }
                                                                                                }
                                                                                            @endphp
                                                                                            <input class="form-check-input"
                                                                                                type="checkbox"
                                                                                                {{ $modAll && $module->permissions->count() > 0 ? 'checked' : '' }}
                                                                                                disabled>
                                                                                            <label
                                                                                                class="form-check-label fw-semibold">{{ $module->nom }}</label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="tree-children-detail ps-3"
                                                                                    id="{{ $nodeMod }}_children">
                                                                                    @foreach ($module->permissions as $permission)
                                                                                        @php
                                                                                            $pid = (int) $permission->id;
                                                                                            $granted =
                                                                                                $fullEtab ||
                                                                                                in_array($pid, $globalPermissionIds, true) ||
                                                                                                in_array($pid, $etabPermIds, true);
                                                                                        @endphp
                                                                                        <div class="tree-node-detail">
                                                                                            <div
                                                                                                class="permission-item-detail {{ $granted ? '' : 'inactive' }}">
                                                                                                <div class="form-check">
                                                                                                    <input
                                                                                                        class="form-check-input"
                                                                                                        type="checkbox"
                                                                                                        {{ $granted ? 'checked' : '' }}
                                                                                                        disabled>
                                                                                                    <label
                                                                                                        class="form-check-label">
                                                                                                        <i
                                                                                                            class="bi bi-key me-1"></i>{{ $permission->action }}
                                                                                                    </label>
                                                                                                </div>
                                                                                            </div>
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
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer modal-footer-detail">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-circle-fill me-1"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleNodeDetail(nodeId) {
        const childrenDiv = document.getElementById(nodeId + '_children');
        const icon = document.getElementById('icon_' + nodeId);
        if (!childrenDiv || !icon) return;

        if (childrenDiv.style.display === 'none' || childrenDiv.style.display === '') {
            childrenDiv.style.display = 'block';
            icon.classList.remove('bi-chevron-right');
            icon.classList.add('bi-chevron-down');
        } else {
            childrenDiv.style.display = 'none';
            icon.classList.remove('bi-chevron-down');
            icon.classList.add('bi-chevron-right');
        }
    }
</script>
