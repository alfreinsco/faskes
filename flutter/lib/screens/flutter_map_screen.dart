import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:geolocator/geolocator.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'dart:async';
import 'package:shared_preferences/shared_preferences.dart';

// Map state management class
class MapState {
  final LatLng center;
  final double zoom;
  final Map<String, dynamic>? route;

  MapState({required this.center, required this.zoom, this.route});

  MapState copyWith({
    LatLng? center,
    double? zoom,
    Map<String, dynamic>? route,
  }) {
    return MapState(
      center: center ?? this.center,
      zoom: zoom ?? this.zoom,
      route: route ?? this.route,
    );
  }
}

class FlutterMapScreen extends StatefulWidget {
  const FlutterMapScreen({Key? key}) : super(key: key);

  @override
  State<FlutterMapScreen> createState() => _FlutterMapScreenState();
}

class _FlutterMapScreenState extends State<FlutterMapScreen> {
  MapController mapController = MapController();
  LatLng? currentLocation;
  List<Map<String, dynamic>> facilities = [];
  bool isLoading = true;
  String? errorMessage;
  LatLng? routeStart;
  LatLng? routeEnd;
  List<LatLng> routePoints = [];
  StreamSubscription<Position>? _positionStreamSubscription;

  // Advanced location tracking
  double? locationAccuracy;
  DateTime? lastLocationUpdate;
  Timer? _locationUpdateTimer;

  // Map state management
  MapState mapState = MapState(
    center: const LatLng(-3.6561, 128.1664),
    zoom: 13.0,
    route: null,
  );

  // Route management
  bool isRouteActive = false;
  Map<String, dynamic>? activeRoute;

  // UI state
  bool showClearRouteButton = false;

  @override
  void initState() {
    super.initState();
    _initializeMap();
  }

  Future<void> _initializeMap() async {
    await _loadMapState();
    await _getCurrentLocation();
    await _loadFacilities();
    _startPeriodicLocationUpdates();
  }

  Future<void> _getCurrentLocation() async {
    try {
      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }

      if (permission == LocationPermission.deniedForever) {
        setState(() {
          errorMessage = 'Location permission denied permanently';
        });
        return;
      }

      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
        timeLimit: const Duration(seconds: 10),
      );

      _updateCurrentLocation(position);
      _startLocationUpdates();
    } catch (e) {
      setState(() {
        errorMessage = 'Error getting location: $e';
      });
      // Fallback to Ambon coordinates
      _updateCurrentLocationFallback();
    }
  }

  void _updateCurrentLocation(Position position) {
    final now = DateTime.now();
    final newLocation = LatLng(position.latitude, position.longitude);

    // Only update if location changed significantly or enough time has passed
    if (currentLocation == null ||
        _calculateDistance(currentLocation!, newLocation) > 0.0001 ||
        lastLocationUpdate == null ||
        now.difference(lastLocationUpdate!).inSeconds > 2) {
      setState(() {
        currentLocation = newLocation;
        locationAccuracy = position.accuracy;
        lastLocationUpdate = now;
        mapState = mapState.copyWith(center: newLocation);
      });

      print(
        'Location updated: ${currentLocation!.latitude}, ${currentLocation!.longitude}',
      );
      print('Accuracy: ${locationAccuracy?.toStringAsFixed(1)} meters');

      _saveMapState();

      // Update route if active
      if (isRouteActive && activeRoute != null) {
        _updateActiveRoute();
      }
    }
  }

  void _updateCurrentLocationFallback() {
    setState(() {
      currentLocation = const LatLng(-3.6561, 128.1664);
      locationAccuracy = 0;
      lastLocationUpdate = DateTime.now();
      mapState = mapState.copyWith(center: currentLocation!);
    });
    print('Using fallback location: Ambon');
  }

  void _startLocationUpdates() {
    try {
      Stream<Position> positionStream = Geolocator.getPositionStream(
        locationSettings: const LocationSettings(
          accuracy: LocationAccuracy.high,
          distanceFilter: 10, // Update every 10 meters
        ),
      );

      _positionStreamSubscription = positionStream.listen(
        (Position position) {
          if (mounted) {
            _updateCurrentLocation(position);
          }
        },
        onError: (error) {
          if (mounted) {
            setState(() {
              errorMessage = 'Location stream error: $error';
            });
          }
        },
      );
    } catch (e) {
      if (mounted) {
        setState(() {
          errorMessage = 'Error starting location updates: $e';
        });
      }
    }
  }

  void _startPeriodicLocationUpdates() {
    _locationUpdateTimer = Timer.periodic(const Duration(seconds: 3), (timer) {
      if (mounted && currentLocation != null) {
        _getCurrentLocation();
      }
    });
  }

  Future<void> _loadFacilities() async {
    try {
      // Replace with your Laravel API endpoint
      final response = await http.get(
        Uri.parse('http://10.34.195.143:8000/api/faskes'),
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        print('API Response: $data'); // Debug log

        // Handle Laravel API response format
        if (data is Map<String, dynamic>) {
          if (data.containsKey('success') && data['success'] == true) {
            // Laravel API format with success wrapper
            if (data.containsKey('data')) {
              final dataContent = data['data'];
              if (dataContent is Map<String, dynamic> &&
                  dataContent.containsKey('data')) {
                // Paginated response format
                final facilitiesList = dataContent['data'] as List<dynamic>;
                setState(() {
                  facilities = List<Map<String, dynamic>>.from(facilitiesList);
                  isLoading = false;
                });
                print(
                  'Loaded ${facilities.length} facilities from paginated API',
                );
              } else if (dataContent is List) {
                // Direct list format
                setState(() {
                  facilities = List<Map<String, dynamic>>.from(dataContent);
                  isLoading = false;
                });
                print(
                  'Loaded ${facilities.length} facilities from direct list API',
                );
              } else {
                setState(() {
                  errorMessage = 'Invalid data format: data.data is not a list';
                  isLoading = false;
                });
              }
            } else {
              setState(() {
                errorMessage = 'No data key found in API response';
                isLoading = false;
              });
            }
          } else {
            setState(() {
              errorMessage =
                  'API request failed: ${data['message'] ?? 'Unknown error'}';
              isLoading = false;
            });
          }
        } else {
          setState(() {
            errorMessage = 'Unexpected response format: ${data.runtimeType}';
            isLoading = false;
          });
        }
      } else {
        setState(() {
          errorMessage = 'Failed to load facilities: ${response.statusCode}';
          isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        errorMessage = 'Error loading facilities: $e';
        isLoading = false;
      });
    }
  }

  // State Management Methods
  Future<void> _saveMapState() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final stateData = {
        'center_lat': mapState.center.latitude,
        'center_lng': mapState.center.longitude,
        'zoom': mapState.zoom,
        'route': activeRoute,
        'user_lat': currentLocation?.latitude,
        'user_lng': currentLocation?.longitude,
      };
      await prefs.setString('map_state', json.encode(stateData));
    } catch (e) {
      print('Error saving map state: $e');
    }
  }

  Future<void> _loadMapState() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final stateString = prefs.getString('map_state');
      if (stateString != null) {
        final stateData = json.decode(stateString);
        setState(() {
          mapState = MapState(
            center: LatLng(
              stateData['center_lat'] ?? -3.6561,
              stateData['center_lng'] ?? 128.1664,
            ),
            zoom: (stateData['zoom'] as num?)?.toDouble() ?? 13.0,
            route: stateData['route'],
          );

          if (stateData['user_lat'] != null && stateData['user_lng'] != null) {
            currentLocation = LatLng(
              stateData['user_lat'],
              stateData['user_lng'],
            );
          }

          if (stateData['route'] != null) {
            activeRoute = stateData['route'];
            isRouteActive = true;
            showClearRouteButton = true;
            _createRouteFromState();
          }
        });
      }
    } catch (e) {
      print('Error loading map state: $e');
    }
  }

  // Advanced Routing Methods
  void _createRoute(LatLng destination, {Map<String, dynamic>? facility}) {
    if (currentLocation == null) {
      _showSnackBar(
        'Lokasi Anda belum terdeteksi. Silakan refresh halaman dan izinkan akses lokasi.',
      );
      return;
    }

    setState(() {
      routeStart = currentLocation;
      routeEnd = destination;
      routePoints = [currentLocation!, destination];
      isRouteActive = true;
      showClearRouteButton = true;

      activeRoute = {
        'destination': {
          'lat': destination.latitude,
          'lng': destination.longitude,
        },
        'timestamp': DateTime.now().millisecondsSinceEpoch,
        'facility': facility,
      };

      mapState = mapState.copyWith(route: activeRoute);
    });

    _saveMapState();
    print('Route created: ${destination.latitude}, ${destination.longitude}');
  }

  void _createRouteFromState() {
    if (activeRoute != null && currentLocation != null) {
      final destination = activeRoute!['destination'];
      final destinationLatLng = LatLng(destination['lat'], destination['lng']);

      setState(() {
        routeStart = currentLocation;
        routeEnd = destinationLatLng;
        routePoints = [currentLocation!, destinationLatLng];
      });

      print('Route restored from state');
    }
  }

  void _updateActiveRoute() {
    if (isRouteActive && activeRoute != null && currentLocation != null) {
      final destination = activeRoute!['destination'];
      final destinationLatLng = LatLng(destination['lat'], destination['lng']);

      setState(() {
        routeStart = currentLocation;
        routeEnd = destinationLatLng;
        routePoints = [currentLocation!, destinationLatLng];
      });

      print('Route updated with new location');
    }
  }

  void _clearRoute() {
    setState(() {
      routeStart = null;
      routeEnd = null;
      routePoints = [];
      isRouteActive = false;
      showClearRouteButton = false;
      activeRoute = null;
      mapState = mapState.copyWith(route: null);
    });

    _saveMapState();
    print('Route cleared');
  }

  void _showSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), duration: const Duration(seconds: 3)),
    );
  }

  Marker _buildFacilityMarker(Map<String, dynamic> facility) {
    double _parseToDouble(dynamic value) {
      if (value == null) return 0.0;
      if (value is double) return value;
      if (value is int) return value.toDouble();
      if (value is String) return double.tryParse(value) ?? 0.0;
      return 0.0;
    }

    final lat = _parseToDouble(facility['latitude']);
    final lng = _parseToDouble(facility['longitude']);
    final type = (facility['type'] ?? '').toString();
    final name = (facility['nama'] ?? 'Unknown Facility').toString();
    final address = (facility['alamat'] ?? '').toString();

    // Custom icon based on type
    IconData iconData;
    Color iconColor;

    switch (type.toLowerCase()) {
      case 'apotek':
        iconData = Icons.local_pharmacy;
        iconColor = Colors.green;
        break;
      case 'puskesmas':
        iconData = Icons.local_hospital;
        iconColor = Colors.blue;
        break;
      case 'rumah sakit':
        iconData = Icons.medical_services;
        iconColor = Colors.red;
        break;
      default:
        iconData = Icons.location_on;
        iconColor = Colors.grey;
    }

    return Marker(
      point: LatLng(lat, lng),
      width: 40,
      height: 40,
      child: GestureDetector(
        onTap: () {
          _showFacilityPopup(context, facility);
        },
        child: Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.3),
                blurRadius: 4,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: Icon(iconData, color: iconColor, size: 24),
        ),
      ),
    );
  }

  void _showFacilityPopup(BuildContext context, Map<String, dynamic> facility) {
    double _parseToDouble(dynamic value) {
      if (value == null) return 0.0;
      if (value is double) return value;
      if (value is int) return value.toDouble();
      if (value is String) return double.tryParse(value) ?? 0.0;
      return 0.0;
    }

    final facilityLocation = LatLng(
      _parseToDouble(facility['latitude']),
      _parseToDouble(facility['longitude']),
    );

    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(facility['nama'] ?? 'Unknown Facility'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (facility['type'] != null)
                  _buildInfoRow('Tipe', facility['type']),
                if (facility['alamat'] != null)
                  _buildInfoRow('Alamat', facility['alamat']),
                if (facility['no_telp'] != null)
                  _buildInfoRow('Telepon', facility['no_telp']),
                if (facility['email'] != null)
                  _buildInfoRow('Email', facility['email']),
                if (facility['website'] != null)
                  _buildInfoRow('Website', facility['website']),
                if (facility['waktu_buka'] != null)
                  _buildInfoRow(
                    'Jam Buka',
                    '${facility['waktu_buka']} - ${facility['waktu_tutup'] ?? 'Tidak tersedia'}',
                  ),
                if (facility['layanan'] != null &&
                    facility['layanan'].isNotEmpty)
                  _buildInfoRow('Layanan', facility['layanan'].join(', ')),
                if (currentLocation != null) ...[
                  const SizedBox(height: 8),
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: _getAccuracyColor(locationAccuracy ?? 0),
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.location_on, size: 16),
                        const SizedBox(width: 4),
                        Text(
                          'Jarak: ${_calculateDistance(currentLocation!, facilityLocation).toStringAsFixed(1)} km',
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                  ),
                ],
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
                _createRoute(
                  facilityLocation,
                  facility: {
                    'name': facility['nama'],
                    'type': facility['type'],
                    'address': facility['alamat'],
                    'phone': facility['no_telp'],
                    'email': facility['email'],
                    'website': facility['website'],
                  },
                );
              },
              child: const Text('🗺️ Rute ke Sini'),
            ),
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text('Tutup'),
            ),
          ],
        );
      },
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 2),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 80,
            child: Text(
              '$label:',
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
          ),
          Expanded(child: Text(value)),
        ],
      ),
    );
  }

  Color _getAccuracyColor(double accuracy) {
    if (accuracy < 10) return Colors.green.shade100;
    if (accuracy < 50) return Colors.orange.shade100;
    return Colors.red.shade100;
  }

  double _calculateDistance(LatLng point1, LatLng point2) {
    const Distance distance = Distance();
    return distance.as(LengthUnit.Kilometer, point1, point2);
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    if (errorMessage != null) {
      return Scaffold(
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.error, size: 64, color: Colors.red),
              const SizedBox(height: 16),
              Text(
                errorMessage!,
                style: const TextStyle(fontSize: 16),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: () {
                  setState(() {
                    errorMessage = null;
                    isLoading = true;
                  });
                  _getCurrentLocation();
                  _loadFacilities();
                },
                child: const Text('Retry'),
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      body: Stack(
        children: [
          FlutterMap(
            mapController: mapController,
            options: MapOptions(
              initialCenter: mapState.center,
              initialZoom: mapState.zoom,
              minZoom: 5.0,
              maxZoom: 18.0,
              onMapReady: () {
                // Update map state when map is ready
                _updateMapState();
              },
              onMapEvent: (MapEvent event) {
                if (event is MapEventMove) {
                  setState(() {
                    mapState = mapState.copyWith(
                      center: event.camera.center,
                      zoom: event.camera.zoom,
                    );
                  });
                  _saveMapState();
                }
              },
            ),
            children: [
              TileLayer(
                urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                userAgentPackageName: 'com.example.faskes',
              ),
              // All markers in one layer
              MarkerLayer(
                markers: [
                  // Current location marker with advanced styling
                  if (currentLocation != null) _buildCurrentLocationMarker(),
                  // Facility markers
                  ...facilities.map(
                    (facility) => _buildFacilityMarker(facility),
                  ),
                ],
              ),
              // Route polyline
              if (routePoints.length >= 2)
                PolylineLayer(
                  polylines: [
                    Polyline(
                      points: routePoints,
                      color: Colors.blue,
                      strokeWidth: 4.0,
                    ),
                  ],
                ),
            ],
          ),
          // Clear route button
          if (showClearRouteButton)
            Positioned(
              top: 50,
              right: 16,
              child: FloatingActionButton(
                mini: true,
                onPressed: _clearRoute,
                backgroundColor: Colors.red,
                child: const Icon(Icons.close, color: Colors.white),
              ),
            ),
          // Current location button
          Positioned(
            bottom: 100,
            right: 16,
            child: FloatingActionButton(
              onPressed: () {
                if (currentLocation != null) {
                  mapController.move(currentLocation!, 15.0);
                }
              },
              child: const Icon(Icons.my_location),
            ),
          ),
        ],
      ),
    );
  }

  // Advanced Current Location Marker
  Marker _buildCurrentLocationMarker() {
    return Marker(
      point: currentLocation!,
      width: 50,
      height: 50,
      child: Container(
        decoration: BoxDecoration(
          color: Colors.blue.withOpacity(0.3),
          shape: BoxShape.circle,
        ),
        child: Stack(
          alignment: Alignment.center,
          children: [
            // Pulsing outer circle
            Container(
              width: 50,
              height: 50,
              decoration: BoxDecoration(
                color: Colors.blue.withOpacity(0.3),
                shape: BoxShape.circle,
              ),
            ),
            // Middle circle
            Container(
              width: 40,
              height: 40,
              decoration: BoxDecoration(
                color: Colors.blue.withOpacity(0.5),
                shape: BoxShape.circle,
              ),
            ),
            // Inner circle with icon
            Container(
              width: 30,
              height: 30,
              decoration: BoxDecoration(
                color: const Color(0xFF2c3e50),
                shape: BoxShape.circle,
                border: Border.all(color: Colors.white, width: 3),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.3),
                    blurRadius: 4,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: const Icon(
                Icons.my_location,
                color: Colors.white,
                size: 16,
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _updateMapState() {
    if (mounted) {
      setState(() {
        mapState = mapState.copyWith(
          center: mapController.camera.center,
          zoom: mapController.camera.zoom,
        );
      });
    }
  }

  @override
  void dispose() {
    _positionStreamSubscription?.cancel();
    _locationUpdateTimer?.cancel();
    super.dispose();
  }
}
