@extends('user.master')

@section('content')
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />

<style>
    .rdv-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 2rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #17324D;
        margin-bottom: 1.5rem;
    }

    .view-switcher {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .btn-view {
        padding: 0.5rem 1rem;
        border: 1px solid #dee2e6;
        background: white;
        border-radius: 6px;
        color: #495057;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-view.active {
        background: #198754;
        border-color: #198754;
        color: white;
    }

    .btn-view:hover {
        background: #f8f9fa;
    }

    .btn-view.active:hover {
        background: #146c43;
    }

    .calendar-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .day-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .day-date {
        font-size: 1.2rem;
        font-weight: 600;
        color: #17324D;
    }

    .day-nav {
        display: flex;
        gap: 0.5rem;
    }

    /* FullCalendar Styles */
    .fc {
        font-family: "Poppins", sans-serif;
    }

    .fc-toolbar-title {
        font-size: 1.3rem !important;
        font-weight: 600 !important;
        color: #17324D !important;
    }

    .fc-button {
        border-radius: 6px !important;
        border: 1px solid #dee2e6 !important;
        background: white !important;
        color: #495057 !important;
        padding: 0.4rem 0.8rem !important;
        font-weight: 500 !important;
    }

    .fc-button:hover {
        background: #f8f9fa !important;
    }

    .fc-button-active {
        background: #198754 !important;
        border-color: #198754 !important;
        color: white !important;
    }

    .fc-daygrid-day-number {
        color: #17324D;
        font-weight: 600;
    }

    .fc-event {
        border-radius: 6px !important;
        border: none !important;
        padding: 4px 8px !important;
        cursor: pointer;
        font-size: 0.85rem !important;
    }

    .fc-event:hover {
        opacity: 0.9;
    }

    .fc-timeGridDay-view .fc-event {
        font-size: 0.9rem !important;
    }

    .btn-nav {
        padding: 0.5rem 1rem;
        border: 1px solid #dee2e6;
        background: white;
        border-radius: 6px;
        color: #17324D;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-nav:hover {
        background: #f8f9fa;
        border-color: #198754;
        color: #198754;
    }

    .btn-add {
        padding: 0.75rem 1.5rem;
        background: #198754;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-add:hover {
        background: #146c43;
    }

    /* Programme du jour vertical */
    .time-slot {
        display: flex;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 0;
        min-height: 70px;
    }

    .time-label {
        width: 80px;
        font-weight: 600;
        color: #495057;
        font-size: 0.95rem;
        padding-right: 1rem;
        border-right: 2px solid #e9ecef;
        display: flex;
        align-items: center;
    }

    .time-content {
        flex: 1;
        padding-left: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .rdv-item {
        background: white;
        border: 2px solid #e9ecef;
        border-left: 4px solid #198754;
        padding: 1rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .rdv-item:hover {
        border-left-color: #146c43;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateX(3px);
    }

    .rdv-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .rdv-client-name {
        font-weight: 600;
        color: #17324D;
        font-size: 1rem;
    }

    .rdv-client-name small {
        font-weight: 400;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .rdv-time {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
    }

    .rdv-services {
        font-size: 0.9rem;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .rdv-services-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .rdv-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #e9ecef;
    }

    .rdv-prix {
        font-weight: 600;
        color: #198754;
        font-size: 1rem;
    }

    .statut-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .statut-en_attente { background: #fff3cd; color: #856404; }
    .statut-confirme { background: #d1ecf1; color: #0c5460; }
    .statut-en_cours { background: #cfe2ff; color: #084298; }
    .statut-termine { background: #d1e7dd; color: #0f5132; }
    .statut-annule { background: #f8d7da; color: #842029; }

    .empty-slot {
        color: #adb5bd;
        font-style: italic;
        font-size: 0.9rem;
        padding: 0.5rem 0;
    }

    .service-tag, .pack-tag {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: #e9ecef;
        border-radius: 12px;
        font-size: 0.8rem;
        margin-right: 0.5rem;
        margin-bottom: 0.25rem;
        color: #495057;
    }

    .pack-tag {
        background: #cfe2ff;
        color: #084298;
    }

    .modal-modern .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .modal-modern .modal-header {
        background: #198754;
        color: white;
        border-radius: 12px 12px 0 0;
        border: none;
        padding: 1.25rem;
    }

    .modal-modern .modal-body {
        padding: 1.5rem;
    }

    .modern-input {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        height: 38px;
        transition: all 0.2s;
        font-size: 0.9rem;
    }

    .modern-input:focus {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(65, 84, 241, 0.1);
        outline: none;
    }

    .services-packs-selection {
        margin-top: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        max-height: 300px;
        overflow-y: auto;
    }

    .selection-item {
        display: inline-block;
        margin: 0.5rem;
        cursor: pointer;
    }

    .selection-bubble {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        color: #495057;
        transition: all 0.2s;
        min-height: 40px;
    }

    .selection-item input:checked + .selection-bubble {
        background: #198754 !important;
        border-color: #198754 !important;
        color: white !important;
    }

    /* Icônes coché / décoché (les deux présents dans le DOM) */
    .selection-item input:not(:checked) + .selection-bubble .rdv-sel-icon-on {
        display: none !important;
    }

    .selection-item input:not(:checked) + .selection-bubble .rdv-sel-icon-off {
        display: inline-block;
    }

    .selection-item input:checked + .selection-bubble .rdv-sel-icon-off {
        display: none !important;
    }

    .selection-item input:checked + .selection-bubble .rdv-sel-icon-on {
        display: inline-block !important;
    }

    .selection-bubble:hover {
        border-color: #198754;
    }

    .selection-item input:checked + .selection-bubble:hover {
        background: #146c43 !important;
        border-color: #146c43 !important;
    }

    .btn-modern {
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        font-size: 0.9rem;
    }

    .btn-modern-primary {
        background: #198754;
        color: white;
    }

    .btn-modern-primary:hover {
        background: #146c43;
        color: white;
    }

    .btn-modern-secondary {
        background: #e9ecef;
        color: #495057;
    }

    .btn-modern-secondary:hover {
        background: #dee2e6;
        color: #495057;
    }

    .btn-modern-danger {
        background: #dc3545;
        color: white;
    }

    .btn-modern-danger:hover {
        background: #c82333;
        color: white;
    }
</style>

<div class="section">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="section-title mb-0">
                    <i class="bi bi-calendar-check me-2"></i>
                    Visites & rendez-vous commerciaux
                </h2>
                <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#homeDashboardAjouterModal">
                    <i class="bi bi-plus-lg me-2"></i>
                    Ajouter
                </button>
            </div>
            <p class="text-muted mb-4">Planifiez et suivez vos visites clients : prestations, équipements (médical, cuisine, bureautique…) et packs. Consultez le programme par jour, semaine ou mois.</p>
        </div>
    </div>

    <style>@include('user.partials.dashboard-agenda-styles')</style>
    @include('user.partials.home-visites-rappels', ['hideAgendaToolbar' => true])
    @include('user.partials.home-dashboard-ajouter')

    <!-- Sélecteur de vue -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="view-switcher">
                <button class="btn-view active" data-view="day" id="btnViewDay">
                    <i class="bi bi-calendar-day me-1"></i> Jour
                </button>
                <button class="btn-view" data-view="week" id="btnViewWeek">
                    <i class="bi bi-calendar-week me-1"></i> Semaine
                </button>
                <button class="btn-view" data-view="month" id="btnViewMonth">
                    <i class="bi bi-calendar-month me-1"></i> Mois
                </button>
            </div>
        </div>
    </div>

    <!-- Vue Calendrier (Semaine/Mois) -->
    <div class="row" id="calendarView" style="display: none;">
        <div class="col-12">
            <div class="calendar-container">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Vue Jour : programme des visites -->
    <div class="row" id="dayView">
        <div class="col-12">
            <div class="rdv-container">
                <div class="day-header">
                    <div class="day-date" id="currentDate"></div>
                    <div class="day-nav">
                        <button class="btn-nav" id="prevDay">
                            <i class="bi bi-chevron-left"></i> Précédent
                        </button>
                        <button class="btn-nav" id="todayBtn">
                            Aujourd'hui
                        </button>
                        <button class="btn-nav" id="nextDay">
                            Suivant <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <div id="dayProgram" class="day-program">
                    <!-- Programme des visites du jour (généré par JavaScript) -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Modifier la visite -->
@if(isset($rdvToEdit) && $rdvToEdit)
    @include('user.rendez-vous.modals.edit', ['rdv' => $rdvToEdit])
@endif

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedDate = new Date();
    let currentView = 'day';
    let calendar = null;

    // Mettre à jour le programme du jour
    function updateDayProgram(date) {
        const dateStr = date.toISOString().split('T')[0];
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('currentDate').textContent = date.toLocaleDateString('fr-FR', options);

        // Récupérer les visites du jour
        fetch('{{ route("rendez-vous.index") }}?start=' + dateStr + '&end=' + dateStr, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(events => {
            displayDayProgram(events, date);
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('dayProgram').innerHTML = '<div class="text-center text-danger">Erreur lors du chargement des visites et rendez-vous</div>';
        });
    }

    // Afficher le programme du jour verticalement
    function displayDayProgram(events, date) {
        const container = document.getElementById('dayProgram');
        container.innerHTML = '';

        // Créer les créneaux horaires (8h à 20h)
        const hours = [];
        for (let i = 8; i <= 20; i++) {
            hours.push(i);
        }

        // Grouper les événements par heure
        const eventsByHour = {};
        events.forEach(event => {
            const eventDate = new Date(event.start);
            const hour = eventDate.getHours();
            if (!eventsByHour[hour]) {
                eventsByHour[hour] = [];
            }
            eventsByHour[hour].push(event);
        });

        // Créer les créneaux
        hours.forEach(hour => {
            const timeSlot = document.createElement('div');
            timeSlot.className = 'time-slot';
            
            const timeLabel = document.createElement('div');
            timeLabel.className = 'time-label';
            timeLabel.textContent = hour.toString().padStart(2, '0') + ':00';
            
            const timeContent = document.createElement('div');
            timeContent.className = 'time-content';

            if (eventsByHour[hour] && eventsByHour[hour].length > 0) {
                eventsByHour[hour].forEach(event => {
                    const rdvItem = document.createElement('div');
                    rdvItem.className = 'rdv-item';
                    rdvItem.onclick = () => loadRendezVousForEdit(event.id);

                    const eventDate = new Date(event.start);
                    const timeStr = eventDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

                    // Titre format : "Raison sociale – Contact · Prestations/Produits"
                    const titleParts = event.title.split(' · ');
                    const clientName = (titleParts[0] || '').trim();
                    const servicesText = (titleParts[1] || '').trim();

                    rdvItem.innerHTML = `
                        <div class="rdv-item-header">
                            <span class="rdv-client-name">${clientName}</span>
                            <span class="rdv-time">${timeStr}</span>
                        </div>
                        ${servicesText ? `<div class="rdv-services-label">Prestations / Produits</div><div class="rdv-services">${servicesText}</div>` : ''}
                        <div class="rdv-footer">
                            <span class="statut-badge statut-${event.extendedProps.statut}">${getStatutLabel(event.extendedProps.statut)}</span>
                            <span class="rdv-prix">${parseFloat(event.extendedProps.prix_total || 0).toFixed(2)} MAD</span>
                        </div>
                    `;

                    timeContent.appendChild(rdvItem);
                });
            } else {
                const emptySlot = document.createElement('div');
                emptySlot.className = 'empty-slot';
                emptySlot.textContent = 'Aucune visite prévue';
                timeContent.appendChild(emptySlot);
            }

            timeSlot.appendChild(timeLabel);
            timeSlot.appendChild(timeContent);
            container.appendChild(timeSlot);
        });
    }

    function getStatutLabel(statut) {
        const labels = {
            'en_attente': 'Visite en attente',
            'confirme': 'Visite confirmée',
            'en_cours': 'Visite en cours',
            'termine': 'Visite terminée',
            'annule': 'Visite annulée'
        };
        return labels[statut] || statut;
    }

    function loadRendezVousForEdit(rdvId) {
        window.location.href = '{{ route("rendez-vous.index") }}?edit=' + rdvId;
    }

    // Initialiser le calendrier FullCalendar
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: currentView === 'week' ? 'timeGridWeek' : 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch('{{ route("rendez-vous.index") }}?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    failureCallback(error);
                });
            },
            eventClick: function(info) {
                loadRendezVousForEdit(info.event.id);
            },
            dateClick: function(info) {
                switchView('day');
                selectedDate = info.date;
                updateDayProgram(selectedDate);
                @if(Auth::user()->hasPermission('Gestion des Rendez-vous','ajouter'))
                    const agendaModalEl = document.getElementById('homeDashboardAjouterModal');
                    if (agendaModalEl) {
                        const addModal = new bootstrap.Modal(agendaModalEl);
                        addModal.show();
                        setTimeout(function() {
                            document.getElementById('homeMtBtnRdv')?.click();
                            const d = document.getElementById('home_rdv_date');
                            if (d) d.value = info.dateStr;
                        }, 350);
                    }
                @endif
            }
        });
        calendar.render();
    }

    // Changer de vue
    function switchView(view) {
        currentView = view;
        
        // Mettre à jour les boutons
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.classList.remove('active');
        });
        const btnId = 'btnView' + view.charAt(0).toUpperCase() + view.slice(1);
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.classList.add('active');
        }

        // Afficher/masquer les vues
        if (view === 'day') {
            document.getElementById('dayView').style.display = 'block';
            document.getElementById('calendarView').style.display = 'none';
            updateDayProgram(selectedDate);
        } else {
            document.getElementById('dayView').style.display = 'none';
            document.getElementById('calendarView').style.display = 'block';
            
            // Initialiser ou mettre à jour le calendrier
            if (!calendar) {
                initCalendar();
            } else {
                if (view === 'week') {
                    calendar.changeView('timeGridWeek');
                } else if (view === 'month') {
                    calendar.changeView('dayGridMonth');
                }
            }
        }
    }

    // Écouteurs pour les boutons de vue
    document.getElementById('btnViewDay').addEventListener('click', () => switchView('day'));
    document.getElementById('btnViewWeek').addEventListener('click', () => switchView('week'));
    document.getElementById('btnViewMonth').addEventListener('click', () => switchView('month'));

    // Navigation
    document.getElementById('prevDay').addEventListener('click', function() {
        selectedDate.setDate(selectedDate.getDate() - 1);
        updateDayProgram(selectedDate);
    });

    document.getElementById('nextDay').addEventListener('click', function() {
        selectedDate.setDate(selectedDate.getDate() + 1);
        updateDayProgram(selectedDate);
    });

    document.getElementById('todayBtn').addEventListener('click', function() {
        selectedDate = new Date();
        updateDayProgram(selectedDate);
    });

    // Initialiser le programme du jour
    updateDayProgram(selectedDate);

    // Ouvrir le modal d'édition si présent dans l'URL
    @if(isset($rdvToEdit) && $rdvToEdit)
        setTimeout(function() {
            const editModal = new bootstrap.Modal(document.getElementById('EditRendezVousModal-{{ $rdvToEdit->id }}'));
            editModal.show();
        }, 500);
    @endif

});
</script>

@endsection
