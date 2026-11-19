<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Client;
use App\Models\ComptaMouvement;
use App\Support\CreditsClients;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreditClientController extends Controller
{
    public const MODULE = 'Gestion des Crédits & Chèques';

    private function refus(string $action): ?RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }
        if (! $user->hasPermission(self::MODULE, $action)) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }

        return null;
    }

    public function index(Request $request)
    {
        if ($r = $this->refus('consulter')) {
            return $r;
        }

        $filtre = $request->input('filtre', 'debiteurs');
        $recherche = trim((string) $request->input('q', ''));

        $clients = Client::query()
            ->when($recherche !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('nom', 'like', "%{$recherche}%")
                ->orWhere('prenom', 'like', "%{$recherche}%")
                ->orWhere('telephone', 'like', "%{$recherche}%")))
            ->orderBy('nom')
            ->get();

        $toutes = CreditsClients::soldes($clients);

        $lignes = $toutes
            ->filter(fn ($s) => match ($filtre) {
                'retard' => $s['montant_echu'] > 0,
                'tous' => $s['client']->actif || abs($s['solde']) > 0.005,
                default => $s['solde'] > 0.005,
            })
            ->sortByDesc('solde')
            ->values();

        $stats = [
            'credit_total' => round($toutes->where('solde', '>', 0)->sum('solde'), 2),
            'nb_debiteurs' => $toutes->where('solde', '>', 0.005)->count(),
            'montant_echu' => round($toutes->sum('montant_echu'), 2),
            'nb_retard' => $toutes->where('montant_echu', '>', 0)->count(),
            'cheques_en_cours' => round((float) Cheque::whereIn('statut', Cheque::STATUTS_EN_COURS)->sum('montant'), 2),
            'nb_cheques_en_cours' => Cheque::whereIn('statut', Cheque::STATUTS_EN_COURS)->count(),
        ];

        return view('user.credits.index', compact('lignes', 'stats', 'filtre', 'recherche'));
    }

    public function show(Client $client)
    {
        if ($r = $this->refus('consulter')) {
            return $r;
        }

        $situation = CreditsClients::soldes([$client])->first();
        $factures = CreditsClients::facturesClient($client);
        $reglements = $client->reglements()->with(['facture', 'cheque'])
            ->orderByDesc('date_operation')->orderByDesc('id')->get();
        $cheques = $client->cheques()->with('facture')
            ->orderByDesc('date_reception')->orderByDesc('id')->get();
        $facturesOuvertes = $factures->filter(fn ($f) => $f['reste'] > 0)->values();

        return view('user.credits.show', compact('client', 'situation', 'factures', 'reglements', 'cheques', 'facturesOuvertes'));
    }

    public function storeReglement(Request $request, Client $client)
    {
        if ($r = $this->refus('ajouter')) {
            return $r;
        }

        $data = $request->validate([
            'montant' => 'required|numeric|min:0.01',
            'date_reglement' => 'required|date',
            'mode' => ['required', Rule::in(array_keys(CreditsClients::MODES))],
            'facture_id' => ['nullable', Rule::exists('factures', 'id')->where('client_id', $client->id)],
            'reference' => 'nullable|string|max:120',
            'notes' => 'nullable|string|max:1000',
            'cheque_numero' => 'required_if:mode,cheque|nullable|string|max:50',
            'cheque_banque' => 'nullable|string|max:120',
            'cheque_tireur' => 'nullable|string|max:191',
            'cheque_echeance' => 'nullable|date',
        ], [
            'cheque_numero.required_if' => 'Le numéro du chèque est obligatoire.',
        ]);

        CreditsClients::enregistrerReglement($client, $data, Auth::id());

        return redirect()->route('credits.show', $client)->with('success', 'Règlement de '.number_format((float) $data['montant'], 2, ',', ' ').' DH enregistré.');
    }

    public function destroyReglement(ComptaMouvement $mouvement)
    {
        if ($r = $this->refus('desactiver')) {
            return $r;
        }
        abort_unless($mouvement->type === 'paiement_client' && $mouvement->client_id, 404);

        $client = $mouvement->client;
        CreditsClients::supprimerReglement($mouvement, Auth::id());

        return redirect()->route('credits.show', $client)->with('success', 'Règlement supprimé.');
    }

    public function updateCreditInitial(Request $request, Client $client)
    {
        if ($r = $this->refus('modifier')) {
            return $r;
        }

        $data = $request->validate(['credit_initial' => 'required|numeric|min:0']);
        $client->update(['credit_initial' => $data['credit_initial'], 'user_update' => Auth::id()]);

        return redirect()->route('credits.show', $client)->with('success', 'Crédit initial mis à jour.');
    }
}
