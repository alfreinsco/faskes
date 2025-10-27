import 'dart:async';
import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:geolocator/geolocator.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:permission_handler/permission_handler.dart';
import '../models/faskes.dart';
import '../services/api_service.dart';
import '../services/directions_service.dart';

class GoogleMapsAdvancedScreen extends StatefulWidget {
  const GoogleMapsAdvancedScreen({Key? key}) : super(key: key);

  @override
  State<GoogleMapsAdvancedScreen> createState() =>
      _GoogleMapsAdvancedScreenState();
}

class _GoogleMapsAdvancedScreenState extends State<GoogleMapsAdvancedScreen> {
  GoogleMapController? _mapController;
  Set<Marker> _markers = {};
  Set<Polyline> _polylines = {};
  List<Faskes> _faskesList = [];
  bool _isLoading = true;
  Position? _currentPosition;
  LatLng _center = const LatLng(-3.6561, 128.1664); // Ambon default
  String _selectedType = 'Semua';
  String _searchQuery = '';
  bool _isTrackingLocation = false;
  StreamSubscription<Position>? _positionStreamSubscription;
  bool _hasActiveRoute = false;
  DirectionsResult? _currentRoute;
  bool _isLoadingRoute = false;

  final List<String> _faskesTypes = [
    'Semua',
    'Rumah Sakit',
    'Puskesmas',
    'Apotek',
  ];

  @override
  void initState() {
    super.initState();
    _requestLocationPermission();
    _loadFaskes();
  }

  @override
  void dispose() {
    _positionStreamSubscription?.cancel();
    super.dispose();
  }

  Future<void> _requestLocationPermission() async {
    final status = await Permission.location.request();
    if (status.isGranted) {
      _getCurrentLocation();
    } else {
      _showSnackBar('Izin lokasi diperlukan untuk menggunakan fitur ini');
    }
  }

  Future<void> _getCurrentLocation() async {
    try {
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        _showSnackBar('Layanan lokasi tidak aktif');
        return;
      }

      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) {
          _showSnackBar('Izin lokasi ditolak');
          return;
        }
      }

      if (permission == LocationPermission.deniedForever) {
        _showSnackBar('Izin lokasi ditolak selamanya');
        return;
      }

      // Get current position
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );

      setState(() {
        _currentPosition = position;
        _center = LatLng(position.latitude, position.longitude);
      });

      if (_mapController != null) {
        _mapController!.animateCamera(CameraUpdate.newLatLng(_center));
      }

      // Start tracking location
      _startLocationTracking();
    } catch (e) {
      _showSnackBar('Gagal mendapatkan lokasi: $e');
    }
  }

  void _startLocationTracking() {
    if (_isTrackingLocation) return;

    setState(() {
      _isTrackingLocation = true;
    });

    _positionStreamSubscription =
        Geolocator.getPositionStream(
          locationSettings: const LocationSettings(
            accuracy: LocationAccuracy.high,
            distanceFilter: 10, // Update every 10 meters
          ),
        ).listen((Position position) {
          setState(() {
            _currentPosition = position;
          });
          _updateMarkers();
        });
  }

  void _stopLocationTracking() {
    _positionStreamSubscription?.cancel();
    setState(() {
      _isTrackingLocation = false;
    });
  }

  Future<void> _loadFaskes() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final faskesList = await ApiService.getAllFaskes();
      setState(() {
        _faskesList = faskesList;
        _isLoading = false;
      });
      _updateMarkers();
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      _showSnackBar('Gagal memuat data faskes: $e');
    }
  }

  void _updateMarkers() {
    _markers.clear();

    // Add current location marker
    if (_currentPosition != null) {
      _markers.add(
        Marker(
          markerId: const MarkerId('current_location'),
          position: LatLng(
            _currentPosition!.latitude,
            _currentPosition!.longitude,
          ),
          icon: BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueBlue),
          infoWindow: const InfoWindow(
            title: '📍 Lokasi Anda',
            snippet: 'Posisi saat ini',
          ),
        ),
      );
    }

    // Add faskes markers
    for (Faskes faskes in _faskesList) {
      if (faskes.hasValidCoordinates) {
        // Filter by type
        if (_selectedType != 'Semua' && faskes.type != _selectedType) {
          continue;
        }

        // Filter by search query
        if (_searchQuery.isNotEmpty &&
            !faskes.nama.toLowerCase().contains(_searchQuery.toLowerCase()) &&
            !faskes.alamat.toLowerCase().contains(_searchQuery.toLowerCase())) {
          continue;
        }

        final lat = double.parse(faskes.latitude!);
        final lng = double.parse(faskes.longitude!);

        _markers.add(
          Marker(
            markerId: MarkerId('faskes_${faskes.id}'),
            position: LatLng(lat, lng),
            icon: _getMarkerIcon(faskes.type),
            infoWindow: InfoWindow(
              title: faskes.nama,
              snippet: '${faskes.type} - ${faskes.alamat}',
            ),
            onTap: () => _showFaskesDetail(faskes),
          ),
        );
      }
    }

    setState(() {});
  }

  BitmapDescriptor _getMarkerIcon(String type) {
    switch (type) {
      case 'Rumah Sakit':
        return BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueRed);
      case 'Puskesmas':
        return BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueBlue);
      case 'Apotek':
        return BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueGreen);
      default:
        return BitmapDescriptor.defaultMarkerWithHue(
          BitmapDescriptor.hueOrange,
        );
    }
  }

  void _showFaskesDetail(Faskes faskes) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (context) => DraggableScrollableSheet(
        initialChildSize: 0.4,
        minChildSize: 0.2,
        maxChildSize: 0.8,
        builder: (context, scrollController) => Container(
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
          ),
          child: SingleChildScrollView(
            controller: scrollController,
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Center(
                    child: Container(
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(
                        color: Colors.grey[300],
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Row(
                    children: [
                      Text(
                        faskes.typeIcon,
                        style: const TextStyle(fontSize: 24),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              faskes.nama,
                              style: const TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            Text(
                              faskes.type,
                              style: TextStyle(
                                fontSize: 14,
                                color: Colors.grey[600],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  _buildDetailRow(Icons.location_on, faskes.alamat),
                  if (faskes.noTelp != null)
                    _buildDetailRow(Icons.phone, faskes.noTelp!),
                  if (faskes.email != null)
                    _buildDetailRow(Icons.email, faskes.email!),
                  if (faskes.waktuBuka != null && faskes.waktuTutup != null)
                    _buildDetailRow(
                      Icons.access_time,
                      '${faskes.waktuBuka} - ${faskes.waktuTutup}',
                    ),
                  if (faskes.layanan != null && faskes.layanan!.isNotEmpty) ...[
                    const SizedBox(height: 12),
                    const Text(
                      'Layanan:',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Wrap(
                      spacing: 8,
                      runSpacing: 4,
                      children: faskes.layanan!
                          .map(
                            (layanan) => Chip(
                              label: Text(layanan),
                              backgroundColor: Colors.blue[50],
                              labelStyle: const TextStyle(fontSize: 12),
                            ),
                          )
                          .toList(),
                    ),
                  ],
                  const SizedBox(height: 20),
                  Row(
                    children: [
                      Expanded(
                        child: ElevatedButton.icon(
                          onPressed: _isLoadingRoute
                              ? null
                              : () {
                                  Navigator.pop(context);
                                  _createRouteToFaskes(faskes);
                                },
                          icon: _isLoadingRoute
                              ? const SizedBox(
                                  width: 16,
                                  height: 16,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    valueColor: AlwaysStoppedAnimation<Color>(
                                      Colors.white,
                                    ),
                                  ),
                                )
                              : const Icon(Icons.directions),
                          label: Text(
                            _isLoadingRoute
                                ? 'Membuat Rute...'
                                : 'Rute ke Lokasi',
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.blue,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 12),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: ElevatedButton.icon(
                          onPressed: () {
                            Navigator.pop(context);
                            _openInGoogleMaps(faskes);
                          },
                          icon: const Icon(Icons.open_in_new),
                          label: const Text('Buka di Maps'),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.green,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 12),
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildDetailRow(IconData icon, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 16, color: Colors.grey[600]),
          const SizedBox(width: 8),
          Expanded(child: Text(text, style: const TextStyle(fontSize: 14))),
        ],
      ),
    );
  }

  Future<void> _createRouteToFaskes(Faskes faskes) async {
    if (!faskes.hasValidCoordinates || _currentPosition == null) {
      _showSnackBar('Tidak dapat membuat rute: koordinat tidak valid');
      return;
    }

    setState(() {
      _isLoadingRoute = true;
    });

    try {
      final destination = LatLng(
        double.parse(faskes.latitude!),
        double.parse(faskes.longitude!),
      );

      // Always try to get real road route first
      final directionsResult = await _getDirectionsWithRetry(
        originLat: _currentPosition!.latitude,
        originLng: _currentPosition!.longitude,
        destinationLat: destination.latitude,
        destinationLng: destination.longitude,
      );

      if (directionsResult != null) {
        // Clear existing polylines
        _polylines.clear();

        // Create polyline for route visualization
        _polylines.add(
          Polyline(
            polylineId: const PolylineId('route'),
            points: directionsResult.points,
            color: Colors.blue,
            width: 5,
            patterns: [PatternItem.dash(20), PatternItem.gap(10)],
          ),
        );

        setState(() {
          _hasActiveRoute = true;
          _currentRoute = directionsResult;
        });

        // Animate camera to show the route
        _mapController?.animateCamera(
          CameraUpdate.newLatLngBounds(
            _getBoundsForRoute(directionsResult.points),
            100.0, // padding
          ),
        );

        _showSnackBar(
          'Rute ke ${faskes.nama} telah dibuat\n'
          'Jarak: ${directionsResult.distance} | '
          'Waktu: ${directionsResult.duration}',
        );
      } else {
        _showSnackBarWithRetry(
          'Gagal mendapatkan rute jalan sebenarnya.\n'
          'Pastikan koneksi internet stabil dan coba lagi.',
          () => _createRouteToFaskes(faskes),
        );
      }
    } catch (e) {
      _showSnackBar('Error: $e');
    } finally {
      setState(() {
        _isLoadingRoute = false;
      });
    }
  }

  Future<DirectionsResult?> _getDirectionsWithRetry({
    required double originLat,
    required double originLng,
    required double destinationLat,
    required double destinationLng,
    int maxRetries = 3,
  }) async {
    for (int attempt = 1; attempt <= maxRetries; attempt++) {
      try {
        print('Attempt $attempt of $maxRetries to get directions');

        final result = await DirectionsService.getDirections(
          originLat: originLat,
          originLng: originLng,
          destinationLat: destinationLat,
          destinationLng: destinationLng,
        );

        if (result != null) {
          print('Directions API success on attempt $attempt');
          return result;
        }

        if (attempt < maxRetries) {
          print('Directions API failed on attempt $attempt, retrying...');
          await Future.delayed(
            Duration(seconds: attempt * 2),
          ); // Exponential backoff
        }
      } catch (e) {
        print('Directions API error on attempt $attempt: $e');
        if (attempt < maxRetries) {
          await Future.delayed(Duration(seconds: attempt * 2));
        }
      }
    }

    print('All attempts failed to get directions');
    return null;
  }

  LatLngBounds _getBoundsForRoute(List<LatLng> points) {
    double minLat = points.first.latitude;
    double maxLat = points.first.latitude;
    double minLng = points.first.longitude;
    double maxLng = points.first.longitude;

    for (var point in points) {
      minLat = minLat < point.latitude ? minLat : point.latitude;
      maxLat = maxLat > point.latitude ? maxLat : point.latitude;
      minLng = minLng < point.longitude ? minLng : point.longitude;
      maxLng = maxLng > point.longitude ? maxLng : point.longitude;
    }

    return LatLngBounds(
      southwest: LatLng(minLat, minLng),
      northeast: LatLng(maxLat, maxLng),
    );
  }

  void _clearRoute() {
    setState(() {
      _polylines.clear();
      _hasActiveRoute = false;
      _currentRoute = null;
    });
    _showSnackBar('Rute telah dihapus');
  }

  Future<void> _openInGoogleMaps(Faskes faskes) async {
    if (!faskes.hasValidCoordinates) {
      _showSnackBar('Koordinat faskes tidak valid');
      return;
    }

    final lat = double.parse(faskes.latitude!);
    final lng = double.parse(faskes.longitude!);

    final url = 'https://www.google.com/maps/dir/?api=1&destination=$lat,$lng';

    try {
      if (await canLaunchUrl(Uri.parse(url))) {
        await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
      } else {
        _showSnackBar('Tidak dapat membuka Google Maps');
      }
    } catch (e) {
      _showSnackBar('Error: $e');
    }
  }

  void _showSnackBar(String message) {
    ScaffoldMessenger.of(
      context,
    ).showSnackBar(SnackBar(content: Text(message)));
  }

  void _showSnackBarWithRetry(String message, VoidCallback onRetry) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        duration: const Duration(seconds: 5),
        action: SnackBarAction(
          label: 'Coba Lagi',
          textColor: Colors.white,
          onPressed: onRetry,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Peta Faskes'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            onPressed: _getCurrentLocation,
            icon: const Icon(Icons.my_location),
            tooltip: 'Lokasi Saya',
          ),
          IconButton(
            onPressed: _isTrackingLocation
                ? _stopLocationTracking
                : _startLocationTracking,
            icon: Icon(
              _isTrackingLocation ? Icons.location_off : Icons.location_on,
            ),
            tooltip: _isTrackingLocation ? 'Stop Tracking' : 'Start Tracking',
          ),
        ],
      ),
      body: Column(
        children: [
          // Search and Filter Bar
          Container(
            color: Colors.blue,
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                // Search Bar
                TextField(
                  decoration: InputDecoration(
                    hintText: 'Cari faskes...',
                    prefixIcon: const Icon(Icons.search),
                    suffixIcon: _searchQuery.isNotEmpty
                        ? IconButton(
                            onPressed: () {
                              setState(() {
                                _searchQuery = '';
                              });
                              _updateMarkers();
                            },
                            icon: const Icon(Icons.clear),
                          )
                        : null,
                    filled: true,
                    fillColor: Colors.white,
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(25),
                      borderSide: BorderSide.none,
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 20,
                      vertical: 12,
                    ),
                  ),
                  onChanged: (value) {
                    setState(() {
                      _searchQuery = value;
                    });
                    _updateMarkers();
                  },
                ),
                const SizedBox(height: 12),
                // Filter Chips
                SizedBox(
                  height: 40,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: _faskesTypes.length,
                    itemBuilder: (context, index) {
                      final type = _faskesTypes[index];
                      final isSelected = _selectedType == type;
                      return Padding(
                        padding: const EdgeInsets.only(right: 8),
                        child: FilterChip(
                          label: Text(type),
                          selected: isSelected,
                          onSelected: (selected) {
                            setState(() {
                              _selectedType = type;
                            });
                            _updateMarkers();
                          },
                          backgroundColor: Colors.white,
                          selectedColor: Colors.blue[100],
                          checkmarkColor: Colors.blue,
                        ),
                      );
                    },
                  ),
                ),
              ],
            ),
          ),
          // Map
          Expanded(
            child: Stack(
              children: [
                _isLoading
                    ? const Center(child: CircularProgressIndicator())
                    : GoogleMap(
                        onMapCreated: (GoogleMapController controller) {
                          _mapController = controller;
                        },
                        initialCameraPosition: CameraPosition(
                          target: _center,
                          zoom: 13.0,
                        ),
                        markers: _markers,
                        polylines: _polylines,
                        myLocationEnabled: true,
                        myLocationButtonEnabled: false,
                        mapType: MapType.normal,
                        zoomControlsEnabled: false,
                        onTap: (LatLng position) {
                          // Hide any open info windows
                          setState(() {});
                        },
                      ),
                // Route information overlay
                if (_hasActiveRoute && _currentRoute != null)
                  Positioned(
                    bottom: 20,
                    left: 20,
                    right: 20,
                    child: Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.1),
                            blurRadius: 8,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.route, color: Colors.blue, size: 24),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                const Text(
                                  'Rute Aktif',
                                  style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                    fontSize: 16,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  'Jarak: ${_currentRoute!.distance}',
                                  style: const TextStyle(fontSize: 14),
                                ),
                                Text(
                                  'Waktu: ${_currentRoute!.duration}',
                                  style: const TextStyle(fontSize: 14),
                                ),
                              ],
                            ),
                          ),
                          IconButton(
                            onPressed: _clearRoute,
                            icon: const Icon(Icons.close, color: Colors.red),
                            tooltip: 'Hapus Rute',
                          ),
                        ],
                      ),
                    ),
                  ),
              ],
            ),
          ),
        ],
      ),
      floatingActionButton: Column(
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          if (_hasActiveRoute)
            FloatingActionButton(
              onPressed: _clearRoute,
              mini: true,
              backgroundColor: Colors.red,
              child: const Icon(Icons.clear, color: Colors.white),
            ),
          const SizedBox(height: 8),
          FloatingActionButton(
            onPressed: _getCurrentLocation,
            mini: true,
            backgroundColor: Colors.white,
            child: const Icon(Icons.my_location, color: Colors.blue),
          ),
          const SizedBox(height: 8),
          FloatingActionButton(
            onPressed: () {
              if (_mapController != null) {
                _mapController!.animateCamera(
                  CameraUpdate.newLatLngZoom(_center, 15.0),
                );
              }
            },
            mini: true,
            backgroundColor: Colors.white,
            child: const Icon(Icons.center_focus_strong, color: Colors.blue),
          ),
        ],
      ),
    );
  }
}
