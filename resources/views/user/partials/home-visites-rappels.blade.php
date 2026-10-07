{{-- 3 blocs : Visites (jour J), RDV (futurs), Rappels (alertes + perso) + bouton Ajouter --}}
@php
    $fmtHeure = function ($h) {
        if ($h === null) {
            return '';
        }
        if ($h instanceof \Carbon\CarbonInterface) {
            return $h->format('H:i');
        }
        if (is_string($h)) {
            return strlen($h) >= 5 ? substr($h, 0, 5) : $h;
        }

        return '';
    };

    $nbBlocs = ($showVisitesBloc ?? false ? 2 : 0) + 1;
    $colClass = match (true) {
        $nbBlocs >= 3 => 'col-lg-4 col-md-6',
        $nbBlocs === 2 => 'col-lg-6',
        default => 'col-12',
    };
@endphp

<div class="ios-agenda-wrap mb-4">
    @if(!($hideAgendaToolbar ?? false))
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="mb-0 d-flex align-items-center gap-2" style="font-size:1.1rem;font-weight:700;color:var(--prowise-blue-dark);">
            <i class="bi bi-grid-1x2-fill"></i> Agenda
        </h5>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#homeDashboardAjouterModal">
            <i class="bi bi-plus-lg me-1"></i> Ajouter
        </button>
    </div>
    @endif

    <div class="row g-3 ios-reminders-section">
        @if($showVisitesBloc ?? false)
        <div class="{{ $colClass }}">
            <div class="ios-reminders-card h-100">
                <div class="ios-reminders-header">
                    <span class="ios-reminders-title">
                        <i class="bi bi-geo-alt"></i> Visites
                    </span>
                    <a href="{{ route('rendez-vous.index') }}" class="ios-reminders-seeall">Tout voir</a>
                </div>
                <p class="ios-reminders-hint mb-2">Aujourd’hui</p>
                <div class="ios-reminders-list">
                    @forelse($visitesAujourdhui ?? [] as $rdv)
                        @php
                            $client = $rdv->client;
                            $nomClient = $client ? trim(($client->nom ?? '') . ' ' . ($client->prenom ?? '')) : 'Client';
                            $services = $rdv->services->pluck('nom')->filter()->take(2)->implode(', ');
                            $packs = $rdv->packs->pluck('nom')->filter()->take(1)->implode(', ');
                            $extra = trim(implode(' · ', array_filter([$services, $packs])));
                        @endphp
                        <a href="{{ route('rendez-vous.index') }}" class="ios-reminder-chip">
                            <span class="ios-reminder-dot" style="background:#34C759;"></span>
                            <span class="ios-reminder-body">
                                <span class="ios-reminder-line1">{{ $nomClient }}</span>
                                <span class="ios-reminder-line2">
                                    @if($fmtHeure($rdv->heure_rdv))
                                        {{ $fmtHeure($rdv->heure_rdv) }}
                                    @else
                                        —
                                    @endif
                                    @if($extra !== '')
                                        · {{ \Illuminate\Support\Str::limit($extra, 42) }}
                                    @endif
                                    @if($rdv->statut)
                                        · {{ $rdv->statut_libelle ?? $rdv->statut }}
                                    @endif
                                </span>
                            </span>
                            <span class="ios-reminder-chev"><i class="bi bi-chevron-right"></i></span>
                        </a>
                    @empty
                        <div class="ios-reminders-empty">
                            <i class="bi bi-calendar-x"></i> Aucune visite prévue aujourd’hui
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="{{ $colClass }}">
            <div class="ios-reminders-card h-100">
                <div class="ios-reminders-header">
                    <span class="ios-reminders-title">
                        <i class="bi bi-calendar-event"></i> RDV
                    </span>
                    <a href="{{ route('rendez-vous.index') }}" class="ios-reminders-seeall">Tout voir</a>
                </div>
                <p class="ios-reminders-hint mb-2">À venir</p>
                <div class="ios-reminders-list">
                    @forelse($rdvProchains ?? [] as $rdv)
                        @php
                            $client = $rdv->client;
                            $nomClient = $client ? trim(($client->nom ?? '') . ' ' . ($client->prenom ?? '')) : 'Client';
                            $services = $rdv->services->pluck('nom')->filter()->take(2)->implode(', ');
                            $packs = $rdv->packs->pluck('nom')->filter()->take(1)->implode(', ');
                            $extra = trim(implode(' · ', array_filter([$services, $packs])));
                        @endphp
                        <a href="{{ route('rendez-vous.index') }}" class="ios-reminder-chip">
                            <span class="ios-reminder-dot" style="background:#007AFF;"></span>
                            <span class="ios-reminder-body">
                                <span class="ios-reminder-line1">{{ $nomClient }}</span>
                                <span class="ios-reminder-line2">
                                    {{ $rdv->date_rdv->locale('fr')->isoFormat('ddd D MMM') }}
                                    @if($fmtHeure($rdv->heure_rdv))
                                        · {{ $fmtHeure($rdv->heure_rdv) }}
                                    @endif
                                    @if($extra !== '')
                                        · {{ \Illuminate\Support\Str::limit($extra, 42) }}
                                    @endif
                                </span>
                            </span>
                            <span class="ios-reminder-chev"><i class="bi bi-chevron-right"></i></span>
                        </a>
                    @empty
                        <div class="ios-reminders-empty">
                            <i class="bi bi-calendar-plus"></i> Aucun rendez-vous à venir
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        <div class="{{ $colClass }}">
            <div class="ios-reminders-card h-100">
                <div class="ios-reminders-header">
                    <span class="ios-reminders-title">
                        <i class="bi bi-bell"></i> Rappels
                    </span>
                    @if($showRappelsBloc ?? false)
                        <a href="{{ route('alertes.index') }}" class="ios-reminders-seeall">Alertes</a>
                    @endif
                </div>
                <div class="ios-reminders-list">
                    @forelse($rappelsHome ?? [] as $r)
                        <a href="{{ $r['url'] }}" class="ios-reminder-chip">
                            <span class="ios-reminder-dot" style="background:{{ $r['accent'] }};"></span>
                            <span class="ios-reminder-body">
                                <span class="ios-reminder-line1">{{ $r['title'] }}</span>
                                <span class="ios-reminder-line2">{{ $r['subtitle'] }}</span>
                            </span>
                            <span class="ios-reminder-chev"><i class="bi bi-chevron-right"></i></span>
                        </a>
                    @empty
                        <div class="ios-reminders-empty">
                            <i class="bi bi-check2-circle"></i> Aucun rappel pour le moment
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
