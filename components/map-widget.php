<?php
function renderMapWidget() {
    ?>
    <div class="col-md-4">
        <div class="dashboard-card3 map-card">
            <div class="map-container" id="mini-map"></div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var donaImeldaCoords = [14.6151, 121.0177];
        var donaImeldaBoundary = [
            [14.618895, 121.014998],
            [14.619204, 121.016134],
            [14.618549, 121.016925],
            [14.618784, 121.019209],
            [14.615562, 121.020689],
            [14.611376, 121.019757],
            [14.607833, 121.022271],
            [14.604648, 121.019936],
            [14.607290, 121.017461],
            [14.615302, 121.016682],
            [14.618833, 121.015037]
        ];

        var miniMap = L.map('mini-map', {
            center: [14.6151, 121.0177],
            dragging: false,
            zoom: 15,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            boxZoom: false,
            keyboard: false,
            zoomControl: false,
            attributionControl: false
        }).setView(donaImeldaCoords, 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19}).addTo(miniMap);

        var polygon = L.polygon(donaImeldaBoundary, {
            color: '#28a745',
            weight: 1,
            fillColor: '#28a745',
            fillOpacity: 0.2
        }).addTo(miniMap)
          .bindPopup('Doña Imelda, Quezon City');

        miniMap.fitBounds(polygon.getBounds());
    });
    </script>

    
    <?php
}
?>