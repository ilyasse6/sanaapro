<?php

namespace App\Http\Controllers;

use App\Models\CatalogueService;
use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CatalogueController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (!$user->hasPermission('Gestion des Catalogues', 'consulter')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter le catalogue.');
        }
        $services = CatalogueService::orderBy('nom')->get();
        $packs = Pack::with('services')->orderBy('nom')->get();
        return view('user.catalogue.index', compact('services', 'packs'));
    }

    public function storeService(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermission('Gestion des Catalogues', 'ajouter')) {
            return redirect()->back()->with('error', 'Permission refusée.');
        }
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'prix' => 'required|numeric|min:0',
            'reduction' => 'nullable|numeric|min:0|max:100',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('tab', 'prestations');
        }
        CatalogueService::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'reduction' => $request->reduction ?? 0,
            'actif' => 1,
            'user_insert' => $user->id,
        ]);
        return redirect()->route('catalogue.index')->with('success', 'Prestation / produit ajouté au catalogue.')->with('tab', 'prestations');
    }

    public function updateService(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermission('Gestion des Catalogues', 'modifier')) {
            return redirect()->back()->with('error', 'Permission refusée.');
        }
        $service = CatalogueService::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'prix' => 'required|numeric|min:0',
            'reduction' => 'nullable|numeric|min:0|max:100',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('tab', 'prestations');
        }
        $service->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'reduction' => $request->reduction ?? 0,
            'user_update' => $user->id,
        ]);
        return redirect()->route('catalogue.index')->with('success', 'Prestation / produit modifiée.')->with('tab', 'prestations');
    }

    public function desactivateService($id)
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermission('Gestion des Catalogues', 'desactiver')) {
            return redirect()->back()->with('error', 'Permission refusée.');
        }
        $service = CatalogueService::findOrFail($id);
        $service->update(['actif' => 0, 'user_update' => $user->id]);
        return redirect()->route('catalogue.index')->with('success', 'Prestation désactivée.')->with('tab', 'prestations');
    }

    public function activateService($id)
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermission('Gestion des Catalogues', 'activer')) {
            return redirect()->back()->with('error', 'Permission refusée.');
        }
        $service = CatalogueService::findOrFail($id);
        $service->update(['actif' => 1, 'user_update' => $user->id]);
        return redirect()->route('catalogue.index')->with('success', 'Prestation réactivée.')->with('tab', 'prestations');
    }

    public function storePack(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermission('Gestion des Catalogues', 'ajouter')) {
            return redirect()->back()->with('error', 'Permission refusée.');
        }
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'prix' => 'required|numeric|min:0',
            'reduction' => 'nullable|numeric|min:0|max:100',
            'services' => 'nullable|array',
            'services.*' => 'exists:catalogue_services,id',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('tab', 'packs');
        }
        $pack = Pack::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'reduction' => $request->reduction ?? 0,
            'actif' => 1,
            'user_insert' => $user->id,
        ]);
        if ($request->filled('services')) {
            $pack->services()->attach($request->services);
        }
        return redirect()->route('catalogue.index')->with('success', 'Pack / ensemble ajouté au catalogue.')->with('tab', 'packs');
    }

    public function updatePack(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermission('Gestion des Catalogues', 'modifier')) {
            return redirect()->back()->with('error', 'Permission refusée.');
        }
        $pack = Pack::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'prix' => 'required|numeric|min:0',
            'reduction' => 'nullable|numeric|min:0|max:100',
            'services' => 'nullable|array',
            'services.*' => 'exists:catalogue_services,id',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('tab', 'packs');
        }
        $pack->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'reduction' => $request->reduction ?? 0,
            'user_update' => $user->id,
        ]);
        $pack->services()->sync($request->services ?? []);
        return redirect()->route('catalogue.index')->with('success', 'Pack modifié.')->with('tab', 'packs');
    }

    public function desactivatePack($id)
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermission('Gestion des Catalogues', 'desactiver')) {
            return redirect()->back()->with('error', 'Permission refusée.');
        }
        $pack = Pack::findOrFail($id);
        $pack->update(['actif' => 0, 'user_update' => $user->id]);
        return redirect()->route('catalogue.index')->with('success', 'Pack désactivé.')->with('tab', 'packs');
    }

    public function activatePack($id)
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermission('Gestion des Catalogues', 'activer')) {
            return redirect()->back()->with('error', 'Permission refusée.');
        }
        $pack = Pack::findOrFail($id);
        $pack->update(['actif' => 1, 'user_update' => $user->id]);
        return redirect()->route('catalogue.index')->with('success', 'Pack réactivé.')->with('tab', 'packs');
    }
}
