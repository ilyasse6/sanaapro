<?php

use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\ClientsApiController;
use App\Http\Controllers\Api\V1\DevisFacturesApiController;
use App\Http\Controllers\Api\V1\EmployesTrackingApiController;
use App\Http\Controllers\Api\V1\MarchesApiController;
use App\Http\Controllers\Api\V1\RendezVousApiController;
use App\Http\Controllers\Api\V1\StockFournisseursApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthApiController::class, 'login']);

    Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
        Route::get('/auth/me', [AuthApiController::class, 'me']);
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);

        Route::middleware('api.permission:Gestion des Clients,consulter')->get('/clients', [ClientsApiController::class, 'index']);
        Route::middleware('api.permission:Gestion des Clients,consulter')->get('/clients/{id}', [ClientsApiController::class, 'show']);
        Route::middleware('api.permission:Gestion des Clients,ajouter')->post('/clients', [ClientsApiController::class, 'store']);
        Route::middleware('api.permission:Gestion des Clients,modifier')->put('/clients/{id}', [ClientsApiController::class, 'update']);

        Route::middleware('api.permission:Gestion des Rendez-vous,consulter')->get('/rendez-vous', [RendezVousApiController::class, 'index']);
        Route::middleware('api.permission:Gestion des Rendez-vous,consulter')->get('/rendez-vous/{id}', [RendezVousApiController::class, 'show']);
        Route::middleware('api.permission:Gestion des Rendez-vous,ajouter')->post('/rendez-vous', [RendezVousApiController::class, 'store']);
        Route::middleware('api.permission:Gestion des Rendez-vous,modifier')->put('/rendez-vous/{id}', [RendezVousApiController::class, 'update']);

        Route::middleware('api.permission:Gestion des Devis,consulter')->get('/devis', [DevisFacturesApiController::class, 'devisIndex']);
        Route::middleware('api.permission:Gestion des Devis,consulter')->get('/devis/{id}/pdf', [DevisFacturesApiController::class, 'devisPdf']);
        Route::middleware('api.permission:Gestion des Devis,consulter')->get('/devis/{id}', [DevisFacturesApiController::class, 'devisShow']);
        Route::middleware('api.permission:Gestion des Factures,consulter')->get('/factures', [DevisFacturesApiController::class, 'facturesIndex']);
        Route::middleware('api.permission:Gestion des Factures,consulter')->get('/factures/{id}/pdf', [DevisFacturesApiController::class, 'facturePdf']);
        Route::middleware('api.permission:Gestion des Factures,consulter')->get('/factures/{id}', [DevisFacturesApiController::class, 'facturesShow']);

        Route::middleware('api.permission:Gestion des Fournisseurs,consulter')->get('/fournisseurs', [StockFournisseursApiController::class, 'fournisseursIndex']);
        Route::middleware('api.permission:Gestion des Fournisseurs,consulter')->get('/fournisseurs/{id}', [StockFournisseursApiController::class, 'fournisseursShow']);
        Route::middleware('api.permission:Gestion des Fournisseurs,consulter')->get('/stock/articles', [StockFournisseursApiController::class, 'articlesIndex']);
        Route::middleware('api.permission:Gestion des Fournisseurs,consulter')->get('/stock/articles/{articleId}/mouvements', [StockFournisseursApiController::class, 'articleMouvements']);
        Route::middleware('api.permission:Gestion des Fournisseurs,ajouter')->post('/stock/mouvements', [StockFournisseursApiController::class, 'mouvementsStore']);

        Route::middleware('api.permission:Gestion des Marchés & Cautions,consulter')->get('/marches', [MarchesApiController::class, 'index']);
        Route::middleware('api.permission:Gestion des Marchés & Cautions,consulter')->get('/marches/{id}', [MarchesApiController::class, 'show']);

        Route::middleware('api.permission:Gestion des Employés & Congés,consulter')->get('/employes', [EmployesTrackingApiController::class, 'employesIndex']);
        Route::middleware('api.permission:Gestion des Employés & Congés,consulter')->get('/employes/{id}', [EmployesTrackingApiController::class, 'employeShow']);
        Route::middleware('api.permission:Suivi des déplacements,consulter')->get('/tracking/snapshot', [EmployesTrackingApiController::class, 'trackingSnapshot']);
    });
});
