@extends('user.master')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .trk-shell {
        background: #f6f8fb;
        border: 1px solid #e6eaf1;
        border-radius: 18px;
        padding: 1rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }
    .trk-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
        margin-bottom: .9rem;
    }
    .trk-title {
        font-size: 1.55rem;
        font-weight: 700;
        color: #101828;
        margin: 0;
    }
    .trk-date {
        font-size: .9rem;
        color: #475467;
        font-weight: 500;
    }
    .trk-board {
        display: grid;
        grid-template-columns: 260px 1fr 280px;
        gap: 1rem;
        min-height: 640px;
    }
    .trk-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
    }
    .trk-left-head {
        padding: .85rem;
        border-bottom: 1px solid #eef2f7;
    }
    .trk-search {
        border: 1px solid #d0d5dd;
        border-radius: 999px;
        background: #fff;
        padding: .45rem .8rem;
        font-size: .85rem;
        color: #667085;
    }
    .trk-emp-list {
        max-height: 580px;
        overflow-y: auto;
    }
    .trk-emp-row {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .7rem .85rem;
        border-bottom: 1px solid #f2f4f7;
        cursor: pointer;
        transition: background .15s ease;
    }
    .trk-emp-row:hover { background: #f9fafb; }
    .trk-emp-row.active { background: #eef4ff; }
    .trk-emp-num {
        width: 22px;
        height: 22px;
        border-radius: 999px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
    }
    .trk-emp-meta strong {
        display: block;
        font-size: .85rem;
        color: #111827;
    }
    .trk-emp-meta small {
        color: #667085;
        font-size: .74rem;
    }
    .trk-map-wrap {
        position: relative;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        min-height: 640px;
        background: #eef2f7;
    }
    #track-map-like {
        position: absolute;
        inset: 0;
        z-index: 1;
    }
    .trk-map-top {
        position: absolute;
        top: .7rem;
        left: .7rem;
        right: .7rem;
        z-index: 650;
        display: flex;
        justify-content: space-between;
        pointer-events: none;
    }
    .trk-map-chip {
        pointer-events: auto;
        background: rgba(255, 255, 255, .92);
        border: 1px solid #e4e7ec;
        border-radius: 999px;
        padding: .4rem .75rem;
        font-size: .76rem;
        color: #344054;
        box-shadow: 0 2px 12px rgba(16, 24, 40, .08);
    }
    .trk-map-route {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }
    .trk-right {
        padding: .9rem;
    }
    .trk-avatar {
        width: 88px;
        height: 88px;
        border-radius: 999px;
        margin: .3rem auto .8rem;
        background: linear-gradient(135deg, #6f86ff, #3849d6);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 700;
        border: 4px solid #eff4ff;
    }
    .trk-right h4 {
        margin: 0;
        font-size: 1.05rem;
        text-align: center;
        color: #111827;
        font-weight: 700;
    }
    .trk-right .sub {
        text-align: center;
        color: #667085;
        font-size: .78rem;
        margin-bottom: .85rem;
    }
    .trk-status {
        text-align: center;
        margin-bottom: .9rem;
    }
    .trk-status span {
        background: #ecfdf3;
        color: #039855;
        border: 1px solid #abefc6;
        border-radius: 999px;
        padding: .28rem .6rem;
        font-size: .68rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: .04em;
    }
    .trk-stat-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .55rem;
    }
    .trk-stat {
        border: 1px solid #eef2f7;
        border-radius: 10px;
        padding: .45rem .5rem;
        background: #fafcff;
    }
    .trk-stat span {
        display: block;
        font-size: .65rem;
        color: #667085;
    }
    .trk-stat strong {
        font-size: .88rem;
        color: #101828;
    }
    .track-leaf-marker { background: transparent !important; border: 0 !important; }
    .track-pin {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,.28);
    }
    @media (max-width: 1199px) {
        .trk-board { grid-template-columns: 1fr; min-height: auto; }
        .trk-map-wrap { min-height: 460px; }
        .trk-panel { max-height: unset; }
    }
</style>

@php
    $activeEmp = $trackingEmployes[0] ?? null;
@endphp

<div class="section">
    <div class="trk-shell">
        <div class="trk-toolbar">
            <h1 class="trk-title">Map Tracking</h1>
            <div class="trk-date">{{ ucfirst($carteDate) }} · {{ $carteHeure }}</div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif

        <div class="trk-board">
            <aside class="trk-panel">
                <div class="trk-left-head">
                    <input class="trk-search w-100" value="Filter employees" readonly>
                </div>
                <div class="trk-emp-list">
                    @foreach($trackingEmployes as $emp)
                        <div class="trk-emp-row {{ $loop->first ? 'active' : '' }}" data-emp='@json($emp)' data-num="{{ $emp['numero'] }}">
                            <span class="trk-emp-num" style="background: {{ $emp['numero'] === 1 ? '#4f46e5' : '#0ea5e9' }}">{{ $emp['numero'] }}</span>
                            <div class="trk-emp-meta">
                                <strong>{{ $emp['nom'] }}</strong>
                                <small>{{ $emp['poste'] }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </aside>

            <div class="trk-map-wrap">
                <div class="trk-map-top">
                    <div class="trk-map-chip">
                        <i class="bi bi-calendar-event me-1"></i>{{ $carteDate }}
                    </div>
                    <div class="trk-map-chip trk-map-route" id="trk-active-route">Route active</div>
                    <div class="trk-map-chip">
                        <i class="bi bi-reception-4 text-success me-1"></i>Live
                    </div>
                </div>
                <div id="track-map-like" aria-label="Carte de suivi"></div>
            </div>

            <aside class="trk-panel trk-right" id="trk-profile-panel">
                @if($activeEmp)
                    <div class="trk-avatar" id="trk-avatar">{{ strtoupper(substr($activeEmp['nom'], 0, 1)) }}</div>
                    <h4 id="trk-name">{{ $activeEmp['nom'] }}</h4>
                    <div class="sub" id="trk-job">{{ $activeEmp['poste'] }}</div>
                    <div class="trk-status"><span id="trk-status">{{ $activeEmp['statut'] }}</span></div>
                    <div class="trk-stat-grid">
                        <div class="trk-stat"><span>Mission</span><strong id="trk-mission">{{ $activeEmp['mission'] }}</strong></div>
                        <div class="trk-stat"><span>Vitesse</span><strong id="trk-speed">{{ $activeEmp['vitesse'] }} km/h</strong></div>
                        <div class="trk-stat"><span>Distance</span><strong id="trk-distance">{{ $activeEmp['distance_km'] }} km</strong></div>
                        <div class="trk-stat"><span>ETA</span><strong id="trk-eta">{{ $activeEmp['duree_eta'] }}</strong></div>
                        <div class="trk-stat"><span>Véhicule</span><strong id="trk-vehicle">{{ $activeEmp['vehicule'] }}</strong></div>
                        <div class="trk-stat"><span>Immat.</span><strong id="trk-immat">{{ $activeEmp['immat'] }}</strong></div>
                        <div class="trk-stat"><span>Carburant</span><strong id="trk-fuel">{{ $activeEmp['carburant_pct'] }} %</strong></div>
                        <div class="trk-stat"><span>MAJ</span><strong id="trk-maj">{{ $activeEmp['derniere_maj'] }}</strong></div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    const employees = @json($trackingEmployes);
    const tileUrl = @json($mapTileUrl);
    const tileSubdomains = @json($mapTileSubdomains);
    const attribution = @json($mapAttribution);

    const mapEl = document.getElementById('track-map-like');
    if (!mapEl || typeof L === 'undefined' || !employees.length) return;

    const map = L.map('track-map-like', {
        zoomControl: true,
        attributionControl: true,
        scrollWheelZoom: true,
    });

    L.tileLayer(tileUrl, {
        attribution: attribution,
        maxZoom: 19,
        subdomains: tileSubdomains || 'abcd',
    }).addTo(map);

    const latlngs = employees.map(e => [e.lat, e.lng]);
    const markerRefs = [];

    function pinIcon(num, color) {
        return L.divIcon({
            className: 'track-leaf-marker',
            html: '<div class="track-pin" style="background:' + color + '">' + num + '</div>',
            iconSize: [26, 26],
            iconAnchor: [13, 13],
        });
    }

    let routeLine = null;
    if (latlngs.length >= 2) {
        const a = latlngs[0];
        const b = latlngs[1];
        const mid = [(a[0] + b[0]) / 2 + 0.035, (a[1] + b[1]) / 2 - 0.015];
        routeLine = L.polyline([a, mid, b], {
            color: '#4f46e5',
            weight: 4,
            opacity: 0.9,
            lineCap: 'round',
            dashArray: '10 8',
        }).addTo(map);
    }

    employees.forEach((e, i) => {
        const color = i === 0 ? '#4f46e5' : '#0ea5e9';
        const marker = L.marker([e.lat, e.lng], { icon: pinIcon(e.numero, color) }).addTo(map);
        marker.bindPopup('<strong>' + e.nom + '</strong><br>' + e.zone + ' · ' + e.quartier);
        markerRefs.push(marker);
    });

    map.fitBounds(L.latLngBounds(latlngs), { padding: [70, 70], maxZoom: 10 });

    const routeLabel = document.getElementById('trk-active-route');
    const rows = Array.from(document.querySelectorAll('.trk-emp-row'));
    const setProfile = (emp) => {
        const byId = (id) => document.getElementById(id);
        byId('trk-avatar').textContent = (emp.nom || '?').trim().charAt(0).toUpperCase();
        byId('trk-name').textContent = emp.nom || '-';
        byId('trk-job').textContent = emp.poste || '-';
        byId('trk-status').textContent = emp.statut || '-';
        byId('trk-mission').textContent = emp.mission || '-';
        byId('trk-speed').textContent = (emp.vitesse || '0') + ' km/h';
        byId('trk-distance').textContent = (emp.distance_km || '0') + ' km';
        byId('trk-eta').textContent = emp.duree_eta || '-';
        byId('trk-vehicle').textContent = emp.vehicule || '-';
        byId('trk-immat').textContent = emp.immat || '-';
        byId('trk-fuel').textContent = (emp.carburant_pct || '0') + ' %';
        byId('trk-maj').textContent = emp.derniere_maj || '-';
    };

    if (employees.length >= 2 && routeLabel) {
        routeLabel.textContent = employees[0].zone + ' → ' + employees[1].zone;
    }

    rows.forEach((row, idx) => {
        row.addEventListener('click', () => {
            rows.forEach(x => x.classList.remove('active'));
            row.classList.add('active');
            const emp = JSON.parse(row.dataset.emp || '{}');
            setProfile(emp);
            if (markerRefs[idx]) {
                map.setView(markerRefs[idx].getLatLng(), 12, { animate: true });
                markerRefs[idx].openPopup();
            }
        });
    });
})();
</script>
@endpush
