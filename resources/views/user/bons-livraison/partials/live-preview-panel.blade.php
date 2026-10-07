{{-- Aperçu live : nécessite le script live-preview-script inclus après le formulaire --}}
<div class="col-lg-4">
    <div class="simulateur-wrapper-bl mb-4">
        <div class="simulateur-bl">
            <div class="simulateur-bl-header">
                <span class="live-badge">● Live</span>
                <div class="d-flex align-items-center gap-2 small opacity-90">
                    <i class="bi bi-eye-fill"></i>
                    <span>Aperçu du bon de livraison</span>
                </div>
                <div class="num-bl" id="simBlNumero">{{ $numeroPreview }}</div>
            </div>
            <div class="simulateur-bl-body">
                <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                    <div class="flex-grow-1">
                        <div class="label-sim">Destinataire</div>
                        <div class="value-sim" id="simBlClient"><span class="simulateur-placeholder">— Choisir un client —</span></div>
                        <div class="small text-muted mt-1" id="simBlClientDetail"></div>
                    </div>
                    <span class="badge bg-secondary badge-statut" id="simBlStatut">Brouillon</span>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <div class="label-sim">Date</div>
                        <div class="value-sim" id="simBlDate">—</div>
                    </div>
                    <div class="col-6">
                        <div class="label-sim">Lieu</div>
                        <div class="value-sim text-truncate" id="simBlLieu" title="">—</div>
                    </div>
                </div>
                <div class="simulateur-bl-kv mb-2">
                    <div><strong>N° commande</strong> <span id="simBlCmd">—</span></div>
                    <div><strong>N° client</strong> <span id="simBlRefCli">—</span></div>
                    <div><strong>Contact</strong> <span id="simBlContact">—</span></div>
                    <div><strong>Émis par</strong> <span id="simBlEmis">—</span></div>
                </div>
                <div class="label-sim mb-1">Lignes livrées</div>
                <div class="table-responsive">
                    <table class="table table-sm simulateur-bl-table mb-0">
                        <thead>
                            <tr>
                                <th>Réf.</th>
                                <th>Description</th>
                                <th class="q">Qté</th>
                                <th>U.</th>
                            </tr>
                        </thead>
                        <tbody id="simBlLignes">
                            <tr><td colspan="4" class="text-center simulateur-placeholder py-2">Aucune ligne</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2" id="simBlInfosWrap" style="display: none;">
                    <div class="label-sim">Informations additionnelles</div>
                    <div class="simulateur-bl-infos" id="simBlInfos"></div>
                </div>
            </div>
        </div>
    </div>
</div>
