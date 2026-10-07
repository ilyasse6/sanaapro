<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use App\Models\Employe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeController extends Controller
{
    private function p(string $a): ?\Illuminate\Http\RedirectResponse
    {
        $u = Auth::user();
        if (! $u) {
            return redirect()->route('login')->with('error', 'Connexion requise.');
        }
        if (! $u->hasPermission('Gestion des Employés & Congés', $a)) {
            return redirect()->back()->with('error', 'Permission insuffisante.');
        }

        return null;
    }

    public function index()
    {
        if ($r = $this->p('consulter')) {
            return $r;
        }
        $employes = Employe::with('user')->where('actif', 1)->orderBy('nom')->paginate(20);

        return view('user.employe.index', compact('employes'));
    }

    public function create()
    {
        if ($r = $this->p('ajouter')) {
            return $r;
        }
        $users = User::where('actif', 1)->orderBy('nom')->get();

        return view('user.employe.create', compact('users'));
    }

    public function store(Request $request)
    {
        if ($r = $this->p('ajouter')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'matricule' => 'required|string|max:50|unique:employes,matricule',
            'nom' => 'required|string|max:120',
            'prenom' => 'required|string|max:120',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:50',
            'poste_libelle' => 'nullable|string|max:120',
            'date_embauche' => 'nullable|date',
            'salaire_mensuel' => 'nullable|numeric|min:0',
            'user_id' => 'nullable|exists:users,id',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }
        Employe::create([...$v->validated(), 'actif' => 1, 'user_insert' => Auth::id()]);

        return redirect()->route('employes.index')->with('success', 'Employé enregistré.');
    }

    public function show(Employe $employe)
    {
        if ($r = $this->p('consulter')) {
            return $r;
        }
        $employe->load(['conges' => fn ($q) => $q->orderByDesc('date_debut'), 'user']);

        return view('user.employe.show', compact('employe'));
    }

    public function edit(Employe $employe)
    {
        if ($r = $this->p('modifier')) {
            return $r;
        }
        $users = User::where('actif', 1)->orderBy('nom')->get();

        return view('user.employe.edit', compact('employe', 'users'));
    }

    public function update(Request $request, Employe $employe)
    {
        if ($r = $this->p('modifier')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'matricule' => 'required|string|max:50|unique:employes,matricule,' . $employe->id,
            'nom' => 'required|string|max:120',
            'prenom' => 'required|string|max:120',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:50',
            'poste_libelle' => 'nullable|string|max:120',
            'date_embauche' => 'nullable|date',
            'salaire_mensuel' => 'nullable|numeric|min:0',
            'user_id' => 'nullable|exists:users,id',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }
        $employe->update([...$v->validated(), 'user_update' => Auth::id()]);

        return redirect()->route('employes.index')->with('success', 'Employé mis à jour.');
    }

    public function storeConge(Request $request, Employe $employe)
    {
        if ($r = $this->p('modifier')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'type_conge' => 'required|in:annuel,maladie,sans_solde,exceptionnel',
            'motif' => 'nullable|string',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v);
        }
        $employe->conges()->create([...$v->validated(), 'statut' => 'en_attente', 'user_insert' => Auth::id()]);

        return redirect()->route('employes.show', $employe)->with('success', 'Demande de congé enregistrée.');
    }

    public function updateCongeStatut(Request $request, Conge $conge)
    {
        if ($r = $this->p('modifier')) {
            return $r;
        }
        $request->validate(['statut' => 'required|in:en_attente,approuve,refuse']);
        $conge->update([
            'statut' => $request->statut,
            'commentaire_rh' => $request->commentaire_rh,
            'user_update' => Auth::id(),
        ]);

        return redirect()->route('employes.show', $conge->employe_id)->with('success', 'Statut du congé mis à jour.');
    }

    public function desactivate(Employe $employe)
    {
        if ($r = $this->p('desactiver')) {
            return $r;
        }
        $employe->update(['actif' => 0, 'user_update' => Auth::id()]);

        return redirect()->route('employes.index')->with('success', 'Employé archivé.');
    }
}
