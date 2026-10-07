@extends('user.master')

@section('content')
<style>
    .ao-hero {
        background: linear-gradient(135deg, #0f2438 0%, #17324D 55%, #198754 160%);
        border-radius: 20px;
        color: #fff;
        padding: 2rem 1.75rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .ao-hero::after {
        content: '';
        position: absolute;
        right: -40px; top: -40px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: rgba(212, 175, 55, 0.25);
    }
    .ao-hero h1 { font-size: 1.55rem; font-weight: 700; margin: 0 0 .5rem; position: relative; z-index: 1; }
    .ao-hero p { opacity: .92; margin: 0; max-width: 640px; position: relative; z-index: 1; }
    .ao-stat {
        background: #fff;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 10px 40px rgba(30, 64, 175, .08);
        height: 100%;
        text-align: center;
    }
    .ao-stat .n { font-size: 1.75rem; font-weight: 800; color: #17324D; line-height: 1.1; }
    .ao-stat .l { color: #6c757d; font-size: .85rem; margin-top: .35rem; }
    .ao-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 40px rgba(30, 64, 175, .08);
        margin-bottom: 1.25rem;
    }
    .ao-card h2 { font-size: 1.1rem; font-weight: 700; color: #0f2438; margin-bottom: 1rem; }
    .ao-feature {
        border: 1px solid rgba(30, 64, 175, .1);
        border-radius: 14px;
        padding: 1.15rem;
        height: 100%;
        background: #fafbff;
    }
    .ao-feature i { font-size: 1.4rem; color: #198754; }
    .ao-feature h3 { font-size: .95rem; font-weight: 700; color: #0f2438; margin: .6rem 0 .35rem; }
    .ao-feature p { font-size: .85rem; color: #6c757d; margin: 0; }
    .ao-lot {
        border: 1px solid #e8ecf4;
        border-radius: 14px;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .ao-lot-head {
        background: linear-gradient(90deg, rgba(30,64,175,.08), rgba(212,175,55,.12));
        padding: .85rem 1.1rem;
        display: flex;
        flex-wrap: wrap;
        gap: .5rem 1rem;
        align-items: center;
        justify-content: space-between;
    }
    .ao-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .25rem .65rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
        background: rgba(30, 64, 175, .1);
        color: #0f2438;
    }
    .ao-badge.gold { background: rgba(212, 175, 55, .2); color: #8a6d0b; }
    .ao-badge.green { background: rgba(22, 163, 74, .12); color: #15803d; }
    .ao-url-input {
        border-radius: 12px;
        border: 1px solid #cfd8ea;
        padding: .75rem 1rem;
    }
    .ao-url-input:focus {
        border-color: #17324D;
        box-shadow: 0 0 0 .2rem rgba(30, 64, 175, .15);
    }
    .ao-step { display: flex; gap: .85rem; margin-bottom: 1rem; }
    .ao-step-n {
        width: 32px; height: 32px; border-radius: 50%;
        background: #17324D; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .85rem; flex-shrink: 0;
    }
    .faq-item { border-bottom: 1px solid #eef1f7; padding: .85rem 0; }
    .faq-item:last-child { border-bottom: 0; }
    .faq-q { font-weight: 600; color: #0f2438; margin-bottom: .25rem; }
    .faq-a { color: #6c757d; font-size: .9rem; margin: 0; }
</style>

<section class="section">
    <div class="ao-hero">
        <h1><i class="bi bi-lightning-charge-fill me-2"></i>Analyse des appels d'offres</h1>
        <p>Calculez en quelques secondes le <strong>prix de référence</strong> par lot, comparez les offres et visualisez les gagnants — inspiré du parcours Marcheo, intégré à SanaaPro.</p>
    </div>

    @include('user.partials.flash')

    <div class="ao-card">
        <h2><i class="bi bi-link-45deg me-1" style="color:#198754;"></i> Lancer une analyse</h2>
        <p class="text-muted small mb-3">Collez l'URL de suivi de consultation depuis <strong>marchespublics.gov.ma</strong>, ou laissez vide pour une analyse de démonstration rapide.</p>
        <form method="post" action="{{ route('analyse-marches.analyser') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-lg-9">
                <label class="form-label fw-semibold">URL de la consultation</label>
                <input type="url" name="url" value="{{ $url }}" class="form-control ao-url-input"
                    placeholder="https://www.marchespublics.gov.ma/...">
                @error('url') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-lg-3 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-search me-1"></i> Analyser
                </button>
                <a href="{{ route('analyse-marches.clear') }}" class="btn btn-outline-secondary"
                    onclick="event.preventDefault(); document.getElementById('ao-clear-form').submit();">
                    Effacer
                </a>
            </div>
        </form>
        <form id="ao-clear-form" method="post" action="{{ route('analyse-marches.clear') }}" class="d-none">@csrf</form>
    </div>

    @if(!empty($result))
        <div class="ao-card">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <h2 class="mb-0"><i class="bi bi-clipboard-data me-1" style="color:#17324D;"></i> Résultats</h2>
                <div class="d-flex flex-wrap gap-2">
                    <span class="ao-badge">Mode : {{ $result['mode'] }}</span>
                    <span class="ao-badge gold">Durée : {{ $result['analyse_en'] }}</span>
                    <span class="ao-badge">{{ $result['generated_at'] }}</span>
                </div>
            </div>
            @if(!empty($result['note']))
                <div class="alert alert-warning py-2 small">{{ $result['note'] }}</div>
            @endif

            @php $c = $result['consultation']; @endphp
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="small text-muted">Référence</div>
                    <div class="fw-semibold">{{ $c['reference'] }}</div>
                </div>
                <div class="col-md-6">
                    <div class="small text-muted">Acheteur</div>
                    <div class="fw-semibold">{{ $c['acheteur'] }}</div>
                </div>
                <div class="col-12">
                    <div class="small text-muted">Objet</div>
                    <div>{{ $c['objet'] }}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted">Procédure</div>
                    <div>{{ $c['procedure'] }}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted">Catégorie</div>
                    <div>{{ $c['categorie'] }}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted">Date limite</div>
                    <div>{{ $c['date_limite'] }}</div>
                </div>
            </div>

            <h3 class="h6 fw-bold text-primary mb-2">Récapitulatif des gagnants</h3>
            <div class="table-responsive mb-4">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Lot</th>
                            <th>Gagnant</th>
                            <th class="text-end">Montant TTC</th>
                            <th class="text-end">Prix de référence</th>
                            <th class="text-end">Écart</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($result['recap_gagnants'] as $g)
                            <tr>
                                <td>Lot {{ $g['lot'] }}</td>
                                <td>{{ $g['entreprise'] }}</td>
                                <td class="text-end">{{ number_format($g['montant'], 2, ',', ' ') }} MAD</td>
                                <td class="text-end">{{ number_format($g['prix_reference'], 2, ',', ' ') }} MAD</td>
                                <td class="text-end">
                                    <span class="ao-badge {{ $g['ecart_pct'] <= 0 ? 'green' : 'gold' }}">
                                        {{ ($g['ecart_pct'] > 0 ? '+' : '').$g['ecart_pct'] }} %
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h3 class="h6 fw-bold text-primary mb-3">Détail par lot</h3>
            @foreach($result['lots'] as $lot)
                <div class="ao-lot">
                    <div class="ao-lot-head">
                        <div>
                            <strong>{{ $lot['libelle'] }}</strong>
                            <div class="small text-muted">{{ $lot['nb_offres'] }} offres</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="ao-badge gold"><i class="bi bi-bookmark-star"></i> Réf. {{ number_format($lot['prix_reference'], 0, ',', ' ') }} MAD</span>
                            <span class="ao-badge green"><i class="bi bi-trophy"></i> {{ $lot['gagnant']['entreprise'] }}</span>
                        </div>
                    </div>
                    <div class="p-3">
                        <div class="row g-2 small mb-2 text-muted">
                            <div class="col-auto">Min : {{ number_format($lot['min'], 0, ',', ' ') }} MAD</div>
                            <div class="col-auto">Moy. : {{ number_format($lot['moyenne'], 0, ',', ' ') }} MAD</div>
                            <div class="col-auto">Max : {{ number_format($lot['max'], 0, ',', ' ') }} MAD</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Rang</th>
                                        <th>Entreprise</th>
                                        <th class="text-end">Offre TTC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lot['offres'] as $offre)
                                        <tr class="{{ $offre['rang'] === 1 ? 'table-success' : '' }}">
                                            <td>{{ $offre['rang'] }}</td>
                                            <td>{{ $offre['entreprise'] }}</td>
                                            <td class="text-end">{{ number_format($offre['montant_ttc'], 2, ',', ' ') }} MAD</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ao-feature">
                <i class="bi bi-cash-coin"></i>
                <h3>Prix de référence par lot</h3>
                <p>Estimation synthétique à partir des offres (moyenne trimmed hors extrêmes).</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ao-feature">
                <i class="bi bi-bar-chart-line"></i>
                <h3>Comparer les offres</h3>
                <p>Classement par lot, récapitulatif des gagnants et écart vs référence.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ao-feature">
                <i class="bi bi-shield-check"></i>
                <h3>Données officielles</h3>
                <p>Import possible depuis marchespublics.gov.ma ; sinon simulation rapide pour tester.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="ao-card h-100">
                <h2>Comment ça marche ?</h2>
                <div class="ao-step">
                    <div class="ao-step-n">1</div>
                    <div><strong>Saisissez l'URL</strong><p class="text-muted small mb-0">Lien de suivi depuis le portail officiel.</p></div>
                </div>
                <div class="ao-step">
                    <div class="ao-step-n">2</div>
                    <div><strong>Traitement automatique</strong><p class="text-muted small mb-0">Extraction / simulation des lots et offres, calcul du prix de référence.</p></div>
                </div>
                <div class="ao-step mb-0">
                    <div class="ao-step-n">3</div>
                    <div><strong>Résultats &amp; gagnants</strong><p class="text-muted small mb-0">Récapitulatif clair, partageable en interne.</p></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ao-card h-100">
                <h2>FAQ</h2>
                <div class="faq-item">
                    <div class="faq-q">Qu'est-ce que le prix de référence ?</div>
                    <p class="faq-a">Estimation par lot à partir des offres soumises (moyenne sans les extrêmes si ≥ 4 offres).</p>
                </div>
                <div class="faq-item">
                    <div class="faq-q">D'où viennent les informations ?</div>
                    <p class="faq-a">Du portail officiel des marchés publics lorsque l'URL est valide ; sinon données de démonstration.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
