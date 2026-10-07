@extends('user.master')

@section('content')
    <div class="section">
        <div class="row g-4">
            <div class="col-xl-12 col-md-12 col-sm-12">
                <div class="fade-in-left" id="form-container-utilisateur">
                    <div class="card user-card">
                        <div class="card-body" id="filters">
                            <h6 class="card-title d-flex align-items-center">
                                <i class="ri-search-line me-2"></i>
                                <span>Recherche des utilisateurs</span>
                            </h6>
                            <div class="row g-3">
                                <!-- Nom -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchNom" class="form-label">
                                            <i class="bi bi-person-fill me-1"></i>Nom :
                                        </label>
                                        <input type="text" class="form-control custom-input" id="searchNom"
                                            placeholder="Saisissez le nom">
                                    </div>
                                </div>

                                <!-- Prénom -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchPrenom" class="form-label">
                                            <i class="bi bi-person-fill me-1"></i>Prénom :
                                        </label>
                                        <input type="text" class="form-control custom-input" id="searchPrenom"
                                            placeholder="Saisissez le prénom">
                                    </div>
                                </div>
                                <!-- Etablissement -->
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchEtablissement" class="form-label">
                                            <i class="bi bi-building-fill me-1"></i>Entreprise :
                                        </label>
                                        <select class="form-select custom-input" id="searchEtablissement">
                                            <option value="">Toutes les entreprises</option>
                                            @foreach ($entreprises as $entreprise)
                                                <option value="{{ $entreprise->abbreviation }}">
                                                    {{ $entreprise->abbreviation }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Poste -->
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchPoste" class="form-label">
                                            <i class="bi bi-briefcase-fill me-1"></i>Poste :
                                        </label>
                                        <select class="form-select custom-input" id="searchPoste">
                                            <option value="">Tous les postes</option>
                                            @foreach ($postes as $poste)
                                                <option value="{{ $poste->intitule }}">{{ $poste->intitule }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Profile -->
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchProfile" class="form-label">
                                            <i class="bi bi-person-badge-fill me-1"></i>Profile :
                                        </label>
                                        <select class="form-select custom-input" id="searchProfile">
                                            <option value="">Tous les profiles</option>
                                            @foreach ($profiles as $profile)
                                                <option value="{{ $profile->nom }}">{{ $profile->nom }}
                                                </option>
                                            @endforeach
                                        </select>
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
                    @if(Auth::user()->hasPermission('Gestion des Utilisateurs','ajouter'))
                        <a data-bs-toggle="modal" data-bs-target="#AddModal" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Ajouter un utilisateur
                        </a>
                    @endif
                </div>
            </div>
            <!-- Modal pour ajouter-->
            @include('user.utilisateur.create')
            @foreach ($utilisateurs as $utilisateur)
                <!-- Modal pour afficher les détails -->
                @include('user.utilisateur.show')
                <!-- Modal pour modifier -->
                @include('user.utilisateur.edit')
                <!-- Modal de confirmation de suppression -->
                @if ($utilisateur->actif == 1)
                    <div class="modal fade" id="desactivateModal-{{ $utilisateur->id }}" tabindex="-1"
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
                                    Voulez-vous vraiment désactiver {{ $utilisateur->nom }} {{ $utilisateur->prenom }} ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="ri-close-circle-fill me-1"></i>Annuler
                                    </button>
                                    <form action="{{ route('utilisateurs.desactivate', $utilisateur->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash me-1"></i>Désactiver
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="modal fade" id="activateModal-{{ $utilisateur->id }}" tabindex="-1"
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
                                    Êtes-vous sûr de vouloir activer cet utilisateur {{ $utilisateur->nom }}
                                    {{ $utilisateur->prenom }} ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="ri-close-circle-fill me-1"></i>Annuler
                                    </button>
                                    <form action="{{ route('utilisateurs.activate', $utilisateur->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
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

            <!-- Liste des utilisateurs -->
            <div class="col-lg-12">
                <div class="fade-in-left">
                    <div class="card user-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-title d-flex align-items-center mb-1">
                                    <i class="ri-list-check me-1"></i>
                                    <span>La liste des utilisateurs</span>
                                </h6>
                            </div>
                            <div class="table-container">
                                <table id="utilisateursTableList" class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Email</th>
                                            <th>Entreprise</th>
                                            <th>Poste</th>
                                            <th>Profile</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($utilisateurs as $utilisateur)
                                            <tr class="text-center" data-nom="{{ $utilisateur->nom }}"
                                                data-prenom="{{ $utilisateur->prenom }}"
                                                data-etablissement="{{ $utilisateur->entreprise->abbreviation ?? 'N/A' }}"
                                                data-poste="{{ $utilisateur->poste_occupe->intitule ?? 'N/A' }}"
                                                data-profile="{{ $utilisateur->profile->nom ?? 'N/A' }}">
                                                <td>{{ Str::limit($utilisateur->nom, 20) }}</td>
                                                <td>{{ Str::limit($utilisateur->prenom, 20) }}</td>
                                                <td>{{ Str::limit($utilisateur->email, 30) }}</td>
                                                <td>{{ Str::limit($utilisateur->entreprise->abbreviation ?? 'N/A', 10) }}
                                                </td>
                                                <td>{{ Str::limit($utilisateur->poste_occupe->intitule ?? 'N/A', 20) }}
                                                </td>
                                                <td>{{ Str::limit($utilisateur->profiles->first()->nom ?? 'N/A', 20) }}</td>
                                                <td>
                                                    @if ($utilisateur->actif == 1)
                                                        <div class="dropdown dropstart">
                                                            <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                                type="button" id="actionDropdown{{ $utilisateur->id }}"
                                                                data-bs-toggle="dropdown" aria-expanded="true">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu"
                                                                aria-labelledby="actionDropdown{{ $utilisateur->id }}">

                                                                <li>
                                                                    <button class="dropdown-item" data-bs-toggle="modal"
                                                                        data-bs-target="#ShowModal-{{ $utilisateur->id }}"
                                                                        title="Voir les détails">
                                                                        <i class="bi bi-eye text-info"></i> Voir
                                                                    </button>
                                                                </li>
                                                                @if(Auth::user()->hasPermission('Gestion des Utilisateurs','modifier'))
                                                                    <li>
                                                                        <button class="dropdown-item" data-bs-toggle="modal"
                                                                            data-bs-target="#EditModal-{{ $utilisateur->id }}"
                                                                            title="Modifier">
                                                                            <i class="bi bi-pencil text-warning"></i> Modifier
                                                                        </button>
                                                                    </li>
                                                                @endif
                                                                @if(Auth::user()->hasPermission('Gestion des Utilisateurs','desactiver'))
                                                                    <li>
                                                                        <button class="dropdown-item" data-bs-toggle="modal"
                                                                            data-bs-target="#desactivateModal-{{ $utilisateur->id }}"
                                                                            title="Désactiver">
                                                                            <i class="bi bi-trash text-danger"></i> Désactiver
                                                                        </button>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    @else
                                                        @if(Auth::user()->hasPermission('Gestion des Utilisateurs','activer'))
                                                            <button class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                                                data-bs-target="#activateModal-{{ $utilisateur->id }}"
                                                                title="Activer l'utilisateur">
                                                                <i class="bi bi-shield-check text-success"></i>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = $("#utilisateursTableList").DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    search: "Rechercher :",
                    lengthMenu: "Afficher _MENU_ entrées",
                    info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                    infoEmpty: "Aucune entrée trouvée",
                    infoFiltered: "(filtré de _MAX_ entrées au total)",
                    zeroRecords: "Aucun résultat trouvé",
                    paginate: {
                        first: "Premier",
                        last: "Dernier",
                        next: "Suivant",
                        previous: "Précédent"
                    }
                }
            });
        });
    </script>

    
    <script src="{{ asset('assets/js/utilisateur/filtrage.js') }}"></script>
@endsection
