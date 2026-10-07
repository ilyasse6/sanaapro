<?php

namespace App\Http\Controllers;

use App\Models\BonCommande;
use App\Models\BonCommandeLigne;
use App\Models\Fournisseur;
use App\Support\SocieteBranding;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BonCommandeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de commande', 'consulter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $query = BonCommande::with(['fournisseur', 'lignes'])->where('actif', 1);
        if ($request->filled('numero')) {
            $query->where('numero', 'like', '%'.$request->numero.'%');
        }
        if ($request->filled('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_from')) {
            $query->where('date_bc', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date_bc', '<=', $request->date_to);
        }

        $bons = $query->orderByDesc('created_at')->paginate(15);
        $fournisseurs = Fournisseur::where('actif', 1)->orderBy('raison_sociale')->get();

        return view('user.bons-commande.index', compact('bons', 'fournisseurs'));
    }

    public function indexDesactives(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de commande', 'consulter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $query = BonCommande::with(['fournisseur', 'lignes'])->where('actif', 0);
        if ($request->filled('numero')) {
            $query->where('numero', 'like', '%'.$request->numero.'%');
        }
        if ($request->filled('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }

        $bons = $query->orderByDesc('updated_at')->paginate(15);
        $fournisseurs = Fournisseur::orderBy('raison_sociale')->get();

        return view('user.bons-commande.index_desactives', compact('bons', 'fournisseurs'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de commande', 'ajouter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $fournisseurs = Fournisseur::where('actif', 1)->orderBy('raison_sociale')->get();
        $preselectedFournisseurId = null;
        if ($request->filled('fournisseur_id')) {
            $fid = (int) $request->query('fournisseur_id');
            if ($fid > 0 && Fournisseur::where('id', $fid)->where('actif', 1)->exists()) {
                $preselectedFournisseurId = $fid;
            }
        }

        return view('user.bons-commande.create', compact('fournisseurs', 'preselectedFournisseurId'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de commande', 'ajouter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $validator = Validator::make($request->all(), [
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_bc' => 'required|date',
            'lieu_livraison' => 'nullable|string|max:255',
            'reference_interne' => 'nullable|string|max:120',
            'reference_fournisseur' => 'nullable|string|max:120',
            'contact_fournisseur' => 'nullable|string|max:255',
            'emis_par' => 'nullable|string|max:255',
            'informations_additionnelles' => 'nullable|string|max:5000',
            'date_livraison_souhaitee' => 'nullable|date',
            'statut' => 'nullable|in:brouillon,valide,annule',
            'lignes' => 'required|array|min:1',
            'lignes.*.reference_produit' => 'nullable|string|max:120',
            'lignes.*.designation' => 'required|string|max:500',
            'lignes.*.quantite' => 'required|numeric|min:0.001',
            'lignes.*.unite' => 'nullable|string|max:30',
            'lignes.*.prix_unitaire_ht' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $user) {
                $fournisseur = Fournisseur::query()->findOrFail($request->fournisseur_id);
                $bc = BonCommande::create([
                    'fournisseur_id' => $request->fournisseur_id,
                    'numero' => BonCommande::genererNumero($fournisseur->id_entreprise),
                    'date_bc' => $request->date_bc,
                    'lieu_livraison' => $request->lieu_livraison,
                    'reference_interne' => $request->reference_interne,
                    'reference_fournisseur' => $request->reference_fournisseur,
                    'contact_fournisseur' => $request->contact_fournisseur,
                    'emis_par' => $request->emis_par ?? trim(($user->prenom ?? '').' '.($user->nom ?? '')),
                    'informations_additionnelles' => $request->informations_additionnelles,
                    'date_livraison_souhaitee' => $request->date_livraison_souhaitee,
                    'statut' => $request->statut ?? 'brouillon',
                    'actif' => 1,
                    'user_insert' => $user->id,
                ]);

                foreach ($request->lignes as $i => $l) {
                    BonCommandeLigne::create([
                        'bon_commande_id' => $bc->id,
                        'reference_produit' => $l['reference_produit'] ?? null,
                        'designation' => $l['designation'],
                        'quantite' => $l['quantite'],
                        'unite' => $l['unite'] ?? 'pièce',
                        'prix_unitaire_ht' => isset($l['prix_unitaire_ht']) && $l['prix_unitaire_ht'] !== '' ? $l['prix_unitaire_ht'] : null,
                        'ordre' => $i,
                    ]);
                }

                SocieteBranding::persistSnapshotForBonCommande($bc);
            });
        } catch (\Exception $e) {
            Log::error('BonCommandeController@store: '.$e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la création du bon de commande.')->withInput();
        }

        return redirect()->route('bons-commande.index')->with('success', 'Bon de commande créé.');
    }

    public function show(BonCommande $bon_commande)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de commande', 'consulter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $bonCommande = $bon_commande;
        $bonCommande->load(['fournisseur', 'lignes']);
        $societe = $bonCommande->branding_affiche;

        return view('user.bons-commande.show', compact('bonCommande', 'societe'));
    }

    public function pdf(BonCommande $bon_commande)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de commande', 'consulter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $bonCommande = $bon_commande;
        $bonCommande->load(['fournisseur', 'lignes']);
        $societe = $bonCommande->branding_affiche;
        $logo_src_for_pdf = SocieteBranding::logoSrcForDomPdf($societe);
        $logo_img_pdf_dims = SocieteBranding::logoDisplayDimensionsForPdf($societe);

        $pdf = Pdf::loadView('user.bons-commande.pdf', compact(
            'bonCommande',
            'societe',
            'logo_src_for_pdf',
            'logo_img_pdf_dims'
        ));

        return $pdf->stream('bc-'.$bonCommande->numero.'.pdf');
    }

    public function edit(BonCommande $bon_commande)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de commande', 'modifier')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }
        if (! $bon_commande->actif) {
            return redirect()->route('bons-commande.index_desactives')->with('error', 'Ce document est désactivé. Réactivez-le pour le modifier.');
        }

        $bonCommande = $bon_commande;
        $bonCommande->load(['fournisseur', 'lignes']);
        $fournisseurs = Fournisseur::where('actif', 1)->orderBy('raison_sociale')->get();

        return view('user.bons-commande.edit', compact('bonCommande', 'fournisseurs'));
    }

    public function update(Request $request, BonCommande $bon_commande)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de commande', 'modifier')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }
        if (! $bon_commande->actif) {
            return redirect()->route('bons-commande.index_desactives')->with('error', 'Document désactivé.');
        }

        $validator = Validator::make($request->all(), [
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_bc' => 'required|date',
            'lieu_livraison' => 'nullable|string|max:255',
            'reference_interne' => 'nullable|string|max:120',
            'reference_fournisseur' => 'nullable|string|max:120',
            'contact_fournisseur' => 'nullable|string|max:255',
            'emis_par' => 'nullable|string|max:255',
            'informations_additionnelles' => 'nullable|string|max:5000',
            'date_livraison_souhaitee' => 'nullable|date',
            'statut' => 'required|in:brouillon,valide,annule',
            'lignes' => 'required|array|min:1',
            'lignes.*.reference_produit' => 'nullable|string|max:120',
            'lignes.*.designation' => 'required|string|max:500',
            'lignes.*.quantite' => 'required|numeric|min:0.001',
            'lignes.*.unite' => 'nullable|string|max:30',
            'lignes.*.prix_unitaire_ht' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $bon_commande, $user) {
                $bon_commande->update([
                    'fournisseur_id' => $request->fournisseur_id,
                    'date_bc' => $request->date_bc,
                    'lieu_livraison' => $request->lieu_livraison,
                    'reference_interne' => $request->reference_interne,
                    'reference_fournisseur' => $request->reference_fournisseur,
                    'contact_fournisseur' => $request->contact_fournisseur,
                    'emis_par' => $request->emis_par,
                    'informations_additionnelles' => $request->informations_additionnelles,
                    'date_livraison_souhaitee' => $request->date_livraison_souhaitee,
                    'statut' => $request->statut,
                    'user_update' => $user->id,
                ]);

                $bon_commande->lignes()->delete();
                foreach ($request->lignes as $i => $l) {
                    BonCommandeLigne::create([
                        'bon_commande_id' => $bon_commande->id,
                        'reference_produit' => $l['reference_produit'] ?? null,
                        'designation' => $l['designation'],
                        'quantite' => $l['quantite'],
                        'unite' => $l['unite'] ?? 'pièce',
                        'prix_unitaire_ht' => isset($l['prix_unitaire_ht']) && $l['prix_unitaire_ht'] !== '' ? $l['prix_unitaire_ht'] : null,
                        'ordre' => $i,
                    ]);
                }

                SocieteBranding::persistSnapshotForBonCommande($bon_commande->fresh());
            });
        } catch (\Exception $e) {
            Log::error('BonCommandeController@update: '.$e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.')->withInput();
        }

        return redirect()->route('bons-commande.show', $bon_commande)->with('success', 'Bon de commande mis à jour.');
    }

    public function desactivate($id)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de commande', 'desactiver')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $bc = BonCommande::findOrFail($id);
        $bc->update(['actif' => 0, 'user_update' => $user->id]);

        return redirect()->route('bons-commande.index')->with('success', 'Bon de commande désactivé.');
    }

    public function activate($id)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de commande', 'activer')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $bc = BonCommande::findOrFail($id);
        $bc->update(['actif' => 1, 'user_update' => $user->id]);

        return redirect()->route('bons-commande.index')->with('success', 'Bon de commande réactivé.');
    }

    public function duplicate(BonCommande $bon_commande)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de commande', 'ajouter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }
        if (! $bon_commande->actif) {
            return redirect()->back()->with('error', 'Réactivez le document avant de le dupliquer.');
        }

        $bon_commande->load('lignes');
        $new = $bon_commande->replicate();
        $new->branding_snapshot = null;
        $new->numero = BonCommande::genererNumero($bon_commande->id_entreprise);
        $new->date_bc = now()->toDateString();
        $new->statut = 'brouillon';
        $new->date_livraison_souhaitee = null;
        $new->user_insert = $user->id;
        $new->user_update = null;
        $new->save();

        foreach ($bon_commande->lignes as $ligne) {
            $new->lignes()->create($ligne->only([
                'reference_produit', 'designation', 'quantite', 'unite', 'prix_unitaire_ht', 'ordre',
            ]));
        }

        if (is_array($bon_commande->branding_snapshot) && $bon_commande->branding_snapshot !== []) {
            $snap = SocieteBranding::duplicateSnapshotFilesBonCommande($bon_commande, $new, $bon_commande->branding_snapshot);
            $new->forceFill(['branding_snapshot' => $snap])->save();
        } else {
            SocieteBranding::persistSnapshotForBonCommande($new);
        }

        return redirect()->route('bons-commande.edit', $new)->with('success', 'Bon dupliqué. Modifiez puis enregistrez.');
    }
}
