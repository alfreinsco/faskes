import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:geolocator/geolocator.dart';
import '../services/routing_service.dart';
import '../models/faskes.dart';

class RoutingScreen extends StatefulWidget {
  final Faskes faskes;
  final Position currentPosition;

  const RoutingScreen({
    super.key,
    required this.faskes,
    required this.currentPosition,
  });

  @override
  State<RoutingScreen> createState() => _RoutingScreenState();
}

class _RoutingScreenState extends State<RoutingScreen> {
  final MapController _mapController = MapController();
  List<LatLng> _routePoints = [];
  RouteResult? _routeResult;
  bool _isCalculatingRoute = false;
  String _transportMode = 'driving-car';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _calculateRoute();
    });
  }

  Future<void> _calculateRoute() async {
    setState(() {
      _isCalculatingRoute = true;
    });

    final startPoint = LatLng(
      widget.currentPosition.latitude,
      widget.currentPosition.longitude,
    );
    final endPoint = LatLng(
      double.parse(widget.faskes.latitude!),
      double.parse(widget.faskes.longitude!),
    );

    try {
      // Try to get route from routing service
      final routeResult = await RoutingService.getRoute(
        start: startPoint,
        end: endPoint,
        profile: _transportMode,
      );

      if (routeResult != null) {
        setState(() {
          _routePoints = routeResult.points;
          _routeResult = routeResult;
          _isCalculatingRoute = false;
        });
        _fitRouteBounds();
      } else {
        // Fallback to straight line if routing service fails
        final fallbackRoute = RoutingService.getStraightLineRoute(
          start: startPoint,
          end: endPoint,
          profile: _transportMode,
        );

        setState(() {
          _routePoints = fallbackRoute.points;
          _routeResult = fallbackRoute;
          _isCalculatingRoute = false;
        });
        _fitRouteBounds();
      }
    } catch (e) {
      // Fallback to straight line on error
      final fallbackRoute = RoutingService.getStraightLineRoute(
        start: startPoint,
        end: endPoint,
        profile: _transportMode,
      );

      setState(() {
        _routePoints = fallbackRoute.points;
        _routeResult = fallbackRoute;
        _isCalculatingRoute = false;
      });
      _fitRouteBounds();
    }
  }

  void _fitRouteBounds() {
    if (_routePoints.isNotEmpty) {
      final bounds = LatLngBounds.fromPoints(_routePoints);
      _mapController.fitCamera(
        CameraFit.bounds(bounds: bounds, padding: const EdgeInsets.all(100)),
      );
    }
  }

  String _getTransportModeText(String mode) {
    switch (mode) {
      case 'driving-car':
        return 'Mobil';
      case 'walking':
        return 'Jalan Kaki';
      case 'cycling-regular':
        return 'Sepeda';
      default:
        return 'Mobil';
    }
  }

  void _showTransportModeDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Pilih Mode Transportasi'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            _buildTransportModeOption(
              'driving-car',
              'Mobil',
              Icons.directions_car,
            ),
            _buildTransportModeOption(
              'walking',
              'Jalan Kaki',
              Icons.directions_walk,
            ),
            _buildTransportModeOption(
              'cycling-regular',
              'Sepeda',
              Icons.directions_bike,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTransportModeOption(String mode, String text, IconData icon) {
    return ListTile(
      leading: Icon(icon),
      title: Text(text),
      trailing: _transportMode == mode
          ? const Icon(Icons.check, color: Colors.blue)
          : null,
      onTap: () {
        setState(() {
          _transportMode = mode;
        });
        Navigator.pop(context);
        _calculateRoute();
      },
    );
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
            onPressed: _calculateRoute,
            tooltip: 'Refresh Rute',
          ),
        ],
      ),
      body: Stack(
        children: [
          FlutterMap(
            mapController: _mapController,
            options: MapOptions(
              initialCenter: LatLng(
                widget.currentPosition.latitude,
                widget.currentPosition.longitude,
              ),
              initialZoom: 13.0,
              minZoom: 3.0,
              maxZoom: 18.0,
            ),
            children: [
              TileLayer(
                urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                userAgentPackageName: 'com.example.faskes',
              ),
              // Route polyline
              if (_routePoints.isNotEmpty)
                PolylineLayer(
                  polylines: [
                    Polyline(
                      points: _routePoints,
                      strokeWidth: 5.0,
                      color: Colors.blue[600]!,
                    ),
                  ],
                ),
              MarkerLayer(
                markers: [
                  // Current location marker
                  Marker(
                    point: LatLng(
                      widget.currentPosition.latitude,
                      widget.currentPosition.longitude,
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
                  // Destination marker
                  Marker(
                    point: LatLng(
                      double.parse(widget.faskes.latitude!),
                      double.parse(widget.faskes.longitude!),
                    ),
                    width: 40,
                    height: 40,
                    child: Container(
                      decoration: BoxDecoration(
                        color: _getMarkerColor(widget.faskes.type),
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
                          _getTypeIcon(widget.faskes.type),
                          style: const TextStyle(
                            fontSize: 16,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
          // Route information panel
          Positioned(
            top: 16,
            left: 16,
            right: 16,
            child: Column(
              children: [
                // Faskes info
                Container(
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
                  child: Row(
                    children: [
                      Text(
                        widget.faskes.typeIcon,
                        style: const TextStyle(fontSize: 24),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              widget.faskes.nama,
                              style: const TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 16,
                              ),
                            ),
                            Text(
                              widget.faskes.type,
                              style: TextStyle(
                                color: _getMarkerColor(widget.faskes.type),
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                            Text(
                              widget.faskes.alamat,
                              style: const TextStyle(fontSize: 12),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                // Route info
                if (_isCalculatingRoute)
                  Container(
                    margin: const EdgeInsets.only(top: 8),
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.orange[50],
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: Colors.orange[200]!),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.1),
                          blurRadius: 4,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Row(
                      children: [
                        const SizedBox(
                          width: 16,
                          height: 16,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            valueColor: AlwaysStoppedAnimation<Color>(
                              Colors.orange,
                            ),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Text(
                          'Menghitung rute terbaik...',
                          style: TextStyle(
                            fontWeight: FontWeight.w500,
                            color: Colors.orange[800],
                          ),
                        ),
                      ],
                    ),
                  ),
                if (_routeResult != null && !_isCalculatingRoute)
                  Container(
                    margin: const EdgeInsets.only(top: 8),
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.blue[50],
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: Colors.blue[200]!),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.1),
                          blurRadius: 4,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Column(
                      children: [
                        Row(
                          children: [
                            Icon(Icons.directions, color: Colors.blue[600]),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                'Rute ke ${widget.faskes.nama}',
                                style: TextStyle(
                                  fontWeight: FontWeight.bold,
                                  color: Colors.blue[800],
                                ),
                              ),
                            ),
                            GestureDetector(
                              onTap: _showTransportModeDialog,
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 6,
                                  vertical: 2,
                                ),
                                decoration: BoxDecoration(
                                  color: Colors.blue[100],
                                  borderRadius: BorderRadius.circular(4),
                                ),
                                child: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Text(
                                      _getTransportModeText(_transportMode),
                                      style: TextStyle(
                                        fontSize: 10,
                                        color: Colors.blue[700],
                                        fontWeight: FontWeight.w500,
                                      ),
                                    ),
                                    const SizedBox(width: 2),
                                    Icon(
                                      Icons.keyboard_arrow_down,
                                      size: 12,
                                      color: Colors.blue[700],
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Icon(
                              Icons.route,
                              color: Colors.blue[600],
                              size: 16,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              _routeResult!.distanceText,
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.blue[600],
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                            const SizedBox(width: 16),
                            Icon(
                              Icons.access_time,
                              color: Colors.blue[600],
                              size: 16,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              _routeResult!.durationText,
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.blue[600],
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                            const Spacer(),
                            Text(
                              'Mengikuti jalan yang tersedia',
                              style: TextStyle(
                                fontSize: 10,
                                color: Colors.blue[600],
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
              ],
            ),
          ),
          // Action buttons
          Positioned(
            bottom: 16,
            right: 16,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                FloatingActionButton(
                  onPressed: _fitRouteBounds,
                  backgroundColor: Colors.cyan[600],
                  child: const Icon(Icons.fit_screen, color: Colors.white),
                  tooltip: 'Tampilkan Rute',
                ),
                const SizedBox(height: 8),
                FloatingActionButton(
                  onPressed: _calculateRoute,
                  backgroundColor: Colors.blue[600],
                  child: const Icon(Icons.refresh, color: Colors.white),
                  tooltip: 'Refresh Rute',
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
