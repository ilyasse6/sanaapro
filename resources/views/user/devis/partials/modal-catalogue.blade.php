{{-- Attend : $services, $packs (avec services) --}}
<style>
    #modalCatalogue .mc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 12px; }
    #modalCatalogue .mc-card { display: flex; flex-direction: column; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; transition: box-shadow .15s, border-color .15s, transform .15s; }
    #modalCatalogue .mc-card:hover { border-color: #198754; box-shadow: 0 8px 22px rgba(15,36,56,.12); transform: translateY(-2px); }
    #modalCatalogue .mc-visuel { height: 130px; background: #f8fafc; display: flex; align-items: center; justify-content: center; padding: 8px; border-bottom: 1px solid #eef2f7; }
    #modalCatalogue .mc-visuel img { max-width: 100%; max-height: 100%; object-fit: contain; }
    #modalCatalogue .mc-visuel .mc-mosaique { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; width: 100%; height: 100%; }
    #modalCatalogue .mc-visuel .mc-mosaique img { width: 100%; height: 100%; }
    #modalCatalogue .mc-visuel i { font-size: 2.4rem; color: #cbd5e1; }
    #modalCatalogue .mc-infos { flex: 1; display: flex; flex-direction: column; gap: 4px; padding: 10px; }
    #modalCatalogue .mc-nom { font-size: .8rem; font-weight: 600; line-height: 1.25; color: #1e293b; }
    #modalCatalogue .mc-prix { font-size: .78rem; font-weight: 700; color: #198754; margin-top: auto; }
    #modalCatalogue .mc-card .add-from-catalogue { border-radius: 0; border-width: 1px 0 0; }
</style>

<div class="modal fade" id="modalCatalogue" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-box-seam me-2"></i>Ajouter depuis le catalogue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <ul class="nav nav-tabs border-0" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabPrestations">Prestations / Produits <span class="badge text-bg-light">{{ $services->count() }}</span></a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabPacks">Packs <span class="badge text-bg-light">{{ $packs->count() }}</span></a></li>
                    </ul>
                    <input type="search" id="mcRecherche" class="form-control form-control-sm" placeholder="Rechercher dans le catalogue…" style="max-width: 280px">
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tabPrestations">
                        <div class="mc-grid">
                            @foreach($services as $s)
                                <div class="mc-card" data-nom="{{ \Illuminate\Support\Str::lower($s->nom.' '.$s->description) }}">
                                    <div class="mc-visuel">
                                        @if($s->image)
                                            <img src="{{ asset($s->image) }}" alt="{{ $s->nom }}" loading="lazy">
                                        @else
                                            <i class="bi bi-image"></i>
                                        @endif
                                    </div>
                                    <div class="mc-infos">
                                        <div class="mc-nom">{{ $s->nom }}</div>
                                        <div class="mc-prix">{{ number_format($s->prix_apres_reduction, 2, ',', ' ') }} MAD</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-success add-from-catalogue" data-designation="{{ $s->nom }}" data-description="{{ $s->description }}" data-prix="{{ $s->prix_apres_reduction }}" data-type="prestation" data-id="{{ $s->id }}"><i class="bi bi-plus-lg me-1"></i>Ajouter</button>
                                </div>
                            @endforeach
                        </div>
                        @if($services->isEmpty())
                            <p class="text-muted mb-0">Aucune prestation dans le catalogue.</p>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="tabPacks">
                        <div class="mc-grid">
                            @foreach($packs as $p)
                                @php $pImages = $p->services->pluck('image')->filter()->take(4); @endphp
                                <div class="mc-card" data-nom="{{ \Illuminate\Support\Str::lower($p->nom.' '.$p->services->pluck('nom')->join(' ')) }}">
                                    <div class="mc-visuel">
                                        @if($pImages->count() > 1)
                                            <div class="mc-mosaique">
                                                @foreach($pImages as $img)<img src="{{ asset($img) }}" alt="" loading="lazy">@endforeach
                                            </div>
                                        @elseif($pImages->count() === 1)
                                            <img src="{{ asset($pImages->first()) }}" alt="{{ $p->nom }}" loading="lazy">
                                        @else
                                            <i class="bi bi-boxes"></i>
                                        @endif
                                    </div>
                                    <div class="mc-infos">
                                        <div class="mc-nom">{{ $p->nom }}</div>
                                        @if($p->services->isNotEmpty())
                                            <div class="small text-muted">{{ $p->services->count() }} prestation(s)</div>
                                        @endif
                                        <div class="mc-prix">{{ number_format($p->prix_apres_reduction, 2, ',', ' ') }} MAD</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-success add-from-catalogue" data-designation="{{ $p->nom }}" data-prix="{{ $p->prix_apres_reduction }}" data-type="pack" data-id="{{ $p->id }}"><i class="bi bi-plus-lg me-1"></i>Ajouter</button>
                                </div>
                            @endforeach
                        </div>
                        @if($packs->isEmpty())
                            <p class="text-muted mb-0">Aucun pack dans le catalogue.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var recherche = document.getElementById('mcRecherche');
    recherche.addEventListener('input', function () {
        var q = this.value.trim().toLowerCase();
        document.querySelectorAll('#modalCatalogue .mc-card').forEach(function (carte) {
            carte.classList.toggle('d-none', q !== '' && carte.dataset.nom.indexOf(q) === -1);
        });
    });
    document.getElementById('modalCatalogue').addEventListener('shown.bs.modal', function () { recherche.focus(); });
});
</script>
