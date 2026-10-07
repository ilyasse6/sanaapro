<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index()
    {
        try {
            $permissions = Permission::with('module')->where('actif', 1)->get();
            $modules = Module::where('actif', 1)->get();
            return view('user.permission.index', compact('permissions', 'modules'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer. ' . $e->getMessage());
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function index_permissions_desactivate()
    {
        try {
            $permissions = Permission::with('module')->where('actif', 0)->get();
            $modules = Module::where('actif', 1)->get();
            return view('user.permission.index', compact('permissions', 'modules'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer. ' . $e->getMessage());
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function store(Request $request)
    {
        // Validate outside the transaction
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:permissions,nom',
            'action' => 'nullable|string|max:255',
            'id_module' => 'required|exists:modules,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $permission = null;
            $currentUser = Auth::user();

            DB::transaction(function () use ($request, &$permission, $currentUser) {
                $permission = Permission::create([
                    'nom' => $request->nom,
                    'action' => $request->action,
                    'actif' => 1,
                    'user_insert' => $currentUser->id,
                    'id_module' => $request->id_module,
                ]);

                // Logs
                Log::channel('permission_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('permission_log')->info("✅ Permission créée", [
                    '🆔 ID Permission' => $permission->id,
                    '📝 Données' => $request->except(['_token', '_method']),
                ]);
                Log::channel('permission_log')->info(str_repeat('*', 100));
            });

            return redirect()->route('permissions.index')->with('success', 'Permission créée avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();

            Log::channel('permission_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
            Log::channel('permission_log')->error("❌ Erreur lors de la création de la permission", [
                '⚠️ Message' => $e->getMessage(),
                '📍 Ligne' => $e->getLine(),
                '📝 Données tentées' => $request->except(['_token', '_method']),
            ]);
            Log::channel('permission_log')->info(str_repeat('*', 100));

            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la permission: '.$e->getMessage())
                ->withInput();
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function update(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $permission = Permission::findOrFail($id);
                $currentUser = Auth::user();

                $validator = Validator::make($request->all(), [
                    'nom' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('permissions')->ignore($permission->id, 'id')
                    ],
                    'action' => 'nullable|string|max:255',
                    'id_module' => 'required|exists:modules,id',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput()->with('failed_modal', 'EditModal-'.$id);
                }

                $permission->update([
                    'nom' => $request->nom,
                    'action' => $request->action,
                    'id_module' => $request->id_module,
                    'user_update' => $currentUser->id,
                ]);

                // Logs
                Log::channel('permission_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
                Log::channel('permission_log')->info("🔄 Permission mise à jour", [
                    '🆔 ID Permission' => $permission->id,
                    '📝 Modifications' => $request->except(['_token', '_method']),
                ]);
                Log::channel('permission_log')->info(str_repeat('*', 100));
            });

            return redirect()->route('permissions.index')->with('success', 'Permission mise à jour avec succès.');

        } catch (\Exception $e) {
            $currentUser = Auth::user();

            Log::channel('permission_log')->info("👤 Utilisateur : {$currentUser->id} - {$currentUser->nom} {$currentUser->prenom}", []);
            Log::channel('permission_log')->error("❌ Erreur lors de la mise à jour de la permission", [
                '⚠️ Message' => $e->getMessage(),
                '📍 Ligne' => $e->getLine(),
                '📝 Données tentées' => $request->except(['_token', '_method']),
            ]);
            Log::channel('permission_log')->info(str_repeat('*', 100));

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour de la permission: '.$e->getMessage())
                ->withInput();
        }
    }

    /*-------------------------------------------------------------------------------------------------------------*/
    public function desactivate($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->actif = 0;
            $permission->user_update = Auth::id();
            $permission->save();

            $user = Auth::user();
            Log::channel('permission_log')->info("👤 Utilisateur : {$user->id} - {$user->nom} {$user->prenom}", []);
            Log::channel('permission_log')->info("🚫 Permission désactivée", [
                '🆔 ID Permission' => $id,
                '📝 Données' => ['actif' => 0, 'user_update' => $user->id],
            ]);
            Log::channel('permission_log')->info(str_repeat('*', 100));

            return redirect()->route('permissions.index')->with('success', 'La permission a été désactivée avec succès.');
        } catch (\Exception $e) {
            $user = Auth::user();
            Log::channel('permission_log')->info("👤 Utilisateur : {$user->id} - {$user->nom} {$user->prenom}", []);
            Log::channel('permission_log')->error("❌ Erreur lors de la désactivation de la permission", [
                '📄 ID Permission' => $id,
                '⚠️ Message' => $e->getMessage(),
                '➡️ Ligne' => $e->getLine()
            ]);
            Log::channel('permission_log')->info(str_repeat('*', 100));

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la désactivation de la permission.');
        }
    }

    /*----------------------------------------------------------------------------------------------------------------------*/
    public function activate($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->actif = 1;
            $permission->user_update = Auth::id();
            $permission->save();

            $user = Auth::user();
            Log::channel('permission_log')->info("👤 Utilisateur : {$user->id} - {$user->nom} {$user->prenom}", []);
            Log::channel('permission_log')->info("✅ Permission activée", [
                '🆔 ID Permission' => $id,
                '📝 Données' => ['actif' => 1, 'user_update' => $user->id],
            ]);
            Log::channel('permission_log')->info(str_repeat('*', 100));

            return redirect()->route('permissions.index')->with('success', 'La permission a été activée avec succès.');
        } catch (\Exception $e) {
            $user = Auth::user();
            Log::channel('permission_log')->info("👤 Utilisateur : {$user->id} - {$user->nom} {$user->prenom}", []);
            Log::channel('permission_log')->error("❌ Erreur lors de l'activation de la permission", [
                '📄 ID Permission' => $id,
                '⚠️ Message' => $e->getMessage(),
                '➡️ Ligne' => $e->getLine()
            ]);
            Log::channel('permission_log')->info(str_repeat('*', 100));

            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'activation de la permission.');
        }
    }
}
