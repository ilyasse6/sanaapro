<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Support\DashboardAgenda;
use App\Models\Client;
use App\Models\CatalogueService;
use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class RendezVousController extends Controller
{
    /**
     * Liste des visites et rendez-vous commerciaux (multiservice).
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            $hasPermission = $user->hasPermission('Gestion des Rendez-vous', 'consulter');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de consulter les visites et rendez-vous.');
            }

            // Si c'est une requête AJAX pour le calendrier
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') == 'XMLHttpRequest' || $request->has('start')) {
                $start = $request->get('start');
                $end = $request->get('end');
                
                $rendezVous = RendezVous::with(['client', 'services', 'packs'])
                    ->where('actif', 1);
                
                if ($start && $end) {
                    $rendezVous->whereBetween('date_rdv', [$start, $end]);
                }
                
                $rendezVous = $rendezVous->get();

                $events = [];
                foreach ($rendezVous as $rdv) {
                    $servicesList = $rdv->services->pluck('nom')->implode(', ');
                    $packsList = $rdv->packs->pluck('nom')->implode(', ');
                    // Titre : Raison sociale – Contact · Prestations/Produits
                    $clientLabel = $rdv->client->nom . ($rdv->client->prenom ? ' – ' . $rdv->client->prenom : '');
                    $title = $clientLabel;
                    if ($servicesList || $packsList) {
                        $title .= ' · ' . trim($servicesList . ($servicesList && $packsList ? ', ' : '') . $packsList);
                    }

                    // Formater l'heure correctement
                    $heureRdv = is_string($rdv->heure_rdv) ? $rdv->heure_rdv : $rdv->heure_rdv->format('H:i:s');
                    $heureParts = explode(':', $heureRdv);
                    $startHour = (int)$heureParts[0];
                    $startMinute = isset($heureParts[1]) ? (int)$heureParts[1] : 0;
                    $endHour = $startHour + 1;
                    if ($endHour >= 24) {
                        $endHour = 23;
                        $endMinute = 59;
                    } else {
                        $endMinute = $startMinute;
                    }
                    
                    $startDateTime = $rdv->date_rdv->format('Y-m-d') . 'T' . sprintf('%02d:%02d', $startHour, $startMinute);
                    $endDateTime = $rdv->date_rdv->format('Y-m-d') . 'T' . sprintf('%02d:%02d', $endHour, $endMinute);
                    
                    $events[] = [
                        'id' => $rdv->id,
                        'title' => $title,
                        'start' => $startDateTime,
                        'end' => $endDateTime,
                        'backgroundColor' => $this->getStatutColor($rdv->statut),
                        'borderColor' => $this->getStatutColor($rdv->statut),
                        'extendedProps' => [
                            'client_id' => $rdv->client_id,
                            'client_nom' => $rdv->client->nom . ($rdv->client->prenom ? ' – ' . $rdv->client->prenom : ''),
                            'statut' => $rdv->statut,
                            'prix_total' => $rdv->prix_total,
                            'notes' => $rdv->notes ?? '',
                            'services' => $rdv->services->pluck('id')->toArray(),
                            'packs' => $rdv->packs->pluck('id')->toArray(),
                        ]
                    ];
                }

                return response()->json($events);
            }

            // Si on veut éditer un RDV spécifique
            $rdvToEdit = null;
            if ($request->has('edit')) {
                $rdvToEdit = RendezVous::with(['client', 'services', 'packs'])
                    ->where('id', $request->get('edit'))
                    ->where('actif', 1)
                    ->first();
            }

            $agenda = DashboardAgenda::build($user, 'rendez-vous', route('rendez-vous.index'));

            return view('user.rendez-vous.index', array_merge(compact('rdvToEdit'), $agenda));

        } catch (QueryException $e) {
            Log::error('Erreur de base de données dans RendezVousController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur de base de données. Veuillez exécuter la migration : php artisan migrate');
        } catch (\Exception $e) {
            Log::error('Erreur dans RendezVousController@index: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Get color for statut
     */
    private function getStatutColor($statut)
    {
        $colors = [
            'en_attente' => '#ffc107',
            'confirme' => '#17a2b8',
            'en_cours' => '#007bff',
            'termine' => '#28a745',
            'annule' => '#dc3545',
        ];

        return $colors[$statut] ?? '#6c757d';
    }

    /**
     * Planifier une nouvelle visite (client + prestations/packs).
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            $hasPermission = $user->hasPermission('Gestion des Rendez-vous', 'ajouter');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de planifier des visites.');
            }

            $validator = Validator::make($request->all(), [
                'client_id' => 'required|exists:clients,id',
                'date_rdv' => 'required|date',
                'heure_rdv' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
                'statut' => 'nullable|in:en_attente,confirme,en_cours,termine,annule',
                'notes' => 'nullable|string|max:1000',
                'services' => 'nullable|array',
                'services.*' => 'exists:catalogue_services,id',
                'packs' => 'nullable|array',
                'packs.*' => 'exists:catalogue_packs,id',
                'redirect_to' => 'nullable|in:home,rendez-vous',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Vérifier qu'au moins un service ou un pack est sélectionné
            $hasServices = $request->has('services') && is_array($request->services) && count($request->services) > 0;
            $hasPacks = $request->has('packs') && is_array($request->packs) && count($request->packs) > 0;
            
            if (!$hasServices && !$hasPacks) {
                return redirect()->back()
                    ->with('error', 'Veuillez sélectionner au moins une prestation ou un pack.')
                    ->withInput();
            }

            $rendezVous = null;
            $prixTotal = 0;

            DB::transaction(function () use ($request, &$rendezVous, &$prixTotal, $user) {
                // Calculer le prix total
                if ($request->has('services') && is_array($request->services) && count($request->services) > 0) {
                    foreach ($request->services as $serviceId) {
                        $service = CatalogueService::find($serviceId);
                        if ($service) {
                            $prixTotal += $service->prix_apres_reduction;
                        }
                    }
                }

                if ($request->has('packs') && is_array($request->packs) && count($request->packs) > 0) {
                    foreach ($request->packs as $packId) {
                        $pack = Pack::find($packId);
                        if ($pack) {
                            $prixTotal += $pack->prix_apres_reduction;
                        }
                    }
                }

                // Créer le rendez-vous
                $rendezVous = RendezVous::create([
                    'client_id' => $request->client_id,
                    'date_rdv' => $request->date_rdv,
                    'heure_rdv' => $request->heure_rdv,
                    'statut' => $request->statut ?? 'en_attente',
                    'notes' => $request->notes ?? null,
                    'prix_total' => $prixTotal,
                    'actif' => 1,
                    'user_insert' => Auth::id(),
                ]);

                // Attacher les services avec leurs prix
                if ($request->has('services') && is_array($request->services) && count($request->services) > 0) {
                    foreach ($request->services as $serviceId) {
                        $service = CatalogueService::find($serviceId);
                        if ($service) {
                            $rendezVous->services()->attach($serviceId, [
                                'prix' => $service->prix_apres_reduction
                            ]);
                        }
                    }
                }

                // Attacher les packs avec leurs prix
                if ($request->has('packs') && is_array($request->packs) && count($request->packs) > 0) {
                    foreach ($request->packs as $packId) {
                        $pack = Pack::find($packId);
                        if ($pack) {
                            $rendezVous->packs()->attach($packId, [
                                'prix' => $pack->prix_apres_reduction
                            ]);
                        }
                    }
                }

                Log::info("✅ Rendez-vous créé par {$user->nom} {$user->prenom}", [
                    'rendez_vous_id' => $rendezVous->id,
                    'client_id' => $request->client_id,
                ]);
            });

            $redirectTo = match ($request->input('redirect_to')) {
                'home' => route('home'),
                'rendez-vous' => route('rendez-vous.index'),
                default => route('rendez-vous.index'),
            };

            return redirect()->to($redirectTo)
                ->with('success', 'Visite planifiée avec succès.');

        } catch (QueryException $e) {
            Log::error('Erreur de base de données dans RendezVousController@store: ' . $e->getMessage());
            $errorMessage = 'Erreur de base de données. ';
            if (str_contains($e->getMessage(), "doesn't exist") || str_contains($e->getMessage(), "Unknown table")) {
                $errorMessage .= 'Les tables des visites/rendez-vous n\'existent pas. Veuillez exécuter la migration : php artisan migrate';
            } else {
                $errorMessage .= config('app.debug') ? $e->getMessage() : 'Vérifiez votre configuration de base de données.';
            }
            return redirect()->back()->with('error', $errorMessage)->withInput();
        } catch (\Exception $e) {
            Log::error('Erreur dans RendezVousController@store: ' . $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile());
            $errorMessage = config('app.debug') 
                ? 'Erreur: ' . $e->getMessage() . ' (Ligne: ' . $e->getLine() . ')'
                : 'Une erreur est survenue lors de la planification de la visite.';
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Modifier une visite planifiée.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            $hasPermission = $user->hasPermission('Gestion des Rendez-vous', 'modifier');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de modifier les visites.');
            }

            $rendezVous = RendezVous::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'client_id' => 'required|exists:clients,id',
                'date_rdv' => 'required|date',
                'heure_rdv' => 'required|date_format:H:i',
                'statut' => 'required|in:en_attente,confirme,en_cours,termine,annule',
                'notes' => 'nullable|string|max:1000',
                'services' => 'nullable|array',
                'services.*' => 'exists:catalogue_services,id',
                'packs' => 'nullable|array',
                'packs.*' => 'exists:catalogue_packs,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $hasServices = $request->filled('services') && is_array($request->services) && count($request->services) > 0;
            $hasPacks = $request->filled('packs') && is_array($request->packs) && count($request->packs) > 0;
            if (! $hasServices && ! $hasPacks) {
                return redirect()->back()
                    ->with('error', 'Sélectionnez au moins une prestation ou un pack.')
                    ->withInput();
            }

            DB::transaction(function () use ($request, $rendezVous, $user) {
                $prixTotal = 0;

                // Calculer le nouveau prix total
                if ($request->filled('services') && is_array($request->services)) {
                    foreach ($request->services as $serviceId) {
                        $service = CatalogueService::find($serviceId);
                        if ($service) {
                            $prixTotal += $service->prix_apres_reduction;
                        }
                    }
                }

                if ($request->filled('packs') && is_array($request->packs)) {
                    foreach ($request->packs as $packId) {
                        $pack = Pack::find($packId);
                        if ($pack) {
                            $prixTotal += $pack->prix_apres_reduction;
                        }
                    }
                }

                // Mettre à jour le rendez-vous
                $rendezVous->update([
                    'client_id' => $request->client_id,
                    'date_rdv' => $request->date_rdv,
                    'heure_rdv' => $request->heure_rdv,
                    'statut' => $request->statut,
                    'notes' => $request->notes,
                    'prix_total' => $prixTotal,
                    'user_update' => Auth::id(),
                ]);

                // Synchroniser les services
                if ($request->has('services')) {
                    $servicesData = [];
                    foreach ($request->services as $serviceId) {
                        $service = CatalogueService::find($serviceId);
                        if ($service) {
                            $servicesData[$serviceId] = ['prix' => $service->prix_apres_reduction];
                        }
                    }
                    $rendezVous->services()->sync($servicesData);
                } else {
                    $rendezVous->services()->detach();
                }

                // Synchroniser les packs
                if ($request->has('packs')) {
                    $packsData = [];
                    foreach ($request->packs as $packId) {
                        $pack = Pack::find($packId);
                        if ($pack) {
                            $packsData[$packId] = ['prix' => $pack->prix_apres_reduction];
                        }
                    }
                    $rendezVous->packs()->sync($packsData);
                } else {
                    $rendezVous->packs()->detach();
                }

                Log::info("✏️ Rendez-vous modifié par {$user->nom} {$user->prenom}", [
                    'rendez_vous_id' => $rendezVous->id,
                ]);
            });

            return redirect()->route('rendez-vous.index')
                ->with('success', 'Visite modifiée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur dans RendezVousController@update: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la modification de la visite.')
                ->withInput();
        }
    }

    /**
     * Désactiver / supprimer une visite.
     */
    public function desactivate($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            $hasPermission = $user->hasPermission('Gestion des Rendez-vous', 'desactiver');
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de supprimer les visites.');
            }

            $rendezVous = RendezVous::findOrFail($id);
            $rendezVous->update([
                'actif' => 0,
                'user_update' => Auth::id(),
            ]);

            return redirect()->route('rendez-vous.index')
                ->with('success', 'Visite supprimée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur dans RendezVousController@desactivate: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression de la visite.');
        }
    }
}
