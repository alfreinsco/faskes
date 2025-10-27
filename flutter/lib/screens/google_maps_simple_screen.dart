import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:geolocator/geolocator.dart';
import '../models/faskes.dart';
import '../services/api_service.dart';

class GoogleMapsSimpleScreen extends StatefulWidget {
  const GoogleMapsSimpleScreen({Key? key}) : super(key: key);

  @override
  State<GoogleMapsSimpleScreen> createState() => _GoogleMapsSimpleScreenState();
}

class _GoogleMapsSimpleScreenState extends State<GoogleMapsSimpleScreen> {
  GoogleMapController? _mapController;
  Set<Marker> _markers = {};
  List<Faskes> _faskesList = [];
  bool _isLoading = true;
  LatLng _center = const LatLng(-6.200000, 106.816666); // Jakarta default

  @override
  void initState() {
    super.initState();
    _loadFaskes();
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

    // Add faskes markers
    for (Faskes faskes in _faskesList) {
      if (faskes.hasValidCoordinates) {
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

  void _showSnackBar(String message) {
    ScaffoldMessenger.of(
      context,
    ).showSnackBar(SnackBar(content: Text(message)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Peta Faskes'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
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
              mapType: MapType.normal,
              zoomControlsEnabled: true,
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          if (_mapController != null) {
            _mapController!.animateCamera(
              CameraUpdate.newLatLngZoom(_center, 15.0),
            );
          }
        },
        backgroundColor: Colors.blue,
        child: const Icon(Icons.center_focus_strong, color: Colors.white),
      ),
    );
  }
}
