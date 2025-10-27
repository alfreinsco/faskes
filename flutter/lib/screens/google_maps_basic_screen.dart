import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';

class GoogleMapsBasicScreen extends StatefulWidget {
  const GoogleMapsBasicScreen({Key? key}) : super(key: key);

  @override
  State<GoogleMapsBasicScreen> createState() => _GoogleMapsBasicScreenState();
}

class _GoogleMapsBasicScreenState extends State<GoogleMapsBasicScreen> {
  GoogleMapController? _mapController;
  Set<Marker> _markers = {};
  LatLng _center = const LatLng(-6.200000, 106.816666); // Jakarta default

  @override
  void initState() {
    super.initState();
    _addSampleMarkers();
  }

  void _addSampleMarkers() {
    _markers.add(
      const Marker(
        markerId: MarkerId('marker1'),
        position: LatLng(-6.200000, 106.816666),
        infoWindow: InfoWindow(title: 'Jakarta', snippet: 'Ibu Kota Indonesia'),
      ),
    );

    _markers.add(
      const Marker(
        markerId: MarkerId('marker2'),
        position: LatLng(-6.175110, 106.865039),
        infoWindow: InfoWindow(title: 'Monas', snippet: 'Monumen Nasional'),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Google Maps Basic'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: GoogleMap(
        onMapCreated: (GoogleMapController controller) {
          _mapController = controller;
        },
        initialCameraPosition: CameraPosition(target: _center, zoom: 13.0),
        markers: _markers,
        mapType: MapType.normal,
        zoomControlsEnabled: true,
        myLocationEnabled: false,
        myLocationButtonEnabled: false,
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
