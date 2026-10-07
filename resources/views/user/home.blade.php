@extends('user.master')

@section('content')
<style>
    /* SanaaPro theme: navy + green, aligned with ParaGestion */
    :root {
        --prowise-blue: #17324D;
        --prowise-blue-dark: #0f2438;
        --prowise-blue-light: #1e4060;
        --prowise-gold: #198754;
        --prowise-gold-light: #8fd4b8;
        --prowise-gold-dark: #146c43;
        --gradient-primary: linear-gradient(135deg, var(--prowise-blue) 0%, var(--prowise-blue-dark) 100%);
        --gradient-prowise: linear-gradient(135deg, var(--prowise-blue) 0%, var(--prowise-gold) 100%);
        --gradient-secondary: linear-gradient(135deg, var(--prowise-gold) 0%, var(--prowise-gold-dark) 100%);
        --gradient-success: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        --gradient-warning: linear-gradient(135deg, var(--prowise-gold-light) 0%, var(--prowise-gold) 100%);
        --gradient-info: linear-gradient(135deg, var(--prowise-blue-light) 0%, var(--prowise-blue) 100%);
        --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        --gradient-orange: linear-gradient(135deg, var(--prowise-gold) 0%, #c2410c 100%);
        --card-shadow: 0 10px 40px rgba(30, 64, 175, 0.08);
        --card-hover-shadow: 0 20px 60px rgba(30, 64, 175, 0.12);
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--card-hover-shadow);
    }

    .stat-card .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 1rem;
    }

    .stat-card .stat-number {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-card .stat-label {
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .stat-card .stat-trend {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .stat-card .stat-trend.up {
        background: rgba(212, 175, 55, 0.2);
        color: var(--prowise-gold-dark);
    }

    .stat-card .stat-trend.down {
        background: rgba(239, 68, 68, 0.15);
        color: #dc2626;
    }

    .stat-card .stat-trend.warning {
        background: rgba(212, 175, 55, 0.2);
        color: var(--prowise-gold-dark);
    }

    .icon-primary { background: var(--gradient-primary); }
    .icon-success { background: var(--gradient-success); }
    .icon-warning { background: var(--gradient-warning); }
    .icon-info { background: var(--gradient-info); }
    .icon-secondary { background: var(--gradient-secondary); }
    .icon-danger { background: var(--gradient-danger); }
    .icon-orange { background: var(--gradient-orange); }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--prowise-blue-dark);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        height: 100%;
    }

    .chart-card .card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--prowise-blue-dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mini-chart {
        height: 120px;
        display: flex;
        align-items: flex-end;
        gap: 6px;
        padding-top: 1rem;
    }

    .mini-chart .bar {
        flex: 1;
        border-radius: 6px 6px 0 0;
        transition: all 0.3s ease;
        min-height: 10px;
    }

    .mini-chart .bar:hover {
        opacity: 0.85;
        transform: scaleY(1.05);
    }

    .category-card {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .category-card:hover {
        background: #f1f5f9;
        transform: translateX(5px);
    }

    .category-card.medical { border-left-color: #ef4444; }
    .category-card.cuisine { border-left-color: var(--prowise-gold); }
    .category-card.bureautique { border-left-color: var(--prowise-blue); }
    .category-card.industriel { border-left-color: #16a34a; }

    .category-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-right: 1rem;
    }

    .category-info { flex: 1; }
    .category-name { font-weight: 600; color: var(--prowise-blue-dark); margin-bottom: 0.25rem; }
    .category-meta { font-size: 0.75rem; color: #6c757d; }
    .category-value { font-weight: 700; color: var(--prowise-blue-dark); }

    .equipment-list, .invoice-list {
        max-height: 320px;
        overflow-y: auto;
    }

    .equipment-item, .invoice-item {
        display: flex;
        align-items: center;
        padding: 0.875rem;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 0.625rem;
        transition: all 0.3s ease;
    }

    .equipment-item:hover, .invoice-item:hover {
        background: #f1f5f9;
        transform: translateX(5px);
    }

    .equipment-avatar, .invoice-avatar {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        margin-right: 1rem;
    }

    .equipment-info, .invoice-info { flex: 1; }
    .equipment-name, .invoice-ref { font-weight: 600; color: var(--prowise-blue-dark); font-size: 0.9rem; }
    .equipment-meta, .invoice-meta { font-size: 0.75rem; color: #6c757d; }
    .equipment-stock, .invoice-amount {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .stock-ok { background: rgba(212, 175, 55, 0.2); color: var(--prowise-gold-dark); }
    .stock-low { background: rgba(212, 175, 55, 0.25); color: var(--prowise-gold-dark); }
    .stock-critique { background: rgba(239, 68, 68, 0.15); color: #dc2626; }

    .treatment-stat {
        text-align: center;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 12px;
    }
    .treatment-stat .number { font-size: 1.75rem; font-weight: 800; margin-bottom: 0.25rem; }
    .treatment-stat .label { font-size: 0.75rem; color: #6c757d; }

    .payment-method {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 10px;
        margin-bottom: 0.5rem;
    }
    .payment-method:last-child { margin-bottom: 0; }
    .payment-method-name { display: flex; align-items: center; gap: 0.5rem; font-weight: 500; color: var(--prowise-blue-dark); font-size: 0.875rem; }
    .payment-method-amount { font-weight: 700; color: var(--prowise-gold-dark); }
    .progress-bar-custom { height: 8px; border-radius: 4px; background: #e9ecef; overflow: hidden; margin-top: 0.5rem; }
    .progress-bar-fill { height: 100%; border-radius: 4px; transition: width 0.5s ease; }

    .quick-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        background: rgba(30, 64, 175, 0.06);
        color: var(--prowise-blue-dark);
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid rgba(30, 64, 175, 0.15);
    }
    .quick-action-btn:hover {
        background: var(--gradient-prowise);
        color: white;
        border-color: transparent;
    }

    @media (max-width: 768px) {
        .stat-card .stat-number { font-size: 1.8rem; }
    }

    @include('user.partials.dashboard-agenda-styles')
</style>

<section class="section dashboard">
    @include('user.partials.home-visites-rappels')
    @include('user.partials.home-dashboard-ajouter')

    @php $s = $dashboardStats ?? []; @endphp

    <!-- Statistiques principales : Factures, Devis, Stock -->
    <h5 class="section-title">
        <i class="bi bi-graph-up-arrow" style="color: var(--prowise-blue);"></i>
        Vue d'ensemble
    </h5>
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="icon-box icon-primary">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <div class="stat-number" style="color: var(--prowise-blue);">{{ number_format($s['factures_ce_mois'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-label">Factures ce mois</div>
                    </div>
                    @if(!empty($s['factures_ce_mois_trend']))
                        <span class="stat-trend {{ $s['factures_ce_mois_trend']['class'] }}"><i class="bi {{ $s['factures_ce_mois_trend']['icon'] }}"></i> {{ $s['factures_ce_mois_trend']['text'] }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="icon-box icon-success">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <div class="stat-number" style="color: var(--prowise-gold-dark);">{{ number_format($s['devis_en_attente'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-label">Devis en attente</div>
                    </div>
                    @if(!empty($s['devis_en_attente_trend']))
                        <span class="stat-trend {{ $s['devis_en_attente_trend']['class'] }}"><i class="bi {{ $s['devis_en_attente_trend']['icon'] }}"></i> {{ $s['devis_en_attente_trend']['text'] }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="icon-box icon-warning">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <div class="stat-number" style="color: var(--prowise-gold-dark);">{{ number_format($s['articles_stock_total'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-label">Articles en stock</div>
                    </div>
                    @if(($s['stock_alertes'] ?? 0) > 0)
                        <span class="stat-trend warning"><i class="bi bi-exclamation-triangle"></i> {{ $s['stock_alertes'] }} alerte(s)</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="icon-box icon-info">
                    <i class="bi bi-people"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <div class="stat-number" style="color: var(--prowise-blue);">{{ number_format($s['clients_actifs'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-label">Clients actifs</div>
                    </div>
                    @if(!empty($s['clients_actifs_trend']))
                        <span class="stat-trend {{ $s['clients_actifs_trend']['class'] }}"><i class="bi {{ $s['clients_actifs_trend']['icon'] }}"></i> {{ $s['clients_actifs_trend']['text'] }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Revenus & CA -->
    <h5 class="section-title">
        <i class="bi bi-cash-stack" style="color: var(--prowise-gold-dark);"></i>
        Chiffre d'affaires & Revenus
    </h5>
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="icon-box icon-success">
                    <i class="bi bi-currency-exchange"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <div class="stat-number" style="color: var(--prowise-gold-dark);">{{ $s['ca_ce_mois_fmt'] ?? '0 MAD' }}</div>
                        <div class="stat-label">CA ce mois</div>
                    </div>
                    @if(!empty($s['ca_ce_mois_trend']))
                        <span class="stat-trend {{ $s['ca_ce_mois_trend']['class'] }}"><i class="bi {{ $s['ca_ce_mois_trend']['icon'] }}"></i> {{ $s['ca_ce_mois_trend']['text'] }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="icon-box icon-primary">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <div class="stat-number" style="color: var(--prowise-blue);">{{ $s['panier_moyen_fmt'] ?? '0 MAD' }}</div>
                        <div class="stat-label">Panier moyen facture</div>
                    </div>
                    @if(!empty($s['panier_moyen_trend']))
                        <span class="stat-trend {{ $s['panier_moyen_trend']['class'] }}"><i class="bi {{ $s['panier_moyen_trend']['icon'] }}"></i> {{ $s['panier_moyen_trend']['text'] }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="icon-box icon-info">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <div class="stat-number" style="color: var(--prowise-blue);">{{ $s['encaissements_ce_mois_fmt'] ?? '0 MAD' }}</div>
                        <div class="stat-label">Encaissements ce mois</div>
                    </div>
                    @if(!empty($s['encaissements_ce_mois_trend']))
                        <span class="stat-trend {{ $s['encaissements_ce_mois_trend']['class'] }}"><i class="bi {{ $s['encaissements_ce_mois_trend']['icon'] }}"></i> {{ $s['encaissements_ce_mois_trend']['text'] }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="icon-box icon-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <div class="stat-number" style="color: var(--prowise-gold-dark);">{{ $s['factures_en_attente_montant_fmt'] ?? '0 MAD' }}</div>
                        <div class="stat-label">Factures en attente</div>
                    </div>
                    @if(!empty($s['factures_en_attente_trend']))
                        <span class="stat-trend {{ $s['factures_en_attente_trend']['class'] }}"><i class="bi {{ $s['factures_en_attente_trend']['icon'] }}"></i> {{ $s['factures_en_attente_trend']['text'] }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Répartition paiements & Stock / Dernières factures -->
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="chart-card">
                <h5 class="card-title">
                    <i class="bi bi-wallet2 text-info"></i>
                    Répartition des paiements
                </h5>
                @forelse(($s['paiements_mois'] ?? collect()) as $paiement)
                    <div class="payment-method {{ $loop->first ? '' : 'mt-3' }}">
                        <span class="payment-method-name"><i class="bi {{ $paiement['icon'] }}" style="color: {{ $paiement['color'] }};"></i> {{ $paiement['label'] }}</span>
                        <span class="payment-method-amount">{{ number_format($paiement['montant'], 0, ',', ' ') }} MAD</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-bar-fill" style="width: {{ $paiement['pct'] }}%; background: var(--gradient-primary);"></div>
                    </div>
                @empty
                    <div class="text-muted small py-4 text-center">
                        <i class="bi bi-inbox me-1"></i>Aucun encaissement ce mois
                    </div>
                @endforelse
                <div class="mt-4 p-3 rounded-3" style="background: rgba(212, 175, 55, 0.15);">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold text-muted">Total encaissé</span>
                        <span class="fw-bold fs-5" style="color: var(--prowise-gold-dark);">{{ $s['total_encaisse_fmt'] ?? '0 MAD' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières factures -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt" style="color: var(--prowise-blue);"></i>
                        Dernières factures
                    </h5>
                    <a href="{{ route('factures.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Voir tout</a>
                </div>
                <div class="invoice-list">
                    @forelse(($s['dernieres_factures'] ?? collect()) as $facture)
                        <a href="{{ $facture['url'] }}" class="invoice-item text-decoration-none text-reset d-flex">
                            <div class="invoice-avatar" style="background: var(--gradient-primary);"><i class="bi bi-receipt"></i></div>
                            <div class="invoice-info">
                                <div class="invoice-ref">{{ $facture['numero'] }}</div>
                                <div class="invoice-meta">{{ $facture['client'] }} • {{ $facture['date_label'] }}</div>
                            </div>
                            <span class="invoice-amount stock-ok">{{ $facture['montant_fmt'] }}</span>
                        </a>
                    @empty
                        <div class="text-muted small py-4 text-center">
                            <i class="bi bi-inbox me-1"></i>Aucune facture
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Équipements stock bas -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-box-seam" style="color: var(--prowise-gold);"></i>
                        Stock à réapprovisionner
                    </h5>
                    <a href="{{ route('stock.articles') }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">Gérer stock</a>
                </div>
                <div class="equipment-list">
                    @forelse(($s['stock_bas'] ?? collect()) as $article)
                        <a href="{{ $article['url'] }}" class="equipment-item text-decoration-none text-reset d-flex">
                            <div class="equipment-avatar" style="background: var(--gradient-danger);"><i class="bi {{ $article['icon'] }}"></i></div>
                            <div class="equipment-info">
                                <div class="equipment-name">{{ $article['nom'] }}</div>
                                <div class="equipment-meta">Ref: {{ $article['reference'] }} • Restant: {{ $article['stock'] }} unités</div>
                            </div>
                            <span class="equipment-stock {{ $article['niveau_class'] }}">{{ $article['niveau'] }}</span>
                        </a>
                    @empty
                        <div class="text-muted small py-4 text-center">
                            <i class="bi bi-check-circle me-1"></i>Aucun article en alerte
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mt-4">
        <div class="col-12">
            <h5 class="section-title">
                <i class="bi bi-lightning-charge" style="color: var(--prowise-gold);"></i>
                Actions rapides
            </h5>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('factures.create') }}" class="quick-action-btn">
                    <i class="bi bi-plus-circle"></i> Nouvelle facture
                </a>
                <a href="{{ route('devis.create') }}" class="quick-action-btn">
                    <i class="bi bi-file-earmark-plus"></i> Créer un devis
                </a>
                <a href="{{ route('stock.articles') }}" class="quick-action-btn">
                    <i class="bi bi-box-seam"></i> Gérer le stock
                </a>
                <a href="{{ route('clients.index') }}" class="quick-action-btn">
                    <i class="bi bi-person-plus"></i> Nouveau client
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
