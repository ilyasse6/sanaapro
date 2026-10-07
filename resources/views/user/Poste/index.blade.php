@extends('user.master')

@section('content')
    <div class="section">
        <div class="row g-4">
            <!-- Formulaire de recherche -->
            <div class="col-xl-12 col-md-12 col-sm-12">
                <div class="fade-in-left" id="form-container-exception">
                    <div class="card exceptions-card">
                        <div class="card-body" id="filters">
                            <h6 class="card-title d-flex align-items-center">
                                <i class="ri-search-line me-2"></i>
                                <span>Recherche des postes</span>
                            </h6>
                            <div class="row g-3">
                                <!-- Intitulé -->
                                <div class="col-lg-6 col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchIntitule" class="form-label">
                                            <i class="bi bi-card-heading me-1"></i>Intitulé :
                                        </label>
                                        <input type="text" class="form-control custom-input" id="searchIntitule"
                                            placeholder="Rechercher par intitulé">
                                    </div>
                                </div>

                                <!-- Statut -->
                                <div class="col-lg-6 col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchStatut" class="form-label">
                                            <i class="bi bi-info-circle-fill me-1"></i>Statut :
                                        </label>
                                        <select class="form-select custom-input" id="searchStatut">
                                            <option value="">Tous les statuts</option>
                                            <option value="Actif">Actif</option>
                                            <option value="Inactif">Inactif</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Date de création -->
                                <div class="col-lg-6 col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchDateDebut" class="form-label">
                                            <i class="bi bi-calendar-date-fill me-1"></i>Date de début :
                                        </label>
                                        <input type="date" class="form-control custom-input" id="searchDateDebut">
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchDateFin" class="form-label">
                                            <i class="bi bi-calendar-date-fill me-1"></i>Date de fin :
                                        </label>
                                        <input type="date" class="form-control custom-input" id="searchDateFin">
                                    </div>
                                </div>

                                <!-- Boutons -->
                                <div class="col-md-12 d-flex justify-content-end mt-3">
                                    <button class="btn btn-dark me-2" id="clearFiltersBtn">
                                        <i class="bi bi-bootstrap-reboot me-1"></i>Réinitialiser
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-dark" id="toggleFormBtn">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                    @if(Auth::user()->hasPermission('Gestion des Postes','ajouter'))
                        <a data-bs-toggle="modal" data-bs-target="#AddModal" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Ajouter un poste
                        </a>
                    @endif
                </div>
            </div>

            <!-- Modal pour ajouter un poste -->
            @include('user.Poste.create')
            @foreach ($postes as $poste)
                <!-- Modal pour afficher les détails -->
                @include('user.Poste.show')
                <!-- Modal pour modifier -->
                @include('user.Poste.edit')
                <!-- Modal de confirmation de suppression -->
                @if ($poste->actif == 1)
                    <div class="modal fade" id="desactivateModal-{{ $poste->id }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Confirmer la suppression
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment supprimer le poste "{{ $poste->intitule }}" ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="ri-close-circle-fill me-1"></i>Annuler
                                    </button>
                                    <form action="{{ route('postes.desactivate', $poste->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash me-1"></i>Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="modal fade" id="activateModal-{{ $poste->id }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Confirmer l'activation
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Êtes-vous sûr de vouloir activer le poste "{{ $poste->intitule }}" ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="ri-close-circle-fill me-1"></i>Annuler
                                    </button>
                                    <form action="{{ route('postes.activate', $poste->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="ri-shield-check-fill me-1"></i>Activer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Liste des postes -->
            <div class="col-lg-12">
                <div class="fade-in-left">
                    <div class="card poste-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-title d-flex align-items-center mb-1">
                                    <i class="ri-list-check me-1"></i>
                                    <span>La liste des postes</span>
                                </h6>
                            </div>
                            <div class="table-container">
                                <table id="postesTableList" class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Intitulé</th>
                                            <th>Description</th>
                                            <th>Statut</th>
                                            <th>Date d'ajout</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($postes as $poste)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ Str::limit($poste->intitule, 30) }}</td>
                                                <td>{{ Str::limit($poste->description, 50) }}</td>
                                                <td>
                                                    @if ($poste->actif == 1)
                                                        <span class="badge bg-success">Actif</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactif</span>
                                                    @endif
                                                </td>
                                                <td>{{ $poste->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    @if ($poste->actif == 1)
                                                        <div class="dropdown dropstart">
                                                            <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                                type="button" id="actionDropdown{{ $poste->id }}"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i> Action
                                                            </button>
                                                            <ul class="dropdown-menu"
                                                                aria-labelledby="actionDropdown{{ $poste->id }}">

                                                                <!-- Voir détails -->
                                                                <li>
                                                                    <button class="dropdown-item" data-bs-toggle="modal"
                                                                        data-bs-target="#DetailModal-{{ $poste->id }}"
                                                                        title="Voir les détails">
                                                                        <i class="bi bi-eye text-info"></i> Détails
                                                                    </button>
                                                                </li>
                                                                <!-- Modifier -->
                                                                @if(Auth::user()->hasPermission('Gestion des Postes','modifier'))
                                                                    <li>
                                                                        <button class="dropdown-item" data-bs-toggle="modal"
                                                                            data-bs-target="#EditModal-{{ $poste->id }}"
                                                                            title="Modifier">
                                                                            <i class="bi bi-pencil text-warning"></i>
                                                                            Modifier
                                                                        </button>
                                                                    </li>
                                                                @endif
                                                                <!-- Désactiver -->
                                                                @if(Auth::user()->hasPermission('Gestion des Postes','desactiver'))
                                                                    <li>
                                                                        <button class="dropdown-item" data-bs-toggle="modal"
                                                                            data-bs-target="#desactivateModal-{{ $poste->id }}"
                                                                            title="Supprimer">
                                                                            <i class="bi bi-trash text-danger"></i>
                                                                            Supprimer
                                                                        </button>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    @else
                                                        <!-- Activer -->
                                                        @if(Auth::user()->hasPermission('Gestion des Postes','activer'))
                                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                                data-bs-target="#activateModal-{{ $poste->id }}"
                                                                title="Activer">
                                                                <i class="bi bi-check-circle text-white"></i>
                                                                Activer
                                                            </button>
                                                        @else
                                                            <span class="badge text-bg-secondary">Non autorisé</span>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/poste/filtrage.js') }}"></script>
    <script src="{{ asset('assets/js/poste/formValidation.js') }}"></script>
@endsection
