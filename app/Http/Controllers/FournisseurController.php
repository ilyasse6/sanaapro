<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FournisseurController extends Controller
{
    private function p(string $a): ?\Illuminate\Http\RedirectResponse
    {
        $u = Auth::user();
        if (! $u) {
            return redirect()->route('login')->with('error', 'Connexion requise.');
        }
        if (! $u->hasPermission('Gestion des Fournisseurs', $a)) {
            return redirect()->back()->with('error', 'Permission insuffisante.');
        }

        return null;
    }

    public function index()
    {
        if ($r = $this->p('consulter')) {
            return $r;
        }
        $fournisseurs = Fournisseur::where('actif', 1)->orderBy('raison_sociale')->paginate(20);

        return view('user.fournisseur.index', compact('fournisseurs'));
    }

    public function create()
    {
        if ($r = $this->p('ajouter')) {
            return $r;
        }

        return view('user.fournisseur.create');
    }

    public function store(Request $request)
    {
        if ($r = $this->p('ajouter')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'raison_sociale' => 'required|string|max:255',
            'ice' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:120',
            'code_postal' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }
        Fournisseur::create([...$v->validated(), 'actif' => 1, 'user_insert' => Auth::id()]);

        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur créé.');
    }

    public function edit(Fournisseur $fournisseur)
    {
        if ($r = $this->p('modifier')) {
            return $r;
        }

        return view('user.fournisseur.edit', compact('fournisseur'));
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        if ($r = $this->p('modifier')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'raison_sociale' => 'required|string|max:255',
            'ice' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:120',
            'code_postal' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }
        $fournisseur->update([...$v->validated(), 'user_update' => Auth::id()]);

        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur mis à jour.');
    }

    public function desactivate(Fournisseur $fournisseur)
    {
        if ($r = $this->p('desactiver')) {
            return $r;
        }
        $fournisseur->update(['actif' => 0, 'user_update' => Auth::id()]);

        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur désactivé.');
    }
}
