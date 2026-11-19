{{-- Répartition d’une facture en mini-factures. Attend : $facture (factureParent.facturesReparties et facturesReparties chargés). --}}
@php
    $rpRacine = $facture->factureParent ?? $facture;
    $rpFamille = collect([$rpRacine])->merge($rpRacine->facturesReparties);
    $rpOrigine = round($rpFamille->sum(fn ($f) => $f->montant_ttc), 2);
    $rpPeutRepartir = $facture->actif
        && in_array($facture->statut, \App\Models\Facture::STATUTS_REPARTISSABLES, true)
        && Auth::user()->hasPermission('Gestion des Factures', 'ajouter');
    $rpRegle = $rpPeutRepartir ? (\App\Support\CreditsClients::payeParFacture([$facture->id])[$facture->id] ?? 0) : 0;
    $rpMax = round($facture->montant_ttc - max($rpRegle, 0.01), 2);
    $rpErreurs = $errors->repartition;
    $rpMoney = fn ($n) => number_format((float) $n, 2, ',', ' ').' DH';
@endphp
@if($rpFamille->count() > 1 || $rpPeutRepartir)
<div class="facture-actions card border-0 shadow-sm mb-3" id="repartition">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Répartition en mini-factures</h6>
            @if($facture->factureParent)
                <span class="small text-muted">— mini-facture issue de
                    <a href="{{ route('factures.show', $facture->factureParent) }}">{{ $facture->factureParent->numero_facture }}</a></span>
            @endif
        </div>

        @if($rpFamille->count() > 1)
            <div class="table-responsive mb-3">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Facture</th><th>Date</th><th>Statut</th><th class="text-end">TTC</th></tr>
                    </thead>
                    <tbody>
                        @foreach($rpFamille as $f)
                            <tr class="{{ $f->id === $facture->id ? 'table-primary' : '' }}">
                                <td>
                                    <a href="{{ route('factures.show', $f) }}" class="fw-semibold text-decoration-none">{{ $f->numero_facture }}</a>
                                    @if($f->id === $rpRacine->id)<span class="badge text-bg-light border ms-1">origine</span>@endif
                                    @unless($f->actif)<span class="badge text-bg-secondary ms-1">désactivée</span>@endunless
                                </td>
                                <td>{{ $f->date_emission?->format('d/m/Y') }}</td>
                                <td>{{ $f->statut_label }}</td>
                                <td class="text-end text-nowrap">{{ $rpMoney($f->montant_ttc) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold"><td colspan="3">Montant d’origine ({{ $rpFamille->count() }} factures)</td><td class="text-end text-nowrap">{{ $rpMoney($rpOrigine) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        @endif

        @if($rpPeutRepartir && $rpMax > 0)
            <p class="small text-muted mb-2">
                Sortez une partie de cette facture ({{ $rpMoney($facture->montant_ttc) }} TTC) dans une nouvelle facture avec son propre numéro.
                Le montant sorti est déduit de cette facture : le total facturé au client ne change pas.
            </p>
            @if($rpErreurs->any())
                <div class="alert alert-danger py-2 small">{{ $rpErreurs->first() }}</div>
            @endif
            <form action="{{ route('factures.repartir', $facture) }}" method="post" class="row g-2 align-items-end" id="formRepartition">
                @csrf
                <div class="col-sm-4 col-lg-3">
                    <label class="form-label small mb-1">Montant TTC de la mini-facture *</label>
                    <div class="input-group input-group-sm">
                        <input type="number" name="montant_ttc" class="form-control" step="0.01" min="0.01" max="{{ $rpMax }}"
                               value="{{ old('montant_ttc') }}" placeholder="Ex : 6000" required data-ttc="{{ $facture->montant_ttc }}">
                        <span class="input-group-text">DH</span>
                    </div>
                </div>
                <div class="col-sm-4 col-lg-2">
                    <label class="form-label small mb-1">Date *</label>
                    <input type="date" name="date_emission" class="form-control form-control-sm" value="{{ old('date_emission', now()->toDateString()) }}" required>
                </div>
                <div class="col-sm-4 col-lg-3">
                    <div class="small text-muted">Reste sur {{ $facture->numero_facture }}</div>
                    <strong id="repartitionReste">{{ $rpMoney($facture->montant_ttc) }}</strong>
                </div>
                <div class="col-12">
                    <label class="form-label small mb-1">Désignation de la mini-facture *</label>
                    <textarea name="designation" class="form-control form-control-sm" rows="2" maxlength="5000" required>{{ old('designation', $facture->designation_unique) }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-scissors me-1"></i>Créer la mini-facture</button>
                    @if($rpRegle > 0)
                        <span class="small text-muted ms-2">{{ $rpMoney($rpRegle) }} déjà réglés sur cette facture : maximum {{ $rpMoney($rpMax) }}.</span>
                    @endif
                </div>
            </form>
            <script>
                (function () {
                    const champ = document.querySelector('#formRepartition [name="montant_ttc"]');
                    const reste = document.getElementById('repartitionReste');
                    const ttc = parseFloat(champ.dataset.ttc);
                    const maj = () => {
                        const v = parseFloat(champ.value) || 0;
                        reste.textContent = (ttc - v).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' DH';
                        reste.classList.toggle('text-danger', v > parseFloat(champ.max));
                    };
                    champ.addEventListener('input', maj);
                    maj();
                })();
            </script>
        @endif
    </div>
</div>
@endif
