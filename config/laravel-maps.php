<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'paths' => resource_path('views'),
    'mapbox' => [
        'access_token' => env('MAPS_MAPBOX_ACCESS_TOKEN', null),
    ],
    'google_maps' => [
        // Clé avec « Maps JavaScript API » activée (restrictions HTTP recommandées)
        'access_token' => env('MAPS_GOOGLE_MAPS_ACCESS_TOKEN', env('GOOGLE_MAPS_API_KEY')),
    ],
    'defaults' => [
        'zoom' => 5,
        'center' => [
            'lat' => 31.7917, // Centre approximatif du Maroc
            'lng' => -7.0926,
        ],
        'scrollWheelZoom' => true,
    ],
    'leaflet' => [
        'tile' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    ],

    /*
     * Suivi déplacements : carte Leaflet (tuiles réelles, style proche dashboards type Risk).
     * TRACKING_MAP_STYLE=osm → fond OSM classique | dark (défaut) → Carto Dark Matter
     */
    'tracking' => [
        'tile_url' => env('TRACKING_MAP_TILE_URL', match (env('TRACKING_MAP_STYLE', 'dark')) {
            'osm' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            default => 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
        }),
        'tile_subdomains' => env('TRACKING_MAP_SUBDOMAINS', match (env('TRACKING_MAP_STYLE', 'dark')) {
            'osm' => 'abc',
            default => 'abcd',
        }),
        'tile_attribution' => env('TRACKING_MAP_ATTRIBUTION', match (env('TRACKING_MAP_STYLE', 'dark')) {
            'osm' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            default => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
        }),
    ],
];
