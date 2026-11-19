{{-- Attend : $galerie (chemin relatif à public/ => libellé), $actuelle (chemin ou null).
     Les vignettes de la galerie sont copiées depuis #catGalerieModele à l'ouverture du modal. --}}
@php $actuelle = $actuelle ?? null; @endphp
<div class="cat-picker">
    <label class="form-label d-flex justify-content-between align-items-center">
        <span>Image</span>
        <span class="small text-muted cat-picker-label">{{ $actuelle ? ($galerie[$actuelle] ?? 'Image actuelle') : 'Aucune image' }}</span>
    </label>
    <input type="hidden" name="image" value="{{ $actuelle }}" class="cat-picker-value">
    <div class="cat-picker-grid">
        <button type="button" class="cat-picker-item cat-picker-none {{ $actuelle ? '' : 'is-selected' }}" data-image="" title="Aucune image">
            <i class="bi bi-slash-circle"></i>
            <span>Aucune</span>
        </button>
        @if($actuelle && ! array_key_exists($actuelle, $galerie))
            <button type="button" class="cat-picker-item is-selected" data-image="{{ $actuelle }}" title="Image actuelle">
                <img src="{{ asset($actuelle) }}" alt="" loading="lazy">
                <span>Actuelle</span>
            </button>
        @endif
    </div>
    <div class="cat-picker-upload mt-2">
        <label class="btn btn-sm btn-outline-secondary mb-0">
            <i class="bi bi-upload me-1"></i>Importer une image
            <input type="file" name="image_upload" accept="image/png,image/jpeg,image/webp" class="d-none cat-picker-file">
        </label>
        <img class="cat-picker-preview d-none" alt="">
        <span class="small text-muted cat-picker-filename"></span>
    </div>
    <small class="text-muted d-block mt-1">PNG, JPG ou WEBP, 5 Mo max. L'image importée est ajoutée à la galerie.</small>
</div>
