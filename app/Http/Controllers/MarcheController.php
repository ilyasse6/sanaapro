<?php

namespace App\Http\Controllers;

use App\Models\Caution;
use App\Models\Marche;
use App\Models\MarcheDocument;
use App\Models\MarcheLot;
use App\Support\MarchesPublicsConsultationImporter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MarcheController extends Controller
{
    private function perm(string $a): ?\Illuminate\Http\RedirectResponse
    {
        $u = Auth::user();
        if (! $u) {
            return redirect()->route('login')->with('error', 'Connexion requise.');
        }
        if (! $u->hasPermission('Gestion des Marchés & Cautions', $a)) {
            return redirect()->back()->with('error', 'Permission insuffisante.');
        }

        return null;
    }

    public function index()
    {
        if ($r = $this->perm('consulter')) {
            return $r;
        }
        $marches = Marche::with('client')->withCount('cautions')->where('actif', 1)->orderByDesc('created_at')->paginate(12);

        return view('user.marche.index', compact('marches'));
    }

    public function create()
    {
        if ($r = $this->perm('ajouter')) {
            return $r;
        }
        return view('user.marche.create');
    }

    /**
     * Récupère les champs d'une fiche consultation marchespublics.gov.ma (span idEntrepriseConsultationSummary_*).
     */
    public function importConsultation(Request $request, MarchesPublicsConsultationImporter $importer): JsonResponse
    {
        $u = Auth::user();
        if (! $u || (! $u->hasPermission('Gestion des Marchés & Cautions', 'ajouter') && ! $u->hasPermission('Gestion des Marchés & Cautions', 'modifier'))) {
            return response()->json(['ok' => false, 'error' => 'Permission insuffisante.'], 403);
        }
        $request->validate([
            'url' => 'required|url|max:2000',
        ]);
        $result = $importer->importFromUrl($request->input('url', ''));

        return response()->json($result);
    }

    public function store(Request $request)
    {
        if ($r = $this->perm('ajouter')) {
            return $r;
        }
        $request->merge([
            'client_id' => $request->filled('client_id') ? $request->client_id : null,
        ]);
        $v = Validator::make($request->all(), [
            'intitule' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'objet' => 'nullable|string',
            'statut' => 'nullable|in:brouillon,en_cours,attribue,execution,cloture,annule',
            'date_ouverture' => 'nullable|date',
            'date_limite' => 'nullable|date',
            'date_limite_plis' => 'nullable|date',
            'montant_estime' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'source_url' => 'nullable|string|max:2000',
            'reference_consultation' => 'nullable|string|max:120',
            'acheteur_public' => 'nullable|string',
            'type_annonce' => 'nullable|string|max:255',
            'procedure_text' => 'nullable|string',
            'categorie_principale' => 'nullable|string|max:120',
            'allotissement' => 'nullable|string|max:120',
            'lieu_execution' => 'nullable|string',
            'domaines_activite' => 'nullable|string',
            'adresse_retrait_dossiers' => 'nullable|string',
            'adresse_depot_offres' => 'nullable|string',
            'lieu_ouverture_plis' => 'nullable|string',
            'prix_acquisition_plans' => 'nullable|numeric|min:0',
            'contact_nom' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_telephone' => 'nullable|string|max:80',
            'contact_fax' => 'nullable|string|max:80',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }

        $data = $v->validated();
        unset($data['client_id']);
        Marche::create(array_merge($data, [
            'reference' => Marche::genererReference(),
            'client_id' => $request->filled('client_id') ? $request->client_id : null,
            'statut' => $request->statut ?? 'brouillon',
            'actif' => 1,
            'user_insert' => Auth::id(),
        ]));

        return redirect()->route('marches.index')->with('success', 'Marché créé.');
    }

    public function show(Marche $marche)
    {
        if ($r = $this->perm('consulter')) {
            return $r;
        }
        $marche->load(['client', 'lots', 'documents', 'cautions']);

        return view('user.marche.show', compact('marche'));
    }

    public function edit(Marche $marche)
    {
        if ($r = $this->perm('modifier')) {
            return $r;
        }
        return view('user.marche.edit', compact('marche'));
    }

    public function update(Request $request, Marche $marche)
    {
        if ($r = $this->perm('modifier')) {
            return $r;
        }
        $request->merge([
            'client_id' => $request->filled('client_id') ? $request->client_id : null,
        ]);
        $v = Validator::make($request->all(), [
            'intitule' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'objet' => 'nullable|string',
            'statut' => 'required|in:brouillon,en_cours,attribue,execution,cloture,annule',
            'date_ouverture' => 'nullable|date',
            'date_limite' => 'nullable|date',
            'date_limite_plis' => 'nullable|date',
            'date_attribution' => 'nullable|date',
            'montant_estime' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'source_url' => 'nullable|string|max:2000',
            'reference_consultation' => 'nullable|string|max:120',
            'acheteur_public' => 'nullable|string',
            'type_annonce' => 'nullable|string|max:255',
            'procedure_text' => 'nullable|string',
            'categorie_principale' => 'nullable|string|max:120',
            'allotissement' => 'nullable|string|max:120',
            'lieu_execution' => 'nullable|string',
            'domaines_activite' => 'nullable|string',
            'adresse_retrait_dossiers' => 'nullable|string',
            'adresse_depot_offres' => 'nullable|string',
            'lieu_ouverture_plis' => 'nullable|string',
            'prix_acquisition_plans' => 'nullable|numeric|min:0',
            'contact_nom' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_telephone' => 'nullable|string|max:80',
            'contact_fax' => 'nullable|string|max:80',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }

        $data = $v->validated();
        $data['client_id'] = $request->filled('client_id') ? $request->client_id : null;
        $data['user_update'] = Auth::id();
        $marche->update($data);

        return redirect()->route('marches.show', $marche)->with('success', 'Marché mis à jour.');
    }

    public function storeLot(Request $request, Marche $marche)
    {
        if ($r = $this->perm('modifier')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'montant' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v);
        }
        $marche->lots()->create($v->validated());

        return redirect()->route('marches.show', $marche)->with('success', 'Lot ajouté.');
    }

    public function destroyLot(MarcheLot $lot)
    {
        if ($r = $this->perm('modifier')) {
            return $r;
        }
        $mid = $lot->marche_id;
        $lot->delete();

        return redirect()->route('marches.show', $mid)->with('success', 'Lot supprimé.');
    }

    public function storeCaution(Request $request, Marche $marche)
    {
        if ($r = $this->perm('modifier')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'type_caution' => 'required|in:provisoire,definitive,retenue_garantie,autre',
            'montant' => 'required|numeric|min:0',
            'organisme' => 'nullable|string|max:255',
            'numero_garantie' => 'nullable|string|max:120',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'nullable|in:active,restituee,appelee,expiree',
            'notes' => 'nullable|string',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v);
        }
        $d = $v->validated();
        $d['statut'] = $request->input('statut', 'active');
        $d['user_insert'] = Auth::id();
        $marche->cautions()->create($d);

        return redirect()->route('marches.show', $marche)->with('success', 'Caution enregistrée.');
    }

    public function updateCaution(Request $request, Caution $caution)
    {
        if ($r = $this->perm('modifier')) {
            return $r;
        }
        $v = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'type_caution' => 'required|in:provisoire,definitive,retenue_garantie,autre',
            'montant' => 'required|numeric|min:0',
            'organisme' => 'nullable|string|max:255',
            'numero_garantie' => 'nullable|string|max:120',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'statut' => 'required|in:active,restituee,appelee,expiree',
            'notes' => 'nullable|string',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withErrors($v);
        }
        $caution->update([...$v->validated(), 'user_update' => Auth::id()]);
        $mid = $caution->marche_id;

        return redirect()->route('marches.show', $mid)->with('success', 'Caution mise à jour.');
    }

    public function destroyCaution(Caution $caution)
    {
        if ($r = $this->perm('modifier')) {
            return $r;
        }
        $mid = $caution->marche_id;
        $caution->delete();

        return redirect()->route('marches.show', $mid)->with('success', 'Caution supprimée.');
    }

    public function storeDocument(Request $request, Marche $marche)
    {
        if ($r = $this->perm('modifier')) {
            return $r;
        }
        $request->validate([
            'fichier' => 'required|file|max:10240',
            'type_document' => 'nullable|string|max:80',
        ]);
        $file = $request->file('fichier');
        $path = $file->store('marche_documents', 'public');
        $marche->documents()->create([
            'nom_fichier' => $file->getClientOriginalName(),
            'chemin' => $path,
            'mime' => $file->getMimeType(),
            'taille_octets' => $file->getSize(),
            'type_document' => $request->type_document,
            'user_insert' => Auth::id(),
        ]);

        return redirect()->route('marches.show', $marche)->with('success', 'Document ajouté.');
    }

    public function destroyDocument(MarcheDocument $document)
    {
        if ($r = $this->perm('modifier')) {
            return $r;
        }
        $mid = $document->marche_id;
        if ($document->chemin && Storage::disk('public')->exists($document->chemin)) {
            Storage::disk('public')->delete($document->chemin);
        }
        $document->delete();

        return redirect()->route('marches.show', $mid)->with('success', 'Document supprimé.');
    }

    public function desactivate(Marche $marche)
    {
        if ($r = $this->perm('desactiver')) {
            return $r;
        }
        $marche->update(['actif' => 0, 'user_update' => Auth::id()]);

        return redirect()->route('marches.index')->with('success', 'Marché archivé.');
    }

    /**
     * Décision sur l’offre : accepter (avec date de départ) ou refuser.
     */
    public function decisionOffre(Request $request, Marche $marche)
    {
        if ($r = $this->perm('modifier')) {
            return $r;
        }

        $request->validate([
            'decision' => 'required|in:accepte,refuse',
            'date_depart' => 'required_if:decision,accepte|nullable|date',
        ], [
            'date_depart.required_if' => 'La date de départ est obligatoire pour accepter le marché.',
        ]);

        if ($request->decision === 'accepte') {
            $marche->update([
                'decision_offre' => 'accepte',
                'date_depart' => $request->date_depart,
                'user_update' => Auth::id(),
            ]);

            return redirect()->route('marches.show', $marche)->with('success', 'Offre acceptée. L’échéancier (4 tranches de 3 mois) est calculé à partir de la date de départ.');
        }

        $marche->update([
            'decision_offre' => 'refuse',
            'date_depart' => null,
            'user_update' => Auth::id(),
        ]);

        return redirect()->route('marches.show', $marche)->with('success', 'Offre refusée.');
    }
}
