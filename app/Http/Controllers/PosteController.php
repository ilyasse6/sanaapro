<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poste;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PosteController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de consultation
            $hasPermission = $user->hasPermission('Gestion des Postes','consulter');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter les postes.');
            }

            $postes = Poste::where('actif', 1)->get();
            return view('user.poste.index', compact('postes'));

        } catch (\Exception $e) {
            Log::error('Erreur dans PosteController@index: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function index_poste_desactivate()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de consultation des inactifs
            $hasPermission = $user->hasPermission('Gestion des Postes','inactif');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter les postes inactifs.');
            }

            $postes = Poste::where('actif', 0)->get();
            return view('user.poste.index', compact('postes'));

        } catch (\Exception $e) {
            Log::error('Erreur dans PosteController@index_poste_desactivate: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de création
            $hasPermission = $user->hasPermission('Gestion des Postes','ajouter');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de créer des postes.');
            }

            // Validate outside the transaction
            $validator = Validator::make($request->all(), [
                'intitule' => 'required|string|max:255|unique:postes,intitule',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $poste = null;

            DB::transaction(function () use ($request, &$poste) {
                $poste = Poste::create([
                    'intitule' => $request->intitule,
                    'description' => $request->description,
                    'actif' => 1
                ]);

                $currentUser = Auth::user();

                // Logs
                Log::channel('poste_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('poste_log')->info("✅ Poste créé", [
                    '🆔 ID Poste' => $poste->id,
                    '📝 Données' => $request->except(['_token', '_method']),
                ]);
                Log::channel('poste_log')->info(str_repeat('*', 100));
            });

            return redirect()->route('postes.index')->with('success', 'Poste créé avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();
            if ($currentUser) {
                Log::channel('poste_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('poste_log')->error("❌ Erreur lors de la création du poste", [
                    '⚠️ Message' => $e->getMessage(),
                    '📍 Ligne' => $e->getLine(),
                    '📝 Données tentées' => $request->except(['_token', '_method']),
                ]);
                Log::channel('poste_log')->info(str_repeat('*', 100));
            }

            return redirect()->back()
                ->with('error', 'Erreur lors de la création du poste: '.$e->getMessage())
                ->withInput();
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de modification
            $hasPermission = $user->hasPermission('Gestion des Postes','modifier');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de modifier des postes.');
            }

            $poste = Poste::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'intitule' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('postes')->ignore($poste->id, 'id')
                ],
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput()
                    ->with('failed_modal', 'EditModal-'.$id);
            }

            DB::transaction(function () use ($request, $poste) {
                $poste->update([
                    'intitule' => $request->intitule,
                    'description' => $request->description,
                ]);

                $currentUser = Auth::user();

                // Logs
                Log::channel('poste_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('poste_log')->info("🔄 Poste mis à jour", [
                    '🆔 ID Poste' => $poste->id,
                    '📝 Modifications' => $request->except(['_token', '_method']),
                ]);
                Log::channel('poste_log')->info(str_repeat('*', 100));
            });

            return redirect()->route('postes.index')->with('success', 'Poste mis à jour avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();
            if ($currentUser) {
                Log::channel('poste_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('poste_log')->error("❌ Erreur lors de la mise à jour du poste", [
                    '⚠️ Message' => $e->getMessage(),
                    '📍 Ligne' => $e->getLine(),
                    '📝 Données tentées' => $request->except(['_token', '_method']),
                ]);
                Log::channel('poste_log')->info(str_repeat('*', 100));
            }

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du poste.')
                ->withInput();
        }
    }

    /*-------------------------------------------------------------------------------------------------------------*/
    public function desactivate($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission de désactivation
            $hasPermission = $user->hasPermission('Gestion des Postes','desactiver');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de désactiver des postes.');
            }

            $poste = Poste::findOrFail($id);
            $poste->actif = 0;
            $poste->save();

            $currentUser = Auth::user();
            Log::channel('poste_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
            Log::channel('poste_log')->info("🚫 Poste désactivé", [
                '🆔 ID Poste' => $id,
                '📝 Données' => ['actif' => 0],
            ]);
            Log::channel('poste_log')->info(str_repeat('*', 100));

            return redirect()->route('postes.index')->with('success', 'Le poste a été désactivé avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();
            if ($currentUser) {
                Log::channel('poste_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('poste_log')->error("❌ Erreur lors de la désactivation du poste", [
                    '📄 ID Poste' => $id,
                    '⚠️ Message' => $e->getMessage(),
                    '➡️ Ligne' => $e->getLine()
                ]);
                Log::channel('poste_log')->info(str_repeat('*', 100));
            }

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la désactivation du poste.');
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function activate($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Vérifier la permission d'activation
            $hasPermission = $user->hasPermission('Gestion des Postes','activer');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission d\'activer des postes.');
            }

            $poste = Poste::findOrFail($id);
            $poste->actif = 1;
            $poste->save();

            $currentUser = Auth::user();
            Log::channel('poste_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
            Log::channel('poste_log')->info("✅ Poste activé", [
                '🆔 ID Poste' => $id,
                '📝 Données' => ['actif' => 1],
            ]);
            Log::channel('poste_log')->info(str_repeat('*', 100));

            return redirect()->route('postes.index')->with('success', 'Le poste a été activé avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();
            if ($currentUser) {
                Log::channel('poste_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('poste_log')->error("❌ Erreur lors de l'activation du poste", [
                    '📄 ID Poste' => $id,
                    '⚠️ Message' => $e->getMessage(),
                    '➡️ Ligne' => $e->getLine()
                ]);
                Log::channel('poste_log')->info(str_repeat('*', 100));
            }

            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'activation du poste.');
        }
    }
}
