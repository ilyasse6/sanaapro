@extends('user.master')

@section('content')
<style>
    :root { --sanaa-blue: #17324D; --sanaa-blue-dark: #0f2438; --sanaa-gold: #198754; }
    .catalogue-hero { background: linear-gradient(135deg, #17324D 0%, #1e4060 55%, #198754 100%); color: #fff; border-radius: 20px; padding: 2rem; margin-bottom: 2rem; }
    .catalogue-hero h1 { margin-bottom: 0.35rem; color: #fff; font-weight: 700; }
    .catalogue-hero p { color: rgba(255, 255, 255, .88); }
    .nav-tabs .nav-link { border-radius: 12px 12px 0 0; font-weight: 600; color: #64748b; }
    .nav-tabs .nav-link.active { background: linear-gradient(135deg, #17324D, #0f2438); color: white; border-color: #17324D; }
    .card-catalogue { border-radius: 16px; box-shadow: 0 10px 40px rgba(30,64,175,0.08); border: none; }
    .table-catalogue thead th { background: #17324D !important; color: #fff !important; border-color: #17324D; }
    .table-catalogue { border-radius: 12px; overflow: visible; }
    .btn-catalogue { border-radius: 10px; font-weight: 600; }
    .modal-header.bg-catalogue { background: linear-gradient(135deg, #17324D, #0f2438); color: white; border-radius: 12px 12px 0 0; }
    .modern-input { border-radius: 8px; border: 1px solid #e2e8f0; }
    .modern-input:focus { border-color: #17324D; box-shadow: 0 0 0 3px rgba(30,64,175,0.1); outline: none; }
    .catalogue-thumb { width: 64px; height: 64px; object-fit: contain; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 4px; }
    .catalogue-thumb-empty { width: 64px; height: 64px; display: inline-flex; align-items: center; justify-content: center; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; color: #94a3b8; }

    .cat-picker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(104px, 1fr)); gap: 10px; max-height: 300px; overflow-y: auto; padding: 4px; }
    .cat-picker-item { display: flex; flex-direction: column; align-items: center; gap: 4px; padding: 6px; background: #fff; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: border-color .15s, box-shadow .15s; }
    .cat-picker-item:hover { border-color: #94a3b8; }
    .cat-picker-item img { width: 100%; height: 78px; object-fit: contain; }
    .cat-picker-item span { font-size: .68rem; line-height: 1.15; color: #475569; text-align: center; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .cat-picker-item.is-selected { border-color: #198754; box-shadow: 0 0 0 3px rgba(25,135,84,.18); }
    .cat-picker-none i { font-size: 1.8rem; color: #94a3b8; height: 78px; display: flex; align-items: center; }
    .cat-picker-upload { display: flex; align-items: center; gap: 10px; }
    .cat-picker-preview { width: 56px; height: 56px; object-fit: contain; border: 2px solid #198754; border-radius: 10px; padding: 2px; background: #fff; }

    .cat-services-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; max-height: 340px; overflow-y: auto; padding: 4px; }
    .cat-choice { position: relative; margin: 0; cursor: pointer; }
    .cat-choice input { position: absolute; opacity: 0; pointer-events: none; }
    .cat-choice-body { display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%; padding: 8px; background: #fff; border: 2px solid #e2e8f0; border-radius: 12px; transition: border-color .15s, box-shadow .15s; }
    .cat-choice:hover .cat-choice-body { border-color: #94a3b8; }
    .cat-choice input:checked + .cat-choice-body { border-color: #198754; box-shadow: 0 0 0 3px rgba(25,135,84,.18); background: #f0fdf4; }
    .cat-choice-body img, .cat-choice-noimg { width: 100%; height: 90px; object-fit: contain; }
    .cat-choice-noimg { display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 2rem; }
    .cat-choice-nom { font-size: .75rem; font-weight: 600; text-align: center; line-height: 1.2; color: #1e293b; }
    .cat-choice-prix { font-size: .72rem; color: #198754; font-weight: 600; }
    .cat-choice-check { position: absolute; top: 6px; right: 6px; width: 22px; height: 22px; border-radius: 50%; background: #e2e8f0; color: transparent; display: flex; align-items: center; justify-content: center; font-size: .8rem; }
    .cat-choice input:checked + .cat-choice-body .cat-choice-check { background: #198754; color: #fff; }
</style>

<div class="section">
    <div class="catalogue-hero">
        <h1><i class="bi bi-box-seam me-2"></i>Gestion des catalogues</h1>
        <p class="mb-0 opacity-90">Menuiserie aluminium (châssis, portes, ouvrants, coulissants…) et packs. Utilisés dans les devis et les visites.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-4" id="catalogueTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ (session('tab') ?: 'prestations') === 'prestations' ? 'active' : '' }}" id="tab-prestations" data-bs-toggle="tab" href="#prestations" role="tab">Prestations / Produits</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ session('tab') === 'packs' ? 'active' : '' }}" id="tab-packs" data-bs-toggle="tab" href="#packs" role="tab">Packs / Ensembles</a>
        </li>
    </ul>

    <div class="tab-content" id="catalogueTabContent">
        {{-- Onglet Prestations --}}
        <div class="tab-pane fade show {{ (session('tab') ?: 'prestations') === 'prestations' ? 'active' : '' }}" id="prestations" role="tabpanel">
            <div class="card card-catalogue">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0"><i class="bi bi-box-seam-fill me-2"></i>Liste des prestations et produits</h5>
                        @if(Auth::user()->hasPermission('Gestion des Catalogues', 'ajouter'))
                            <button type="button" class="btn btn-primary btn-catalogue" data-bs-toggle="modal" data-bs-target="#modalAddService">
                                <i class="bi bi-plus-circle me-1"></i>Ajouter une prestation
                            </button>
                        @endif
                    </div>
                    <div class="table-responsive table-actions-scroll-wrap">
                        <table class="table table-catalogue table-hover align-middle table-fixed-action-dropdowns">
                            <thead>
                                <tr>
                                    <th class="text-center">Visuel</th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th class="text-end">Prix (MAD)</th>
                                    <th class="text-center">Réduction</th>
                                    <th class="text-end">Prix après réduction</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center table-actions-col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $s)
                                    <tr>
                                        <td class="text-center">
                                            @if($s->image)
                                                <img src="{{ asset($s->image) }}" alt="{{ $s->nom }}" class="catalogue-thumb" loading="lazy">
                                            @else
                                                <span class="catalogue-thumb-empty"><i class="bi bi-image"></i></span>
                                            @endif
                                        </td>
                                        <td>{{ $s->nom }}</td>
                                        <td><span class="text-muted small" title="{{ $s->description }}">{{ Str::limit($s->description, 90) }}</span></td>
                                        <td class="text-end">{{ number_format($s->prix, 2, ',', ' ') }}</td>
                                        <td class="text-center">{{ $s->reduction ? $s->reduction . ' %' : '—' }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($s->prix_apres_reduction, 2, ',', ' ') }} MAD</td>
                                        <td class="text-center">
                                            @if($s->actif)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-secondary">Inactif</span>
                                            @endif
                                        </td>
                                        <td class="text-center table-actions-cell">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    @if(Auth::user()->hasPermission('Gestion des Catalogues', 'modifier'))
                                                        <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditService-{{ $s->id }}"><i class="bi bi-pencil me-2"></i>Modifier</button></li>
                                                    @endif
                                                    @if($s->actif && Auth::user()->hasPermission('Gestion des Catalogues', 'desactiver'))
                                                        <li><button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#modalDesactivateService-{{ $s->id }}"><i class="bi bi-trash me-2"></i>Désactiver</button></li>
                                                    @endif
                                                    @if(!$s->actif && Auth::user()->hasPermission('Gestion des Catalogues', 'activer'))
                                                        <li><form action="{{ route('catalogue.activate.service', $s->id) }}" method="post" class="d-inline">@csrf<button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i>Activer</button></form></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @include('user.catalogue.modals.edit-service', ['service' => $s])
                                    @if($s->actif)
                                        <div class="modal fade" id="modalDesactivateService-{{ $s->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Confirmer la désactivation</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">Désactiver « {{ $s->nom }} » ? Il ne sera plus proposé dans les devis/visites.</div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('catalogue.desactivate.service', $s->id) }}" method="post">@csrf<button type="submit" class="btn btn-danger">Désactiver</button></form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-4">Aucune prestation. Ajoutez des produits ou exécutez le seeder.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Onglet Packs --}}
        <div class="tab-pane fade {{ session('tab') === 'packs' ? 'show active' : '' }}" id="packs" role="tabpanel">
            <div class="card card-catalogue">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0"><i class="bi bi-box-seam me-2"></i>Liste des packs / ensembles</h5>
                        @if(Auth::user()->hasPermission('Gestion des Catalogues', 'ajouter'))
                            <button type="button" class="btn btn-primary btn-catalogue" data-bs-toggle="modal" data-bs-target="#modalAddPack">
                                <i class="bi bi-plus-circle me-1"></i>Ajouter un pack
                            </button>
                        @endif
                    </div>
                    <div class="table-responsive table-actions-scroll-wrap">
                        <table class="table table-catalogue table-hover align-middle table-fixed-action-dropdowns">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Contenu (prestations)</th>
                                    <th class="text-end">Prix (MAD)</th>
                                    <th class="text-center">Réduction</th>
                                    <th class="text-end">Prix après réduction</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center table-actions-col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packs as $p)
                                    <tr>
                                        <td>{{ $p->nom }}</td>
                                        <td><span class="small">{{ $p->services->pluck('nom')->join(', ') ?: '—' }}</span></td>
                                        <td class="text-end">{{ number_format($p->prix, 2, ',', ' ') }}</td>
                                        <td class="text-center">{{ $p->reduction ? $p->reduction . ' %' : '—' }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($p->prix_apres_reduction, 2, ',', ' ') }} MAD</td>
                                        <td class="text-center">
                                            @if($p->actif)<span class="badge bg-success">Actif</span>@else<span class="badge bg-secondary">Inactif</span>@endif
                                        </td>
                                        <td class="text-center table-actions-cell">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    @if(Auth::user()->hasPermission('Gestion des Catalogues', 'modifier'))
                                                        <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditPack-{{ $p->id }}"><i class="bi bi-pencil me-2"></i>Modifier</button></li>
                                                    @endif
                                                    @if($p->actif && Auth::user()->hasPermission('Gestion des Catalogues', 'desactiver'))
                                                        <li><button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#modalDesactivatePack-{{ $p->id }}"><i class="bi bi-trash me-2"></i>Désactiver</button></li>
                                                    @endif
                                                    @if(!$p->actif && Auth::user()->hasPermission('Gestion des Catalogues', 'activer'))
                                                        <li><form action="{{ route('catalogue.activate.pack', $p->id) }}" method="post">@csrf<button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i>Activer</button></form></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @include('user.catalogue.modals.edit-pack', ['pack' => $p, 'services' => $services])
                                    @if($p->actif)
                                        <div class="modal fade" id="modalDesactivatePack-{{ $p->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Confirmer la désactivation</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">Désactiver le pack « {{ $p->nom }} » ?</div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('catalogue.desactivate.pack', $p->id) }}" method="post">@csrf<button type="submit" class="btn btn-danger">Désactiver</button></form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Aucun pack. Ajoutez des ensembles ou exécutez le seeder.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.catalogue.modals.add-service')
@include('user.catalogue.modals.add-pack', ['services' => $services])

<template id="catGalerieModele">
    @foreach($galerie as $img => $titre)
        <button type="button" class="cat-picker-item" data-image="{{ $img }}" title="{{ $titre }}">
            <img src="{{ asset($img) }}" alt="{{ $titre }}" loading="lazy">
            <span>{{ $titre }}</span>
        </button>
    @endforeach
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tab = '{{ session('tab', 'prestations') }}';
    if (tab === 'packs') {
        document.getElementById('tab-packs').click();
    }
    var modeleGalerie = document.getElementById('catGalerieModele');

    document.querySelectorAll('.cat-picker').forEach(function (picker) {
        var grille = picker.querySelector('.cat-picker-grid');
        var valeur = picker.querySelector('.cat-picker-value');
        var label = picker.querySelector('.cat-picker-label');
        var fichier = picker.querySelector('.cat-picker-file');
        var apercu = picker.querySelector('.cat-picker-preview');
        var nomFichier = picker.querySelector('.cat-picker-filename');

        function selectionner(item) {
            grille.querySelectorAll('.cat-picker-item').forEach(function (i) { i.classList.toggle('is-selected', i === item); });
            valeur.value = item ? item.dataset.image : '';
            label.textContent = item && item.dataset.image ? item.title : 'Aucune image';
        }

        function remplir() {
            if (grille.dataset.rempli) { return; }
            grille.dataset.rempli = '1';
            var dejaPresentes = Array.prototype.map.call(grille.querySelectorAll('.cat-picker-item'), function (i) { return i.dataset.image; });
            modeleGalerie.content.querySelectorAll('.cat-picker-item').forEach(function (modele) {
                if (dejaPresentes.indexOf(modele.dataset.image) !== -1) { return; }
                var item = modele.cloneNode(true);
                item.classList.toggle('is-selected', item.dataset.image === valeur.value);
                grille.appendChild(item);
            });
        }

        grille.addEventListener('click', function (e) {
            var item = e.target.closest('.cat-picker-item');
            if (!item) { return; }
            fichier.value = '';
            apercu.classList.add('d-none');
            nomFichier.textContent = '';
            selectionner(item);
        });

        var modal = picker.closest('.modal');
        if (modal) {
            modal.addEventListener('show.bs.modal', remplir);
        } else {
            remplir();
        }

        fichier.addEventListener('change', function () {
            var f = fichier.files[0];
            if (!f) { return; }
            selectionner(null);
            apercu.src = URL.createObjectURL(f);
            apercu.classList.remove('d-none');
            nomFichier.textContent = f.name;
            label.textContent = 'Nouvelle image';
        });
    });

    document.querySelectorAll('.cat-services').forEach(function (bloc) {
        var compteur = bloc.querySelector('.cat-services-count');
        var cases = bloc.querySelectorAll('input[type="checkbox"]');
        function compter() {
            var n = Array.prototype.filter.call(cases, function (c) { return c.checked; }).length;
            compteur.textContent = n ? '(' + n + ' sélectionnée' + (n > 1 ? 's' : '') + ')' : '';
        }
        cases.forEach(function (c) { c.addEventListener('change', compter); });
        compter();
        bloc.querySelector('.cat-services-search').addEventListener('input', function () {
            var q = this.value.trim().toLowerCase();
            bloc.querySelectorAll('.cat-choice').forEach(function (choix) {
                choix.classList.toggle('d-none', q !== '' && choix.dataset.nom.indexOf(q) === -1);
            });
        });
    });

    @if($errors->any() && session('tab') === 'prestations' && ! old('_method'))
        new bootstrap.Modal(document.getElementById('modalAddService')).show();
    @endif
});
</script>
@endpush
