<?php

use App\Http\Controllers\AlertesController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ComptaController;
// use App\Http\Controllers\UserDroitController;
use App\Http\Controllers\DiagnosticController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MarcheController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PosteController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RappelPersonnelController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// home routes

Route::middleware('auth')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/rappels-personnels', [RappelPersonnelController::class, 'store'])->name('rappels-personnels.store');
    Route::get('/', fn () => redirect()->route('home'));
    // Route::post('/user-droit/store', [UserDroitController::class, 'store'])->name('user-droit.store');
    // Route::get('/user-droit/{id}/permissions', [UserDroitController::class, 'getPermissions']);
    Route::resource('utilisateurs', UserController::class);
    Route::delete('/utilisateurs/{id}/desactivate', [UserController::class, 'desactivate'])->name('utilisateurs.desactivate');
    Route::patch('/utilisateurs/{id}/activate', [UserController::class, 'activate'])->name('utilisateurs.activate');
    Route::get('/utilisateurs-desactivate', [UserController::class, 'index_users_desactivate'])->name('utilisateurs.index_desactivate');
    Route::post('/user/{userId}/droits/store', [UserController::class, 'storeDroits'])->name('user.droits.store');
    Route::get('/user/{id}/droits', [UserController::class, 'getUserDroits'])->name('user.droits.get');
    Route::post('/utilisateurs/check-email', [UserController::class, 'checkEmail'])->name('utilisateurs.checkEmail');
    Route::middleware('super.admin')->group(function () {
        Route::resource('entreprises', EntrepriseController::class);
        Route::post('/entreprises/{id}/desactivate', [EntrepriseController::class, 'desactivate'])->name('entreprises.desactivate');
        Route::post('/entreprises/{id}/activate', [EntrepriseController::class, 'activate'])->name('entreprises.activate');
        Route::get('/entreprises-desactivate', [EntrepriseController::class, 'index_entreprises_desactivate'])->name('entreprises.index_desactivate');
    });

    //---------------------------------------------------------------------------------------------------------------------------------
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients-desactivate', [ClientController::class, 'index_desactivate'])->name('clients.index_desactivate');
    Route::post('/clients/store', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{id}', [ClientController::class, 'update'])->name('clients.update');
    Route::post('/clients/{id}/activate', [ClientController::class, 'activate'])->name('clients.activate');
    Route::post('/clients/{id}/desactivate', [ClientController::class, 'desactivate'])->name('clients.desactivate');
    //---------------------------------------------------------------------------------------------------------------------------------
    Route::post('/clients/{clientId}/diagnostics/store', [DiagnosticController::class, 'store'])->name('diagnostics.store');
    //---------------------------------------------------------------------------------------------------------------------------------
    // Gestion des Rendez-vous
    Route::get('/rendez-vous', [App\Http\Controllers\RendezVousController::class, 'index'])->name('rendez-vous.index');
    Route::post('/rendez-vous/store', [App\Http\Controllers\RendezVousController::class, 'store'])->name('rendez-vous.store');
    Route::put('/rendez-vous/{id}', [App\Http\Controllers\RendezVousController::class, 'update'])->name('rendez-vous.update');
    Route::post('/rendez-vous/{id}/desactivate', [App\Http\Controllers\RendezVousController::class, 'desactivate'])->name('rendez-vous.desactivate');
    //---------------------------------------------------------------------------------------------------------------------------------
    // Gestion des Devis (commercial)
    Route::get('/devis', [App\Http\Controllers\DevisController::class, 'index'])->name('devis.index');
    Route::get('/devis/desactives', [App\Http\Controllers\DevisController::class, 'indexDesactives'])->name('devis.index_desactives');
    Route::get('/devis/create', [App\Http\Controllers\DevisController::class, 'create'])->name('devis.create');
    Route::post('/devis', [App\Http\Controllers\DevisController::class, 'store'])->name('devis.store');
    Route::post('/devis/bulk', [App\Http\Controllers\DevisController::class, 'bulkAction'])->name('devis.bulk');
    Route::get('/devis/{devi}', [App\Http\Controllers\DevisController::class, 'show'])->name('devis.show');
    Route::get('/devis/{devi}/edit', [App\Http\Controllers\DevisController::class, 'edit'])->name('devis.edit');
    Route::get('/devis/{devi}/pdf', [App\Http\Controllers\DevisController::class, 'pdf'])->name('devis.pdf');
    Route::put('/devis/{devi}', [App\Http\Controllers\DevisController::class, 'update'])->name('devis.update');
    Route::post('/devis/{id}/desactivate', [App\Http\Controllers\DevisController::class, 'desactivate'])->name('devis.desactivate');
    Route::post('/devis/{id}/activate', [App\Http\Controllers\DevisController::class, 'activate'])->name('devis.activate');
    Route::get('/devis/{devi}/duplicate', [App\Http\Controllers\DevisController::class, 'duplicate'])->name('devis.duplicate');
    //---------------------------------------------------------------------------------------------------------------------------------
    // Bons de livraison
    Route::get('/bons-livraison', [App\Http\Controllers\BonLivraisonController::class, 'index'])->name('bons-livraison.index');
    Route::get('/bons-livraison/desactives', [App\Http\Controllers\BonLivraisonController::class, 'indexDesactives'])->name('bons-livraison.index_desactives');
    Route::get('/bons-livraison/create', [App\Http\Controllers\BonLivraisonController::class, 'create'])->name('bons-livraison.create');
    Route::post('/bons-livraison', [App\Http\Controllers\BonLivraisonController::class, 'store'])->name('bons-livraison.store');
    Route::get('/bons-livraison/{bon_livraison}', [App\Http\Controllers\BonLivraisonController::class, 'show'])->name('bons-livraison.show');
    Route::get('/bons-livraison/{bon_livraison}/edit', [App\Http\Controllers\BonLivraisonController::class, 'edit'])->name('bons-livraison.edit');
    Route::get('/bons-livraison/{bon_livraison}/pdf', [App\Http\Controllers\BonLivraisonController::class, 'pdf'])->name('bons-livraison.pdf');
    Route::put('/bons-livraison/{bon_livraison}', [App\Http\Controllers\BonLivraisonController::class, 'update'])->name('bons-livraison.update');
    Route::post('/bons-livraison/{id}/desactivate', [App\Http\Controllers\BonLivraisonController::class, 'desactivate'])->name('bons-livraison.desactivate');
    Route::post('/bons-livraison/{id}/activate', [App\Http\Controllers\BonLivraisonController::class, 'activate'])->name('bons-livraison.activate');
    Route::get('/bons-livraison/{bon_livraison}/duplicate', [App\Http\Controllers\BonLivraisonController::class, 'duplicate'])->name('bons-livraison.duplicate');
    //---------------------------------------------------------------------------------------------------------------------------------
    // Bons de commande
    Route::get('/bons-commande', [App\Http\Controllers\BonCommandeController::class, 'index'])->name('bons-commande.index');
    Route::get('/bons-commande/desactives', [App\Http\Controllers\BonCommandeController::class, 'indexDesactives'])->name('bons-commande.index_desactives');
    Route::get('/bons-commande/create', [App\Http\Controllers\BonCommandeController::class, 'create'])->name('bons-commande.create');
    Route::post('/bons-commande', [App\Http\Controllers\BonCommandeController::class, 'store'])->name('bons-commande.store');
    Route::get('/bons-commande/{bon_commande}', [App\Http\Controllers\BonCommandeController::class, 'show'])->name('bons-commande.show');
    Route::get('/bons-commande/{bon_commande}/edit', [App\Http\Controllers\BonCommandeController::class, 'edit'])->name('bons-commande.edit');
    Route::get('/bons-commande/{bon_commande}/pdf', [App\Http\Controllers\BonCommandeController::class, 'pdf'])->name('bons-commande.pdf');
    Route::put('/bons-commande/{bon_commande}', [App\Http\Controllers\BonCommandeController::class, 'update'])->name('bons-commande.update');
    Route::post('/bons-commande/{id}/desactivate', [App\Http\Controllers\BonCommandeController::class, 'desactivate'])->name('bons-commande.desactivate');
    Route::post('/bons-commande/{id}/activate', [App\Http\Controllers\BonCommandeController::class, 'activate'])->name('bons-commande.activate');
    Route::get('/bons-commande/{bon_commande}/duplicate', [App\Http\Controllers\BonCommandeController::class, 'duplicate'])->name('bons-commande.duplicate');
    //---------------------------------------------------------------------------------------------------------------------------------
    // Gestion des Catalogues (prestations / produits et packs)
    Route::get('/catalogue', [App\Http\Controllers\CatalogueController::class, 'index'])->name('catalogue.index');
    Route::post('/catalogue/prestations', [App\Http\Controllers\CatalogueController::class, 'storeService'])->name('catalogue.store.service');
    Route::put('/catalogue/prestations/{id}', [App\Http\Controllers\CatalogueController::class, 'updateService'])->name('catalogue.update.service');
    Route::post('/catalogue/prestations/{id}/desactivate', [App\Http\Controllers\CatalogueController::class, 'desactivateService'])->name('catalogue.desactivate.service');
    Route::post('/catalogue/prestations/{id}/activate', [App\Http\Controllers\CatalogueController::class, 'activateService'])->name('catalogue.activate.service');
    Route::post('/catalogue/packs', [App\Http\Controllers\CatalogueController::class, 'storePack'])->name('catalogue.store.pack');
    Route::put('/catalogue/packs/{id}', [App\Http\Controllers\CatalogueController::class, 'updatePack'])->name('catalogue.update.pack');
    Route::post('/catalogue/packs/{id}/desactivate', [App\Http\Controllers\CatalogueController::class, 'desactivatePack'])->name('catalogue.desactivate.pack');
    Route::post('/catalogue/packs/{id}/activate', [App\Http\Controllers\CatalogueController::class, 'activatePack'])->name('catalogue.activate.pack');
    //---------------------------------------------------------------------------------------------------------------------------------
    // Factures (CRUD, lot, conversion depuis devis, PDF)
    Route::get('/factures', [FactureController::class, 'index'])->name('factures.index');
    Route::get('/factures/create', [FactureController::class, 'create'])->name('factures.create');
    Route::post('/factures', [FactureController::class, 'store'])->name('factures.store');
    Route::post('/factures/bulk', [FactureController::class, 'bulkAction'])->name('factures.bulk');
    Route::get('/factures/desactives', [FactureController::class, 'indexDesactives'])->name('factures.index_desactives');
    Route::post('/devis/{devi}/convertir-facture', [FactureController::class, 'convertirDepuisDevis'])->name('factures.convertir-devis');
    Route::post('/factures/{id}/activate', [FactureController::class, 'activate'])->name('factures.activate');
    Route::get('/factures/{facture}/edit', [FactureController::class, 'edit'])->name('factures.edit');
    Route::put('/factures/{facture}', [FactureController::class, 'update'])->name('factures.update');
    Route::get('/factures/{facture}/duplicate', [FactureController::class, 'duplicate'])->name('factures.duplicate');
    Route::get('/factures/{facture}/pdf', [FactureController::class, 'pdf'])->name('factures.pdf');
    Route::post('/factures/{facture}/statut', [FactureController::class, 'updateStatut'])->name('factures.update-statut');
    Route::get('/factures/{facture}', [FactureController::class, 'show'])->name('factures.show');
    Route::post('/factures/{id}/desactivate', [FactureController::class, 'desactivate'])->name('factures.desactivate');
    //---------------------------------------------------------------------------------------------------------------------------------
    // Marchés, lots, cautions, documents
    Route::get('/marches', [MarcheController::class, 'index'])->name('marches.index');
    Route::get('/marches/create', [MarcheController::class, 'create'])->name('marches.create');
    Route::post('/marches/import-consultation', [MarcheController::class, 'importConsultation'])->name('marches.import-consultation');
    Route::post('/marches', [MarcheController::class, 'store'])->name('marches.store');
    Route::get('/marches/{marche}', [MarcheController::class, 'show'])->name('marches.show');
    Route::get('/marches/{marche}/edit', [MarcheController::class, 'edit'])->name('marches.edit');
    Route::put('/marches/{marche}', [MarcheController::class, 'update'])->name('marches.update');
    Route::post('/marches/{marche}/decision-offre', [MarcheController::class, 'decisionOffre'])->name('marches.decision-offre');
    Route::post('/marches/{marche}/desactivate', [MarcheController::class, 'desactivate'])->name('marches.desactivate');
    Route::post('/marches/{marche}/lots', [MarcheController::class, 'storeLot'])->name('marches.lots.store');
    Route::delete('/marche-lots/{lot}', [MarcheController::class, 'destroyLot'])->name('marches.lots.destroy');
    Route::post('/marches/{marche}/cautions', [MarcheController::class, 'storeCaution'])->name('marches.cautions.store');
    Route::put('/cautions/{caution}', [MarcheController::class, 'updateCaution'])->name('marches.cautions.update');
    Route::delete('/cautions/{caution}', [MarcheController::class, 'destroyCaution'])->name('marches.cautions.destroy');
    Route::post('/marches/{marche}/documents', [MarcheController::class, 'storeDocument'])->name('marches.documents.store');
    Route::delete('/marche-documents/{document}', [MarcheController::class, 'destroyDocument'])->name('marches.documents.destroy');
    // Analyse appels d'offres (type Marcheo)
    Route::get('/analyse-marches', [\App\Http\Controllers\AnalyseMarchesController::class, 'index'])->name('analyse-marches.index');
    Route::post('/analyse-marches', [\App\Http\Controllers\AnalyseMarchesController::class, 'analyser'])->name('analyse-marches.analyser');
    Route::post('/analyse-marches/clear', [\App\Http\Controllers\AnalyseMarchesController::class, 'clear'])->name('analyse-marches.clear');
    //---------------------------------------------------------------------------------------------------------------------------------
    Route::get('/fournisseurs', [FournisseurController::class, 'index'])->name('fournisseurs.index');
    Route::get('/fournisseurs/create', [FournisseurController::class, 'create'])->name('fournisseurs.create');
    Route::post('/fournisseurs', [FournisseurController::class, 'store'])->name('fournisseurs.store');
    Route::get('/fournisseurs/{fournisseur}/edit', [FournisseurController::class, 'edit'])->name('fournisseurs.edit');
    Route::put('/fournisseurs/{fournisseur}', [FournisseurController::class, 'update'])->name('fournisseurs.update');
    Route::post('/fournisseurs/{fournisseur}/desactivate', [FournisseurController::class, 'desactivate'])->name('fournisseurs.desactivate');
    //---------------------------------------------------------------------------------------------------------------------------------
    Route::get('/stock/articles', [StockController::class, 'indexArticles'])->name('stock.articles');
    Route::get('/stock/articles/create', [StockController::class, 'createArticle'])->name('stock.articles.create');
    Route::post('/stock/articles', [StockController::class, 'storeArticle'])->name('stock.articles.store');
    Route::get('/stock/articles/{article}/edit', [StockController::class, 'editArticle'])->name('stock.articles.edit');
    Route::put('/stock/articles/{article}', [StockController::class, 'updateArticle'])->name('stock.articles.update');
    Route::post('/stock/articles/{article}/desactivate', [StockController::class, 'desactivateArticle'])->name('stock.articles.desactivate');
    Route::get('/stock/mouvements', [StockController::class, 'indexMouvements'])->name('stock.mouvements');
    Route::post('/stock/mouvements', [StockController::class, 'storeMouvement'])->name('stock.mouvements.store');
    //---------------------------------------------------------------------------------------------------------------------------------
    Route::get('/compta', [ComptaController::class, 'index'])->name('compta.index');
    Route::get('/compta/mouvements', [ComptaController::class, 'mouvements'])->name('compta.mouvements');
    Route::post('/compta/mouvements', [ComptaController::class, 'storeMouvement'])->name('compta.mouvements.store');
    //---------------------------------------------------------------------------------------------------------------------------------
    Route::get('/employes', [EmployeController::class, 'index'])->name('employes.index');
    Route::get('/employes/create', [EmployeController::class, 'create'])->name('employes.create');
    Route::post('/employes', [EmployeController::class, 'store'])->name('employes.store');
    Route::get('/employes/{employe}', [EmployeController::class, 'show'])->name('employes.show');
    Route::get('/employes/{employe}/edit', [EmployeController::class, 'edit'])->name('employes.edit');
    Route::put('/employes/{employe}', [EmployeController::class, 'update'])->name('employes.update');
    Route::post('/employes/{employe}/desactivate', [EmployeController::class, 'desactivate'])->name('employes.desactivate');
    Route::post('/employes/{employe}/conges', [EmployeController::class, 'storeConge'])->name('employes.conges.store');
    Route::post('/conges/{conge}/statut', [EmployeController::class, 'updateCongeStatut'])->name('employes.conges.statut');
    //---------------------------------------------------------------------------------------------------------------------------------
    Route::get('/alertes', [AlertesController::class, 'index'])->name('alertes.index');
    Route::get('/parametres', [AlertesController::class, 'parametres'])->name('parametres');
    Route::get('/alertes/parametres', [AlertesController::class, 'parametres'])->name('alertes.parametres');
    Route::post('/alertes/parametres', [AlertesController::class, 'saveParametres'])->name('alertes.parametres.save');
    // GET : URL réservée au POST du formulaire — redirige vers la page des paramètres si ouverte dans le navigateur
    Route::get('/alertes/parametres/branding', function () {
        return redirect()->route('parametres');
    })->name('alertes.parametres.branding.get');
    Route::post('/alertes/parametres/branding', [AlertesController::class, 'saveBranding'])->name('alertes.parametres.branding');
    //---------------------------------------------------------------------------------------------------------------------------------
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    //---------------------------------------------------------------------------------------------------------------------------------
    Route::middleware('super.admin')->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions-desactivate', [PermissionController::class, 'index_permissions_desactivate'])->name('permissions.index_desactivate');
        Route::post('/permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
        Route::put('/permissions/{id}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::post('/permissions/{id}/activate', [PermissionController::class, 'activate'])->name('permissions.activate');
        Route::post('/permissions/{id}/desactivate', [PermissionController::class, 'desactivate'])->name('permissions.desactivate');
        //---------------------------------------------------------------------------------------------------------------------------------
        Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
        Route::get('/profil-desactivate', [ProfilController::class, 'index_profiles_desactivate'])->name('profil.index_desactivate');
        Route::post('/profil/store', [ProfilController::class, 'store'])->name('profil.store');
        Route::put('/profil/{id}', [ProfilController::class, 'update'])->name('profil.update');
        Route::post('/profil/{id}/activate', [ProfilController::class, 'activate'])->name('profil.activate');
        Route::post('/profil/{id}/desactivate', [ProfilController::class, 'desactivate'])->name('profil.desactivate');
        //---------------------------------------------------------------------------------------------------------------------------------
        Route::get('/postes', [PosteController::class, 'index'])->name('postes.index');
        Route::get('/postes-desactivate', [PosteController::class, 'index_poste_desactivate'])->name('postes.index_desactivate');
        Route::post('/postes/store', [PosteController::class, 'store'])->name('postes.store');
        Route::put('/postes/{id}', [PosteController::class, 'update'])->name('postes.update');
        Route::post('/postes/{id}/activate', [PosteController::class, 'activate'])->name('postes.activate');
        Route::post('/postes/{id}/desactivate', [PosteController::class, 'desactivate'])->name('postes.desactivate');
    });
    Route::patch('/profil-switch', [ProfilController::class, 'switchProfile'])->name('profil.switch');
    // profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //-------------------------------------------------------------------------------------------------------------------------------------
    //notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        // Page listing all notifications of the authenticated user
        Route::get('/', [NotificationController::class, 'index'])->name('index');

        // URL générée : /notifications/json
        // Nom de la route : notifications.json
        Route::get('/json', [NotificationController::class, 'getUnread'])->name('json');

        // URL générée : /notifications/{id}/read
        // Nom de la route : notifications.read
        Route::post('/{notificationId}/read', [NotificationController::class, 'markAsRead'])->name('read');

    });
});
Route::middleware('log.access')->group(function () {
    Route::get('/api/logs', [LogController::class, 'index']);
});
require __DIR__.'/auth.php';
