<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Entreprise;
use App\Models\Profile;
use App\Models\ProfilePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EntrepriseController extends Controller
{
    private function ensureEntrepriseAccess(Entreprise $entreprise): ?\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Connexion requise.');
        }
        if ($user->isSuperAdmin()) {
            return null;
        }
        if ((int) $user->id_entreprise !== (int) $entreprise->id) {
            return redirect()->back()->with('error', 'Accès refusé pour cette entreprise.');
        }

        return null;
    }

    private function copyAppSettingsFromTemplate(int $toEntrepriseId): void
    {
        $sourceId = (int) Entreprise::query()->where('id', '<', $toEntrepriseId)->orderByDesc('id')->value('id');
        if ($sourceId < 1) {
            return;
        }

        foreach (AppSetting::query()->where('id_entreprise', $sourceId)->get() as $row) {
            AppSetting::query()->firstOrCreate(
                ['cle' => $row->cle, 'id_entreprise' => $toEntrepriseId],
                ['valeur' => $row->valeur]
            );
        }
    }

    public function index()
    {
        try {
            $user = Auth::user();
            $q = Entreprise::query()->where('actif', 1)->orderBy('abbreviation');
            if ($user && ! $user->isSuperAdmin()) {
                $q->where('id', $user->id_entreprise);
            }
            $entreprises = $q->get();

            return view('user.entreprise.liste-entreprises', compact('entreprises'));
        } catch (\Exception $e) {
            Log::channel('entreprise_log')->error('Erreur liste entreprises actives', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function index_entreprises_desactivate()
    {
        try {
            $user = Auth::user();
            $q = Entreprise::query()->where('actif', 0)->orderBy('abbreviation');
            if ($user && ! $user->isSuperAdmin()) {
                $q->where('id', $user->id_entreprise);
            }
            $entreprises = $q->paginate(10);

            return view('user.entreprise.liste-entreprises', compact('entreprises'));
        } catch (\Exception $e) {
            Log::channel('entreprise_log')->error('Erreur liste entreprises inactives', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function store(Request $request)
    {
        try {
            if (! Auth::user()?->isSuperAdmin()) {
                return redirect()->back()->with('error', 'Seul un super administrateur peut créer une nouvelle entreprise.');
            }

            $validator = Validator::make($request->all(), [
                'abbreviation' => 'required|string|unique:entreprises',
                'description' => 'required|string|max:255',
                'type' => ['required', Rule::in(array_keys(Entreprise::TYPE_LABELS))],
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $profileDirecteur = Profile::query()->where('nom', 'Directeur')->first();
            if (! $profileDirecteur) {
                return redirect()->back()->with('error', 'Profil « Directeur » introuvable. Exécutez les seeders.');
            }

            DB::transaction(function () use ($request, $profileDirecteur) {
                $entreprise = Entreprise::create([
                    'abbreviation' => $request->abbreviation,
                    'description' => $request->description,
                    'type' => $request->type,
                    'user_insert' => Auth::id(),
                    'actif' => 1,
                ]);

                ProfilePermission::create([
                    'id_profile' => $profileDirecteur->id,
                    'id_permission' => null,
                    'id_entreprise' => $entreprise->id,
                    'actif' => 1,
                    'date_debut' => now(),
                    'date_fin' => null,
                ]);

                $this->copyAppSettingsFromTemplate((int) $entreprise->id);
            });

            return redirect()->route('entreprises.index')
                ->with('success', 'L\'entreprise a été créée avec succès. Vous pouvez ajouter le directeur dans Gestion des utilisateurs.');
        } catch (\Exception $e) {
            Log::channel('entreprise_log')->error('Erreur création entreprise', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de l\'entreprise.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $entreprise = Entreprise::findOrFail($id);
            if ($r = $this->ensureEntrepriseAccess($entreprise)) {
                return $r;
            }

            $validator = Validator::make($request->all(), [
                'abbreviation' => [
                    'required',
                    'string',
                    Rule::unique('entreprises')->ignore($entreprise->id),
                ],
                'description' => 'required|string|max:255',
                'type' => ['required', Rule::in(array_keys(Entreprise::TYPE_LABELS))],
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->with('failed_modal', 'EditModal-'.$id);
            }

            $entreprise->update([
                'abbreviation' => $request->abbreviation,
                'description' => $request->description,
                'type' => $request->type,
                'user_update' => Auth::id(),
            ]);

            return redirect()->route('entreprises.index')
                ->with('success', 'L\'entreprise a été mise à jour avec succès.');
        } catch (\Exception $e) {
            Log::channel('entreprise_log')->error('Erreur mise à jour entreprise', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'entreprise.');
        }
    }

    public function desactivate($id)
    {
        try {
            $entreprise = Entreprise::findOrFail($id);
            if ($r = $this->ensureEntrepriseAccess($entreprise)) {
                return $r;
            }
            $entreprise->update([
                'actif' => 0,
                'user_update' => Auth::id(),
            ]);

            return redirect()->route('entreprises.index')
                ->with('success', 'L\'entreprise a été désactivée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la désactivation de l\'entreprise.');
        }
    }

    public function activate($id)
    {
        try {
            $entreprise = Entreprise::findOrFail($id);
            if ($r = $this->ensureEntrepriseAccess($entreprise)) {
                return $r;
            }
            $entreprise->update([
                'actif' => 1,
                'user_update' => Auth::id(),
            ]);

            return redirect()->route('entreprises.index')
                ->with('success', 'L\'entreprise a été activée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'activation de l\'entreprise.');
        }
    }
}
