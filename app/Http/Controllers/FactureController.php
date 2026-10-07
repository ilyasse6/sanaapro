<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Devis;
use App\Models\Facture;
use App\Models\FactureLigne;
use App\Support\ClientSaisi;
use App\Support\SocieteBranding;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FactureController extends Controller
{
    private function ensure(string $action): ?\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Connexion requise.');
        }
        if (! $user->hasPermission('Gestion des Factures', $action)) {
            return redirect()->back()->with('error', 'Permission insuffisante.');
        }

        return null;
    }

    public function index(Request $request)
    {
        if ($r = $this->ensure('consulter')) {
            return $r;
        }

        $query = Facture::with(['client', 'devis', 'lignes'])->where('actif', 1);

        if ($request->filled('numero')) {
            $query->where('numero_facture', 'like', '%'.$request->numero.'%');
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date_emission', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_emission', '<=', $request->date_to);
        }

        $factures = $query->orderByDesc('created_at')->paginate(15);
        $clients = Client::where('actif', 1)->orderBy('nom')->get();

        return view('user.facture.index', compact('factures', 'clients'));
    }

    public function indexDesactives(Request $request)
    {
        if ($r = $this->ensure('consulter')) {
            return $r;
        }

        $query = Facture::with(['client', 'devis', 'lignes'])->where('actif', 0);

        if ($request->filled('numero')) {
            $query->where('numero_facture', 'like', '%'.$request->numero.'%');
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date_emission', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_emission', '<=', $request->date_to);
        }

        $factures = $query->orderByDesc('updated_at')->paginate(15);
        $clients = Client::orderBy('nom')->get();

        return view('user.facture.index_desactives', compact('factures', 'clients'));
    }

    public function show(Facture $facture)
    {
        if ($r = $this->ensure('consulter')) {
            return $r;
        }
        $facture->load(['client', 'devis', 'lignes']);

        return view('user.facture.show', compact('facture'));
    }

    public function pdf(Facture $facture)
    {
        if ($r = $this->ensure('consulter')) {
            return $r;
        }
        $facture->load(['client', 'devis', 'lignes']);
        $societe = $facture->branding_affiche;

        [$logo_src_for_pdf, $logo_img_pdf_dims] = SocieteBranding::logoPourPdfAvecDefaut($societe, 135, 82);

        $pdf = Pdf::loadView('user.facture.pdf', compact('facture', 'societe', 'logo_src_for_pdf', 'logo_img_pdf_dims'))
            ->setPaper('a4');

        return $pdf->stream('facture-'.$facture->numero_facture.'.pdf');
    }

    public function convertirDepuisDevis(Devis $devi)
    {
        if ($r = $this->ensure('ajouter')) {
            return $r;
        }

        if ($devi->facture) {
            return redirect()->route('factures.show', $devi->facture)
                ->with('info', 'Ce devis a déjà une facture.');
        }

        if (! $devi->actif) {
            return redirect()->back()
                ->with('error', 'Ce devis est désactivé. Réactivez-le avant de créer une facture.');
        }

        if (! in_array($devi->statut, ['accepte', 'envoye'], true)) {
            return redirect()->back()
                ->with('error', 'Convertissez un devis au statut « Envoyé » ou « Accepté » uniquement.');
        }

        $user = Auth::user();

        $nouvelleFactureId = null;

        try {
            DB::transaction(function () use ($devi, $user, &$nouvelleFactureId) {
                $devi->load('lignes');
                $facture = Facture::create([
                    'client_id' => $devi->client_id,
                    'devis_id' => $devi->id,
                    'numero_facture' => Facture::genererNumero($devi->id_entreprise),
                    'date_emission' => now()->toDateString(),
                    'date_echeance' => now()->addDays(30)->toDateString(),
                    'statut' => 'emise',
                    'remise_globale_pct' => 0,
                    'taux_tva' => $devi->taux_tva,
                    'total_ttc' => $devi->montant_ttc,
                    'conditions_paiement' => $devi->conditions_paiement,
                    'notes' => $devi->notes,
                    'objet' => $devi->objet,
                    'actif' => 1,
                    'user_insert' => $user->id,
                ]);
                $nouvelleFactureId = $facture->id;

                $this->enregistrerLigneUnique($facture, trim((string) $devi->objet) ?: Facture::DESIGNATION_DEFAUT);

                $devi->update([
                    'statut' => 'accepte',
                    'user_update' => $user->id,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('FactureController@convertir: '.$e->getMessage());

            return redirect()->back()->with('error', 'Impossible de créer la facture.');
        }

        if ($nouvelleFactureId) {
            SocieteBranding::persistSnapshotForFacture(Facture::findOrFail($nouvelleFactureId));
        }

        $facture = Facture::where('devis_id', $devi->id)->first();

        return redirect()->route('factures.edit', $facture)
            ->with('success', 'Facture générée à partir du devis : le total TTC est déjà rempli, vérifiez la désignation puis enregistrez.');
    }

    /** Champs du formulaire « facture une ligne » (désignation + total TTC). */
    private function reglesFormulaire(bool $statutRequis): array
    {
        return ClientSaisi::rules() + [
            'date_emission' => 'required|date',
            'date_echeance' => 'nullable|date|after_or_equal:date_emission',
            'statut' => ($statutRequis ? 'required' : 'nullable').'|in:brouillon,emise,envoyee,payee_partielle,payee,annulee,avoir',
            'taux_tva' => 'nullable|numeric|min:0|max:100',
            'designation' => 'required|string|max:5000',
            'total_ttc' => 'required|numeric|min:0',
            'client_ice' => 'nullable|string|max:50',
        ];
    }

    private function messagesFormulaire(): array
    {
        return ClientSaisi::messages() + [
            'designation.required' => 'Veuillez saisir la désignation.',
            'total_ttc.required' => 'Veuillez saisir le total TTC.',
        ];
    }

    private function resoudreClient(Request $request, int $userId): Client
    {
        $client = Client::query()->findOrFail(ClientSaisi::resolveId($request, $userId));
        $ice = trim((string) $request->input('client_ice'));
        if ($ice !== '' && $ice !== (string) $client->ice) {
            $client->update(['ice' => $ice]);
        }

        return $client;
    }

    private function enregistrerLigneUnique(Facture $facture, string $designation): void
    {
        $facture->lignes()->delete();
        FactureLigne::create([
            'facture_id' => $facture->id,
            'type_entree' => 'libre',
            'designation' => $designation,
            'quantite' => 1,
            'unite' => 'forfait',
            'prix_unitaire_ht' => Facture::htDepuisTtc((float) $facture->total_ttc, (float) $facture->taux_tva),
            'remise_pct' => 0,
            'ordre' => 0,
        ]);
    }

    public function updateStatut(Request $request, Facture $facture)
    {
        if ($r = $this->ensure('modifier')) {
            return $r;
        }
        if (! $facture->actif) {
            return redirect()->back()->with('error', 'Réactivez la facture avant de modifier son statut.');
        }

        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:brouillon,emise,envoyee,payee_partielle,payee,annulee,avoir',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $facture->update([
            'statut' => $request->statut,
            'user_update' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Statut de la facture mis à jour.');
    }

    public function desactivate($id)
    {
        if ($r = $this->ensure('desactiver')) {
            return $r;
        }
        $f = Facture::findOrFail($id);
        $f->update(['actif' => 0, 'user_update' => Auth::id()]);

        return redirect()->route('factures.index_desactives')->with('success', 'Facture archivée. Elle figure dans la liste des factures désactivées.');
    }

    public function activate($id)
    {
        if ($r = $this->ensure('activer')) {
            return $r;
        }
        $f = Facture::findOrFail($id);
        if ($f->actif) {
            return redirect()->route('factures.index')->with('info', 'Cette facture est déjà active.');
        }
        $f->update(['actif' => 1, 'user_update' => Auth::id()]);

        return redirect()->route('factures.index')->with('success', 'Facture réactivée. Elle est de nouveau dans la liste principale.');
    }

    public function create(Request $request)
    {
        if ($r = $this->ensure('ajouter')) {
            return $r;
        }

        $clients = Client::where('actif', 1)->orderBy('nom')->get();

        $preselectedClientId = null;
        if ($request->filled('client_id')) {
            $cid = (int) $request->query('client_id');
            if ($cid > 0 && Client::where('id', $cid)->where('actif', 1)->exists()) {
                $preselectedClientId = $cid;
            }
        }

        $societe = SocieteBranding::all();
        $numeroApercu = Facture::genererNumero();

        return view('user.facture.create', compact('clients', 'preselectedClientId', 'societe', 'numeroApercu'));
    }

    public function store(Request $request)
    {
        if ($r = $this->ensure('ajouter')) {
            return $r;
        }

        $validator = Validator::make($request->all(), $this->reglesFormulaire(false), $this->messagesFormulaire());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $nouvelleFactureId = null;

        try {
            DB::transaction(function () use ($request, $user, &$nouvelleFactureId) {
                $client = $this->resoudreClient($request, $user->id);
                $facture = Facture::create([
                    'client_id' => $client->id,
                    'devis_id' => null,
                    'numero_facture' => Facture::genererNumero($client->id_entreprise),
                    'date_emission' => $request->date_emission,
                    'date_echeance' => $request->date_echeance ?: null,
                    'statut' => $request->statut ?? 'brouillon',
                    'remise_globale_pct' => 0,
                    'taux_tva' => $request->taux_tva ?? 20,
                    'total_ttc' => round((float) $request->total_ttc, 2),
                    'actif' => 1,
                    'user_insert' => $user->id,
                ]);
                $nouvelleFactureId = $facture->id;

                $this->enregistrerLigneUnique($facture, trim((string) $request->designation));
            });
        } catch (\Throwable $e) {
            Log::error('FactureController@store: '.$e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la création de la facture.')->withInput();
        }

        if ($nouvelleFactureId) {
            SocieteBranding::persistSnapshotForFacture(Facture::findOrFail($nouvelleFactureId));
        }

        return redirect()->route('factures.index')->with('success', 'Facture créée avec succès.');
    }

    public function edit(Facture $facture)
    {
        if ($r = $this->ensure('modifier')) {
            return $r;
        }
        if (! $facture->actif) {
            return redirect()->route('factures.index_desactives')->with('error', 'Cette facture est désactivée. Réactivez-la pour la modifier.');
        }

        $facture->load(['client', 'lignes', 'devis']);
        $clients = Client::where('actif', 1)->orderBy('nom')->get();
        $societe = $facture->branding_affiche;

        return view('user.facture.edit', compact('facture', 'clients', 'societe'));
    }

    public function update(Request $request, Facture $facture)
    {
        if ($r = $this->ensure('modifier')) {
            return $r;
        }
        if (! $facture->actif) {
            return redirect()->route('factures.index_desactives')->with('error', 'Cette facture est désactivée. Réactivez-la pour la modifier.');
        }

        $validator = Validator::make($request->all(), $this->reglesFormulaire(true), $this->messagesFormulaire());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        try {
            DB::transaction(function () use ($request, $facture, $user) {
                $facture->update([
                    'client_id' => $this->resoudreClient($request, $user->id)->id,
                    'date_emission' => $request->date_emission,
                    'date_echeance' => $request->date_echeance ?: null,
                    'statut' => $request->statut,
                    'remise_globale_pct' => 0,
                    'taux_tva' => $request->taux_tva ?? 20,
                    'total_ttc' => round((float) $request->total_ttc, 2),
                    'user_update' => $user->id,
                ]);

                $this->enregistrerLigneUnique($facture, trim((string) $request->designation));
            });
        } catch (\Throwable $e) {
            Log::error('FactureController@update: '.$e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la modification de la facture.')->withInput();
        }

        return redirect()->route('factures.index')->with('success', 'Facture modifiée avec succès.');
    }

    public function duplicate(Facture $facture)
    {
        if ($r = $this->ensure('ajouter')) {
            return $r;
        }
        if (! $facture->actif) {
            return redirect()->back()->with('error', 'Réactivez la facture avant de la dupliquer.');
        }

        $user = Auth::user();

        try {
            $nouvelleId = DB::transaction(function () use ($facture, $user) {
                $facture->load('lignes');
                $copy = $facture->replicate();
                $copy->devis_id = null;
                $copy->branding_snapshot = null;
                $copy->numero_facture = Facture::genererNumero($facture->id_entreprise);
                $copy->date_emission = now()->toDateString();
                $copy->date_echeance = now()->addDays(30)->toDateString();
                $copy->statut = 'brouillon';
                $copy->actif = 1;
                $copy->user_insert = $user->id;
                $copy->user_update = null;
                $copy->save();

                foreach ($facture->lignes as $i => $ligne) {
                    FactureLigne::create([
                        'facture_id' => $copy->id,
                        'type_entree' => $ligne->type_entree,
                        'catalogue_service_id' => $ligne->catalogue_service_id,
                        'catalogue_pack_id' => $ligne->catalogue_pack_id,
                        'designation' => $ligne->designation,
                        'quantite' => $ligne->quantite,
                        'unite' => $ligne->unite,
                        'prix_unitaire_ht' => $ligne->prix_unitaire_ht,
                        'remise_pct' => $ligne->remise_pct,
                        'ordre' => $i,
                    ]);
                }

                return $copy->id;
            });
        } catch (\Throwable $e) {
            Log::error('FactureController@duplicate: '.$e->getMessage());

            return redirect()->back()->with('error', 'Impossible de dupliquer la facture.');
        }

        $parent = $facture->fresh();
        $nouvelle = Facture::findOrFail($nouvelleId);
        SocieteBranding::duplicateSnapshotForFacture($parent, $nouvelle);

        return redirect()->route('factures.edit', $nouvelle)->with('success', 'Facture dupliquée. Le lien vers le devis d’origine n’est pas conservé (nouvelle facture autonome).');
    }

    public function bulkAction(Request $request)
    {
        if ($r = $this->ensure('consulter')) {
            return $r;
        }

        $ids = $request->input('ids', []);
        if (! is_array($ids)) {
            $ids = $ids !== null && $ids !== '' ? [(int) $ids] : [];
        }
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn ($id) => $id > 0)));
        $request->merge(['ids' => $ids]);

        $validator = Validator::make($request->all(), [
            'scope' => 'required|in:actifs,inactifs',
            'ids' => 'required|array|min:1|max:200',
            'ids.*' => 'integer|exists:factures,id',
            'bulk_action' => 'required|string',
        ], [
            'ids.required' => 'Aucune facture sélectionnée.',
            'ids.min' => 'Cochez au moins une facture.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first() ?: 'Sélection ou action invalide.')
                ->withInput();
        }

        $data = $validator->validated();
        $scopeActif = $data['scope'] === 'actifs' ? 1 : 0;
        $ids = $data['ids'];
        $action = $data['bulk_action'];
        $user = Auth::user();

        $allowedStatutActions = [
            'statut_brouillon' => 'brouillon',
            'statut_emise' => 'emise',
            'statut_envoyee' => 'envoyee',
            'statut_payee_partielle' => 'payee_partielle',
            'statut_payee' => 'payee',
            'statut_annulee' => 'annulee',
            'statut_avoir' => 'avoir',
        ];

        if ($action === 'imprimer') {
            $liste = Facture::with(['client', 'devis', 'lignes'])
                ->whereIn('id', $ids)
                ->where('actif', $scopeActif)
                ->get();

            if ($liste->isEmpty()) {
                return redirect()->back()->with('info', 'Aucune facture à imprimer pour cette sélection.');
            }

            $ordered = $liste->sortBy(function ($f) use ($ids) {
                $p = array_search($f->id, $ids, true);

                return $p === false ? 9999 : $p;
            })->values();

            $items = $ordered->map(function (Facture $facture) {
                $remiseGlobaleHt = $facture->remise_globale_pct > 0
                    ? round($facture->montant_lignes_ht * ($facture->remise_globale_pct / 100), 2)
                    : 0;

                return [
                    'facture' => $facture,
                    'societe' => $facture->branding_affiche,
                    'remiseGlobaleHt' => $remiseGlobaleHt,
                ];
            });

            return response()->view('user.facture.print_batch', [
                'items' => $items->all(),
                'retourListe' => $data['scope'] === 'actifs' ? route('factures.index') : route('factures.index_desactives'),
            ]);
        }

        $count = 0;

        if ($action === 'desactiver') {
            if ($data['scope'] !== 'actifs') {
                return redirect()->back()->with('error', 'Action non autorisée pour cette liste.');
            }
            if (! $user->hasPermission('Gestion des Factures', 'desactiver')) {
                return redirect()->back()->with('error', 'Permission insuffisante.');
            }
            $count = Facture::whereIn('id', $ids)->where('actif', 1)->update(['actif' => 0, 'user_update' => $user->id]);
        } elseif ($action === 'activer') {
            if ($data['scope'] !== 'inactifs') {
                return redirect()->back()->with('error', 'Action non autorisée pour cette liste.');
            }
            if (! $user->hasPermission('Gestion des Factures', 'activer')) {
                return redirect()->back()->with('error', 'Permission insuffisante.');
            }
            $count = Facture::whereIn('id', $ids)->where('actif', 0)->update(['actif' => 1, 'user_update' => $user->id]);
        } elseif (isset($allowedStatutActions[$action])) {
            if (! $user->hasPermission('Gestion des Factures', 'modifier')) {
                return redirect()->back()->with('error', 'Permission insuffisante pour modifier les statuts.');
            }
            $statut = $allowedStatutActions[$action];
            $count = Facture::whereIn('id', $ids)->where('actif', $scopeActif)->update(['statut' => $statut, 'user_update' => $user->id]);
        } else {
            return redirect()->back()->with('error', 'Action inconnue.');
        }

        if ($count === 0) {
            return redirect()->back()->with('info', 'Aucune facture modifiée.');
        }

        $message = match ($action) {
            'desactiver' => $count.' facture(s) désactivée(s).',
            'activer' => $count.' facture(s) réactivée(s).',
            default => 'Statut mis à jour pour '.$count.' facture(s).',
        };

        return redirect()->back()->with('success', $message);
    }
}
