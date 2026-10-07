<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ComptaMouvement;
use App\Models\Employe;
use App\Models\Facture;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ComptaController extends Controller
{
    private function p(string $a): ?\Illuminate\Http\RedirectResponse
    {
        $u = Auth::user();
        if (! $u) {
            return redirect()->route('login')->with('error', 'Connexion requise.');
        }
        if (! $u->hasPermission('Gestion de la Comptabilité', $a)) {
            return redirect()->back()->with('error', 'Permission insuffisante.');
        }

        return null;
    }

    public function index()
    {
        if ($r = $this->p('consulter')) {
            return $r;
        }

        $debut = now()->startOfMonth();
        $fin = now()->endOfMonth();

        $entrees = (float) ComptaMouvement::whereIn('type', ['entree', 'paiement_client'])
            ->whereBetween('date_operation', [$debut, $fin])->sum('montant');
        $sorties = (float) ComptaMouvement::whereIn('type', ['depense', 'paiement_fournisseur', 'salaire'])
            ->whereBetween('date_operation', [$debut, $fin])->sum('montant');

        $impayees = Facture::where('actif', 1)
            ->whereIn('statut', ['emise', 'envoyee', 'payee_partielle'])
            ->with(['client', 'lignes'])
            ->orderBy('date_echeance')
            ->limit(10)
            ->get();

        return view('user.compta.index', compact('entrees', 'sorties', 'impayees'));
    }

    public function mouvements()
    {
        if ($r = $this->p('consulter')) {
            return $r;
        }
        $mouvements = ComptaMouvement::with(['client', 'fournisseur', 'facture', 'employe'])
            ->orderByDesc('date_operation')
            ->paginate(30);
        $clients = Client::where('actif', 1)->orderBy('nom')->get();
        $fournisseurs = Fournisseur::where('actif', 1)->orderBy('raison_sociale')->get();
        $factures = Facture::where('actif', 1)->orderByDesc('id')->limit(100)->get();
        $employes = Employe::where('actif', 1)->orderBy('nom')->get();

        return view('user.compta.mouvements', compact('mouvements', 'clients', 'fournisseurs', 'factures', 'employes'));
    }

    public function storeMouvement(Request $request)
    {
        if ($r = $this->p('ajouter')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'type' => 'required|in:entree,depense,paiement_client,paiement_fournisseur,credit,salaire',
            'montant' => 'required|numeric|min:0.01',
            'date_operation' => 'required|date',
            'libelle' => 'required|string|max:255',
            'mode_paiement' => 'nullable|string|max:50',
            'reference_externe' => 'nullable|string|max:120',
            'client_id' => 'nullable|exists:clients,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'facture_id' => 'nullable|exists:factures,id',
            'employe_id' => 'nullable|exists:employes,id',
            'notes' => 'nullable|string',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }

        ComptaMouvement::create([...$v->validated(), 'user_insert' => Auth::id()]);

        if ($request->type === 'paiement_client' && $request->filled('facture_id')) {
            $facture = Facture::find($request->facture_id);
            if ($facture && in_array($facture->statut, ['emise', 'envoyee', 'payee_partielle'], true)) {
                $facture->update(['statut' => 'payee', 'user_update' => Auth::id()]);
            }
        }

        return redirect()->route('compta.mouvements')->with('success', 'Écriture enregistrée.');
    }
}
