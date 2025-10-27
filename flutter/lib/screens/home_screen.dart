import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/faskes_provider.dart';
import '../widgets/faskes_card.dart';
import '../widgets/search_bar_widget.dart';
import '../widgets/filter_chips.dart';
import '../widgets/stats_card.dart';
import 'webview_maps_screen.dart';
import 'flutter_map_screen.dart';
import 'google_maps_advanced_screen.dart';
import 'faskes_detail_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<FaskesProvider>().loadFaskes(refresh: true);
    });

    _scrollController.addListener(_onScroll);
  }

  void _onScroll() {
    if (_scrollController.position.pixels >=
        _scrollController.position.maxScrollExtent * 0.8) {
      context.read<FaskesProvider>().loadFaskes();
    }
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: const Text(
          'FASKES',
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white),
        ),
        backgroundColor: Colors.cyan[600],
        elevation: 0,
        actions: [
          PopupMenuButton<String>(
            icon: const Icon(Icons.map, color: Colors.white),
            onSelected: (String choice) {
              if (choice == 'webview') {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const WebViewMapsScreen(),
                  ),
                );
              } else if (choice == 'flutter_map') {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const FlutterMapScreen(),
                  ),
                );
              } else if (choice == 'google_maps') {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const GoogleMapsAdvancedScreen(),
                  ),
                );
              }
            },
            itemBuilder: (BuildContext context) => [
              const PopupMenuItem<String>(
                value: 'google_maps',
                child: Row(
                  children: [
                    Icon(Icons.map, color: Colors.red),
                    SizedBox(width: 8),
                    Text('Google Maps'),
                  ],
                ),
              ),
              const PopupMenuItem<String>(
                value: 'flutter_map',
                child: Row(
                  children: [
                    Icon(Icons.map, color: Colors.blue),
                    SizedBox(width: 8),
                    Text('Flutter Map (Stabil)'),
                  ],
                ),
              ),
              const PopupMenuItem<String>(
                value: 'webview',
                child: Row(
                  children: [
                    Icon(Icons.web, color: Colors.orange),
                    SizedBox(width: 8),
                    Text('WebView (Laravel)'),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
      body: Consumer<FaskesProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading && provider.faskes.isEmpty) {
            return const Center(
              child: CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(Colors.cyan),
              ),
            );
          }

          if (provider.error.isNotEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.error_outline, size: 64, color: Colors.grey[400]),
                  const SizedBox(height: 16),
                  Text(
                    'Terjadi kesalahan',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w500,
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    provider.error,
                    textAlign: TextAlign.center,
                    style: TextStyle(color: Colors.grey[500]),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () {
                      provider.loadFaskes(refresh: true);
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.cyan[600],
                      foregroundColor: Colors.white,
                    ),
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: () => provider.loadFaskes(refresh: true),
            child: CustomScrollView(
              controller: _scrollController,
              slivers: [
                // Stats Section
                SliverToBoxAdapter(
                  child: Container(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Statistik Faskes',
                          style: TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                            color: Colors.black87,
                          ),
                        ),
                        const SizedBox(height: 16),
                        Row(
                          children: [
                            Expanded(
                              child: StatsCard(
                                title: 'Total Faskes',
                                value: provider.faskes.length.toString(),
                                icon: Icons.local_hospital,
                                color: Colors.blue,
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: StatsCard(
                                title: 'Aktif',
                                value: provider
                                    .getActiveFaskesCount()
                                    .toString(),
                                icon: Icons.check_circle,
                                color: Colors.green,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        Row(
                          children: [
                            Expanded(
                              child: StatsCard(
                                title: 'Rumah Sakit',
                                value: provider
                                    .getFaskesByType('Rumah Sakit')
                                    .length
                                    .toString(),
                                icon: Icons.local_hospital,
                                color: Colors.red,
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: StatsCard(
                                title: 'Puskesmas',
                                value: provider
                                    .getFaskesByType('Puskesmas')
                                    .length
                                    .toString(),
                                icon: Icons.medical_services,
                                color: Colors.blue,
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: StatsCard(
                                title: 'Apotek',
                                value: provider
                                    .getFaskesByType('Apotek')
                                    .length
                                    .toString(),
                                icon: Icons.medication,
                                color: Colors.green,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),

                // Search and Filter Section
                SliverToBoxAdapter(
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: Column(
                      children: [
                        SearchBarWidget(
                          onChanged: provider.searchFaskes,
                          hintText: 'Cari faskes...',
                        ),
                        const SizedBox(height: 12),
                        FilterChips(
                          onTypeSelected: provider.filterByType,
                          onStatusSelected: provider.filterByStatus,
                          onClearFilters: provider.clearFilters,
                        ),
                      ],
                    ),
                  ),
                ),

                // Faskes List
                SliverPadding(
                  padding: const EdgeInsets.all(16),
                  sliver: SliverList(
                    delegate: SliverChildBuilderDelegate(
                      (context, index) {
                        if (index < provider.faskes.length) {
                          final faskes = provider.faskes[index];
                          return Padding(
                            padding: const EdgeInsets.only(bottom: 12),
                            child: FaskesCard(
                              faskes: faskes,
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) =>
                                        FaskesDetailScreen(faskes: faskes),
                                  ),
                                );
                              },
                            ),
                          );
                        } else if (provider.isLoading) {
                          return const Padding(
                            padding: EdgeInsets.all(16),
                            child: Center(
                              child: CircularProgressIndicator(
                                valueColor: AlwaysStoppedAnimation<Color>(
                                  Colors.cyan,
                                ),
                              ),
                            ),
                          );
                        } else {
                          return const SizedBox.shrink();
                        }
                      },
                      childCount:
                          provider.faskes.length + (provider.isLoading ? 1 : 0),
                    ),
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
