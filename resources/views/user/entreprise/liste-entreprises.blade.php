@extends('user.master')

@section('content')
    <div class="section">
        <div class="row g-4">
            <div class="col-xl-12 col-md-12 col-sm-12">
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-2">
                    @if (Auth::user()?->isSuperAdmin())
                    <a data-bs-toggle="modal" data-bs-target="#AddEntrepriseModal" class="btn btn-primary flex-grow-1 flex-md-grow-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Ajouter une entreprise
                    </a>
                    @endif
                </div>
            </div>
            <!-- Modal pour ajouter-->
            @if (Auth::user()?->isSuperAdmin())
            @include('user.entreprise.ajouter-entreprise')
            @endif
            @foreach ($entreprises as $entreprise)
                <!-- Modal de mise à jour -->
                @include('user.entreprise.modifier-entreprise')
                <!-- Modal de détail -->
                @include('user.entreprise.details-entreprise')
                <!-- Modal de confirmation de suppression -->
                @if ($entreprise->actif == 1)
                    <div class="modal fade" id="desactivateModal-{{ $entreprise->id }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Confirmer la désactivation
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment désactiver l'entreprise {{ $entreprise->abbreviation }} ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="ri-close-circle-fill me-1"></i>Annuler
                                    </button>
                                    <form action="{{ route('entreprises.desactivate', $entreprise->id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash me-1"></i>Désactiver
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="modal fade" id="activateModal-{{ $entreprise->id }}" tabindex="-1"
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
                                    Êtes-vous sûr de vouloir activer cette entreprise {{ $entreprise->abbreviation }} ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="ri-close-circle-fill me-1"></i>Annuler
                                    </button>
                                    <form action="{{ route('entreprises.activate', $entreprise->id) }}"
                                        method="POST">
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

            <!-- Liste des entreprises -->
            <div class="col-lg-12">
                <div class="fade-in-left">
                    <div class="card hopital-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-title d-flex align-items-center mb-1">
                                    <i class="ri-list-check me-1"></i>
                                    <span>La liste des entreprises</span>
                                </h6>
                            </div>
                            <div class="table-container">
                                <table id="hopitalsTableList" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Abréviation</th>
                                            <th>Nom</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($entreprises as $entreprise)
                                            <tr class="text-center"
                                                data-status="{{ $entreprise->actif ? 'actif' : 'inactif' }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $entreprise->abbreviation }}</td>
                                                <td>{{ Str::limit($entreprise->description, 50) }}</td>
                                                <td>
                                                    @php
                                                        $typeBadge = match ($entreprise->type) {
                                                            'AUTO_ENTREPRENEUR' => 'bg-success',
                                                            'SA', 'SAS' => 'bg-dark',
                                                            'SNC', 'SCS' => 'bg-warning text-dark',
                                                            'GIE', 'COOPERATIVE', 'ASSOCIATION' => 'bg-info text-dark',
                                                            default => 'bg-primary',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $typeBadge }}">
                                                        {{ $entreprise->type_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="dropdown dropstart">
                                                        <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                            type="button" id="actionDropdown{{ $entreprise->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i> Action
                                                        </button>
                                                        <ul class="dropdown-menu"
                                                            aria-labelledby="actionDropdown{{ $entreprise->id }}">
                                                            <!-- Voir détails -->
                                                            <li>
                                                                <button class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#DetailModal-{{ $entreprise->id }}"
                                                                    title="Voir les détails">
                                                                    <i class="bi bi-eye-fill text-info"></i>
                                                                    Détails
                                                                </button>
                                                            </li>

                                                            @if ($entreprise->actif == 1)
                                                                <!-- Modifier -->
                                                                <li>
                                                                    <button class="dropdown-item" data-bs-toggle="modal"
                                                                        data-bs-target="#EditModal-{{ $entreprise->id }}"
                                                                        title="Modifier">
                                                                        <i class="bi bi-pencil text-warning"></i>
                                                                        Modifier
                                                                    </button>
                                                                </li>

                                                                <!-- Désactiver -->
                                                                <li>
                                                                    <button class="dropdown-item" data-bs-toggle="modal"
                                                                        data-bs-target="#desactivateModal-{{ $entreprise->id }}"
                                                                        title="Désactiver">
                                                                        <i class="bi bi-trash text-danger"></i>
                                                                        Désactiver
                                                                    </button>
                                                                </li>
                                                            @else
                                                                <!-- Activer -->
                                                                <li>
                                                                    <button class="dropdown-item" data-bs-toggle="modal"
                                                                        data-bs-target="#activateModal-{{ $entreprise->id }}"
                                                                        title="Activer">
                                                                        <i class="ri-shield-check-fill text-success"></i>
                                                                        Activer
                                                                    </button>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>

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
        document.addEventListener("DOMContentLoaded", () => {
            // Initialisation de DataTable
            const table = $("#hopitalsTableList").DataTable({
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
    <!--script pour validation-->
    <script src="{{ asset('assets/js/Entreprise/formValidation.js') }}"></script>
@endsection
