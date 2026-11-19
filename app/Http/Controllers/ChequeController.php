<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Client;
use App\Models\Facture;
use App\Support\CreditsClients;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class ChequeController extends Controller
{
    private function refus(string $action): ?RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission(CreditClientController::MODULE, $action)) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        return null;
    }

    public function index(Request $request)
    {
        if ($r = $this->refus('consulter')) {
            return $r;
        }

        $statut = $request->input('statut', 'en_cours');
        $recherche = trim((string) $request->input('q', ''));

        $cheques = Cheque::query()
            ->with(['client', 'facture'])
            ->when($statut === 'en_cours', fn ($q) => $q->whereIn('statut', Cheque::STATUTS_EN_COURS))
            ->when($statut === 'a_deposer', fn ($q) => $q->where('statut', 'en_portefeuille')->whereDate('date_echeance', '<=', today()))
            ->when(array_key_exists($statut, Cheque::STATUTS), fn ($q) => $q->where('statut', $statut))
            ->when($request->filled('client_id'), fn ($q) => $q->where('client_id', $request->client_id))
            ->when($request->filled('du'), fn ($q) => $q->whereDate('date_echeance', '>=', $request->du))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('date_echeance', '<=', $request->au))
            ->when($recherche !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('numero', 'like', "%{$recherche}%")
                ->orWhere('banque', 'like', "%{$recherche}%")
                ->orWhere('tireur', 'like', "%{$recherche}%")))
            ->orderByRaw("FIELD(statut, 'en_portefeuille', 'remis', 'impaye', 'encaisse', 'restitue')")
            ->orderBy('date_echeance')
            ->paginate(25)
            ->withQueryString();

        $parStatut = Cheque::query()
            ->groupBy('statut')
            ->selectRaw('statut, COUNT(*) AS nb, SUM(montant) AS total')
            ->get()
            ->keyBy('statut');
        $aDeposer = Cheque::where('statut', 'en_portefeuille')->whereDate('date_echeance', '<=', today());
        $semaine = Cheque::where('statut', 'en_portefeuille')
            ->whereDate('date_echeance', '>', today())
            ->whereDate('date_echeance', '<=', today()->addDays(7));

        $stats = [
            'portefeuille' => $parStatut->get('en_portefeuille'),
            'remis' => $parStatut->get('remis'),
            'impaye' => $parStatut->get('impaye'),
            'a_deposer_nb' => (clone $aDeposer)->count(),
            'a_deposer_total' => (float) (clone $aDeposer)->sum('montant'),
            'semaine_nb' => (clone $semaine)->count(),
            'semaine_total' => (float) (clone $semaine)->sum('montant'),
            'encaisse_mois' => (float) Cheque::where('statut', 'encaisse')
                ->whereBetween('date_encaissement', [now()->startOfMonth(), now()->endOfMonth()])->sum('montant'),
        ];

        $clients = Client::where('actif', 1)->orderBy('nom')->get();
        $facturesOuvertes = Facture::query()
            ->where('actif', 1)
            ->whereIn('statut', ['emise', 'envoyee', 'payee_partielle'])
            ->orderByDesc('date_emission')
            ->get(['id', 'client_id', 'numero_facture']);

        return view('user.cheques.index', compact('cheques', 'stats', 'statut', 'recherche', 'clients', 'facturesOuvertes'));
    }

    public function store(Request $request)
    {
        if ($r = $this->refus('ajouter')) {
            return $r;
        }

        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'facture_id' => ['nullable', Rule::exists('factures', 'id')->where('client_id', $request->client_id)],
            'montant' => 'required|numeric|min:0.01',
            'date_reglement' => 'required|date',
            'cheque_numero' => 'required|string|max:50',
            'cheque_banque' => 'nullable|string|max:120',
            'cheque_tireur' => 'nullable|string|max:191',
            'cheque_echeance' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        CreditsClients::enregistrerReglement(Client::findOrFail($data['client_id']), $data + ['mode' => 'cheque'], Auth::id());

        return redirect()->route('cheques.index')->with('success', 'Chèque n° '.$data['cheque_numero'].' enregistré et imputé au compte du client.');
    }

    public function action(Request $request, Cheque $cheque)
    {
        if ($r = $this->refus('modifier')) {
            return $r;
        }

        $data = $request->validate([
            'action' => 'required|in:remettre,encaisser,impaye,restituer,representer',
            'date' => 'nullable|date',
            'motif' => 'nullable|string|max:255',
        ]);

        try {
            $message = CreditsClients::changerStatutCheque($cheque, $data['action'], $data, Auth::id());
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', $message);
    }
}
