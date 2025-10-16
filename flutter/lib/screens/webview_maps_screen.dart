import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import 'package:geolocator/geolocator.dart';

class WebViewMapsScreen extends StatefulWidget {
  const WebViewMapsScreen({super.key});

  @override
  State<WebViewMapsScreen> createState() => _WebViewMapsScreenState();
}

class _WebViewMapsScreenState extends State<WebViewMapsScreen> {
  late final WebViewController _controller;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _initializeWebView();
  }

  void _initializeWebView() {
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (String url) {
            setState(() {
              _isLoading = true;
            });
          },
          onPageFinished: (String url) {
            setState(() {
              _isLoading = false;
            });
            // Enable geolocation in WebView after page loads
            _enableGeolocation();
          },
          onWebResourceError: (WebResourceError error) {
            setState(() {
              _isLoading = false;
            });
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text('Error loading maps: ${error.description}'),
                backgroundColor: Colors.red,
              ),
            );
          },
        ),
      )
      ..loadRequest(Uri.parse('http://172.17.1.226:8001/maps'));
  }

  void _enableGeolocation() async {
    try {
      // Get current location using Flutter's geolocator for better accuracy
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
        timeLimit: const Duration(seconds: 10),
      );

      print(
        'Flutter geolocation - Lat: ${position.latitude}, Lng: ${position.longitude}',
      );
      print('Accuracy: ${position.accuracy} meters');

      // Inject JavaScript with the accurate location
      _controller.runJavaScript('''
        // Set current location from Flutter geolocator
        if (typeof currentLocation !== 'undefined') {
          currentLocation = {
            lat: ${position.latitude},
            lng: ${position.longitude}
          };
          console.log('Current location set from Flutter:', currentLocation);
          
          // Center map on current location
          if (typeof map !== 'undefined') {
            map.setView([currentLocation.lat, currentLocation.lng], 15);
            
            // Add current location marker with same design as maps.blade.php
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
                      ${position.latitude.toStringAsFixed(6)}, ${position.longitude.toStringAsFixed(6)}
                    </span>
                  </div>
                  <div style="
                    background: ${position.accuracy < 10
          ? '#d4edda'
          : position.accuracy < 50
          ? '#fff3cd'
          : '#f8d7da'};
                    color: ${position.accuracy < 10
          ? '#155724'
          : position.accuracy < 50
          ? '#856404'
          : '#721c24'};
                    padding: 6px 10px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: bold;
                  ">
                    Akurasi: ${position.accuracy.toStringAsFixed(0)} meter
                  </div>
                </div>
              </div>
            `);
          }
        }
        
        // Set up continuous location updates every 3 seconds
        function startLocationWatch() {
          if (navigator.geolocation) {
            const options = {
              enableHighAccuracy: true,
              timeout: 5000,
              maximumAge: 3000 // 3 seconds for real-time updates
            };
            
            navigator.geolocation.watchPosition(
              function(position) {
                console.log('Location updated:', position.coords);
                if (typeof currentLocation !== 'undefined') {
                  currentLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                    accuracy: position.coords.accuracy
                  };
                  console.log('Location updated to:', currentLocation);
                  
                  // Update location marker with new position
                  updateLocationMarker();
                }
              },
              function(error) {
                console.log('Watch position error:', error);
              },
              options
            );
          }
        }
        
        // Function to update location marker
        function updateLocationMarker() {
          if (typeof map !== 'undefined' && typeof currentLocation !== 'undefined') {
            // Remove existing location marker
            map.eachLayer(function(layer) {
              if (layer instanceof L.Marker && layer.options.icon && 
                  layer.options.icon.options && 
                  layer.options.icon.options.className === 'current-location-marker') {
                map.removeLayer(layer);
              }
            });
            
            // Add new location marker with same design
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
                      ' + currentLocation.lat.toFixed(6) + ', ' + currentLocation.lng.toFixed(6) + '
                    </span>
                  </div>
                  <div style="
                    background: ' + (currentLocation.accuracy ? 
                      (currentLocation.accuracy < 10 ? '#d4edda' : 
                       currentLocation.accuracy < 50 ? '#fff3cd' : '#f8d7da') : '#e2e3e5') + ';
                    color: ' + (currentLocation.accuracy ? 
                      (currentLocation.accuracy < 10 ? '#155724' : 
                       currentLocation.accuracy < 50 ? '#856404' : '#721c24') : '#6c757d') + ';
                    padding: 6px 10px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: bold;
                  ">
                    Akurasi: ' + (currentLocation.accuracy ? Math.round(currentLocation.accuracy) + ' meter' : 'Tidak diketahui') + '
                  </div>
                </div>
              </div>
            `);
          }
        }
        
        // Start watching position
        startLocationWatch();
        
        // Add periodic location refresh every 3 seconds as backup
        setInterval(function() {
          if (navigator.geolocation && typeof currentLocation !== 'undefined') {
            navigator.geolocation.getCurrentPosition(
              function(position) {
                console.log('Periodic location update:', position.coords);
                if (typeof currentLocation !== 'undefined') {
                  currentLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                    accuracy: position.coords.accuracy
                  };
                  console.log('Periodic location updated to:', currentLocation);
                  updateLocationMarker();
                }
              },
              function(error) {
                console.log('Periodic location update failed:', error);
              },
              {
                enableHighAccuracy: true,
                timeout: 3000,
                maximumAge: 5000 // 5 seconds
              }
            );
          }
        }, 3000); // Update every 3 seconds
      ''');
    } catch (e) {
      print('Error getting location: $e');

      // Fallback to browser geolocation
      _controller.runJavaScript('''
        // Fallback to browser geolocation
        function getCurrentLocationFallback() {
          if (navigator.geolocation) {
            console.log('Using browser geolocation fallback...');
            
            const options = {
              enableHighAccuracy: true,
              timeout: 10000,
              maximumAge: 5000 // 5 seconds for more frequent updates
            };
            
            navigator.geolocation.getCurrentPosition(
              function(position) {
                console.log('Browser geolocation success:', position.coords);
                console.log('Accuracy:', position.coords.accuracy, 'meters');
                
                if (typeof currentLocation !== 'undefined') {
                  currentLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                  };
                  console.log('Current location updated:', currentLocation);
                  
                  if (typeof map !== 'undefined') {
                    map.setView([currentLocation.lat, currentLocation.lng], 15);
                    
                    // Add current location marker with same design
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
                    }).addTo(map).bindPopup('Lokasi Anda saat ini');
                  }
                }
              },
              function(error) {
                console.log('Browser geolocation error:', error);
                
                // Final fallback to Ambon coordinates
                if (typeof currentLocation !== 'undefined') {
                  currentLocation = {
                    lat: -3.6561,
                    lng: 128.1664
                  };
                }
              },
              options
            );
          } else {
            console.log('Geolocation not supported');
            if (typeof currentLocation !== 'undefined') {
              currentLocation = {
                lat: -3.6561,
                lng: 128.1664
              };
            }
          }
        }
        
        getCurrentLocationFallback();
        
        // Add periodic location refresh every 3 seconds for fallback too
        setInterval(function() {
          if (navigator.geolocation && typeof currentLocation !== 'undefined') {
            navigator.geolocation.getCurrentPosition(
              function(position) {
                console.log('Fallback periodic location update:', position.coords);
                if (typeof currentLocation !== 'undefined') {
                  currentLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                    accuracy: position.coords.accuracy
                  };
                  console.log('Fallback periodic location updated to:', currentLocation);
                  updateLocationMarker();
                }
              },
              function(error) {
                console.log('Fallback periodic location update failed:', error);
              },
              {
                enableHighAccuracy: true,
                timeout: 3000,
                maximumAge: 5000 // 5 seconds
              }
            );
          }
        }, 3000); // Update every 3 seconds
      ''');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Peta Fasilitas Kesehatan'),
        backgroundColor: Colors.blue[600],
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () {
              _controller.reload();
            },
            tooltip: 'Refresh',
          ),
        ],
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: _controller),
          if (_isLoading)
            const Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  CircularProgressIndicator(),
                  SizedBox(height: 16),
                  Text(
                    'Memuat Peta...',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.w500),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}
