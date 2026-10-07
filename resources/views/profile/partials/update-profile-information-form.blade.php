<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="fade-in-left">
                <div class="card profile-card">
                    <div class="card-body">
                        <h6 class="card-title d-flex align-items-center">
                            <i class="bi bi-info-square-fill me-1"></i>
                            <span>Informations du profil</span>
                        </h6>
                        <div class="row g-3">
                            <!-- Nom -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom" class="form-label">
                                        <i class="bi bi-person-bounding-box me-1"></i>Nom :
                                    </label>
                                    <input type="text" class="form-control custom-input" id="nom"
                                        value="{{ $user->nom }}" readonly>
                                </div>
                            </div>

                            <!-- Prénom -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prenom" class="form-label">
                                        <i class="bi bi-person-bounding-box me-1"></i>Prénom :
                                    </label>
                                    <input type="text" class="form-control custom-input" id="prenom"
                                        value="{{ $user->prenom }}" readonly>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope-at-fill me-1"></i>Email :
                                    </label>
                                    <input type="text" class="form-control custom-input" id="email"
                                        value="{{ $user->email }}" readonly>
                                </div>
                            </div>
                            <!-- Établissement -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="etablissement" class="form-label">
                                        <i class="bi bi-hospital-fill me-1"></i>Établissement :
                                    </label>
                                    <input type="text" class="form-control custom-input" id="etablissement"
                                        value="{{ $user->etablissement->abbreviation ?? 'Non renseigné' }}" readonly>
                                </div>
                            </div>
                            <!-- Poste -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="poste" class="form-label">
                                        <i class="bi bi-person-workspace me-1"></i>Poste :
                                    </label>
                                    <input type="text" class="form-control custom-input" id="poste"
                                        value="{{$user->poste_occupe ? $user->poste_occupe->intitule : $user->poste }}" readonly>
                                </div>
                            </div>
                            <!-- Profile -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profile" class="form-label">
                                        <i class="bi bi-person-vcard-fill me-1"></i>Profile :
                                    </label>
                                    <input type="text" class="form-control custom-input" id="profile"
                                        value="{{ $user->profile->nom ?? 'Non renseigné' }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
