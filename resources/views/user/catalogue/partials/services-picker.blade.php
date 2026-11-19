{{-- Attend : $services, $selection (ids cochés) --}}
<div class="cat-services">
    <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
        <label class="form-label mb-0">Prestations incluses <span class="small text-muted cat-services-count"></span></label>
        <input type="search" class="form-control form-control-sm modern-input cat-services-search" placeholder="Rechercher…" style="max-width: 220px">
    </div>
    <div class="cat-services-grid">
        @foreach($services->where('actif', true) as $s)
            <label class="cat-choice" data-nom="{{ \Illuminate\Support\Str::lower($s->nom) }}">
                <input type="checkbox" name="services[]" value="{{ $s->id }}" {{ in_array($s->id, $selection) ? 'checked' : '' }}>
                <span class="cat-choice-body">
                    <span class="cat-choice-check"><i class="bi bi-check-lg"></i></span>
                    @if($s->image)
                        <img src="{{ asset($s->image) }}" alt="" loading="lazy">
                    @else
                        <span class="cat-choice-noimg"><i class="bi bi-image"></i></span>
                    @endif
                    <span class="cat-choice-nom">{{ $s->nom }}</span>
                    <span class="cat-choice-prix">{{ number_format($s->prix_apres_reduction, 2, ',', ' ') }} MAD</span>
                </span>
            </label>
        @endforeach
    </div>
</div>
