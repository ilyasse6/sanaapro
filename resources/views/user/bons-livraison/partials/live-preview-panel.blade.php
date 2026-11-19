{{-- Aperçu live : nécessite le script live-preview-script inclus après le formulaire --}}
<div class="col-lg-5">
    <div class="card bl-card bl-apercu">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary"><i class="bi bi-eye me-1"></i>Aperçu du bon de livraison</h6>
                <span class="bl-apercu-live">● Live</span>
            </div>
            <div class="bl-apercu-fond">
                @include('user.bons-livraison.partials.document-afifalu', [
                    'societe' => $societe,
                    'bonLivraison' => $bonLivraison ?? null,
                    'numeroApercu' => $numeroPreview,
                ])
            </div>
        </div>
    </div>
</div>
