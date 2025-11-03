import 'package:flutter/material.dart';
import 'package:latlong2/latlong.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:http/http.dart' as http;
import 'package:geolocator/geolocator.dart';
import '../services/api_service.dart';
import '../models/faskes.dart';
import 'dart:convert';

class RoutingMapsScreen extends StatefulWidget {
  const RoutingMapsScreen({super.key});

  @override
  State<RoutingMapsScreen> createState() => _RoutingMapsScreenState();
}

class _RoutingMapsScreenState extends State<RoutingMapsScreen> {
  bool isVisible = true; // Default: peta selalu tampil
  List<LatLng> routePoints = []; // Kosong sampai route dihitung
  bool isLoading = false;
  LatLng? userLocation; // Lokasi user dari GPS
  final LatLng defaultLocation = LatLng(
    -3.635897,
    128.215689,
  ); // Default: Ambon
  bool isGettingLocation = true;
  final MapController mapController = MapController();
  List<Faskes> faskesList = []; // List faskes dari API
  bool isLoadingFaskes = false;

  @override
  void initState() {
    super.initState();
    _getUserLocation();
    _loadFaskes();
  }

  @override
  void dispose() {
    super.dispose();
  }

  // Mendapatkan lokasi user dari GPS
  Future<void> _getUserLocation() async {
    try {
      // Cek permission
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        setState(() {
          isGettingLocation = false;
        });
        return;
      }

      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) {
          setState(() {
            isGettingLocation = false;
          });
          return;
        }
      }

      if (permission == LocationPermission.deniedForever) {
        setState(() {
          isGettingLocation = false;
        });
        return;
      }

      // Dapatkan lokasi saat ini
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );

      final newLocation = LatLng(position.latitude, position.longitude);
      setState(() {
        userLocation = newLocation;
        isGettingLocation = false;
      });

      // Update peta center ke lokasi user
      mapController.move(newLocation, 13);
    } catch (e) {
      print('Error getting location: $e');
      setState(() {
        isGettingLocation = false;
      });
    }
  }

  // Memuat data faskes dari API
  Future<void> _loadFaskes() async {
    setState(() {
      isLoadingFaskes = true;
    });

    try {
      final faskes = await ApiService.getAllFaskes();
      setState(() {
        faskesList = faskes;
        isLoadingFaskes = false;
      });
    } catch (e) {
      print('Error loading faskes: $e');
      setState(() {
        isLoadingFaskes = false;
      });
    }
  }

  // Mendapatkan warna marker berdasarkan type faskes
  Color _getFaskesColor(String type) {
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

  // Menampilkan modal detail faskes
  void _showFaskesDetailModal(Faskes faskes) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (BuildContext context) {
        return Container(
          height: MediaQuery.of(context).size.height * 0.6,
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.only(
              topLeft: Radius.circular(20),
              topRight: Radius.circular(20),
            ),
          ),
          child: Column(
            children: [
              // Handle bar
              Container(
                margin: const EdgeInsets.only(top: 12),
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: Colors.grey[300],
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              // Header
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: _getFaskesColor(faskes.type),
                  borderRadius: const BorderRadius.only(
                    topLeft: Radius.circular(20),
                    topRight: Radius.circular(20),
                  ),
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.2),
                        shape: BoxShape.circle,
                      ),
                      child: Text(
                        faskes.typeIcon,
                        style: const TextStyle(fontSize: 24),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            faskes.nama,
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 4),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 8,
                              vertical: 4,
                            ),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.2),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Text(
                              faskes.type,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 12,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    IconButton(
                      icon: const Icon(Icons.close, color: Colors.white),
                      onPressed: () => Navigator.pop(context),
                    ),
                  ],
                ),
              ),
              // Content
              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Tombol Rute
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          onPressed: () {
                            Navigator.pop(context); // Tutup modal
                            _calculateRouteToFaskes(faskes);
                          },
                          icon: const Icon(Icons.route, color: Colors.white),
                          label: const Text(
                            'Tampilkan Rute',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 16,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: _getFaskesColor(faskes.type),
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      // Status
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: faskes.isActive
                              ? Colors.green[50]
                              : Colors.red[50],
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: faskes.isActive
                                ? Colors.green[200]!
                                : Colors.red[200]!,
                          ),
                        ),
                        child: Row(
                          children: [
                            Icon(
                              faskes.isActive
                                  ? Icons.check_circle
                                  : Icons.cancel,
                              color: faskes.isActive
                                  ? Colors.green[600]
                                  : Colors.red[600],
                              size: 20,
                            ),
                            const SizedBox(width: 8),
                            Text(
                              faskes.isActive
                                  ? 'Faskes Aktif'
                                  : 'Faskes Tidak Aktif',
                              style: TextStyle(
                                color: faskes.isActive
                                    ? Colors.green[800]
                                    : Colors.red[800],
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),
                      // Alamat
                      _buildDetailRow(
                        icon: Icons.location_on,
                        title: 'Alamat',
                        content: faskes.alamat,
                        iconColor: Colors.red,
                      ),
                      const SizedBox(height: 12),
                      // Nomor Telepon
                      if (faskes.noTelp != null) ...[
                        _buildDetailRow(
                          icon: Icons.phone,
                          title: 'Telepon',
                          content: faskes.noTelp!,
                          iconColor: Colors.blue,
                        ),
                        const SizedBox(height: 12),
                      ],
                      // Email
                      if (faskes.email != null) ...[
                        _buildDetailRow(
                          icon: Icons.email,
                          title: 'Email',
                          content: faskes.email!,
                          iconColor: Colors.orange,
                        ),
                        const SizedBox(height: 12),
                      ],
                      // Jam Operasional
                      if (faskes.waktuBuka != null &&
                          faskes.waktuTutup != null) ...[
                        _buildDetailRow(
                          icon: Icons.access_time,
                          title: 'Jam Operasional',
                          content: '${faskes.waktuBuka} - ${faskes.waktuTutup}',
                          iconColor: Colors.purple,
                        ),
                        const SizedBox(height: 12),
                      ],
                      // Layanan
                      if (faskes.layanan != null &&
                          faskes.layanan!.isNotEmpty) ...[
                        const Text(
                          'Layanan',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: Colors.black87,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Wrap(
                          spacing: 8,
                          runSpacing: 8,
                          children: faskes.layanan!.map((layanan) {
                            return Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 12,
                                vertical: 6,
                              ),
                              decoration: BoxDecoration(
                                color: Colors.cyan[50],
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(color: Colors.cyan[300]!),
                              ),
                              child: Text(
                                layanan,
                                style: TextStyle(
                                  color: Colors.cyan[800],
                                  fontSize: 12,
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            );
                          }).toList(),
                        ),
                      ],
                    ],
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  // Widget untuk detail row
  Widget _buildDetailRow({
    required IconData icon,
    required String title,
    required String content,
    required Color iconColor,
  }) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 20, color: iconColor),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: Colors.grey,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                content,
                style: const TextStyle(fontSize: 14, color: Colors.black87),
              ),
            ],
          ),
        ),
      ],
    );
  }

  // Menghitung rute dari lokasi user ke faskes
  Future<void> _calculateRouteToFaskes(Faskes faskes) async {
    if (userLocation == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Lokasi Anda tidak tersedia. Pastikan GPS aktif.'),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    if (!faskes.hasValidCoordinates) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Koordinat faskes tidak valid'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final faskesLat = double.tryParse(faskes.latitude!);
    final faskesLng = double.tryParse(faskes.longitude!);

    if (faskesLat == null || faskesLng == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Koordinat faskes tidak valid'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      isLoading = true;
    });

    try {
      // Request route from OSRM: dari lokasi user ke faskes
      var startLat = userLocation!.latitude;
      var startLng = userLocation!.longitude;
      var endLat = faskesLat;
      var endLng = faskesLng;

      var url = Uri.parse(
        'http://router.project-osrm.org/route/v1/driving/$startLng,$startLat;$endLng,$endLat?steps=true&annotations=true&geometries=geojson&overview=full',
      );
      var response = await http.get(url);

      print('OSRM Response: ${response.body}');

      if (response.statusCode == 200) {
        var data = jsonDecode(response.body);

        if (data['code'] == 'Ok' &&
            data['routes'] != null &&
            data['routes'].isNotEmpty) {
          setState(() {
            routePoints = [];
            var route = data['routes'][0];
            var coordinates = route['geometry']['coordinates'];

            for (int i = 0; i < coordinates.length; i++) {
              var coord = coordinates[i];
              // GeoJSON format: [longitude, latitude]
              routePoints.add(LatLng(coord[1].toDouble(), coord[0].toDouble()));
            }

            isVisible = true;
            isLoading = false;
          });

          // Update center peta ke titik tengah rute
          if (routePoints.isNotEmpty) {
            final midPoint = routePoints[routePoints.length ~/ 2];
            mapController.move(midPoint, 13);
          }

          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Rute ke ${faskes.nama} berhasil ditampilkan'),
              backgroundColor: Colors.green,
              duration: const Duration(seconds: 2),
            ),
          );
        } else {
          setState(() {
            isLoading = false;
          });
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                'Tidak dapat menemukan rute: ${data['code'] ?? 'Unknown error'}',
              ),
              backgroundColor: Colors.red,
            ),
          );
        }
      } else {
        setState(() {
          isLoading = false;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: ${response.statusCode}'),
            backgroundColor: Colors.red,
          ),
        );
      }
    } catch (e) {
      setState(() {
        isLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
      print('Error calculating route to faskes: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Routing Maps',
          style: TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.w700,
            color: Colors.white,
          ),
        ),
        backgroundColor: Colors.cyan[600],
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      backgroundColor: Colors.grey[300],
      body: Visibility(
        visible: isVisible,
        child: FlutterMap(
          mapController: mapController,
          options: MapOptions(
            center: routePoints.isNotEmpty
                ? routePoints[0]
                : (userLocation ??
                      defaultLocation), // Gunakan lokasi user jika ada, jika tidak gunakan Ambon
            zoom: routePoints.isNotEmpty ? 13 : 11,
          ),
          children: [
            TileLayer(
              urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
              userAgentPackageName: 'com.example.faskes',
            ),
            // Marker untuk lokasi user
            if (userLocation != null)
              MarkerLayer(
                markers: [
                  Marker(
                    point: userLocation!,
                    width: 50,
                    height: 50,
                    child: Container(
                      decoration: BoxDecoration(
                        color: Colors.cyan[600],
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white, width: 3),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.3),
                            blurRadius: 6,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: const Icon(
                        Icons.person,
                        color: Colors.white,
                        size: 24,
                      ),
                    ),
                  ),
                ],
              ),
            // Marker untuk setiap faskes
            MarkerLayer(
              markers: faskesList
                  .where((faskes) => faskes.hasValidCoordinates)
                  .map((faskes) {
                    final lat = double.tryParse(faskes.latitude!);
                    final lng = double.tryParse(faskes.longitude!);
                    if (lat == null || lng == null) return null;

                    return Marker(
                      point: LatLng(lat, lng),
                      width: 40,
                      height: 40,
                      child: GestureDetector(
                        onTap: () {
                          _showFaskesDetailModal(faskes);
                        },
                        child: Container(
                          decoration: BoxDecoration(
                            color: _getFaskesColor(faskes.type),
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
                              faskes.typeIcon,
                              style: const TextStyle(fontSize: 20),
                            ),
                          ),
                        ),
                      ),
                    );
                  })
                  .where((marker) => marker != null)
                  .cast<Marker>()
                  .toList(),
            ),
            // Polyline untuk route
            if (routePoints.length > 1)
              PolylineLayer(
                polylineCulling: false,
                polylines: [
                  Polyline(
                    points: routePoints,
                    color: Colors.blue,
                    strokeWidth: 6,
                  ),
                ],
              ),
          ],
        ),
      ),
    );
  }
}
