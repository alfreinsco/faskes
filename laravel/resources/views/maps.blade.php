<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Peta Fasilitas Kesehatan</title>
    <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
    <link rel="stylesheet" href="{{ asset('leaflet-routing-machine/dist/leaflet-routing-machine.css') }}">
    <style>
        #map {
            height: 100vh;
            width: 100%;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .clear-route-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            z-index: 1000;
            background: #e74c3c;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .clear-route-btn:hover {
            background: #c0392b;
            transform: scale(1.1);
        }

        .clear-route-btn.show {
            display: flex;
        }

        /* Animation for current location marker */
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.3;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.1;
            }

            100% {
                transform: scale(1);
                opacity: 0.3;
            }
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <!-- Clear Route Button -->
    <button class="clear-route-btn" onclick="clearRoute()" title="Hapus Rute Aktif">
        ✕
    </button>

    <!-- Load Leaflet.js first -->
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
    <script src="{{ asset('leaflet-routing-machine/dist/leaflet-routing-machine.js') }}"></script>

    <script>
        // Initialize map
        var map = L.map('map').setView([-3.6561, 128.1664], 13);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Variables for routing and state management
        var currentLocation = null;
        var routingControl = null;
        var locationWatchId = null;
        var lastLocationUpdate = null;
        var mapState = {
            center: [-3.6561, 128.1664],
            zoom: 13,
            route: null
        };

        // Get user's current location with high accuracy and real-time updates
        function getCurrentLocation() {
            if (navigator.geolocation) {
                const options = {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 30000 // 30 seconds for more frequent updates
                };

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        updateCurrentLocation({
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        });
                    },
                    function(error) {
                        console.log('Geolocation error:', error);
                        let errorMessage = 'Tidak dapat mengakses lokasi: ';
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Izin lokasi ditolak';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Lokasi tidak tersedia';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'Timeout saat mengambil lokasi';
                                break;
                            default:
                                errorMessage += 'Error tidak diketahui';
                                break;
                        }
                        console.log(errorMessage);

                        // Fallback to Ambon coordinates
                        updateCurrentLocation({
                            lat: -3.6561,
                            lng: 128.1664,
                            accuracy: 0
                        });
                    },
                    options
                );

                // Watch position for continuous real-time updates
                locationWatchId = navigator.geolocation.watchPosition(
                    function(position) {
                        updateCurrentLocation({
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        });
                    },
                    function(error) {
                        console.log('Watch position error:', error);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 10000 // 10 seconds for real-time updates
                    }
                );
            } else {
                console.log('Geolocation not supported');
                // Fallback if geolocation is not supported
                updateCurrentLocation({
                    lat: -3.6561,
                    lng: 128.1664,
                    accuracy: 0
                });
            }
        }

        // Update current location and refresh UI
        function updateCurrentLocation(location) {
            const now = Date.now();

            // Only update if location changed significantly or enough time has passed
            if (!currentLocation ||
                Math.abs(currentLocation.lat - location.lat) > 0.0001 ||
                Math.abs(currentLocation.lng - location.lng) > 0.0001 ||
                !lastLocationUpdate ||
                (now - lastLocationUpdate) > 5000) { // Update at least every 5 seconds

                currentLocation = {
                    lat: location.lat,
                    lng: location.lng,
                    accuracy: location.accuracy
                };
                lastLocationUpdate = now;

                console.log('Location updated:', currentLocation);
                console.log('Accuracy:', location.accuracy, 'meters');

                // Update map state
                mapState.center = [currentLocation.lat, currentLocation.lng];

                // Update URL with new location
                updateURLState();

                // Update or create location marker
                updateLocationMarker();

                // Update route if active
                if (routingControl && mapState.route) {
                    updateActiveRoute();
                }
            }
        }

        // Update location marker
        function updateLocationMarker() {
            // Remove existing location marker
            map.eachLayer(function(layer) {
                if (layer instanceof L.Marker && layer.options.icon &&
                    layer.options.icon.options &&
                    layer.options.icon.options.className === 'current-location-marker') {
                    map.removeLayer(layer);
                }
            });

            // Add new location marker with better design
            L.marker([currentLocation.lat, currentLocation.lng], {
                icon: L.divIcon({
                    className: 'current-location-marker',
                    html: `
                        <div style="
                            position: relative;
                            width: 40px;
                            height: 40px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        ">
                            <!-- Outer pulsing circle -->
                            <div style="
                                position: absolute;
                                width: 40px;
                                height: 40px;
                                background: #3498db;
                                border-radius: 50%;
                                opacity: 0.3;
                                animation: pulse 2s infinite;
                            "></div>

                            <!-- Middle circle -->
                            <div style="
                                position: absolute;
                                width: 30px;
                                height: 30px;
                                background: #3498db;
                                border-radius: 50%;
                                opacity: 0.5;
                                animation: pulse 2s infinite 0.5s;
                            "></div>

                            <!-- Inner circle with icon -->
                            <div style="
                                position: relative;
                                width: 20px;
                                height: 20px;
                                background: #2c3e50;
                                border: 3px solid #ffffff;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                                z-index: 10;
                            ">
                                <div style="
                                    color: #ffffff;
                                    font-size: 10px;
                                    font-weight: bold;
                                ">📍</div>
                            </div>
                        </div>

                        <style>
                            @keyframes pulse {
                                0% { transform: scale(1); opacity: 0.3; }
                                50% { transform: scale(1.2); opacity: 0.1; }
                                100% { transform: scale(1); opacity: 0.3; }
                            }
                        </style>
                    `,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20],
                    popupAnchor: [0, -20]
                })
            }).addTo(map).bindPopup(`
                <div style="
                    text-align: center;
                    min-width: 200px;
                    font-family: Arial, sans-serif;
                ">
                    <div style="
                        background: #3498db;
                        color: white;
                        padding: 8px 12px;
                        border-radius: 8px 8px 0 0;
                        font-weight: bold;
                        font-size: 14px;
                    ">
                        📍 Lokasi Anda
                    </div>
                    <div style="
                        background: #f8f9fa;
                        padding: 12px;
                        border-radius: 0 0 8px 8px;
                        border: 1px solid #dee2e6;
                    ">
                        <div style="margin-bottom: 8px;">
                            <strong>Koordinat:</strong><br>
                            <span style="font-family: monospace; font-size: 12px; color: #6c757d;">
                                ${currentLocation.lat.toFixed(6)}, ${currentLocation.lng.toFixed(6)}
                            </span>
                        </div>
                        <div style="
                            background: ${currentLocation.accuracy ?
                                (currentLocation.accuracy < 10 ? '#d4edda' :
                                 currentLocation.accuracy < 50 ? '#fff3cd' : '#f8d7da') : '#e2e3e5'};
                            color: ${currentLocation.accuracy ?
                                (currentLocation.accuracy < 10 ? '#155724' :
                                 currentLocation.accuracy < 50 ? '#856404' : '#721c24') : '#6c757d'};
                            padding: 6px 10px;
                            border-radius: 4px;
                            font-size: 12px;
                            font-weight: bold;
                        ">
                            Akurasi: ${currentLocation.accuracy ? Math.round(currentLocation.accuracy) + ' meter' : 'Tidak diketahui'}
                        </div>
                    </div>
                </div>
            `);
        }

        // Update active route with new location
        function updateActiveRoute() {
            if (routingControl && mapState.route) {
                // Remove existing route
                map.removeControl(routingControl);

                // Create new route with updated location
                const destination = {
                    lat: mapState.route.destination.lat,
                    lng: mapState.route.destination.lng
                };
                createRoute(destination);
            }
        }

        // URL State Management Functions
        function updateURLState() {
            const params = new URLSearchParams();

            // Add map center and zoom
            params.set('lat', mapState.center[0].toFixed(6));
            params.set('lng', mapState.center[1].toFixed(6));
            params.set('zoom', mapState.zoom);

            // Add current location if available
            if (currentLocation) {
                params.set('user_lat', currentLocation.lat.toFixed(6));
                params.set('user_lng', currentLocation.lng.toFixed(6));
            }

            // Add route if active
            if (mapState.route) {
                const routeString = JSON.stringify(mapState.route);
                params.set('route', routeString);
                console.log('Adding route to URL:', routeString);
            } else {
                console.log('No route to add to URL');
            }

            // Update URL without page reload
            const newURL = window.location.pathname + '?' + params.toString();
            window.history.replaceState({}, '', newURL);
            console.log('URL state updated:', newURL);
            console.log('Current mapState:', mapState);
        }

        function loadStateFromURL() {
            const params = new URLSearchParams(window.location.search);

            // Load map center and zoom
            const lat = parseFloat(params.get('lat'));
            const lng = parseFloat(params.get('lng'));
            const zoom = parseInt(params.get('zoom'));

            if (!isNaN(lat) && !isNaN(lng)) {
                mapState.center = [lat, lng];
                mapState.zoom = !isNaN(zoom) ? zoom : 13;
                map.setView(mapState.center, mapState.zoom);
            }

            // Load user location
            const userLat = parseFloat(params.get('user_lat'));
            const userLng = parseFloat(params.get('user_lng'));

            if (!isNaN(userLat) && !isNaN(userLng)) {
                currentLocation = {
                    lat: userLat,
                    lng: userLng
                };
                console.log('User location loaded from URL:', currentLocation);
            }

            // Load route
            const routeParam = params.get('route');
            if (routeParam) {
                try {
                    mapState.route = JSON.parse(routeParam);
                    console.log('Route loaded from URL:', mapState.route);

                    // Wait for currentLocation to be available
                    if (mapState.route && currentLocation) {
                        console.log('Creating route from URL state...');
                        createRouteFromState();
                    } else if (mapState.route) {
                        // If currentLocation is not ready, wait for it
                        console.log('Waiting for current location to create route...');
                        const checkLocation = setInterval(function() {
                            if (currentLocation) {
                                clearInterval(checkLocation);
                                console.log('Current location available, creating route...');
                                createRouteFromState();
                            }
                        }, 500);
                    }
                } catch (e) {
                    console.log('Error parsing route from URL:', e);
                }
            }
        }

        function createRouteFromState() {
            if (mapState.route && currentLocation) {
                const destination = {
                    lat: mapState.route.destination.lat,
                    lng: mapState.route.destination.lng
                };
                createRoute(destination, false); // Don't close popup when loading from URL
            }
        }

        // Initialize geolocation
        getCurrentLocation();

        // Load state from URL on page load (delay to ensure map is ready)
        setTimeout(function() {
            loadStateFromURL();
        }, 1000);

        // Add event listeners for map state changes
        map.on('moveend', function() {
            const center = map.getCenter();
            mapState.center = [center.lat, center.lng];
            updateURLState();
        });

        map.on('zoomend', function() {
            mapState.zoom = map.getZoom();
            updateURLState();
        });

        // Add periodic location refresh (every 30 seconds)
        setInterval(function() {
            if (navigator.geolocation && currentLocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        updateCurrentLocation({
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        });
                    },
                    function(error) {
                        console.log('Periodic location update failed:', error);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 60000 // 1 minute
                    }
                );
            }
        }, 30000); // Update every 30 seconds

        // Function to create route
        function createRoute(destination, closePopup = false) {
            if (!currentLocation) {
                alert('Lokasi Anda belum terdeteksi. Silakan refresh halaman dan izinkan akses lokasi.');
                return;
            }

            // Close popup if requested
            if (closePopup) {
                map.closePopup();
            }

            // Remove existing routing control
            if (routingControl) {
                map.removeControl(routingControl);
            }

            // Create new routing control
            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(currentLocation.lat, currentLocation.lng),
                    L.latLng(destination.lat, destination.lng)
                ],
                show: false,
                collapsible: true,
                routeWhileDragging: true,
                addWaypoints: false,
                createMarker: function() {
                    return null;
                },
                lineOptions: {
                    styles: [{
                        color: '#3388ff',
                        weight: 5
                    }]
                }
            }).addTo(map);

            // Update map state with route information
            mapState.route = {
                destination: {
                    lat: destination.lat,
                    lng: destination.lng
                },
                timestamp: Date.now(),
                facility: destination.facility || null // Store facility info if available
            };

            console.log('Route state updated:', mapState.route);

            // Update URL with route state
            updateURLState();

            // Show clear route button
            document.querySelector('.clear-route-btn').classList.add('show');

            console.log('Route created and URL updated');
        }

        // Function to clear route
        function clearRoute() {
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }

            // Clear route from map state
            mapState.route = null;

            // Update URL to remove route state
            updateURLState();

            // Hide clear route button
            document.querySelector('.clear-route-btn').classList.remove('show');

            console.log('Route cleared and URL updated');
        }

        // Add markers for health facilities from database
        var healthFacilities = @json($healthFacilities);

        // Define icon paths based on facility type
        function getIconForType(type) {
            var iconPath = '{{ asset('images/icons/') }}/';
            var iconSize = [32, 32];
            var iconAnchor = [16, 32];
            var popupAnchor = [0, -32];

            switch (type) {
                case 'Rumah Sakit':
                    return L.icon({
                        iconUrl: iconPath + 'icon-rumah-sakit.png',
                        iconSize: iconSize,
                        iconAnchor: iconAnchor,
                        popupAnchor: popupAnchor
                    });
                case 'Puskesmas':
                    return L.icon({
                        iconUrl: iconPath + 'icon-puskesmas.png',
                        iconSize: iconSize,
                        iconAnchor: iconAnchor,
                        popupAnchor: popupAnchor
                    });
                case 'Apotek':
                    return L.icon({
                        iconUrl: iconPath + 'icon-apotek.png',
                        iconSize: iconSize,
                        iconAnchor: iconAnchor,
                        popupAnchor: popupAnchor
                    });
                default:
                    // Default icon for unknown types
                    return L.icon({
                        iconUrl: iconPath + 'icon-puskesmas.png',
                        iconSize: iconSize,
                        iconAnchor: iconAnchor,
                        popupAnchor: popupAnchor
                    });
            }
        }

        // Create markers for each facility
        healthFacilities.forEach(function(facility) {
            var customIcon = getIconForType(facility.type);
            var marker = L.marker([facility.lat, facility.lng], {
                    icon: customIcon
                })
                .addTo(map);

            // Create popup content with facility details
            var popupContent = `
                <div style="min-width: 250px;">
                    <h4 style="margin: 0 0 8px 0; color: #2c3e50;">${facility.name}</h4>
                    <p style="margin: 4px 0; color: #7f8c8d;"><strong>Alamat:</strong> ${facility.address || 'Tidak tersedia'}</p>
                    <p style="margin: 4px 0; color: #7f8c8d;"><strong>Tipe:</strong> ${facility.type || 'Tidak tersedia'}</p>
                    ${facility.phone ? `<p style="margin: 4px 0; color: #7f8c8d;"><strong>Telepon:</strong> ${facility.phone}</p>` : ''}
                    ${facility.opening_hours ? `<p style="margin: 4px 0; color: #7f8c8d;"><strong>Jam Buka:</strong> ${facility.opening_hours} - ${facility.closing_hours || 'Tidak tersedia'}</p>` : ''}
                    ${facility.services && facility.services.length > 0 ? `<p style="margin: 4px 0; color: #7f8c8d;"><strong>Layanan:</strong> ${facility.services.join(', ')}</p>` : ''}
                    <div style="margin-top: 12px; text-align: center;">
                        <button onclick="console.log('Creating route to:', {lat: ${facility.lat}, lng: ${facility.lng}}); createRoute({lat: ${facility.lat}, lng: ${facility.lng}, facility: {name: '${facility.name}', type: '${facility.type}', address: '${facility.address || ''}'}}, true)"
                                style="background: #3498db; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 14px; transition: all 0.3s ease;"
                                onmouseover="this.style.background='#2980b9'; this.style.transform='translateY(-1px)'"
                                onmouseout="this.style.background='#3498db'; this.style.transform='translateY(0)'">
                            🗺️ Rute ke Sini
                        </button>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent);
        });
    </script>
</body>

</html>
