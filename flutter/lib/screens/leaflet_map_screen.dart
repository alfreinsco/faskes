import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import 'package:geolocator/geolocator.dart';
import '../models/faskes.dart';

class LeafletMapScreen extends StatefulWidget {
  final List<Faskes> faskesList;
  final Position? currentPosition;

  const LeafletMapScreen({
    super.key,
    required this.faskesList,
    this.currentPosition,
  });

  @override
  State<LeafletMapScreen> createState() => _LeafletMapScreenState();
}

class _LeafletMapScreenState extends State<LeafletMapScreen> {
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
            _loadMapData();
          },
          onWebResourceError: (WebResourceError error) {
            print('WebView error: ${error.description}');
            setState(() {
              _isLoading = false;
            });
          },
        ),
      )
      ..loadHtmlString(_generateMapHTML());
  }

  void _loadMapData() {
    if (widget.currentPosition != null) {
      _controller.runJavaScript('''
        // Set current position
        var currentPos = [${widget.currentPosition!.latitude}, ${widget.currentPosition!.longitude}];
        map.setView(currentPos, 13);
        
        // Add current location marker
        L.marker(currentPos)
          .addTo(map)
          .bindPopup('<b>Lokasi Anda</b>')
          .openPopup();
      ''');
    }

    // Add faskes markers
    for (var faskes in widget.faskesList) {
      if (faskes.latitude != null && faskes.longitude != null) {
        final lat = double.parse(faskes.latitude!);
        final lng = double.parse(faskes.longitude!);

        _controller.runJavaScript('''
          var marker = L.marker([$lat, $lng])
            .addTo(map)
            .bindPopup(`
              <div style="width: 250px;">
                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                  <span style="font-size: 20px;">${faskes.typeIcon}</span>
                  <div style="margin-left: 8px; flex: 1;">
                    <div style="font-weight: bold; font-size: 16px;">${faskes.nama}</div>
                    <div style="color: ${_getMarkerColor(faskes.type)}; font-weight: 500;">${faskes.type}</div>
                  </div>
                </div>
                <div style="font-size: 12px; margin-bottom: 8px;">${faskes.alamat}</div>
                ${faskes.noTelp != null ? '<div style="font-size: 12px; margin-bottom: 8px;">📞 ${faskes.noTelp}</div>' : ''}
                <div style="display: flex; gap: 8px; margin-top: 8px;">
                  <button onclick="showRoute($lat, $lng, '${faskes.nama}')" 
                          style="background: #1976d2; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; cursor: pointer;">
                    Rute
                  </button>
                  <button onclick="showDetails('${faskes.id}')" 
                          style="background: #666; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; cursor: pointer;">
                    Detail
                  </button>
                </div>
              </div>
            `);
        ''');
      }
    }
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

  String _generateMapHTML() {
    return '''
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Faskes</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <style>
        body { margin: 0; padding: 0; }
        #map { height: 100vh; width: 100%; }
        .leaflet-popup-content { margin: 8px 12px; }
        .leaflet-popup-content-wrapper { border-radius: 8px; }
    </style>
</head>
<body>
    <div id="map"></div>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    
    <script>
        // Initialize map
        var map = L.map('map').setView([-2.5489, 118.0149], 5);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Routing control
        var routingControl = null;
        
        // Function to show route
        function showRoute(lat, lng, name) {
            // Remove existing routing control
            if (routingControl) {
                map.removeControl(routingControl);
            }
            
            // Get current position (you can modify this to get actual current position)
            var currentPos = [${widget.currentPosition?.latitude ?? -2.5489}, ${widget.currentPosition?.longitude ?? 118.0149}];
            var destination = [lat, lng];
            
            // Create routing control
            routingControl = L.Routing.control({
                show: false,
                waypoints: [
                    L.latLng(currentPos[0], currentPos[1]),
                    L.latLng(destination[0], destination[1])
                ],
                routeWhileDragging: true,
                addWaypoints: false,
                createMarker: function(i, waypoint, n) {
                    if (i === 0) {
                        return L.marker(waypoint.latLng, {
                            icon: L.divIcon({
                                className: 'custom-div-icon',
                                html: '<div style="background-color: #4caf50; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                                iconSize: [20, 20],
                                iconAnchor: [10, 10]
                            })
                        }).bindPopup('Lokasi Anda');
                    } else {
                        return L.marker(waypoint.latLng, {
                            icon: L.divIcon({
                                className: 'custom-div-icon',
                                html: '<div style="background-color: #1976d2; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                                iconSize: [20, 20],
                                iconAnchor: [10, 10]
                            })
                        }).bindPopup(name);
                    }
                },
                lineOptions: {
                    styles: [{ color: '#1976d2', weight: 5, opacity: 0.8 }]
                },
                show: false,
                addWaypoints: false,
                routeWhileDragging: false,
                fitSelectedRoutes: true,
                showAlternatives: false
            }).addTo(map);
            
            // Fit map to show route
            routingControl.on('routesfound', function(e) {
                var routes = e.routes;
                var summary = routes[0].summary;
                console.log('Route found: ' + summary.totalDistance + ' meters, ' + Math.round(summary.totalTime / 60) + ' minutes');
            });
        }
        
        // Function to show details (placeholder)
        function showDetails(faskesId) {
            alert('Detail faskes: ' + faskesId);
        }
        
        // Function to clear route
        function clearRoute() {
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }
        }
        
        // Function to change transport mode
        function changeTransportMode(mode) {
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }
            // Recalculate route with new mode
            // This would need to be implemented based on your routing service
        }
    </script>
</body>
</html>
    ''';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Peta Faskes',
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white),
        ),
        backgroundColor: Colors.cyan[600],
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.white),
            onPressed: () {
              _controller.reload();
            },
            tooltip: 'Refresh Peta',
          ),
        ],
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: _controller),
          if (_isLoading) const Center(child: CircularProgressIndicator()),
          Positioned(
            top: 16,
            left: 16,
            right: 16,
            child: Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(8),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.1),
                    blurRadius: 4,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Text(
                'Menampilkan ${widget.faskesList.length} faskes di peta',
                style: const TextStyle(
                  fontWeight: FontWeight.w500,
                  color: Colors.black87,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
