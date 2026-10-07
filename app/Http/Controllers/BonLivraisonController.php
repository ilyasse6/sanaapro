<?php

namespace App\Http\Controllers;

use App\Models\BonLivraison;
use App\Models\BonLivraisonLigne;
use App\Models\Client;
use App\Support\SocieteBranding;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BonLivraisonController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de livraison', 'consulter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $query = BonLivraison::with(['client', 'lignes'])->where('actif', 1);
        if ($request->filled('numero')) {
            $query->where('numero', 'like', '%'.$request->numero.'%');
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_from')) {
            $query->where('date_bl', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date_bl', '<=', $request->date_to);
        }

        $bons = $query->orderByDesc('created_at')->paginate(15);
        $clients = Client::where('actif', 1)->orderBy('nom')->get();

        return view('user.bons-livraison.index', compact('bons', 'clients'));
    }

    public function indexDesactives(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de livraison', 'consulter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $query = BonLivraison::with(['client', 'lignes'])->where('actif', 0);
        if ($request->filled('numero')) {
            $query->where('numero', 'like', '%'.$request->numero.'%');
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $bons = $query->orderByDesc('updated_at')->paginate(15);
        $clients = Client::orderBy('nom')->get();

        return view('user.bons-livraison.index_desactives', compact('bons', 'clients'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de livraison', 'ajouter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $clients = Client::where('actif', 1)->orderBy('nom')->get();
        $preselectedClientId = null;
        if ($request->filled('client_id')) {
            $cid = (int) $request->query('client_id');
            if ($cid > 0 && Client::where('id', $cid)->where('actif', 1)->exists()) {
                $preselectedClientId = $cid;
            }
        }

        return view('user.bons-livraison.create', compact('clients', 'preselectedClientId'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de livraison', 'ajouter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'date_bl' => 'required|date',
            'lieu' => 'nullable|string|max:255',
            'numero_commande' => 'nullable|string|max:120',
            'reference_client' => 'nullable|string|max:120',
            'contact_client' => 'nullable|string|max:255',
            'emis_par' => 'nullable|string|max:255',
            'informations_additionnelles' => 'nullable|string|max:5000',
            'recu_le' => 'nullable|date',
            'livre_le' => 'nullable|date',
            'statut' => 'nullable|in:brouillon,valide,annule',
            'lignes' => 'required|array|min:1',
            'lignes.*.reference_produit' => 'nullable|string|max:120',
            'lignes.*.designation' => 'required|string|max:500',
            'lignes.*.quantite' => 'required|numeric|min:0.001',
            'lignes.*.unite' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $user) {
                $client = Client::query()->findOrFail($request->client_id);
                $bl = BonLivraison::create([
                    'client_id' => $request->client_id,
                    'numero' => BonLivraison::genererNumero($client->id_entreprise),
                    'date_bl' => $request->date_bl,
                    'lieu' => $request->lieu,
                    'numero_commande' => $request->numero_commande,
                    'reference_client' => $request->reference_client,
                    'contact_client' => $request->contact_client,
                    'emis_par' => $request->emis_par ?? trim(($user->prenom ?? '').' '.($user->nom ?? '')),
                    'informations_additionnelles' => $request->informations_additionnelles,
                    'recu_le' => $request->recu_le,
                    'livre_le' => $request->livre_le,
                    'statut' => $request->statut ?? 'brouillon',
                    'actif' => 1,
                    'user_insert' => $user->id,
                ]);

                foreach ($request->lignes as $i => $l) {
                    BonLivraisonLigne::create([
                        'bon_livraison_id' => $bl->id,
                        'reference_produit' => $l['reference_produit'] ?? null,
                        'designation' => $l['designation'],
                        'quantite' => $l['quantite'],
                        'unite' => $l['unite'] ?? 'pièce',
                        'ordre' => $i,
                    ]);
                }

                SocieteBranding::persistSnapshotForBonLivraison($bl);
            });
        } catch (\Exception $e) {
            Log::error('BonLivraisonController@store: '.$e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la création du bon de livraison.')->withInput();
        }

        return redirect()->route('bons-livraison.index')->with('success', 'Bon de livraison créé.');
    }

    public function show(BonLivraison $bonLivraison)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de livraison', 'consulter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $bonLivraison->load(['client', 'lignes']);
        $societe = $bonLivraison->branding_affiche;

        return view('user.bons-livraison.show', compact('bonLivraison', 'societe'));
    }

    public function pdf(BonLivraison $bonLivraison)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de livraison', 'consulter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $bonLivraison->load(['client', 'lignes']);
        $societe = $bonLivraison->branding_affiche;
        $logo_src_for_pdf = SocieteBranding::logoSrcForDomPdf($societe);
        $logo_img_pdf_dims = SocieteBranding::logoDisplayDimensionsForPdf($societe);

        $pdf = Pdf::loadView('user.bons-livraison.pdf', compact(
            'bonLivraison',
            'societe',
            'logo_src_for_pdf',
            'logo_img_pdf_dims'
        ));

        return $pdf->stream('bl-'.$bonLivraison->numero.'.pdf');
    }

    public function edit(BonLivraison $bonLivraison)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de livraison', 'modifier')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }
        if (! $bonLivraison->actif) {
            return redirect()->route('bons-livraison.index_desactives')->with('error', 'Ce document est désactivé. Réactivez-le pour le modifier.');
        }

        $bonLivraison->load(['client', 'lignes']);
        $clients = Client::where('actif', 1)->orderBy('nom')->get();

        return view('user.bons-livraison.edit', compact('bonLivraison', 'clients'));
    }

    public function update(Request $request, BonLivraison $bonLivraison)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de livraison', 'modifier')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }
        if (! $bonLivraison->actif) {
            return redirect()->route('bons-livraison.index_desactives')->with('error', 'Document désactivé.');
        }

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'date_bl' => 'required|date',
            'lieu' => 'nullable|string|max:255',
            'numero_commande' => 'nullable|string|max:120',
            'reference_client' => 'nullable|string|max:120',
            'contact_client' => 'nullable|string|max:255',
            'emis_par' => 'nullable|string|max:255',
            'informations_additionnelles' => 'nullable|string|max:5000',
            'recu_le' => 'nullable|date',
            'livre_le' => 'nullable|date',
            'statut' => 'required|in:brouillon,valide,annule',
            'lignes' => 'required|array|min:1',
            'lignes.*.reference_produit' => 'nullable|string|max:120',
            'lignes.*.designation' => 'required|string|max:500',
            'lignes.*.quantite' => 'required|numeric|min:0.001',
            'lignes.*.unite' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $bonLivraison, $user) {
                $bonLivraison->update([
                    'client_id' => $request->client_id,
                    'date_bl' => $request->date_bl,
                    'lieu' => $request->lieu,
                    'numero_commande' => $request->numero_commande,
                    'reference_client' => $request->reference_client,
                    'contact_client' => $request->contact_client,
                    'emis_par' => $request->emis_par,
                    'informations_additionnelles' => $request->informations_additionnelles,
                    'recu_le' => $request->recu_le,
                    'livre_le' => $request->livre_le,
                    'statut' => $request->statut,
                    'user_update' => $user->id,
                ]);

                $bonLivraison->lignes()->delete();
                foreach ($request->lignes as $i => $l) {
                    BonLivraisonLigne::create([
                        'bon_livraison_id' => $bonLivraison->id,
                        'reference_produit' => $l['reference_produit'] ?? null,
                        'designation' => $l['designation'],
                        'quantite' => $l['quantite'],
                        'unite' => $l['unite'] ?? 'pièce',
                        'ordre' => $i,
                    ]);
                }

                SocieteBranding::persistSnapshotForBonLivraison($bonLivraison->fresh());
            });
        } catch (\Exception $e) {
            Log::error('BonLivraisonController@update: '.$e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.')->withInput();
        }

        return redirect()->route('bons-livraison.show', $bonLivraison)->with('success', 'Bon de livraison mis à jour.');
    }

    public function desactivate($id)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de livraison', 'desactiver')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $bl = BonLivraison::findOrFail($id);
        $bl->update(['actif' => 0, 'user_update' => $user->id]);

        return redirect()->route('bons-livraison.index')->with('success', 'Bon de livraison désactivé.');
    }

    public function activate($id)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de livraison', 'activer')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        $bl = BonLivraison::findOrFail($id);
        $bl->update(['actif' => 1, 'user_update' => $user->id]);

        return redirect()->route('bons-livraison.index')->with('success', 'Bon de livraison réactivé.');
    }

    public function duplicate(BonLivraison $bonLivraison)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Bons de livraison', 'ajouter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }
        if (! $bonLivraison->actif) {
            return redirect()->back()->with('error', 'Réactivez le document avant de le dupliquer.');
        }

        $bonLivraison->load('lignes');
        $new = $bonLivraison->replicate();
        $new->branding_snapshot = null;
        $new->numero = BonLivraison::genererNumero($bonLivraison->id_entreprise);
        $new->date_bl = now()->toDateString();
        $new->statut = 'brouillon';
        $new->recu_le = null;
        $new->livre_le = null;
        $new->user_insert = $user->id;
        $new->user_update = null;
        $new->save();

        foreach ($bonLivraison->lignes as $ligne) {
            $new->lignes()->create($ligne->only([
                'reference_produit', 'designation', 'quantite', 'unite', 'ordre',
            ]));
        }

        if (is_array($bonLivraison->branding_snapshot) && $bonLivraison->branding_snapshot !== []) {
            $snap = SocieteBranding::duplicateSnapshotFilesBonLivraison($bonLivraison, $new, $bonLivraison->branding_snapshot);
            $new->forceFill(['branding_snapshot' => $snap])->save();
        } else {
            SocieteBranding::persistSnapshotForBonLivraison($new);
        }

        return redirect()->route('bons-livraison.edit', $new)->with('success', 'Bon dupliqué. Modifiez puis enregistrez.');
    }
}
