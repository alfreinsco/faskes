import 'dart:convert';
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
  Position? _currentPosition;

  @override
  void initState() {
    super.initState();
    _currentPosition = widget.currentPosition;
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
            _setupJavaScriptChannels();
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

  void _setupJavaScriptChannels() {
    // Add JavaScript channel for location updates
    _controller.addJavaScriptChannel(
      'FlutterMethodChannel',
      onMessageReceived: (JavaScriptMessage message) {
        try {
          final data = jsonDecode(message.message);
          if (data['type'] == 'requestLocationUpdate') {
            _updateLocationAndRoute();
          }
        } catch (e) {
          print('Error parsing JavaScript message: $e');
        }
      },
    );
  }

  Future<void> _updateLocationAndRoute() async {
    try {
      // Get current location
      final position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
        timeLimit: const Duration(seconds: 5),
      );

      // Update the WebView with new location
      await _controller.runJavaScript('''
        updateRouteWithNewLocation(${position.latitude}, ${position.longitude});
      ''');

      // Update Flutter state
      setState(() {
        _currentPosition = position;
      });
    } catch (e) {
      print('Error updating location: $e');
      // If location update fails, use the last known position
      if (_currentPosition != null) {
        await _controller.runJavaScript('''
          updateRouteWithNewLocation(${_currentPosition!.latitude}, ${_currentPosition!.longitude});
        ''');
      }
    }
  }

  void _loadRouteData() {
    if (_currentPosition == null) return;

    final startLat = _currentPosition!.latitude;
    final startLng = _currentPosition!.longitude;
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
            display: none !important;
        }
        .leaflet-routing-alt { 
            display: none !important;
        }
        .custom-div-icon {
            background: transparent !important;
            border: none !important;
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
        
        
        // Function to change transport mode
        function changeMode(mode) {
            currentMode = mode;
            
            // Update button states
            document.querySelectorAll('.mode-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            
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
                show: false,
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
                
                // Route found, no need to show info popup
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
            
            // No fallback line drawn - only show markers
            // Fallback route shown, no need to display info
        }

        // Function to update location and route every 3 seconds
        function startLocationUpdate() {
            setInterval(function() {
                // Get current location from Flutter
                if (window.FlutterMethodChannel) {
                    window.FlutterMethodChannel.postMessage(JSON.stringify({
                        type: 'requestLocationUpdate'
                    }));
                }
            }, 3000); // Update every 3 seconds
        }

        // Function to update route with new location
        function updateRouteWithNewLocation(newLat, newLng) {
            var startPoint = L.latLng(newLat, newLng);
            var endPoint = L.latLng(${double.parse(widget.faskes.latitude!)}, ${double.parse(widget.faskes.longitude!)});
            
            // Update waypoints
            routingControl.setWaypoints([startPoint, endPoint]);
            
            // Recalculate route
            routingControl.route();
            
            // Update current position marker
            if (currentPositionMarker) {
                map.removeLayer(currentPositionMarker);
            }
            currentPositionMarker = L.marker([newLat, newLng], {
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: "<div style='background-color: #4CAF50; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 0 2px rgba(0,0,0,0.3);'></div>",
                    iconSize: [20, 20]
                })
            }).addTo(map).bindPopup("Lokasi Anda");
        }
        
        // Initialize routing control
        createRoutingControl();
        
        // Start location update every 3 seconds
        startLocationUpdate();
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
