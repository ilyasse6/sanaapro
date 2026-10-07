@extends('user.master')
@section('content')
@include('user.marche.partials.styles')

<div class="section">
    <div class="marche-hero">
        <div class="marche-hero-content d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-briefcase me-2"></i>Marchés &amp; cautions</h1>
                <p>Suivez vos appels d'offres, lots, documents et cautions.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'consulter'))
                    <a href="{{ route('analyse-marches.index') }}" class="btn btn-marche btn-marche-outline">
                        <i class="bi bi-graph-up-arrow me-1"></i>Analyse AO
                    </a>
                @endif
                @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'ajouter'))
                    <a href="{{ route('marches.create') }}" class="btn btn-marche btn-marche-light">
                        <i class="bi bi-plus-circle me-1"></i>Nouveau marché
                    </a>
                @endif
            </div>
        </div>
    </div>

    @include('user.partials.flash')

    <div class="card-marche">
        <div class="table-responsive">
            <table class="table table-marche table-hover mb-0">
                <thead>
                    <tr>
                        <th>Réf.</th>
                        <th>Intitulé</th>
                        <th>Statut</th>
                        <th>Maître d'ouvrage</th>
                        <th class="text-center">Cautions</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($marches as $m)
                        <tr>
                            <td class="fw-semibold text-nowrap" style="color: var(--prowise-blue-dark);">{{ $m->reference }}</td>
                            <td>
                                <span class="marche-intitule-cell" title="{{ e($m->intitule) }}">{{ $m->intitule }}</span>
                            </td>
                            <td><span class="{{ $m->statut_badge_class }}">{{ $m->statut_label }}</span></td>
                            <td class="small text-muted">{{ $m->acheteur_public ? \Illuminate\Support\Str::limit($m->acheteur_public, 48) : ($m->client?->nom ?? '—') }}</td>
                            <td class="text-center">
                                <span class="badge rounded-pill" style="background: rgba(212,175,55,0.2); color: #8a6d0b;">{{ $m->cautions_count }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('marches.show', $m) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i>Détail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="marche-empty">
                                    <i class="bi bi-inbox"></i>
                                    <div class="fw-semibold mb-1">Aucun marché</div>
                                    <p class="small mb-3">Créez votre premier marché ou importez une consultation.</p>
                                    @if(Auth::user()->hasPermission('Gestion des Marchés & Cautions', 'ajouter'))
                                        <a href="{{ route('marches.create') }}" class="btn btn-marche btn-marche-primary btn-sm">
                                            <i class="bi bi-plus-lg me-1"></i>Nouveau marché
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($marches->hasPages())
            <div class="p-3 border-top">{{ $marches->links() }}</div>
        @endif
    </div>
</div>
@endsection
