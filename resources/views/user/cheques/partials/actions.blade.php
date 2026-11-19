{{-- Attend : $cheque. Nécessite cheques.partials.action-modal une fois dans la page. --}}
@php
    $chqActions = [
        'en_portefeuille' => ['remettre', 'encaisser', 'impaye', 'restituer'],
        'remis' => ['encaisser', 'impaye'],
        'encaisse' => ['impaye'],
        'impaye' => ['representer', 'restituer'],
        'restitue' => [],
    ][$cheque->statut] ?? [];
    $chqLibelles = [
        'remettre' => ['bi-bank', 'Remettre en banque'],
        'encaisser' => ['bi-check2-circle', 'Marquer encaissé'],
        'impaye' => ['bi-x-octagon', 'Marquer impayé'],
        'restituer' => ['bi-arrow-return-left', 'Restituer au client'],
        'representer' => ['bi-arrow-repeat', 'Représenter (remettre en portefeuille)'],
    ];
@endphp
@if($chqActions && Auth::user()->hasPermission('Gestion des Crédits & Chèques', 'modifier'))
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Actions</button>
        <ul class="dropdown-menu dropdown-menu-end">
            @foreach($chqActions as $a)
                <li>
                    <button type="button" class="dropdown-item {{ $a === 'impaye' ? 'text-danger' : '' }}"
                        data-bs-toggle="modal" data-bs-target="#chequeActionModal"
                        data-url="{{ route('cheques.action', $cheque) }}"
                        data-action="{{ $a }}"
                        data-titre="{{ $chqLibelles[$a][1] }}"
                        data-cheque="n° {{ $cheque->numero }} — {{ number_format((float) $cheque->montant, 2, ',', ' ') }} DH">
                        <i class="bi {{ $chqLibelles[$a][0] }} me-2"></i>{{ $chqLibelles[$a][1] }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
@endif
