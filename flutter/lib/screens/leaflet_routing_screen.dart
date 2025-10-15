import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import 'package:geolocator/geolocator.dart';
import '../models/faskes.dart';

class LeafletRoutingScreen extends StatefulWidget {
  final Faskes faskes;
  final Position currentPosition;

  const LeafletRoutingScreen({
    super.key,
    required this.faskes,
    required this.currentPosition,
  });

  @override
  State<LeafletRoutingScreen> createState() => _LeafletRoutingScreenState();
}

class _LeafletRoutingScreenState extends State<LeafletRoutingScreen> {
  late WebViewController _controller;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _initializeWebView();
  }

  void _initializeWebView() {
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageFinished: (String url) {
            setState(() {
              _isLoading = false;
            });
            _loadRouteData();
          },
          onWebResourceError: (WebResourceError error) {
            print('WebView error: ${error.description}');
            setState(() {
              _isLoading = false;
            });
          },
        ),
      )
      ..loadHtmlString(_generateRoutingHTML());
  }

  void _loadRouteData() {
    final startLat = widget.currentPosition.latitude;
    final startLng = widget.currentPosition.longitude;
    final endLat = double.parse(widget.faskes.latitude!);
    final endLng = double.parse(widget.faskes.longitude!);

    _controller.runJavaScript('''
      // Set route waypoints
      var startPoint = L.latLng($startLat, $startLng);
      var endPoint = L.latLng($endLat, $endLng);
      
      // Set map view to show both points
      var group = new L.featureGroup([L.marker(startPoint), L.marker(endPoint)]);
      map.fitBounds(group.getBounds().pad(0.1));
      
      // Create routing control
      routingControl = L.Routing.control({
        waypoints: [startPoint, endPoint],
        routeWhileDragging: false,
        addWaypoints: false,
        createMarker: function(i, waypoint, n) {
          if (i === 0) {
            return L.marker(waypoint.latLng, {
              icon: L.divIcon({
                className: 'custom-div-icon',
                html: '<div style="background-color: #4caf50; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 16px;">📍</div>',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
              })
            }).bindPopup('<b>Lokasi Anda</b>');
          } else {
            return L.marker(waypoint.latLng, {
              icon: L.divIcon({
                className: 'custom-div-icon',
                html: '<div style="background-color: ${_getMarkerColor(widget.faskes.type)}; width: 40px; height: 40px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">${widget.faskes.typeIcon}</div>',
                iconSize: [40, 40],
                iconAnchor: [20, 20]
              })
            }).bindPopup(`
              <div style="width: 250px;">
                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                  <span style="font-size: 20px;">${widget.faskes.typeIcon}</span>
                  <div style="margin-left: 8px; flex: 1;">
                    <div style="font-weight: bold; font-size: 16px;">${widget.faskes.nama}</div>
                    <div style="color: ${_getMarkerColor(widget.faskes.type)}; font-weight: 500;">${widget.faskes.type}</div>
                  </div>
                </div>
                <div style="font-size: 12px; margin-bottom: 8px;">${widget.faskes.alamat}</div>
                ${widget.faskes.noTelp != null ? '<div style="font-size: 12px; margin-bottom: 8px;">📞 ${widget.faskes.noTelp}</div>' : ''}
              </div>
            `);
          }
        },
        lineOptions: {
          styles: [{ color: '#1976d2', weight: 5, opacity: 0.8 }]
        },
        show: true,
        addWaypoints: false,
        routeWhileDragging: false,
        fitSelectedRoutes: true,
        showAlternatives: false,
        collapsible: true,
        routeLine: function(route, options) {
          return L.polyline(route.coordinates, options);
        }
      }).addTo(map);
      
      // Handle route events
      routingControl.on('routesfound', function(e) {
        var routes = e.routes;
        var summary = routes[0].summary;
        var distance = Math.round(summary.totalDistance / 1000 * 10) / 10;
        var duration = Math.round(summary.totalTime / 60);
        
        // Update route info
        updateRouteInfo(distance, duration);
      });
      
      routingControl.on('routingerror', function(e) {
        console.log('Routing error:', e);
        // Fallback to straight line
        showFallbackRoute();
      });
    ''');
  }

  String _getMarkerColor(String type) {
    switch (type) {
      case 'Rumah Sakit':
        return '#d32f2f';
      case 'Puskesmas':
        return '#1976d2';
      case 'Apotek':
        return '#388e3c';
      default:
        return '#757575';
    }
  }

  String _generateRoutingHTML() {
    return '''
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rute ke ${widget.faskes.nama}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <style>
        body { margin: 0; padding: 0; }
        #map { height: 100vh; width: 100%; }
        .leaflet-routing-container { 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin: 10px;
        }
        .leaflet-routing-alt { 
            max-height: 200px; 
            overflow-y: auto; 
        }
        .custom-div-icon {
            background: transparent !important;
            border: none !important;
        }
        .route-info {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            background: white;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
        }
        .transport-mode {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        .mode-btn {
            background: #1976d2;
            color: white;
            border: none;
            padding: 8px 12px;
            margin: 2px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .mode-btn.active {
            background: #0d47a1;
        }
    </style>
</head>
<body>
    <div id="map"></div>
    
    <div class="route-info" id="routeInfo">
        <div style="display: flex; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 20px;">${widget.faskes.typeIcon}</span>
            <div style="margin-left: 8px; flex: 1;">
                <div style="font-weight: bold; font-size: 16px;">Rute ke ${widget.faskes.nama}</div>
                <div style="color: ${_getMarkerColor(widget.faskes.type)}; font-weight: 500;">${widget.faskes.type}</div>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 12px; color: #666;">Jarak</div>
                <div id="distance" style="font-weight: bold; color: #1976d2;">-</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #666;">Waktu</div>
                <div id="duration" style="font-weight: bold; color: #1976d2;">-</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #666;">Mode</div>
                <div id="mode" style="font-weight: bold; color: #1976d2;">Mobil</div>
            </div>
        </div>
    </div>
    
    <div class="transport-mode">
        <button class="mode-btn active" onclick="changeMode('driving-car')">🚗</button>
        <button class="mode-btn" onclick="changeMode('walking')">🚶</button>
        <button class="mode-btn" onclick="changeMode('cycling-regular')">🚴</button>
    </div>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    
    <script>
        // Initialize map
        var map = L.map('map').setView([${widget.currentPosition.latitude}, ${widget.currentPosition.longitude}], 13);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Routing control
        var routingControl = null;
        var currentMode = 'driving-car';
        
        // Function to update route info
        function updateRouteInfo(distance, duration) {
            document.getElementById('distance').textContent = distance + ' km';
            document.getElementById('duration').textContent = duration + ' menit';
            document.getElementById('routeInfo').style.display = 'block';
        }
        
        // Function to change transport mode
        function changeMode(mode) {
            currentMode = mode;
            
            // Update button states
            document.querySelectorAll('.mode-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Update mode display
            var modeText = '';
            switch(mode) {
                case 'driving-car': modeText = 'Mobil'; break;
                case 'walking': modeText = 'Jalan Kaki'; break;
                case 'cycling-regular': modeText = 'Sepeda'; break;
            }
            document.getElementById('mode').textContent = modeText;
            
            // Recalculate route
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }
            
            // Recreate routing control with new mode
            setTimeout(function() {
                createRoutingControl();
            }, 100);
        }
        
        // Function to create routing control
        function createRoutingControl() {
            var startPoint = L.latLng(${widget.currentPosition.latitude}, ${widget.currentPosition.longitude});
            var endPoint = L.latLng(${double.parse(widget.faskes.latitude!)}, ${double.parse(widget.faskes.longitude!)});
            
            routingControl = L.Routing.control({
                waypoints: [startPoint, endPoint],
                routeWhileDragging: false,
                addWaypoints: false,
                createMarker: function(i, waypoint, n) {
                    if (i === 0) {
                        return L.marker(waypoint.latLng, {
                            icon: L.divIcon({
                                className: 'custom-div-icon',
                                html: '<div style="background-color: #4caf50; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 16px;">📍</div>',
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            })
                        }).bindPopup('<b>Lokasi Anda</b>');
                    } else {
                        return L.marker(waypoint.latLng, {
                            icon: L.divIcon({
                                className: 'custom-div-icon',
                                html: '<div style="background-color: ${_getMarkerColor(widget.faskes.type)}; width: 40px; height: 40px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">${widget.faskes.typeIcon}</div>',
                                iconSize: [40, 40],
                                iconAnchor: [20, 20]
                            })
                        }).bindPopup(`
                            <div style="width: 250px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="font-size: 20px;">${widget.faskes.typeIcon}</span>
                                    <div style="margin-left: 8px; flex: 1;">
                                        <div style="font-weight: bold; font-size: 16px;">${widget.faskes.nama}</div>
                                        <div style="color: ${_getMarkerColor(widget.faskes.type)}; font-weight: 500;">${widget.faskes.type}</div>
                                    </div>
                                </div>
                                <div style="font-size: 12px; margin-bottom: 8px;">${widget.faskes.alamat}</div>
                                ${widget.faskes.noTelp != null ? '<div style="font-size: 12px; margin-bottom: 8px;">📞 ${widget.faskes.noTelp}</div>' : ''}
                            </div>
                        `);
                    }
                },
                lineOptions: {
                    styles: [{ color: '#1976d2', weight: 5, opacity: 0.8 }]
                },
                show: true,
                addWaypoints: false,
                routeWhileDragging: false,
                fitSelectedRoutes: true,
                showAlternatives: false,
                collapsible: true
            }).addTo(map);
            
            // Handle route events
            routingControl.on('routesfound', function(e) {
                var routes = e.routes;
                var summary = routes[0].summary;
                var distance = Math.round(summary.totalDistance / 1000 * 10) / 10;
                var duration = Math.round(summary.totalTime / 60);
                
                updateRouteInfo(distance, duration);
            });
            
            routingControl.on('routingerror', function(e) {
                console.log('Routing error:', e);
                showFallbackRoute();
            });
        }
        
        // Function to show fallback route
        function showFallbackRoute() {
            var startPoint = L.latLng(${widget.currentPosition.latitude}, ${widget.currentPosition.longitude});
            var endPoint = L.latLng(${double.parse(widget.faskes.latitude!)}, ${double.parse(widget.faskes.longitude!)});
            
            // Calculate straight line distance
            var distance = startPoint.distanceTo(endPoint) / 1000;
            var duration = Math.round(distance * 2); // Rough estimate
            
            // Draw straight line
            L.polyline([startPoint, endPoint], {
                color: '#1976d2',
                weight: 5,
                opacity: 0.8,
                dashArray: '10, 10'
            }).addTo(map);
            
            updateRouteInfo(distance.toFixed(1), duration);
        }
        
        // Initialize routing control
        createRoutingControl();
    </script>
</body>
</html>
    ''';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          'Rute ke ${widget.faskes.nama}',
          style: const TextStyle(
            fontWeight: FontWeight.bold,
            color: Colors.white,
          ),
        ),
        backgroundColor: Colors.cyan[600],
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.white),
            onPressed: () {
              _controller.reload();
            },
            tooltip: 'Refresh Rute',
          ),
        ],
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: _controller),
          if (_isLoading) const Center(child: CircularProgressIndicator()),
        ],
      ),
    );
  }
}
