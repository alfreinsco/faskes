import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../models/faskes.dart';

class FaskesDetailScreen extends StatelessWidget {
  final Faskes faskes;

  const FaskesDetailScreen({
    super.key,
    required this.faskes,
  });

  void _launchUrl(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  void _launchPhone(String phone) async {
    final uri = Uri.parse('tel:$phone');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  void _launchEmail(String email) async {
    final uri = Uri.parse('mailto:$email');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: CustomScrollView(
        slivers: [
          // App Bar with Image
          SliverAppBar(
            expandedHeight: 250,
            pinned: true,
            flexibleSpace: FlexibleSpaceBar(
              background: Stack(
                fit: StackFit.expand,
                children: [
                  if (faskes.gambar != null)
                    Image.network(
                      faskes.gambar!,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) {
                        return Container(
                          color: Colors.grey[300],
                          child: const Icon(
                            Icons.local_hospital,
                            size: 80,
                            color: Colors.grey,
                          ),
                        );
                      },
                    )
                  else
                    Container(
                      color: Colors.grey[300],
                      child: const Icon(
                        Icons.local_hospital,
                        size: 80,
                        color: Colors.grey,
                      ),
                    ),
                  // Gradient overlay
                  Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        colors: [
                          Colors.transparent,
                          Colors.black.withOpacity(0.7),
                        ],
                      ),
                    ),
                  ),
                  // Title overlay
                  Positioned(
                    bottom: 16,
                    left: 16,
                    right: 16,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          faskes.nama,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Row(
                          children: [
                            Text(
                              faskes.typeIcon,
                              style: const TextStyle(fontSize: 20),
                            ),
                            const SizedBox(width: 8),
                            Text(
                              faskes.type,
                              style: TextStyle(
                                color: Colors.white.withOpacity(0.9),
                                fontSize: 16,
                                fontWeight: FontWeight.w500,
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
            backgroundColor: Colors.cyan[600],
            leading: IconButton(
              icon: const Icon(Icons.arrow_back, color: Colors.white),
              onPressed: () => Navigator.pop(context),
            ),
            actions: [
              IconButton(
                icon: const Icon(Icons.share, color: Colors.white),
                onPressed: () {
                  // TODO: Implement share functionality
                },
              ),
            ],
          ),

          // Content
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Status Card
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: faskes.isActive ? Colors.green[50] : Colors.red[50],
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: faskes.isActive ? Colors.green[200]! : Colors.red[200]!,
                      ),
                    ),
                    child: Row(
                      children: [
                        Icon(
                          faskes.isActive ? Icons.check_circle : Icons.cancel,
                          color: faskes.isActive ? Colors.green[600] : Colors.red[600],
                        ),
                        const SizedBox(width: 12),
                        Text(
                          faskes.isActive ? 'Faskes Aktif' : 'Faskes Tidak Aktif',
                          style: TextStyle(
                            color: faskes.isActive ? Colors.green[800] : Colors.red[800],
                            fontWeight: FontWeight.w600,
                            fontSize: 16,
                          ),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Alamat
                  _buildInfoSection(
                    icon: Icons.location_on,
                    title: 'Alamat',
                    content: faskes.alamat,
                  ),

                  const SizedBox(height: 16),

                  // Kontak
                  if (faskes.noTelp != null) ...[
                    _buildInfoSection(
                      icon: Icons.phone,
                      title: 'Nomor Telepon',
                      content: faskes.noTelp!,
                      onTap: () => _launchPhone(faskes.noTelp!),
                    ),
                    const SizedBox(height: 16),
                  ],

                  if (faskes.email != null) ...[
                    _buildInfoSection(
                      icon: Icons.email,
                      title: 'Email',
                      content: faskes.email!,
                      onTap: () => _launchEmail(faskes.email!),
                    ),
                    const SizedBox(height: 16),
                  ],

                  if (faskes.website != null) ...[
                    _buildInfoSection(
                      icon: Icons.web,
                      title: 'Website',
                      content: faskes.website!,
                      onTap: () => _launchUrl(faskes.website!),
                    ),
                    const SizedBox(height: 16),
                  ],

                  // Jam Operasional
                  if (faskes.waktuBuka != null && faskes.waktuTutup != null) ...[
                    _buildInfoSection(
                      icon: Icons.access_time,
                      title: 'Jam Operasional',
                      content: '${faskes.waktuBuka} - ${faskes.waktuTutup}',
                    ),
                    const SizedBox(height: 16),
                  ],

                  // Layanan
                  if (faskes.layanan != null && faskes.layanan!.isNotEmpty) ...[
                    const Text(
                      'Layanan',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: Colors.black87,
                      ),
                    ),
                    const SizedBox(height: 12),
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
                            color: Colors.cyan[100],
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: Colors.cyan[300]!),
                          ),
                          child: Text(
                            layanan,
                            style: TextStyle(
                              color: Colors.cyan[800],
                              fontWeight: FontWeight.w500,
                              fontSize: 12,
                            ),
                          ),
                        );
                      }).toList(),
                    ),
                    const SizedBox(height: 24),
                  ],

                  // Koordinat
                  if (faskes.hasValidCoordinates) ...[
                    const Text(
                      'Lokasi',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: Colors.black87,
                      ),
                    ),
                    const SizedBox(height: 12),
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.grey[50],
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: Colors.grey[300]!),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              const Icon(Icons.my_location, size: 20, color: Colors.grey),
                              const SizedBox(width: 8),
                              Text(
                                'Latitude: ${faskes.latitude}',
                                style: const TextStyle(fontSize: 14),
                              ),
                            ],
                          ),
                          const SizedBox(height: 4),
                          Row(
                            children: [
                              const Icon(Icons.my_location, size: 20, color: Colors.grey),
                              const SizedBox(width: 8),
                              Text(
                                'Longitude: ${faskes.longitude}',
                                style: const TextStyle(fontSize: 14),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ],
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoSection({
    required IconData icon,
    required String title,
    required String content,
    VoidCallback? onTap,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(icon, size: 20, color: Colors.grey[600]),
            const SizedBox(width: 8),
            Text(
              title,
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: Colors.black87,
              ),
            ),
          ],
        ),
        const SizedBox(height: 8),
        GestureDetector(
          onTap: onTap,
          child: Container(
            width: double.infinity,
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.grey[50],
              borderRadius: BorderRadius.circular(8),
              border: Border.all(color: Colors.grey[300]!),
            ),
            child: Text(
              content,
              style: TextStyle(
                fontSize: 14,
                color: onTap != null ? Colors.blue[600] : Colors.black87,
              ),
            ),
          ),
        ),
      ],
    );
  }
}
