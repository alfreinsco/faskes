import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';

class LocationPermissionWidget extends StatefulWidget {
  final Widget child;

  const LocationPermissionWidget({super.key, required this.child});

  @override
  State<LocationPermissionWidget> createState() =>
      _LocationPermissionWidgetState();
}

class _LocationPermissionWidgetState extends State<LocationPermissionWidget> {
  bool _isCheckingPermission = true;
  bool _hasPermission = false;
  String _permissionStatus = '';

  @override
  void initState() {
    super.initState();
    _checkLocationPermission();
  }

  Future<void> _checkLocationPermission() async {
    try {
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        setState(() {
          _isCheckingPermission = false;
          _hasPermission = false;
          _permissionStatus = 'Layanan lokasi tidak aktif';
        });
        return;
      }

      LocationPermission permission = await Geolocator.checkPermission();
      setState(() {
        _isCheckingPermission = false;
        _hasPermission =
            permission == LocationPermission.whileInUse ||
            permission == LocationPermission.always;
        _permissionStatus = _hasPermission
            ? 'Izin lokasi diberikan'
            : 'Izin lokasi diperlukan';
      });
    } catch (e) {
      setState(() {
        _isCheckingPermission = false;
        _hasPermission = false;
        _permissionStatus = 'Error: $e';
      });
    }
  }

  Future<void> _requestLocationPermission() async {
    try {
      LocationPermission permission = await Geolocator.requestPermission();
      setState(() {
        _hasPermission =
            permission == LocationPermission.whileInUse ||
            permission == LocationPermission.always;
        _permissionStatus = _hasPermission
            ? 'Izin lokasi diberikan'
            : 'Izin lokasi ditolak';
      });
    } catch (e) {
      setState(() {
        _permissionStatus = 'Error: $e';
      });
    }
  }

  Future<void> _openAppSettings() async {
    await Geolocator.openAppSettings();
  }

  @override
  Widget build(BuildContext context) {
    if (_isCheckingPermission) {
      return const Scaffold(
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CircularProgressIndicator(),
              SizedBox(height: 16),
              Text('Memeriksa izin lokasi...'),
            ],
          ),
        ),
      );
    }

    if (_hasPermission) {
      return widget.child;
    }

    return Scaffold(
      body: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.location_on, size: 80, color: Colors.cyan[600]),
            const SizedBox(height: 24),
            Text(
              'Izin Akses Lokasi',
              style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                fontWeight: FontWeight.bold,
                color: Colors.cyan[600],
              ),
            ),
            const SizedBox(height: 16),
            Text(
              'Aplikasi memerlukan akses lokasi untuk:',
              style: Theme.of(context).textTheme.bodyLarge,
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 16),
            const Column(
              children: [
                _PermissionItem(
                  icon: Icons.map,
                  text: 'Menampilkan faskes di peta',
                ),
                _PermissionItem(
                  icon: Icons.directions,
                  text: 'Memberikan rute ke faskes',
                ),
                _PermissionItem(
                  icon: Icons.my_location,
                  text: 'Menampilkan lokasi Anda saat ini',
                ),
              ],
            ),
            const SizedBox(height: 32),
            if (_permissionStatus.contains('ditolak'))
              Column(
                children: [
                  Text(
                    'Izin lokasi ditolak. Silakan aktifkan di pengaturan aplikasi.',
                    style: TextStyle(
                      color: Colors.red[600],
                      fontWeight: FontWeight.w500,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton.icon(
                    onPressed: _openAppSettings,
                    icon: const Icon(Icons.settings),
                    label: const Text('Buka Pengaturan'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.orange[600],
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(
                        horizontal: 24,
                        vertical: 12,
                      ),
                    ),
                  ),
                ],
              )
            else
              ElevatedButton.icon(
                onPressed: _requestLocationPermission,
                icon: const Icon(Icons.location_on),
                label: const Text('Berikan Izin Lokasi'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.cyan[600],
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 24,
                    vertical: 12,
                  ),
                ),
              ),
            const SizedBox(height: 16),
            TextButton(
              onPressed: () {
                setState(() {
                  _hasPermission = true; // Skip permission for now
                });
              },
              child: const Text('Lewati untuk sementara'),
            ),
          ],
        ),
      ),
    );
  }
}

class _PermissionItem extends StatelessWidget {
  final IconData icon;
  final String text;

  const _PermissionItem({required this.icon, required this.text});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        children: [
          Icon(icon, color: Colors.cyan[600], size: 20),
          const SizedBox(width: 12),
          Expanded(
            child: Text(text, style: Theme.of(context).textTheme.bodyMedium),
          ),
        ],
      ),
    );
  }
}
