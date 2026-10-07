<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Prowise — Gestion d'équipements</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <!-- Favicons -->
    <link href="{{ asset('assets/imgs/logo.png') }}" rel="icon" type="image/png">
    <link href="{{ asset('assets/imgs/logo.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    {{-- <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet"> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> --}}
    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/code.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/code.css') }}" rel="stylesheet"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <style>
        * {
            font-family: "Poppins", sans-serif;
            font-weight: 400;
        }
    </style>
    <style>
        .profile-dropdown {
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
        }

        .profile-item {
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .profile-item:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .profile-item.all-profiles {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .profile-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .profile-info {
            flex-grow: 1;
        }

        /* Style spécifique pour les checkboxes de profil uniquement */
        .profile-dropdown .profile-item .form-check-input {
            position: absolute;
            opacity: 0;
        }

        .profile-dropdown .profile-item .form-check-indicator {
            width: 18px;
            height: 18px;
            border: 2px solid #dee2e6;
            border-radius: 4px;
            position: relative;
        }

        .profile-dropdown .profile-item .form-check-input:checked ~ .form-check-label .form-check-indicator {
            background-color: #198754;
            border-color: #198754;
        }

        .profile-dropdown .profile-item .form-check-input:checked ~ .form-check-label .form-check-indicator::after {
            content: "";
            position: absolute;
            left: 4px;
            top: 1px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .disabled-profiles {
            opacity: 0.6;
            pointer-events: none;
        }

        .dropdown-menu-arrow::before {
            content: "";
            position: absolute;
            top: -10px;
            right: 15px;
            border-width: 0 10px 10px;
            border-style: solid;
            border-color: transparent transparent white;
            filter: drop-shadow(0 -2px 1px rgba(0, 0, 0, 0.05));
        }

    </style>
    <link href="{{ asset('css/sanaapro-theme.css') }}?v={{ @filemtime(public_path('css/sanaapro-theme.css')) ?: 1 }}" rel="stylesheet">
</head>

<body>
    <!-- Loader overlay -->
    <div id="loader-wrapper">
        <div style="text-align: center;">
            <div class="hourglassBackground">
                <div class="hourglassContainer">
                    <div class="hourglassCurves"></div>
                    <div class="hourglassCapTop"></div>
                    <div class="hourglassGlassTop"></div>
                    <div class="hourglassSand"></div>
                    <div class="hourglassSandStream"></div>
                    <div class="hourglassCapBottom"></div>
                    <div class="hourglassGlass"></div>
                </div>
            </div>
            <div class="loader-text">Gestion des dossiers</div>
        </div>
    </div>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <i class="bi bi-list toggle-sidebar-btn pe-3"></i>
        </div>



        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="badge bg-primary badge-number" id="notification-count">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <li class="dropdown-header">
                            Vous avez <span id="notification-header-count">0</span> nouvelles notifications
                            <a href="{{ route('notifications.index') }}"><span class="badge rounded-pill bg-primary p-2 ms-2">Voir tout</span></a>
                        </li>
                        <div id="notification-items-container">
                            {{-- Le contenu sera injecté par JavaScript --}}
                        </div>
                    </ul>
                </li><!-- End Notification Nav -->

                <li class="nav-item dropdown">

                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-chat-left-text"></i>
                        <span class="badge bg-success badge-number">3</span>
                    </a><!-- End Messages Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                        <li class="dropdown-header">
                            You have 3 new messages
                            <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li class="message-item">
                            <a href="#">
                                <img src="assets/img/messages-1.jpg" alt="" class="rounded-circle">
                                <div>
                                    <h4>Maria Hudson</h4>
                                    <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                    <p>4 hrs. ago</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li class="message-item">
                            <a href="#">
                                <img src="assets/img/messages-2.jpg" alt="" class="rounded-circle">
                                <div>
                                    <h4>Anna Nelson</h4>
                                    <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                    <p>6 hrs. ago</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li class="message-item">
                            <a href="#">
                                <img src="assets/img/messages-3.jpg" alt="" class="rounded-circle">
                                <div>
                                    <h4>David Muldon</h4>
                                    <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                    <p>8 hrs. ago</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li class="dropdown-footer">
                            <a href="#">Show all messages</a>
                        </li>

                    </ul><!-- End Messages Dropdown Items -->

                </li><!-- End Messages Nav -->

                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon position-relative" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-people text-primary"></i>
                        <span class="badge bg-success badge-number">{{ Auth::user()->profiles->where('actif', 1)->count() }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile-dropdown p-3"
                        style="min-width: 320px;">
                        <li class="mb-2">
                            <h6 class="dropdown-header px-0 d-flex align-items-center">
                                <i class="bi bi-sliders me-2"></i>
                                <span>Sélectionner les profils actifs</span>
                            </h6>
                        </li>

                        <form method="POST" action="{{ route('profil.switch') }}" id="profileForm">
                            @csrf
                            @method('PATCH')

                            <li class="mb-2">
                                <div class="form-check profile-item all-profiles" onclick="event.stopPropagation();">
                                    <input class="form-check-input" type="checkbox" id="all_profiles"
                                        name="all_profiles" value="1"
                                        {{ Auth::user()->hasAllProfilesActive() ? 'checked' : '' }}
                                        onchange="toggleAllProfiles(this)">
                                    <label class="form-check-label d-flex align-items-center w-100"
                                        for="all_profiles">
                                        <div class="profile-icon bg-primary bg-opacity-10">
                                            <i class="bi bi-collection text-primary"></i>
                                        </div>
                                        <div class="profile-info ms-3">
                                            <span class="fw-bold">Tous les profils</span>
                                        </div>
                                        <div class="form-check-indicator ms-auto"></div>
                                    </label>
                                </div>
                            </li>

                            <li>
                                <hr class="dropdown-divider my-2">
                            </li>

                            <div id="individual_profiles"
                                class="{{ Auth::user()->hasAllProfilesActive() ? 'disabled-profiles' : '' }}">
                                @foreach (Auth::user()->profiles->where('actif', 1) as $profile)
                                    <li class="mb-2">
                                        <div class="form-check profile-item" onclick="event.stopPropagation();">
                                            <input class="form-check-input profile-checkbox" type="checkbox"
                                                id="profile_{{ $profile->id }}" name="selected_profiles[]"
                                                value="{{ $profile->id }}"
                                                {{ Auth::user()->hasActiveProfile($profile->id) && !Auth::user()->hasAllProfilesActive() ? 'checked' : '' }}>
                                            <label class="form-check-label d-flex align-items-center w-100"
                                                for="profile_{{ $profile->id }}">
                                                <div class="profile-icon bg-secondary bg-opacity-10">
                                                    <i class="bi bi-person-circle text-secondary"></i>
                                                </div>
                                                <div class="profile-info ms-3">
                                                    <span class="fw-bold">{{ $profile->nom }}</span>
                                                    {{-- @if ($profile->description)
                                                    <small class="text-muted d-block">{{ Str::limit($profile->description, 30) }}</small>
                                                @endif --}}
                                                </div>
                                                <div class="form-check-indicator ms-auto"></div>
                                            </label>
                                        </div>
                                    </li>
                                @endforeach
                            </div>

                            <li>
                                <hr class="dropdown-divider my-2">
                            </li>

                            <li class="text-center pt-1">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Appliquer les changements
                                </button>
                            </li>
                        </form>
                    </ul>
                </li>
                <li class="nav-item dropdown pe-5">

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                        data-bs-toggle="dropdown">
                        <span class="d-none d-md-block dropdown-toggle ps-2">
                            {{ ucfirst(substr(Auth::user()->prenom, 0, 1)) }}.{{ ucfirst(Auth::user()->nom) }}
                        </span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{ ucfirst(Auth::user()->prenom) }} {{ ucfirst(Auth::user()->nom) }}</h6>
                            <span>{{ ucfirst(Auth::user()->poste_occupe->intitule) }}</span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person"></i>
                                <span>Mon Profile</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                                <i class="bi bi-gear"></i>
                                <span>Parametre</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Déconnexion</span>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </a>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <div class="sp-brand">
            @php
                $menuLogo = \App\Support\SocieteBranding::all(\App\Models\AppSetting::resolvedEntrepriseId())['logo_url']
                    ?? asset('assets/imgs/afifalu-logo.png');
            @endphp
            <img src="{{ $menuLogo }}" alt="SanaaPro" class="sp-brand-mark">
            <div class="sp-brand-text">
                <strong>SANAAPRO</strong>
                <small>Gestion d'entreprise</small>
            </div>
        </div>
        <ul class="sidebar-nav" id="sidebar-nav">

            <!-- Accueil (toujours visible) -->
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/home') }}" id="nav-accueil" data-route="home">
                    <i class="bi bi-house"></i>
                    <span>Accueil</span>
                </a>
            </li>
            <!-- Gestion des Clients -->
            @if (auth()->user()->hasPermission('Gestion des Clients', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('clients.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#clients-nav" data-bs-toggle="collapse" href="#" id="nav-clients">
                        <i class="bi bi-people-fill"></i>
                        <span>Gestion des Clients</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="clients-nav"
                        class="nav-content collapse {{ Route::is('clients.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">
                        @if (auth()->user()->hasPermission('Gestion des Clients', 'consulter'))
                            <li>
                                <a href="{{ route('clients.index') }}"
                                    class="{{ Route::is('clients.index') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Liste des Clients</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermission('Gestion des Clients', 'consulter'))
                            <li>
                                <a href="{{ route('clients.index_desactivate') }}"
                                    class="{{ Route::is('clients.index_desactivate') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Clients Supprimés</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
            <!-- Visites & Rendez-vous (commercial multiservice) -->
            @if (auth()->user()->hasPermission('Gestion des Rendez-vous', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('rendez-vous.*') ? 'active' : 'collapsed' }}"
                        href="{{ route('rendez-vous.index') }}" id="nav-rendez-vous">
                        <i class="bi bi-calendar-check"></i>
                        <span>Visites & Rendez-vous</span>
                    </a>
                </li>
            @endif
            <!-- Gestion des Devis -->
            @if (auth()->user()->hasPermission('Gestion des Devis', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('devis.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#devis-nav" data-bs-toggle="collapse" href="#" id="nav-devis">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Gestion des Devis</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="devis-nav"
                        class="nav-content collapse {{ Route::is('devis.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('devis.index') }}"
                                class="{{ request()->routeIs('devis.*') && !request()->routeIs('devis.index_desactives') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span>Liste des devis</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('devis.index_desactives') }}"
                                class="{{ request()->routeIs('devis.index_desactives') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span>Devis désactivés</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            <!-- Ordre menu : devis → factures → commande → livraison -->
            @if (auth()->user()->hasPermission('Gestion des Factures', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('factures.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#factures-nav" data-bs-toggle="collapse" href="#" id="nav-factures">
                        <i class="bi bi-receipt-cutoff"></i>
                        <span>Gestion des Factures</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="factures-nav"
                        class="nav-content collapse {{ Route::is('factures.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('factures.index') }}"
                                class="{{ request()->routeIs('factures.*') && ! request()->routeIs('factures.index_desactives') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span>Liste des factures</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('factures.index_desactives') }}"
                                class="{{ request()->routeIs('factures.index_desactives') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span>Factures désactivées</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (auth()->user()->hasPermission('Gestion des Bons de commande', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('bons-commande.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#bc-nav" data-bs-toggle="collapse" href="#" id="nav-bons-commande">
                        <i class="bi bi-bag-check"></i>
                        <span>Bons de commande</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="bc-nav"
                        class="nav-content collapse {{ Route::is('bons-commande.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('bons-commande.index') }}"
                                class="{{ request()->routeIs('bons-commande.*') && ! request()->routeIs('bons-commande.index_desactives') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span>Liste</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('bons-commande.index_desactives') }}"
                                class="{{ request()->routeIs('bons-commande.index_desactives') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span>Archivés</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (auth()->user()->hasPermission('Gestion des Bons de livraison', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('bons-livraison.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#bl-nav" data-bs-toggle="collapse" href="#" id="nav-bons-livraison">
                        <i class="bi bi-truck"></i>
                        <span>Bons de livraison</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="bl-nav"
                        class="nav-content collapse {{ Route::is('bons-livraison.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('bons-livraison.index') }}"
                                class="{{ request()->routeIs('bons-livraison.*') && ! request()->routeIs('bons-livraison.index_desactives') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span>Liste</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('bons-livraison.index_desactives') }}"
                                class="{{ request()->routeIs('bons-livraison.index_desactives') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span>Archivés</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            <!-- Gestion des Catalogues -->
            @if (auth()->user()->hasPermission('Gestion des Catalogues', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('catalogue.*') ? 'active' : 'collapsed' }}"
                        href="{{ route('catalogue.index') }}" id="nav-catalogue">
                        <i class="bi bi-box-seam"></i>
                        <span>Gestion des Catalogues</span>
                    </a>
                </li>
            @endif
            @if (auth()->user()->hasPermission('Gestion des Marchés & Cautions', 'consulter'))
                @php
                    $marchesNavOpen = request()->routeIs('marches.*', 'analyse-marches.*');
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $marchesNavOpen ? '' : 'collapsed' }}"
                        data-bs-target="#marches-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-journal-bookmark"></i>
                        <span>Gestion de marché et cautions</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="marches-nav"
                        class="nav-content collapse {{ $marchesNavOpen ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('marches.index') }}"
                                class="{{ request()->routeIs('marches.*') && ! request()->routeIs('analyse-marches.*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span>Marchés &amp; cautions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('analyse-marches.index') }}"
                                class="{{ request()->routeIs('analyse-marches.*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span>Analyse des appels d'offres</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (auth()->user()->hasPermission('Alertes & Paramètres', 'modifier'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('parametres', 'alertes.parametres') ? 'active' : '' }}"
                        href="{{ route('parametres') }}" id="nav-parametres">
                        <i class="bi bi-gear-fill"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
            @endif
            <!-- Gestion des utilisateurs -->
            @if (auth()->user()->hasPermission('Gestion des Utilisateurs', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('utilisateurs.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#gestion_utilisateur" data-bs-toggle="collapse" href="#">
                        <i class="ri-account-box-fill"></i>
                        <span>Gest. utilisateurs</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="gestion_utilisateur"
                        class="nav-content collapse {{ Route::is('utilisateurs.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">

                        @if (auth()->user()->hasPermission('Gestion des Utilisateurs', 'consulter'))
                            <li>
                                <a href="{{ route('utilisateurs.index') }}"
                                    class="{{ Route::is('utilisateurs.index') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Liste des utilisateurs</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->hasPermission('Gestion des Utilisateurs', 'inactif'))
                            <li>
                                <a href="{{ route('utilisateurs.index_desactivate') }}"
                                    class="{{ Route::is('utilisateurs.index_desactivate') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Utilisateurs supprimés</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif


            <!-- Hôpitaux -->
            @if (auth()->user()->isSuperAdmin() && auth()->user()->hasPermission('Gestion des Entreprises', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('entreprises.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#hopitaux-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-hospital"></i>
                        <span>Entreprises</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="hopitaux-nav"
                        class="nav-content collapse {{ Route::is('entreprises.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">

                        @if (auth()->user()->hasPermission('Gestion des Entreprises', 'consulter'))
                            <li>
                                <a href="{{ route('entreprises.index') }}"
                                    class="{{ Route::is('entreprises.index') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Liste des entreprises</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->hasPermission('Gestion des Entreprises', 'inactif'))
                            <li>
                                <a href="{{ route('entreprises.index_desactivate') }}"
                                    class="{{ Route::is('entreprises.index_desactivate') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Entreprises désactivées</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (auth()->user()->isSuperAdmin() && auth()->user()->hasPermission('Gestion des Permissions', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('permissions.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#permissions-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-shield-lock"></i>
                        <span>Permissions</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="permissions-nav"
                        class="nav-content collapse {{ Route::is('permissions.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">

                        @if (auth()->user()->hasPermission('Gestion des Permissions', 'consulter'))
                            <li>
                                <a href="{{ route('permissions.index') }}"
                                    class="{{ Route::is('permissions.index') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Liste des Permissions</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->hasPermission('Gestion des Permissions', 'inactif'))
                            <li>
                                <a href="{{ route('permissions.index_desactivate') }}"
                                    class="{{ Route::is('permissions.index_desactivate') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Permissions désactivées</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- Profils -->
            @if (auth()->user()->isSuperAdmin() && auth()->user()->hasPermission('Gestion des Profiles', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('profil.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#profils-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-person-badge"></i>
                        <span>Profils</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="profils-nav" class="nav-content collapse {{ Route::is('profil.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">

                        @if (auth()->user()->hasPermission('Gestion des Profiles', 'consulter'))
                            <li>
                                <a href="{{ route('profil.index') }}"
                                    class="{{ Route::is('profil.index') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Liste des Profils</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->hasPermission('Gestion des Profiles', 'inactif'))
                            <li>
                                <a href="{{ route('profil.index_desactivate') }}"
                                    class="{{ Route::is('profil.index_desactivate') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Profils désactivés</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- Participants -->
            <!-- @if (auth()->user()->hasPermission('Gestion des Participants', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('participants.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#participants-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-people-fill"></i>
                        <span>Participants</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="participants-nav"
                        class="nav-content collapse {{ Route::is('participants.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">

                        @if (auth()->user()->hasPermission('Gestion des Participants', 'consulter'))
                            <li>
                                <a href="{{ route('participants.index') }}"
                                    class="{{ Route::is('participants.index') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Liste des Participants</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->hasPermission('Gestion des Participants', 'inactif'))
                            <li>
                                <a href="{{ route('participants.index_desactivate') }}"
                                    class="{{ Route::is('participants.index_desactivate') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Participants désactivés</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif -->

            <!-- Postes -->
            @if (auth()->user()->isSuperAdmin() && auth()->user()->hasPermission('Gestion des Postes', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('postes.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#postes-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-briefcase"></i>
                        <span>Postes</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="postes-nav" class="nav-content collapse {{ Route::is('postes.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">

                        @if (auth()->user()->hasPermission('Gestion des Postes', 'consulter'))
                            <li>
                                <a href="{{ route('postes.index') }}"
                                    class="{{ Route::is('postes.index') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Liste des Postes</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->hasPermission('Gestion des Postes', 'inactif'))
                            <li>
                                <a href="{{ route('postes.index_desactivate') }}"
                                    class="{{ Route::is('postes.index_desactivate') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Postes désactivés</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (auth()->user()->hasPermission('Gestion des Staff', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('actstaff.*') ? 'active' : 'collapsed' }}"
                        data-bs-target="#actstaff-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-activity"></i>
                        <span>Activités/Staff</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="actstaff-nav" class="nav-content collapse {{ Route::is('actstaff.*') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">

                        @if (auth()->user()->hasPermission('Gestion des Staff', 'consulter'))
                            <li>
                                <a href="{{ route('actstaff.index') }}"
                                    class="{{ Route::is('actstaff.index') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Liste des Activités/Staff</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->hasPermission('Gestion des Staff', 'inactif'))
                            <li>
                                <a href="{{ route('actstaff.index_desactivate') }}"
                                    class="{{ Route::is('actstaff.index_desactivate') ? 'active' : '' }}">
                                    <i class="bi bi-circle"></i>
                                    <span>Activités/Staff désactivés</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- Normes et protocoles -->
            @if (auth()->user()->hasPermission('NORMES_PROTOCOLES', 'consulter'))
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Normes et protocoles</span>
                    </a>
                </li>
            @endif

            <!-- Notifications -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('notifications.index') }}">
                    <i class="bi bi-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
            

            <!-- Déconnexion (toujours visible) -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Déconnexion</span>
                </a>
            </li>
        </ul>
    </aside><!-- End Sidebar-->
    <div class="content">
        <main id="main" class="main">
            @yield('content') <!-- Dynamic content section -->

        </main>
    </div>
    {{-- messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success bg-success text-light alert-dismissible fade show"
            style="position: fixed; bottom: 20px; right: 20px; max-width: 90%; width: 450px; z-index: 1050;"
            role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session()->get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <span id="timer" style="font-size: 12px; margin-left: 10px;">5s</span>
        </div>
    @endif
    @if (session()->has('fail'))
        <div class="alert alert-danger bg-danger text-light alert-dismissible fade show"
            style="position: fixed; bottom: 20px; right: 20px; max-width: 90%; width: 450px; z-index: 1050;"
            role="alert">
            <i class="bi bi-person-x-fill me-1"></i>
            {{ session()->get('fail') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <span id="timer" style="font-size: 12px; margin-left: 10px;">5s</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger bg-danger text-light alert-dismissible fade show"
            style="position: fixed; bottom: 20px; right: 20px; max-width: 90%; width: 450px; z-index: 1050;"
            role="alert">
            <i class="bi bi-person-x-fill me-1"></i>
            {{ session()->get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <span id="timer" style="font-size: 12px; margin-left: 10px;">5s</span>
        </div>
    @endif

    
    @if ($errors->any())
        <div class="alert alert-danger bg-danger text-light alert-dismissible fade show"
            style="position: fixed; bottom: 20px; right: 20px; max-width: 90%; width: 450px; z-index: 1050;"
            role="alert">
            <i class="bi bi-person-x-fill me-1"></i>
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <span id="timer" style="font-size: 12px; margin-left: 10px;">5s</span>
        </div>
    @endif
    <!--******************************************************************************************-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            hideLoader();
        });

        window.addEventListener('load', function() {
            hideLoader();
        });

        function hideLoader() {
            const loader = document.getElementById('loader-wrapper');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 500);
            }
        }
    </script>
    <script>
        function toggleAllProfiles(checkbox) {
            const individualProfiles = document.getElementById('individual_profiles');
            const profileCheckboxes = document.querySelectorAll('.profile-checkbox');

            if (checkbox.checked) {
                individualProfiles.classList.add('disabled-profiles');
                profileCheckboxes.forEach(cb => cb.checked = false);
            } else {
                individualProfiles.classList.remove('disabled-profiles');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const allProfilesCheckbox = document.getElementById('all_profiles');
            const profileCheckboxes = document.querySelectorAll('.profile-checkbox');

            // Empêcher la fermeture du dropdown lors du clic sur le formulaire
            document.getElementById('profileForm').addEventListener('click', function(event) {
                event.stopPropagation();
            });

            // Gestion des checkboxes individuelles
            profileCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked && allProfilesCheckbox.checked) {
                        allProfilesCheckbox.checked = false;
                        toggleAllProfiles(allProfilesCheckbox);
                    }
                });
            });

            // Fermer le dropdown uniquement quand on clique sur "Appliquer"
            document.querySelector('button[type="submit"]').addEventListener('click', function() {
                // Le dropdown se fermera automatiquement après le submit
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-fermeture des alertes après 5 secondes
            const alerts = document.querySelectorAll('.alert');

            alerts.forEach(function(alert) {
                let seconds = 5;
                const timer = alert.querySelector('#timer');

                const interval = setInterval(function() {
                    seconds--;
                    if (timer) timer.textContent = seconds + 's';

                    if (seconds <= 0) {
                        clearInterval(interval);
                        // Utiliser Bootstrap pour fermer l'alerte
                        bootstrap.Alert.getOrCreateInstance(alert).close();
                    }
                }, 1000);
            });
        });
    </script>
    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <!-- section khalid-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> --}}

    <!-- 2. Ensuite DataTables core -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <!-- 3. Puis les extensions DataTables -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <!-- Les toasts seront ajoutés ici par JavaScript -->
    </div>
    @auth
    <script>
    $(document).ready(function() {
        
        // Initialisation
        console.log("Script de notification initialisé.");
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        let knownNotificationIds = new Set();
        let isFirstFetch = true;

        // ======================================================================
        // FONCTION POUR AFFICHER LE TOAST
        // ======================================================================
        function showToast(notification) {
            const title = notification.data.title || 'Nouvelle Notification';
            const message = notification.data.message || 'Vous avez une nouvelle alerte.';
            const toastId = 'toast-' + notification.id;

            const toastHtml = `
                <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-secondary text-white">
                        <i class="bi bi-bell-fill me-2"></i>
                        <strong class="me-auto">${title}</strong>
                        <small>à l'instant</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">${message}</div>
                </div>
            `;
            $('.toast-container').append(toastHtml);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
            toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
        }

        // ======================================================================
        // FONCTION POUR RÉCUPÉRER LES NOTIFICATIONS
        // ======================================================================
        function fetchNotifications() {
            $.ajax({
                url: "{{ route('notifications.json') }}", 
                type: 'GET',
                dataType: 'json',
                success: function(notifications) {
                    updateNotificationUI(notifications);

                    if (isFirstFetch) {
                        notifications.forEach(n => knownNotificationIds.add(n.id));
                        isFirstFetch = false;
                    } else {
                        notifications.forEach(notification => {
                            if (!knownNotificationIds.has(notification.id)) {
                                showToast(notification);
                                knownNotificationIds.add(notification.id);
                            }
                        });
                    }
                }
            });
        }
        // ======================================================================
        // FONCTION POUR AFFICHER LES NOTIFICATIONS
        // ======================================================================

        function updateNotificationUI(notifications) {
            $('#notification-count').text(notifications.length);
            $('#notification-header-count').text(notifications.length);

            var container = $('#notification-items-container');
            container.empty();
            
            if (notifications.length === 0) {
                container.append('<li class="dropdown-divider"></li><li class="notification-item"><div><h4>Aucune nouvelle notification</h4></div></li>');
            } else {
                notifications.forEach(function(notification) {
                    let iconClass = 'bi bi-info-circle text-info';
                    let title = notification.data.title || 'Nouvelle Notification';
                    let message = notification.data.message || '...';
                    let author = 'Système';

                    let itemHtml = `
                        <li class="dropdown-divider"></li>
                        <a href="#" class="notification-link" data-id="${notification.id}" data-url="${notification.data.url}">
                            <li class="notification-item">
                                <i class="${iconClass}"></i>
                                <div>
                                    <h4>${title}</h4>
                                    <p>${message}</p>
                                    <p>Par: ${author}</p>
                                </div>
                            </li>
                        </a>
                    `;
                    container.append(itemHtml);
                });
            }
        }
        // ======================================================================
        // GESTION DU CLIC POUR MARQUER COMME LU
        // ======================================================================
        $(document).on('click', '.notification-link', function(e) {
            e.preventDefault();
            const notificationId = $(this).data('id');
            const redirectUrl = $(this).data('url');

            let urlTemplate = "{{ route('notifications.read', ['notificationId' => 'PLACEHOLDER']) }}";
            let finalUrl = urlTemplate.replace('PLACEHOLDER', notificationId);

            $.ajax({
                url: finalUrl,
                type: 'POST',
                success: function(response) {
                    console.log('Notification marquée comme lue.');
                    window.location.href = redirectUrl;
                },
                error: function(jqXHR) {
                    console.error("Échec du marquage comme lu.", jqXHR);
                    window.location.href = redirectUrl; // On redirige quand même
                }
            });
        });

        // ======================================================================
        // DÉMARRAGE DU SYSTÈME
        // ======================================================================
        fetchNotifications(); // Lancer une première fois
        setInterval(fetchNotifications, 3000);

        // ======================================================================
        // GESTION DE L'ACTIVATION DES MENUS SIDEBAR
        // ======================================================================
        function updateActiveMenu() {
            // Obtenir l'URL actuelle et la route
            const currentPath = window.location.pathname;
            const currentRoute = '{{ request()->route()->getName() ?? "" }}';
            
            // Normaliser le chemin
            let normalizedPath = currentPath.replace(/^\/epilworld\/public/, '').replace(/\/$/, '');
            if (!normalizedPath.startsWith('/')) {
                normalizedPath = '/' + normalizedPath;
            }
            
            // Vérification STRICTE : Accueil uniquement si route exacte 'home' OU chemin exact '/home'
            // Exclure explicitement toutes les autres routes
            const isHome = (currentRoute === 'home') || 
                          (normalizedPath === '/home') || 
                          (normalizedPath === '/epilworld/public/home') ||
                          (normalizedPath.endsWith('/home') && normalizedPath.split('/').length === 2);
            
            // Vérification STRICTE pour Clients : 
            // - Route commence par 'clients.' OU 
            // - Chemin est exactement '/clients' OU commence par '/clients/'
            // ET on n'est PAS sur home
            const isClients = !isHome && (
                currentRoute.startsWith('clients.') || 
                normalizedPath === '/clients' || 
                normalizedPath.startsWith('/clients/')
            );

            const isParametres = (currentRoute === 'parametres' || currentRoute === 'alertes.parametres') ||
                normalizedPath.endsWith('/parametres') ||
                normalizedPath.includes('/alertes/parametres');
            
            // TOUJOURS retirer active sur les entrées gérées par ce script
            $('#nav-accueil').removeClass('active');
            $('#nav-clients').removeClass('active');
            $('#nav-parametres').removeClass('active');
            
            if (isHome) {
                $('#nav-accueil').addClass('active');
            } else if (isClients) {
                $('#nav-clients').addClass('active');
            } else if (isParametres && $('#nav-parametres').length) {
                $('#nav-parametres').addClass('active');
            }
            // Les autres menus gèrent leur activation via Route::is() dans Blade
        }
        
        // Exécuter après que le DOM soit complètement chargé
        $(document).ready(function() {
            // Exécuter immédiatement
            updateActiveMenu();
            // Petit délai pour s'assurer que tout est chargé
            setTimeout(function() {
                updateActiveMenu();
            }, 50);
            // Délai supplémentaire pour être sûr
            setTimeout(function() {
                updateActiveMenu();
            }, 200);
            // Délai final pour capturer tous les changements
            setTimeout(function() {
                updateActiveMenu();
            }, 500);
        });
        
        // Exécuter aussi après le chargement complet de la page
        $(window).on('load', function() {
            setTimeout(function() {
                updateActiveMenu();
            }, 150);
        });
        
        // Exécuter aussi lors de la navigation (pushState/popState)
        window.addEventListener('popstate', function() {
            setTimeout(function() {
                updateActiveMenu();
            }, 50);
        });
        
        // Surveiller les changements d'URL (pour les applications SPA ou navigation AJAX)
        let lastUrl = location.href;
        new MutationObserver(function() {
            const url = location.href;
            if (url !== lastUrl) {
                lastUrl = url;
                setTimeout(function() {
                    updateActiveMenu();
                }, 50);
            }
        }).observe(document, { subtree: true, childList: true });
        
        // Gérer le clic - retirer active de tous, puis activer celui cliqué
        $(document).on('click', '#nav-accueil, #nav-clients, #nav-devis, #nav-factures, #nav-parametres', function(e) {
            $('.sidebar-nav > .nav-item > .nav-link').removeClass('active');
            $(this).addClass('active');
        });
        
        // Réexécuter lors des changements de navigation (pour les liens AJAX)
        $(document).ajaxComplete(function() {
            setTimeout(function() {
                updateActiveMenu();
            }, 50);
        });
        
        // Réexécuter lors des changements de hash (pour les ancres)
        $(window).on('hashchange', function() {
            setTimeout(function() {
                updateActiveMenu();
            }, 50);
        });

    }); // Fin de $(document).ready()
    </script>
    @endauth
    <!-- ====================================================================== -->
    @stack('scripts')
</body>

</html>
