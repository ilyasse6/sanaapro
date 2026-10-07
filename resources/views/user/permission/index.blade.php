@extends('user.master')

@section('content')
    <div class="section">
        <div class="row g-4">
            <div class="col-xl-12 col-md-12 col-sm-12">
                <div class="d-flex justify-content-between">
                        <a data-bs-toggle="modal" data-bs-target="#AddModal" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Ajouter une permission
                        </a>
                </div>
            </div>

            <!-- Modal pour ajouter une permission -->
            @include('user.permission.create')
            @foreach ($permissions as $permission)
                <!-- Modal pour afficher les détails -->
                @include('user.permission.show')
                <!-- Modal pour modifier -->
                @include('user.permission.edit')
                <!-- Modal de confirmation de suppression -->
                @if ($permission->actif == 1)
                    <div class="modal fade" id="desactivateModal-{{ $permission->id }}" tabindex="-1"
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
                                    Voulez-vous vraiment supprimer la permission "{{ $permission->nom }}" ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="ri-close-circle-fill me-1"></i>Annuler
                                    </button>
                                    <form action="{{ route('permissions.desactivate', $permission->id) }}" method="POST">
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
                    <div class="modal fade" id="activateModal-{{ $permission->id }}" tabindex="-1"
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
                                    Êtes-vous sûr de vouloir activer la permission "{{ $permission->nom }}" ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="ri-close-circle-fill me-1"></i>Annuler
                                    </button>
                                    <form action="{{ route('permissions.activate', $permission->id) }}" method="POST">
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

            <!-- Liste des permissions -->
            <div class="col-lg-12">
                <div class="fade-in-left">
                    <div class="card permission-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-title d-flex align-items-center mb-1">
                                    <i class="ri-list-check me-1"></i>
                                    <span>La liste des permissions</span>
                                </h6>
                            </div>
                            <div class="table-container">
                                <table id="permissionsTableList" class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Nom</th>
                                            <th>Action</th>
                                            <th>Module</th>
                                            <th>Statut</th>
                                            <th>Date d'ajout</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permissions as $permission)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $permission->nom }}</td>
                                                <td>{{ $permission->action ?? '-' }}</td>
                                                <td>{{ $permission->module->nom }}</td>
                                                <td>
                                                    @if($permission->actif)
                                                        <span class="badge bg-success">Actif</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactif</span>
                                                    @endif
                                                </td>
                                                <td>{{ $permission->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    @if ($permission->actif == 1)
                                                        <div class="dropdown dropstart">
                                                            <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                                type="button" id="actionDropdown{{ $permission->id }}"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i> Action
                                                            </button>
                                                            <ul class="dropdown-menu"
                                                                aria-labelledby="actionDropdown{{ $permission->id }}">

                                                                <!-- Voir détails -->
                                                                    <li>
                                                                        <button class="dropdown-item"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#DetailModal-{{ $permission->id }}"
                                                                            title="Voir les détails">
                                                                            <i class="bi bi-eye text-info"></i> Détails
                                                                        </button>
                                                                    </li>

                                                                <!-- Modifier -->
                                                                    <li>
                                                                        <button class="dropdown-item"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#EditModal-{{ $permission->id }}"
                                                                            title="Modifier">
                                                                            <i class="bi bi-pencil text-warning"></i>
                                                                            Modifier
                                                                        </button>
                                                                    </li>

                                                                <!-- Désactiver -->
                                                                    <li>
                                                                        <button class="dropdown-item"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#desactivateModal-{{ $permission->id }}"
                                                                            title="Supprimer">
                                                                            <i class="bi bi-trash text-danger"></i>
                                                                            Supprimer
                                                                        </button>
                                                                    </li>

                                                            </ul>
                                                        </div>
                                                    @else
                                                        <!-- Activer -->
                                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                                data-bs-target="#activateModal-{{ $permission->id }}"
                                                                title="Activer">
                                                                <i class="bi bi-check-circle text-white"></i>
                                                                Activer
                                                            </button>
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
            const table = $("#permissionsTableList").DataTable({
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
    <script src="{{asset('assets/js/Permission/formValidation.js')}}"></script>
@endsection
