import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:geolocator/geolocator.dart';
import '../models/faskes.dart';
import 'leaflet_routing_screen.dart';

class SimpleMapScreen extends StatefulWidget {
  final List<Faskes> faskesList;
  final Position? currentPosition;

  const SimpleMapScreen({
    super.key,
    required this.faskesList,
    this.currentPosition,
  });

  @override
  State<SimpleMapScreen> createState() => _SimpleMapScreenState();
}

class _SimpleMapScreenState extends State<SimpleMapScreen> {
  final MapController _mapController = MapController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _focusOnCurrentLocation();
    });
  }

  void _focusOnCurrentLocation() {
    if (widget.currentPosition != null) {
      // Focus on current location with smooth animation
      _mapController.move(
        LatLng(
          widget.currentPosition!.latitude,
          widget.currentPosition!.longitude,
        ),
        15.0, // Zoom level 15 for detailed view
      );

      // Show snackbar to inform user
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text('Memfokuskan ke lokasi Anda'),
          backgroundColor: Colors.green[600],
          duration: const Duration(seconds: 2),
        ),
      );
    } else {
      // Fallback to fit all faskes if no current location
      _fitBounds();
    }
  }

  void _fitBounds() {
    if (widget.faskesList.isNotEmpty) {
      final List<LatLng> points = [];
      for (final faskes in widget.faskesList) {
        if (faskes.latitude != null && faskes.longitude != null) {
          final lat = double.parse(faskes.latitude!);
          final lng = double.parse(faskes.longitude!);
          points.add(LatLng(lat, lng));
        }
      }

      if (points.isNotEmpty) {
        final bounds = LatLngBounds.fromPoints(points);
        _mapController.fitCamera(
          CameraFit.bounds(bounds: bounds, padding: const EdgeInsets.all(50)),
        );
      }
    }
  }

  Color _getMarkerColor(String type) {
    switch (type) {
      case 'Rumah Sakit':
        return Colors.red;
      case 'Puskesmas':
        return Colors.blue;
      case 'Apotek':
        return Colors.green;
      default:
        return Colors.grey;
    }
  }

  String _getTypeIcon(String type) {
    switch (type) {
      case 'Rumah Sakit':
        return '🏥';
      case 'Puskesmas':
        return '🏥';
      case 'Apotek':
        return '💊';
      default:
        return '🏥';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        FlutterMap(
          mapController: _mapController,
          options: MapOptions(
            initialCenter: const LatLng(-2.5489, 118.0149),
            initialZoom: 5.0,
            minZoom: 3.0,
            maxZoom: 18.0,
          ),
          children: [
            TileLayer(
              urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
              userAgentPackageName: 'com.example.faskes',
            ),
            MarkerLayer(
              markers: [
                // Current location marker
                if (widget.currentPosition != null)
                  Marker(
                    point: LatLng(
                      widget.currentPosition!.latitude,
                      widget.currentPosition!.longitude,
                    ),
                    width: 30,
                    height: 30,
                    child: Container(
                      decoration: BoxDecoration(
                        color: Colors.green[600],
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
                      child: const Center(
                        child: Icon(
                          Icons.my_location,
                          color: Colors.white,
                          size: 16,
                        ),
                      ),
                    ),
                  ),
                // Faskes markers
                ...widget.faskesList
                    .where(
                      (faskes) =>
                          faskes.latitude != null && faskes.longitude != null,
                    )
                    .map((faskes) {
                      final lat = double.parse(faskes.latitude!);
                      final lng = double.parse(faskes.longitude!);

                      return Marker(
                        point: LatLng(lat, lng),
                        width: 40,
                        height: 40,
                        child: GestureDetector(
                          onTap: () {
                            _showFaskesDialog(faskes);
                          },
                          child: Container(
                            decoration: BoxDecoration(
                              color: _getMarkerColor(faskes.type),
                              shape: BoxShape.circle,
                              border: Border.all(color: Colors.white, width: 2),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.3),
                                  blurRadius: 4,
                                  offset: const Offset(0, 2),
                                ),
                              ],
                            ),
                            child: Center(
                              child: Text(
                                _getTypeIcon(faskes.type),
                                style: const TextStyle(
                                  fontSize: 16,
                                  color: Colors.white,
                                ),
                              ),
                            ),
                          ),
                        ),
                      );
                    })
                    .toList(),
              ],
            ),
          ],
        ),
        // Floating action buttons
        Positioned(
          bottom: 20,
          right: 20,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // My Location button
              if (widget.currentPosition != null)
                FloatingActionButton(
                  onPressed: _focusOnCurrentLocation,
                  backgroundColor: Colors.green[600],
                  child: const Icon(Icons.my_location, color: Colors.white),
                  heroTag: "my_location",
                ),
              const SizedBox(height: 10),
              // Route button
              FloatingActionButton.extended(
                onPressed: () {
                  if (widget.faskesList.isNotEmpty) {
                    _showFaskesListDialog();
                  } else {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text('Tidak ada faskes yang tersedia'),
                        backgroundColor: Colors.orange,
                      ),
                    );
                  }
                },
                icon: const Icon(Icons.directions, color: Colors.white),
                label: const Text(
                  'Rute',
                  style: TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                backgroundColor: Colors.blue[600],
                elevation: 8,
                heroTag: "route",
              ),
            ],
          ),
        ),
      ],
    );
  }

  void _showFaskesListDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Pilih Faskes untuk Rute'),
        content: SizedBox(
          width: double.maxFinite,
          height: 300,
          child: ListView.builder(
            itemCount: widget.faskesList.length,
            itemBuilder: (context, index) {
              final faskes = widget.faskesList[index];
              return ListTile(
                leading: Text(
                  faskes.typeIcon,
                  style: const TextStyle(fontSize: 20),
                ),
                title: Text(
                  faskes.nama,
                  style: const TextStyle(fontWeight: FontWeight.bold),
                ),
                subtitle: Text(faskes.type),
                trailing: Icon(Icons.directions, color: Colors.blue[600]),
                onTap: () {
                  Navigator.pop(context);
                  _showRoute(faskes);
                },
              );
            },
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Batal'),
          ),
        ],
      ),
    );
  }

  void _showFaskesDialog(Faskes faskes) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Row(
          children: [
            Text(faskes.typeIcon, style: const TextStyle(fontSize: 24)),
            const SizedBox(width: 8),
            Expanded(
              child: Text(faskes.nama, style: const TextStyle(fontSize: 18)),
            ),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              faskes.type,
              style: TextStyle(
                color: _getMarkerColor(faskes.type),
                fontWeight: FontWeight.w500,
                fontSize: 16,
              ),
            ),
            const SizedBox(height: 8),
            Text(faskes.alamat, style: const TextStyle(fontSize: 14)),
            if (faskes.noTelp != null) ...[
              const SizedBox(height: 8),
              Row(
                children: [
                  const Icon(Icons.phone, size: 16, color: Colors.grey),
                  const SizedBox(width: 4),
                  Text(faskes.noTelp!, style: const TextStyle(fontSize: 14)),
                ],
              ),
            ],
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Tutup'),
          ),
          ElevatedButton.icon(
            onPressed: () {
              Navigator.pop(context);
              _showRoute(faskes);
            },
            icon: const Icon(Icons.directions, size: 16),
            label: const Text('Rute'),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.blue[600],
              foregroundColor: Colors.white,
            ),
          ),
        ],
      ),
    );
  }

  void _showRoute(Faskes faskes) async {
    if (widget.currentPosition == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text(
            'Tidak dapat mengakses lokasi saat ini. Pastikan GPS aktif dan izin lokasi diberikan.',
          ),
          backgroundColor: Colors.red,
          duration: Duration(seconds: 3),
        ),
      );
      return;
    }

    // Navigate to Leaflet routing screen
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => LeafletRoutingScreen(
          faskes: faskes,
          currentPosition: widget.currentPosition!,
        ),
      ),
    );
  }
}
