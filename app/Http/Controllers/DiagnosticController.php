<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DiagnosticController extends Controller
{
    /**
     * Store a newly created diagnostic (AJAX)
     */
    public function store(Request $request, $clientId)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => 'Vous devez être connecté.'], 401);
                }
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            $client = Client::findOrFail($clientId);

            $validator = Validator::make($request->all(), [
                'deja_fait_laser' => 'required|boolean',
                'autre_centre' => 'nullable|string|max:255',
                'nb_seances_anterieures' => 'nullable|integer|min:0',
                'type_peau' => 'nullable|string|max:10',
                'antecedents_medicaux' => 'nullable|string',
                'medications_cours' => 'nullable|string',
                'allergies' => 'nullable|string',
                'grossesse_allaitement' => 'nullable|boolean',
                'zones_traiter' => 'nullable|array',
                'zone_cible' => 'nullable|string|max:255',
                'contre_indications' => 'nullable|string',
                'consentement_eclaire' => 'required|boolean',
                'date_prise_rdv' => 'nullable|date',
                'heure_rdv' => 'nullable',
                'type_traitement' => 'nullable|string|max:255',
                'nb_seances_prevues' => 'nullable|integer|min:1|max:20',
                'notes_diagnostic' => 'nullable|string',
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

            $diagnostic = null;

            DB::transaction(function () use ($request, $clientId, &$diagnostic) {
                $zonesTraiter = $request->has('zones_traiter') && is_array($request->zones_traiter) 
                    ? implode(', ', $request->zones_traiter) 
                    : null;

                $diagnostic = Diagnostic::create([
                    'client_id' => $clientId,
                    'deja_fait_laser' => (bool)$request->deja_fait_laser,
                    'autre_centre' => $request->autre_centre ?: null,
                    'nb_seances_anterieures' => $request->nb_seances_anterieures ?: null,
                    'type_peau' => $request->type_peau ?: null,
                    'antecedents_medicaux' => $request->antecedents_medicaux ?: null,
                    'medications_cours' => $request->medications_cours ?: null,
                    'allergies' => $request->allergies ?: null,
                    'grossesse_allaitement' => $request->has('grossesse_allaitement') ? (bool)$request->grossesse_allaitement : false,
                    'zones_traiter' => $zonesTraiter,
                    'zone_cible' => $request->zone_cible ?: null,
                    'contre_indications' => $request->contre_indications ?: null,
                    'consentement_eclaire' => (bool)$request->consentement_eclaire,
                    'date_prise_rdv' => $request->date_prise_rdv ?: null,
                    'heure_rdv' => $request->heure_rdv ?: null,
                    'type_traitement' => $request->type_traitement ?: null,
                    'nb_seances_prevues' => $request->nb_seances_prevues ?: null,
                    'notes_diagnostic' => $request->notes_diagnostic ?: null,
                    'actif' => 1,
                    'user_insert' => Auth::id(),
                ]);

                $currentUser = Auth::user();

                // Logs
                try {
                    Log::channel('client_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                    Log::channel('client_log')->info("✅ Diagnostic créé", [
                        '🆔 ID Diagnostic' => $diagnostic->id,
                        '👤 ID Client' => $clientId,
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
                    'message' => 'Le diagnostic a été créé avec succès.',
                    'diagnostic' => $diagnostic
                ]);
            }

            return redirect()->route('clients.index')
                ->with('success', 'Le diagnostic a été créé avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();
            if ($currentUser) {
                try {
                    Log::channel('client_log')->error("❌ Erreur lors de la création du diagnostic", [
                        '👤 Utilisateur' => "{$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}",
                        '⚠️ Message' => $e->getMessage(),
                        '➡️ Ligne' => $e->getLine(),
                    ]);
                } catch (\Exception $logException) {
                    // Ignorer les erreurs de log
                }
            }

            $isAjax = $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest';
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la création du diagnostic.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création du diagnostic.')
                ->withInput();
        }
    }
}

