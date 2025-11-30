/**
 * ACF OpenStreetMap Initialization Script
 * Inicjalizuje mapy Leaflet na podstawie danych z ACF
 */

(function($) {
    'use strict';

    /**
     * Inicjalizacja mapy
     */
    function initMap(mapElement) {
        // Pobierz dane z atrybutu data-map
        const mapData = JSON.parse(mapElement.getAttribute('data-map'));

        if (!mapData || !mapData.lat || !mapData.lng) {
            console.error('Brak danych mapy');
            return;
        }

        // Utwórz mapę
        const map = L.map(mapElement.id).setView([mapData.lat, mapData.lng], mapData.zoom);

        // Dodaj warstwę OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        }).addTo(map);

        // Dodaj marker
        const marker = L.marker([mapData.lat, mapData.lng]).addTo(map);

        // Dodaj popup z opisem jeśli istnieje
        if (mapData.opis) {
            marker.bindPopup(mapData.opis).openPopup();
        }

        // Opcjonalnie: dodaj okrąg wokół lokalizacji
        // L.circle([mapData.lat, mapData.lng], {
        //     color: 'blue',
        //     fillColor: '#30a3ec',
        //     fillOpacity: 0.2,
        //     radius: 500
        // }).addTo(map);
    }

    /**
     * Inicjalizacja wszystkich map na stronie
     */
    function initAllMaps() {
        const mapElements = document.querySelectorAll('.acf-map');

        mapElements.forEach(function(mapElement) {
            // Sprawdź czy mapa już została zainicjalizowana
            if (!mapElement.classList.contains('map-initialized')) {
                initMap(mapElement);
                mapElement.classList.add('map-initialized');
            }
        });
    }

    // Inicjalizacja po załadowaniu DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllMaps);
    } else {
        initAllMaps();
    }

    // Obsługa AJAX (jeśli używasz dynamicznego ładowania treści)
    $(document).on('acf/setup_fields', function(e, postbox) {
        initAllMaps();
    });

})(jQuery);
