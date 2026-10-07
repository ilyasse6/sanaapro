@extends('user.master')

@section('content')
<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-client: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --card-shadow: 0 10px 40px rgba(0,0,0,0.1);
        --card-hover-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }

    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }

    .modern-card:hover {
        box-shadow: var(--card-hover-shadow);
        transform: translateY(-2px);
    }

    .modern-input {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.5rem 0.75rem;
        height: 38px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .modern-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    select.modern-input {
        height: 38px;
        padding: 0.5rem 0.75rem;
    }

    .btn-modern {
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-modern-primary {
        background: var(--gradient-primary);
        color: white;
    }

    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .table-modern {
        border-radius: 12px;
        overflow: hidden;
    }

    .table-modern thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table-modern tbody tr {
        transition: all 0.2s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #f8f9ff;
        transform: scale(1.01);
    }

    .badge-modern {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .modal-modern .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .modal-modern .modal-header {
        background: var(--gradient-client);
        color: white;
        border-radius: 20px 20px 0 0;
        border: none;
        padding: 1.5rem;
    }

    .modal-modern .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    .modal-modern .modal-body {
        padding: 2rem;
    }

    .modal-modern .modal-footer {
        border-top: 1px solid #e2e8f0;
        padding: 1.5rem 2rem;
        border-radius: 0 0 20px 20px;
    }

    .action-btn {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: scale(1.05);
    }

    .modal-modern .modal-header.bg-success {
        border-radius: 20px 20px 0 0;
    }

    /* Espacement entre recherche et tableau */
    .search-table-spacing {
        margin-bottom: 2.5rem;
    }

    .table-container {
        position: relative;
        z-index: 0;
        overflow: visible !important;
    }
</style>

    <div class="section">
        <div class="row g-4">
            <div class="col-xl-12 col-md-12 col-sm-12">
                <div class="fade-in-left" id="form-container-client" style="display: none;">
                    <div class="modern-card">
                        <div class="card-body" id="filters">
                            <h6 class="card-title d-flex align-items-center mb-4">
                                <i class="ri-search-line me-2"></i>
                                <span>Recherche des clients supprimés</span>
                            </h6>
                            <div class="row g-3">
                                <!-- Raison sociale -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchNom" class="form-label">
                                            <i class="bi bi-building me-1"></i>Raison sociale :
                                        </label>
                                        <input type="text" class="form-control modern-input" id="searchNom"
                                            placeholder="Nom entreprise ou client">
                                    </div>
                                </div>

                                <!-- Contact -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchPrenom" class="form-label">
                                            <i class="bi bi-person-fill me-1"></i>Contact :
                                        </label>
                                        <input type="text" class="form-control modern-input" id="searchPrenom"
                                            placeholder="Personne à contacter">
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchEmail" class="form-label">
                                            <i class="bi bi-envelope me-1"></i>Email :
                                        </label>
                                        <input type="text" class="form-control modern-input" id="searchEmail"
                                            placeholder="Email">
                                    </div>
                                </div>

                                <!-- Téléphone -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="searchTelephone" class="form-label">
                                            <i class="bi bi-telephone me-1"></i>Téléphone :
                                        </label>
                                        <input type="text" class="form-control modern-input" id="searchTelephone"
                                            placeholder="Téléphone">
                                    </div>
                                </div>

                                <!-- Boutons -->
                                <div class="col-md-12 d-flex justify-content-end mt-3">
                                    <button class="btn btn-modern btn-secondary" id="clearFiltersBtn">
                                        <i class="bi bi-bootstrap-reboot me-1"></i>Réinitialiser
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between search-table-spacing">
                    <button class="btn btn-modern btn-secondary" id="toggleFormBtn">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                </div>
            </div>

            @foreach ($clients as $client)
                <!-- Modal pour afficher les détails -->
                @include('user.client.details-client')
                <!-- Modal pour modifier -->
                @include('user.client.modifier-client')
                <!-- Modal de confirmation d'activation -->
                <div class="modal fade modal-modern" id="activateModal-{{ $client->id }}" tabindex="-1"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    Confirmer l'activation
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center mb-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                                </div>
                                <p class="text-center">
                                    Êtes-vous sûr de vouloir activer le client <strong>"{{ $client->nom }}"</strong> ?
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">
                                    <i class="ri-close-circle-fill me-1"></i>Annuler
                                </button>
                                <button type="button" class="btn btn-success btn-modern" onclick="activateClient({{ $client->id }})">
                                    <i class="ri-shield-check-fill me-1"></i>Activer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Liste des clients supprimés -->
            <div class="col-lg-12">
                <div class="fade-in-left">
                    <div class="modern-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-4">
                                <h5 class="card-title d-flex align-items-center mb-0">
                                    <i class="ri-list-check me-2"></i>
                                    <span>Liste des clients supprimés</span>
                                </h5>
                            </div>
                            <div class="table-container" style="overflow: visible !important;">
                                <table id="clientsTableList" class="table table-modern table-hover table-fixed-action-dropdowns" style="overflow: visible !important;">
                                    <thead>
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Raison sociale</th>
                                            <th>Contact</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Ville</th>
                                            <th>Secteur</th>
                                            <th>Statut</th>
                                            <th>Date de suppression</th>
                                            <th class="text-center table-actions-col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($clients as $client)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $client->nom }}</td>
                                                <td>{{ $client->prenom }}</td>
                                                <td>{{ $client->email ?? '-' }}</td>
                                                <td>{{ $client->telephone ?? '-' }}</td>
                                                <td>{{ $client->ville ?? '-' }}</td>
                                                <td>{{ $client->secteur_activite ?? '-' }}</td>
                                                <td>
                                                    <span class="badge badge-modern bg-danger">Inactif</span>
                                                </td>
                                                <td>{{ $client->updated_at->format('d/m/Y H:i') }}</td>
                                                <td class="text-center table-actions-cell">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle action-btn"
                                                            type="button" id="actionDropdown{{ $client->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i> Action
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                                                            aria-labelledby="actionDropdown{{ $client->id }}">
                                                            <!-- Voir détails -->
                                                            <li>
                                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                    data-bs-target="#DetailModal-{{ $client->id }}"
                                                                    title="Voir les détails">
                                                                    <i class="bi bi-eye text-info me-2"></i> Détails
                                                                </a>
                                                            </li>
                                                            <!-- Modifier -->
                                                            @if(Auth::user()->hasPermission('Gestion des Clients','modifier'))
                                                                <li>
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                        data-bs-target="#EditModal-{{ $client->id }}"
                                                                        title="Modifier">
                                                                        <i class="bi bi-pencil text-warning me-2"></i>
                                                                        Modifier
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            <!-- Activer -->
                                                            @if(Auth::user()->hasPermission('Gestion des Clients','activer'))
                                                                <li>
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                        data-bs-target="#activateModal-{{ $client->id }}"
                                                                        title="Activer">
                                                                        <i class="bi bi-check-circle text-success me-2"></i>
                                                                        Activer
                                                                    </a>
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
    <script src="{{ asset('assets/js/Client/clientAjax.js') }}"></script>
    <script src="{{ asset('assets/js/Client/filter.js') }}"></script>
@endsection

