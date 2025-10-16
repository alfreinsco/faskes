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
            
            // Add current location marker
            L.marker([currentLocation.lat, currentLocation.lng], {
              icon: L.divIcon({
                className: 'current-location-marker',
                html: '<div style="background: #e74c3c; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">📍</div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
              })
            }).addTo(map).bindPopup('Lokasi Anda saat ini (Akurasi: ${position.accuracy}m)');
          }
        }
        
        // Set up continuous location updates
        function startLocationWatch() {
          if (navigator.geolocation) {
            const options = {
              enableHighAccuracy: true,
              timeout: 15000,
              maximumAge: 30000 // 30 seconds
            };
            
            navigator.geolocation.watchPosition(
              function(position) {
                console.log('Location updated:', position.coords);
                if (typeof currentLocation !== 'undefined') {
                  currentLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                  };
                  console.log('Location updated to:', currentLocation);
                }
              },
              function(error) {
                console.log('Watch position error:', error);
              },
              options
            );
          }
        }
        
        // Start watching position
        startLocationWatch();
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
              timeout: 15000,
              maximumAge: 300000
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
