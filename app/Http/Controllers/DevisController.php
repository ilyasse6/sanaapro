<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\CatalogueService;
use App\Models\Client;
use App\Models\Devis;
use App\Models\DevisLigne;
use App\Models\Pack;
use App\Support\ClientSaisi;
use App\Support\SocieteBranding;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DevisController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'consulter')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter les devis.');
        }

        $query = Devis::with(['client', 'lignes', 'facture'])->where('actif', 1);

        if ($request->filled('numero')) {
            $query->where('numero_devis', 'like', '%'.$request->numero.'%');
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_from')) {
            $query->where('date_emission', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date_emission', '<=', $request->date_to);
        }

        $devis = $query->orderBy('created_at', 'desc')->paginate(15);
        $clients = Client::where('actif', 1)->orderBy('nom')->get();

        return view('user.devis.index', compact('devis', 'clients'));
    }

    public function indexDesactives(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'consulter')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter les devis.');
        }

        $query = Devis::with(['client', 'lignes'])->where('actif', 0);

        if ($request->filled('numero')) {
            $query->where('numero_devis', 'like', '%'.$request->numero.'%');
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_from')) {
            $query->where('date_emission', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date_emission', '<=', $request->date_to);
        }

        $devis = $query->orderBy('updated_at', 'desc')->paginate(15);
        $clients = Client::orderBy('nom')->get();

        return view('user.devis.index_desactives', compact('devis', 'clients'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'ajouter')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de créer des devis.');
        }

        $clients = Client::where('actif', 1)->orderBy('nom')->get();
        $services = CatalogueService::where('actif', 1)->orderBy('nom')->get();
        $packs = Pack::where('actif', 1)->orderBy('nom')->get();

        $preselectedClientId = null;
        if ($request->filled('client_id')) {
            $cid = (int) $request->query('client_id');
            if ($cid > 0 && Client::where('id', $cid)->where('actif', 1)->exists()) {
                $preselectedClientId = $cid;
            }
        }

        $apercu = $this->apercuDevisData($services);
        $apercu['numero'] = Devis::genererNumero(Auth::user()?->id_entreprise);

        return view('user.devis.create', compact('clients', 'services', 'packs', 'preselectedClientId', 'apercu'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'ajouter')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de créer des devis.');
        }

        $validator = Validator::make($request->all(), ClientSaisi::rules() + [
            'date_emission' => 'required|date',
            'date_validite' => 'required|date|after_or_equal:date_emission',
            'statut' => 'nullable|in:brouillon,envoye,accepte,refuse',
            'remise_globale_pct' => 'nullable|numeric|min:0|max:100',
            'marge_pct' => 'nullable|numeric|min:0|max:1000',
            'taux_tva' => 'nullable|numeric|min:0|max:100',
            'conditions_paiement' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:2000',
            'objet' => 'nullable|string|max:500',
            'lignes' => 'required|array|min:1',
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.quantite' => 'required|numeric|min:0.001',
            'lignes.*.unite' => 'nullable|string|max:30',
            'lignes.*.prix_unitaire_ht' => 'required|numeric|min:0',
            'lignes.*.remise_pct' => 'nullable|numeric|min:0|max:100',
            'lignes.*.largeur' => 'nullable|numeric|min:0|max:99999',
            'lignes.*.hauteur' => 'nullable|numeric|min:0|max:99999',
            'lignes.*.calcul' => 'nullable|string|max:2000',
        ], ClientSaisi::messages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $user) {
                $client = Client::query()->findOrFail(ClientSaisi::resolveId($request, $user->id));
                $devis = Devis::create([
                    'client_id' => $client->id,
                    'numero_devis' => Devis::genererNumero($client->id_entreprise),
                    'date_emission' => $request->date_emission,
                    'date_validite' => $request->date_validite,
                    'statut' => $request->statut ?? 'brouillon',
                    'remise_globale_pct' => $request->remise_globale_pct ?? 0,
                    'marge_pct' => $request->marge_pct ?? 0,
                    'taux_tva' => $request->taux_tva ?? 20,
                    'conditions_paiement' => $request->conditions_paiement,
                    'notes' => $request->notes,
                    'objet' => $request->objet,
                    'actif' => 1,
                    'user_insert' => $user->id,
                ]);

                foreach ($request->lignes as $i => $l) {
                    DevisLigne::create(['devis_id' => $devis->id] + $this->ligneAttributs($l, $i, $request));
                }

                SocieteBranding::persistSnapshotForDevis($devis);
            });
        } catch (\Exception $e) {
            Log::error('DevisController@store: '.$e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la création du devis.')->withInput();
        }

        return redirect()->route('devis.index')->with('success', 'Devis créé avec succès.');
    }

    public function show(Devis $devi)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'consulter')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter ce devis.');
        }

        $devi->load(['client', 'lignes.catalogueService', 'facture']);
        $societe = $devi->branding_affiche;

        return view('user.devis.show', compact('devi', 'societe'));
    }

    public function pdf(Devis $devi)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'consulter')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter ce devis.');
        }

        $devi->load(['client', 'lignes.catalogueService', 'facture']);
        $societe = $devi->branding_affiche;

        [$logo_src_for_pdf, $logo_img_pdf_dims] = SocieteBranding::logoPourPdfAvecDefaut($societe, 140, 80);

        $pdf = Pdf::loadView('user.devis.pdf', compact('devi', 'societe', 'logo_src_for_pdf', 'logo_img_pdf_dims'))
            ->setPaper('a4');

        return $pdf->stream('devis-'.$devi->numero_devis.'.pdf');
    }

    public function edit(Devis $devi)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'modifier')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de modifier les devis.');
        }
        if (! $devi->actif) {
            return redirect()->route('devis.index_desactives')->with('error', 'Ce devis est désactivé. Réactivez-le pour le modifier.');
        }

        $devi->load(['client', 'lignes']);
        $clients = Client::where('actif', 1)->orderBy('nom')->get();
        $services = CatalogueService::where('actif', 1)->orderBy('nom')->get();
        $packs = Pack::where('actif', 1)->orderBy('nom')->get();

        $apercu = $this->apercuDevisData($services);
        $apercu['numero'] = $devi->numero_devis;

        return view('user.devis.edit', compact('devi', 'clients', 'services', 'packs', 'apercu'));
    }

    /**
     * En-tête, pied de page et schémas produits pour l'aperçu live du devis.
     */
    private function apercuDevisData($services): array
    {
        $branding = SocieteBranding::all(AppSetting::resolvedEntrepriseId());

        return [
            'logo_url' => $branding['logo_url'] ?? asset('assets/imgs/afifalu-logo.png'),
            'titre' => (string) AppSetting::get('devis_entete_titre', SocieteBranding::DEFAULT_DEVIS_ENTETE_TITRE),
            'activite' => (string) AppSetting::get('devis_entete_activite', SocieteBranding::DEFAULT_DEVIS_ENTETE_ACTIVITE),
            'pied_page' => (string) AppSetting::get('devis_pied_page', SocieteBranding::DEFAULT_DEVIS_PIED_PAGE),
            'images' => $services->filter(fn ($s) => ! empty($s->image))
                ->mapWithKeys(fn ($s) => [$s->id => asset($s->image)])
                ->all(),
        ];
    }

    public function update(Request $request, Devis $devi)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'modifier')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de modifier les devis.');
        }
        if (! $devi->actif) {
            return redirect()->route('devis.index_desactives')->with('error', 'Ce devis est désactivé. Réactivez-le pour le modifier.');
        }

        $validator = Validator::make($request->all(), ClientSaisi::rules() + [
            'date_emission' => 'required|date',
            'date_validite' => 'required|date',
            'statut' => 'required|in:brouillon,envoye,accepte,refuse',
            'remise_globale_pct' => 'nullable|numeric|min:0|max:100',
            'marge_pct' => 'nullable|numeric|min:0|max:1000',
            'taux_tva' => 'nullable|numeric|min:0|max:100',
            'conditions_paiement' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:2000',
            'objet' => 'nullable|string|max:500',
            'lignes' => 'required|array|min:1',
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.quantite' => 'required|numeric|min:0.001',
            'lignes.*.unite' => 'nullable|string|max:30',
            'lignes.*.prix_unitaire_ht' => 'required|numeric|min:0',
            'lignes.*.remise_pct' => 'nullable|numeric|min:0|max:100',
            'lignes.*.largeur' => 'nullable|numeric|min:0|max:99999',
            'lignes.*.hauteur' => 'nullable|numeric|min:0|max:99999',
            'lignes.*.calcul' => 'nullable|string|max:2000',
        ], ClientSaisi::messages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $devi, $user) {
                $devi->update([
                    'client_id' => ClientSaisi::resolveId($request, $user->id),
                    'date_emission' => $request->date_emission,
                    'date_validite' => $request->date_validite,
                    'statut' => $request->statut,
                    'remise_globale_pct' => $request->remise_globale_pct ?? 0,
                    'marge_pct' => $request->marge_pct ?? 0,
                    'taux_tva' => $request->taux_tva ?? 20,
                    'conditions_paiement' => $request->conditions_paiement,
                    'notes' => $request->notes,
                    'objet' => $request->objet,
                    'user_update' => $user->id,
                ]);

                $devi->lignes()->delete();
                foreach ($request->lignes as $i => $l) {
                    DevisLigne::create(['devis_id' => $devi->id] + $this->ligneAttributs($l, $i, $request));
                }

                SocieteBranding::persistSnapshotForDevis($devi->fresh());
            });
        } catch (\Exception $e) {
            Log::error('DevisController@update: '.$e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la modification du devis.')->withInput();
        }

        return redirect()->route('devis.index')->with('success', 'Devis modifié avec succès.');
    }

    private function ligneAttributs(array $l, int $ordre, Request $request): array
    {
        $largeur = ($l['largeur'] ?? '') !== '' ? $l['largeur'] : null;
        $hauteur = ($l['hauteur'] ?? '') !== '' ? $l['hauteur'] : null;
        $calcul = DevisLigne::normaliserCalcul($l['calcul'] ?? null);

        return [
            'type_entree' => $l['type_entree'] ?? 'libre',
            'catalogue_service_id' => $l['catalogue_service_id'] ?? null,
            'catalogue_pack_id' => $l['catalogue_pack_id'] ?? null,
            'designation' => $l['designation'],
            'largeur' => $largeur,
            'hauteur' => $hauteur,
            'calcul_details' => $calcul,
            'quantite' => $l['quantite'],
            'unite' => $l['unite'] ?? 'pièce',
            'prix_unitaire_ht' => DevisLigne::calculerPrixUnitaire($calcul, $largeur, $hauteur, $request->marge_pct ?? 0) ?? $l['prix_unitaire_ht'],
            'remise_pct' => $l['remise_pct'] ?? 0,
            'ordre' => $ordre,
        ];
    }

    public function bulkAction(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'consulter')) {
            return redirect()->back()->with('error', 'Accès refusé.');
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
            'ids.*' => 'integer|exists:devis,id',
            'bulk_action' => 'required|string',
        ], [
            'ids.required' => 'Aucun devis sélectionné.',
            'ids.min' => 'Cochez au moins un devis.',
            'scope.required' => 'Contexte de liste manquant.',
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

        $allowedStatutActions = [
            'statut_brouillon' => 'brouillon',
            'statut_envoye' => 'envoye',
            'statut_accepte' => 'accepte',
            'statut_refuse' => 'refuse',
        ];

        $count = 0;

        if ($action === 'imprimer') {
            $liste = Devis::with(['client', 'lignes.catalogueService'])
                ->whereIn('id', $ids)
                ->where('actif', $scopeActif)
                ->get();

            if ($liste->isEmpty()) {
                return redirect()->back()->with('info', 'Aucun devis à imprimer pour cette sélection.');
            }

            $ordered = $liste->sortBy(function ($d) use ($ids) {
                $p = array_search($d->id, $ids, true);

                return $p === false ? 9999 : $p;
            })->values();

            $items = $ordered->map(function (Devis $devi) {
                return [
                    'devi' => $devi,
                    'societe' => $devi->branding_affiche,
                ];
            });

            return response()->view('user.devis.print_batch', [
                'items' => $items->all(),
                'retourListe' => $data['scope'] === 'actifs' ? route('devis.index') : route('devis.index_desactives'),
            ]);
        }

        if ($action === 'desactiver') {
            if ($data['scope'] !== 'actifs') {
                return redirect()->back()->with('error', 'Action non autorisée pour cette liste.');
            }
            if (! $user->hasPermission('Gestion des Devis', 'desactiver')) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de désactiver les devis.');
            }
            $count = Devis::whereIn('id', $ids)->where('actif', 1)->update(['actif' => 0, 'user_update' => $user->id]);
        } elseif ($action === 'activer') {
            if ($data['scope'] !== 'inactifs') {
                return redirect()->back()->with('error', 'Action non autorisée pour cette liste.');
            }
            if (! $user->hasPermission('Gestion des Devis', 'activer')) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de réactiver les devis.');
            }
            $count = Devis::whereIn('id', $ids)->where('actif', 0)->update(['actif' => 1, 'user_update' => $user->id]);
        } elseif (isset($allowedStatutActions[$action])) {
            if (! $user->hasPermission('Gestion des Devis', 'modifier')) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de modifier le statut des devis.');
            }
            $statut = $allowedStatutActions[$action];
            $count = Devis::whereIn('id', $ids)->where('actif', $scopeActif)->update(['statut' => $statut, 'user_update' => $user->id]);
        } else {
            return redirect()->back()->with('error', 'Action inconnue.');
        }

        if ($count === 0) {
            return redirect()->back()->with('info', 'Aucun devis modifié (aucune ligne correspondante dans la sélection).');
        }

        $message = match ($action) {
            'desactiver' => $count.' devis désactivé(s). Ils sont disponibles dans « Devis désactivés ».',
            'activer' => $count.' devis réactivé(s).',
            default => 'Statut mis à jour pour '.$count.' devis.',
        };

        return redirect()->back()->with('success', $message);
    }

    public function desactivate($id)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'desactiver')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de désactiver les devis.');
        }

        $devi = Devis::findOrFail($id);
        $devi->update(['actif' => 0, 'user_update' => $user->id]);

        return redirect()->route('devis.index')->with('success', 'Devis désactivé. Il apparaît dans la liste des devis désactivés.');
    }

    public function activate($id)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'activer')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de réactiver les devis.');
        }

        $devi = Devis::findOrFail($id);
        if ($devi->actif) {
            return redirect()->route('devis.index')->with('info', 'Ce devis est déjà actif.');
        }
        $devi->update(['actif' => 1, 'user_update' => $user->id]);

        return redirect()->route('devis.index')->with('success', 'Devis réactivé. Il est de nouveau dans la liste principale.');
    }

    public function duplicate(Devis $devi)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission('Gestion des Devis', 'ajouter')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de créer des devis.');
        }
        if (! $devi->actif) {
            return redirect()->back()->with('error', 'Impossible de dupliquer un devis désactivé. Réactivez-le d\'abord.');
        }

        $new = $devi->replicate();
        $new->branding_snapshot = null;
        $new->numero_devis = Devis::genererNumero($devi->id_entreprise);
        $new->date_emission = now()->toDateString();
        $new->date_validite = now()->addDays(30)->toDateString();
        $new->statut = 'brouillon';
        $new->user_insert = $user->id;
        $new->user_update = null;
        $new->created_at = now();
        $new->updated_at = now();
        $new->save();

        foreach ($devi->lignes as $ligne) {
            $new->lignes()->create($ligne->only([
                'type_entree', 'catalogue_service_id', 'catalogue_pack_id',
                'designation', 'largeur', 'hauteur', 'calcul_details',
                'quantite', 'unite', 'prix_unitaire_ht', 'remise_pct', 'ordre',
            ]));
        }

        if (is_array($devi->branding_snapshot) && $devi->branding_snapshot !== []) {
            $snap = SocieteBranding::duplicateSnapshotFiles($devi, $new, $devi->branding_snapshot);
            $new->forceFill(['branding_snapshot' => $snap])->save();
        } else {
            SocieteBranding::persistSnapshotForDevis($new);
        }

        return redirect()->route('devis.edit', $new)->with('success', 'Devis dupliqué. Vous pouvez modifier le nouveau devis.');
    }
}
