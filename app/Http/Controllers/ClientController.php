<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Diagnostic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of active clients
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de consultation
            $hasPermission = $user->hasPermission('Gestion des Clients', 'consulter');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter les clients.');
            }

            $clients = Client::with('diagnostics')
                ->where('actif', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('user.client.index', compact('clients'));

        } catch (\Exception $e) {
            Log::error('Erreur dans ClientController@index: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Display a listing of deactivated clients
     */
    public function index_desactivate()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de consultation
            $hasPermission = $user->hasPermission('Gestion des Clients', 'consulter');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter les clients.');
            }

            $clients = Client::with('diagnostics')
                ->where('actif', 0)
                ->orderBy('updated_at', 'desc')
                ->get();

            return view('user.client.index_desactivate', compact('clients'));

        } catch (\Exception $e) {
            Log::error('Erreur dans ClientController@index_desactivate: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Store a newly created client (AJAX)
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => 'Vous devez être connecté.'], 401);
                }
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de création
            $hasPermission = $user->hasPermission('Gestion des Clients', 'ajouter');
            if (!$hasPermission) {
                if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => 'Vous n\'avez pas la permission de créer des clients.'], 403);
                }
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de créer des clients.');
            }

            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'date_naissance' => 'required|date',
                'email' => 'nullable|email|max:255',
                'telephone' => 'nullable|string|max:50',
                'adresse' => 'nullable|string|max:500',
                'ville' => 'nullable|string|max:100',
                'code_postal' => 'nullable|string|max:20',
                'ice' => 'nullable|string|max:50',
                'rc' => 'nullable|string|max:50',
                'secteur_activite' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur de validation',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $client = null;

            DB::transaction(function () use ($request, &$client) {
                $client = Client::create([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'date_naissance' => $request->date_naissance,
                    'cin' => null,
                    'email' => $request->email ?: null,
                    'telephone' => $request->telephone ?: null,
                    'adresse' => $request->adresse ?: null,
                    'ville' => $request->ville ?: null,
                    'code_postal' => $request->code_postal ?: null,
                    'ice' => $request->ice ?: null,
                    'rc' => $request->rc ?: null,
                    'secteur_activite' => $request->secteur_activite ?: null,
                    'notes' => $request->notes ?: null,
                    'besoin_diagnostic' => 0,
                    'actif' => 1,
                    'user_insert' => Auth::id(),
                ]);

                $currentUser = Auth::user();

                // Logs
                try {
                    Log::channel('client_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                    Log::channel('client_log')->info("✅ Client créé", [
                        '🆔 ID Client' => $client->id,
                        '📝 Données' => $request->except(['_token', '_method']),
                    ]);
                    Log::channel('client_log')->info(str_repeat('*', 100));
                } catch (\Exception $logException) {
                    // Ignorer les erreurs de log
                }
            });

            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Le client a été créé avec succès.',
                    'client' => $client
                ]);
            }

            return redirect()->route('clients.index')
                ->with('success', 'Le client a été créé avec succès.');

        } catch (\Illuminate\Database\QueryException $e) {
            // Erreur de base de données (table n'existe probablement pas)
            $currentUser = Auth::user();
            if ($currentUser) {
                try {
                    Log::channel('client_log')->error("❌ Erreur de base de données lors de la création du client", [
                        '👤 Utilisateur' => "{$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}",
                        '⚠️ Message' => $e->getMessage(),
                        '➡️ Ligne' => $e->getLine(),
                        '📁 Fichier' => $e->getFile(),
                    ]);
                } catch (\Exception $logException) {
                    // Ignorer les erreurs de log
                }
            }

            $isAjax = $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest';
            
            $errorMessage = 'Erreur de base de données. ';
            if (str_contains($e->getMessage(), "doesn't exist") || str_contains($e->getMessage(), "Unknown table")) {
                $errorMessage .= 'La table "clients" n\'existe pas. Veuillez exécuter la migration : php artisan migrate';
            } else {
                $errorMessage .= config('app.debug') ? $e->getMessage() : 'Vérifiez votre configuration de base de données.';
            }
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        } catch (\Exception $e) {
            $currentUser = Auth::user();
            if ($currentUser) {
                try {
                    Log::channel('client_log')->error("❌ Erreur lors de la création du client", [
                        '👤 Utilisateur' => "{$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}",
                        '⚠️ Message' => $e->getMessage(),
                        '➡️ Ligne' => $e->getLine(),
                        '📁 Fichier' => $e->getFile(),
                        '🔍 Trace' => $e->getTraceAsString()
                    ]);
                } catch (\Exception $logException) {
                    // Ignorer les erreurs de log
                }
            }

            $isAjax = $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest';
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la création du client.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création du client.')
                ->withInput();
        }
    }

    /**
     * Update the specified client (AJAX)
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Vous devez être connecté.'], 401);
                }
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de modification
            $hasPermission = $user->hasPermission('Gestion des Clients', 'modifier');
            if (!$hasPermission) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Vous n\'avez pas la permission de modifier des clients.'], 403);
                }
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de modifier des clients.');
            }

            $client = Client::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'date_naissance' => 'required|date',
                'email' => 'nullable|email|max:255',
                'telephone' => 'nullable|string|max:50',
                'adresse' => 'nullable|string|max:500',
                'ville' => 'nullable|string|max:100',
                'code_postal' => 'nullable|string|max:20',
                'ice' => 'nullable|string|max:50',
                'rc' => 'nullable|string|max:50',
                'secteur_activite' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur de validation',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('failed_modal', 'EditModal-' . $id);
            }

            DB::transaction(function () use ($request, $client) {
                $client->update([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'date_naissance' => $request->date_naissance,
                    'email' => $request->email ?: null,
                    'telephone' => $request->telephone ?: null,
                    'adresse' => $request->adresse ?: null,
                    'ville' => $request->ville ?: null,
                    'code_postal' => $request->code_postal ?: null,
                    'ice' => $request->ice ?: null,
                    'rc' => $request->rc ?: null,
                    'secteur_activite' => $request->secteur_activite ?: null,
                    'notes' => $request->notes ?: null,
                    'user_update' => Auth::id(),
                ]);

                $currentUser = Auth::user();

                // Logs
                Log::channel('client_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('client_log')->info("🔄 Client mis à jour", [
                    '🆔 ID Client' => $client->id,
                    '📝 Modifications' => $request->except(['_token', '_method']),
                ]);
                Log::channel('client_log')->info(str_repeat('*', 100));
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Le client a été mis à jour avec succès.',
                    'client' => $client->fresh()
                ]);
            }

            return redirect()->route('clients.index')
                ->with('success', 'Le client a été mis à jour avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();
            if ($currentUser) {
                Log::channel('client_log')->error("❌ Erreur lors de la mise à jour du client", [
                    '👤 Utilisateur' => "{$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}",
                    '⚠️ Message' => $e->getMessage(),
                    '➡️ Ligne' => $e->getLine(),
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la mise à jour du client.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du client.')
                ->withInput();
        }
    }

    /**
     * Desactivate the specified client (AJAX)
     */
    public function desactivate($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Vous devez être connecté.'], 401);
                }
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de désactivation
            $hasPermission = $user->hasPermission('Gestion des Clients', 'desactiver');
            if (!$hasPermission) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Vous n\'avez pas la permission de désactiver des clients.'], 403);
                }
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de désactiver des clients.');
            }

            $client = Client::find($id);
            if (!$client) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Client introuvable.'], 404);
                }
                return redirect()->back()->with('error', 'Client introuvable.');
            }

            $client->update([
                'actif' => 0,
                'user_update' => Auth::id(),
            ]);

            try {
                $currentUser = Auth::user();
                if ($currentUser) {
                    Log::channel('client_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                    Log::channel('client_log')->info("🗑️ Client désactivé", ['🆔 ID Client' => $client->id]);
                    Log::channel('client_log')->info(str_repeat('*', 100));
                }
            } catch (\Throwable $e) {
                Log::warning('Client désactivé (log client_log ignoré): ' . $e->getMessage());
            }

            if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Le client a été désactivé avec succès.'
                ]);
            }

            return redirect()->route('clients.index')
                ->with('success', 'Le client a été désactivé avec succès.');

        } catch (\Throwable $e) {
            try {
                $currentUser = Auth::user();
                if ($currentUser) {
                    Log::channel('client_log')->error("❌ Erreur lors de la désactivation du client", [
                        '👤 Utilisateur' => "{$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}",
                        '⚠️ Message' => $e->getMessage(),
                        '➡️ Ligne' => $e->getLine(),
                    ]);
                }
            } catch (\Throwable $logEx) {
                Log::warning('Log client_log ignoré: ' . $logEx->getMessage());
            }

            $message = 'Une erreur est survenue lors de la désactivation du client.';
            if (config('app.debug')) {
                $message .= ' (' . $e->getMessage() . ')';
            }

            if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return redirect()->back()
                ->with('error', $message);
        }
    }

    /**
     * Activate the specified client (AJAX)
     */
    public function activate($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Vous devez être connecté.'], 401);
                }
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            $hasPermission = $user->hasPermission('Gestion des Clients', 'activer');
            if (!$hasPermission) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Vous n\'avez pas la permission d\'activer des clients.'], 403);
                }
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission d\'activer des clients.');
            }

            $client = Client::find($id);
            if (!$client) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Client introuvable.'], 404);
                }
                return redirect()->back()->with('error', 'Client introuvable.');
            }

            $client->update([
                'actif' => 1,
                'user_update' => Auth::id(),
            ]);

            if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Le client a été activé avec succès.'
                ]);
            }

            return redirect()->route('clients.index')
                ->with('success', 'Le client a été activé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur dans ClientController@activate: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de l\'activation du client.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'activation du client.');
        }
    }
}

