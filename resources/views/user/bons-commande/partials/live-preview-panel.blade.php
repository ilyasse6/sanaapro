{{-- Aperçu live : nécessite le script live-preview-script inclus après le formulaire --}}
<div class="col-lg-4">
    <div class="simulateur-wrapper-bc mb-4">
        <div class="simulateur-bc">
            <div class="simulateur-bc-header">
                <span class="live-badge">● Live</span>
                <div class="d-flex align-items-center gap-2 small opacity-90">
                    <i class="bi bi-eye-fill"></i>
                    <span>Aperçu du bon de commande</span>
                </div>
                <div class="num-bc" id="simBcNumero">{{ $numeroPreview }}</div>
            </div>
            <div class="simulateur-bc-body">
                <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                    <div class="flex-grow-1">
                        <div class="label-sim">Fournisseur</div>
                        <div class="value-sim" id="simBcFournisseur"><span class="simulateur-placeholder">— Choisir un fournisseur —</span></div>
                        <div class="small text-muted mt-1" id="simBcFournisseurDetail"></div>
                    </div>
                    <span class="badge bg-secondary badge-statut" id="simBcStatut">Brouillon</span>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <div class="label-sim">Date</div>
                        <div class="value-sim" id="simBcDate">—</div>
                    </div>
                    <div class="col-6">
                        <div class="label-sim">Livraison</div>
                        <div class="value-sim text-truncate" id="simBcLivSouh" title="">—</div>
                    </div>
                </div>
                <div class="simulateur-bc-kv mb-2">
                    <div><strong>Réf. interne</strong> <span id="simBcRefInt">—</span></div>
                    <div><strong>Réf. fournisseur</strong> <span id="simBcRefF">—</span></div>
                    <div><strong>Contact</strong> <span id="simBcContact">—</span></div>
                    <div><strong>Émis par</strong> <span id="simBcEmis">—</span></div>
                    <div><strong>Lieu livr.</strong> <span id="simBcLieu">—</span></div>
                </div>
                <div class="label-sim mb-1">Lignes</div>
                <div class="table-responsive">
                    <table class="table table-sm simulateur-bc-table mb-0">
                        <thead>
                            <tr>
                                <th>Réf.</th>
                                <th>Description</th>
                                <th class="q">Qté</th>
                                <th>U.</th>
                                <th class="p">PU</th>
                            </tr>
                        </thead>
                        <tbody id="simBcLignes">
                            <tr><td colspan="5" class="text-center simulateur-placeholder py-2">Aucune ligne</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="simulateur-bc-total" id="simBcTotalWrap" style="display: none;">Total HT : <span id="simBcTotal">0,00</span> MAD</div>
                <div class="mt-2" id="simBcInfosWrap" style="display: none;">
                    <div class="label-sim">Informations additionnelles</div>
                    <div class="simulateur-bc-infos" id="simBcInfos"></div>
                </div>
            </div>
        </div>
    </div>
</div>
