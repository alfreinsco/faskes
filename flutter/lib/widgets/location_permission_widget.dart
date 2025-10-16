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
  String _errorMessage = '';

  @override
  void initState() {
    super.initState();
    _checkLocationPermission();
  }

  Future<void> _checkLocationPermission() async {
    try {
      // Check if location services are enabled
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        setState(() {
          _isCheckingPermission = false;
          _hasPermission = false;
          _errorMessage =
              'Layanan lokasi tidak aktif. Silakan aktifkan di pengaturan perangkat.';
        });
        return;
      }

      // Check current permission status
      LocationPermission permission = await Geolocator.checkPermission();

      setState(() {
        _isCheckingPermission = false;
        _hasPermission =
            permission == LocationPermission.whileInUse ||
            permission == LocationPermission.always;
        _errorMessage = _hasPermission
            ? ''
            : 'Izin lokasi diperlukan untuk menampilkan peta dan rute.';
      });
    } catch (e) {
      setState(() {
        _isCheckingPermission = false;
        _hasPermission = false;
        _errorMessage = 'Error memeriksa izin lokasi: $e';
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
        _errorMessage = _hasPermission ? '' : 'Izin lokasi ditolak.';
      });

      if (!_hasPermission) {
        // Show dialog to open app settings
        _showSettingsDialog();
      }
    } catch (e) {
      setState(() {
        _errorMessage = 'Error meminta izin lokasi: $e';
      });
    }
  }

  void _showSettingsDialog() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Izin Lokasi Diperlukan'),
          content: const Text(
            'Aplikasi memerlukan akses lokasi untuk menampilkan peta dan rute. '
            'Silakan aktifkan izin lokasi di pengaturan aplikasi.',
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text('Tutup'),
            ),
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
                Geolocator.openAppSettings();
              },
              child: const Text('Buka Pengaturan'),
            ),
          ],
        );
      },
    );
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
      backgroundColor: Colors.grey[50],
      body: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Icon
            Container(
              width: 120,
              height: 120,
              decoration: BoxDecoration(
                color: Colors.blue[50],
                shape: BoxShape.circle,
                border: Border.all(color: Colors.blue[200]!, width: 2),
              ),
              child: Icon(Icons.location_on, size: 60, color: Colors.blue[600]),
            ),

            const SizedBox(height: 32),

            // Title
            Text(
              'Akses Lokasi Diperlukan',
              style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                fontWeight: FontWeight.bold,
                color: Colors.grey[800],
              ),
              textAlign: TextAlign.center,
            ),

            const SizedBox(height: 16),

            // Description
            Text(
              _errorMessage.isNotEmpty
                  ? _errorMessage
                  : 'Aplikasi memerlukan akses lokasi untuk:',
              style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                color: _errorMessage.isNotEmpty
                    ? Colors.red[600]
                    : Colors.grey[600],
              ),
              textAlign: TextAlign.center,
            ),

            const SizedBox(height: 24),

            // Features list
            Column(
              children: [
                _PermissionFeature(
                  icon: Icons.map,
                  title: 'Menampilkan Peta',
                  description:
                      'Menampilkan fasilitas kesehatan di peta interaktif',
                ),
                const SizedBox(height: 16),
                _PermissionFeature(
                  icon: Icons.directions,
                  title: 'Navigasi Rute',
                  description: 'Memberikan rute dari lokasi Anda ke fasilitas',
                ),
                const SizedBox(height: 16),
                _PermissionFeature(
                  icon: Icons.my_location,
                  title: 'Lokasi Real-time',
                  description: 'Menampilkan posisi Anda saat ini di peta',
                ),
              ],
            ),

            const SizedBox(height: 40),

            // Allow button
            SizedBox(
              width: double.infinity,
              height: 56,
              child: ElevatedButton.icon(
                onPressed: _requestLocationPermission,
                icon: const Icon(Icons.location_on, size: 24),
                label: const Text(
                  'Izinkan Akses Lokasi',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.blue[600],
                  foregroundColor: Colors.white,
                  elevation: 2,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
              ),
            ),

            const SizedBox(height: 16),

            // Skip button
            TextButton(
              onPressed: () {
                setState(() {
                  _hasPermission = true;
                });
              },
              child: Text(
                'Lewati untuk sementara',
                style: TextStyle(color: Colors.grey[600], fontSize: 14),
              ),
            ),

            const SizedBox(height: 24),

            // Privacy note
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.blue[50],
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.blue[200]!),
              ),
              child: Row(
                children: [
                  Icon(Icons.info_outline, color: Colors.blue[600], size: 20),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      'Lokasi Anda tidak akan disimpan atau dibagikan dengan pihak ketiga.',
                      style: TextStyle(color: Colors.blue[700], fontSize: 12),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _PermissionFeature extends StatelessWidget {
  final IconData icon;
  final String title;
  final String description;

  const _PermissionFeature({
    required this.icon,
    required this.title,
    required this.description,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Container(
          width: 48,
          height: 48,
          decoration: BoxDecoration(
            color: Colors.blue[100],
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(icon, color: Colors.blue[600], size: 24),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontWeight: FontWeight.w600,
                  fontSize: 16,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                description,
                style: TextStyle(color: Colors.grey[600], fontSize: 14),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
