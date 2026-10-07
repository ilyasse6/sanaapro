{{--
    Champ client : sélection dans la liste OU saisie libre.
    Paramètres : $clients, $selectedClientId (optionnel), $avecOptionVide (bool, défaut true).
--}}
@php
    $modeLibre = old('client_mode') === 'libre';
    $avecOptionVide = $avecOptionVide ?? true;
@endphp
<div class="js-client-field">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label mb-0">Client (raison sociale / contact) <span class="text-danger">*</span></label>
        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none js-client-mode-toggle"
            data-label-liste="Choisir dans la liste" data-label-libre="Saisir un nom libre">
            <i class="bi {{ $modeLibre ? 'bi-list-ul' : 'bi-pencil' }} me-1"></i><span>{{ $modeLibre ? 'Choisir dans la liste' : 'Saisir un nom libre' }}</span>
        </button>
    </div>
    <input type="hidden" name="client_mode" value="{{ $modeLibre ? 'libre' : 'liste' }}" class="js-client-mode">
    <select name="client_id" class="form-select modern-input js-client-select {{ $modeLibre ? 'd-none' : '' }}" {{ $modeLibre ? 'disabled' : 'required' }}>
        @if($avecOptionVide)
            <option value="">Choisir un client</option>
        @endif
        @foreach($clients as $c)
            <option value="{{ $c->id }}" {{ (string) old('client_id', $selectedClientId ?? '') === (string) $c->id ? 'selected' : '' }}>{{ $c->nom }}@if($c->prenom) – {{ $c->prenom }}@endif</option>
        @endforeach
    </select>
    <input type="text" name="client_libre" class="form-control modern-input js-client-libre {{ $modeLibre ? '' : 'd-none' }}"
        value="{{ old('client_libre') }}" maxlength="255" placeholder="Raison sociale du client"
        {{ $modeLibre ? 'required' : 'disabled' }}>
    <div class="form-text js-client-libre-aide {{ $modeLibre ? '' : 'd-none' }}">Si ce nom n'existe pas encore, une fiche client sera créée automatiquement.</div>
</div>

<script>
    window.clientSelectionLabel = window.clientSelectionLabel || function (form) {
        const libre = form.querySelector('.js-client-libre');
        if (libre && !libre.disabled) return libre.value.trim();
        const sel = form.querySelector('select[name="client_id"]');
        const opt = sel && sel.options[sel.selectedIndex];
        return opt && opt.value ? opt.text.trim() : '';
    };

    document.querySelectorAll('.js-client-field:not([data-init])').forEach(function (field) {
        field.dataset.init = '1';
        const toggle = field.querySelector('.js-client-mode-toggle');
        const mode = field.querySelector('.js-client-mode');
        const select = field.querySelector('.js-client-select');
        const libre = field.querySelector('.js-client-libre');
        const aide = field.querySelector('.js-client-libre-aide');

        toggle.addEventListener('click', function () {
            const versLibre = mode.value !== 'libre';
            mode.value = versLibre ? 'libre' : 'liste';
            select.classList.toggle('d-none', versLibre);
            select.disabled = versLibre;
            select.required = !versLibre;
            libre.classList.toggle('d-none', !versLibre);
            aide.classList.toggle('d-none', !versLibre);
            libre.disabled = !versLibre;
            libre.required = versLibre;
            toggle.querySelector('i').className = 'bi ' + (versLibre ? 'bi-list-ul' : 'bi-pencil') + ' me-1';
            toggle.querySelector('span').textContent = versLibre ? toggle.dataset.labelListe : toggle.dataset.labelLibre;
            (versLibre ? libre : select).focus();
            (versLibre ? libre : select).dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
</script>
