import 'package:flutter/foundation.dart';
import '../models/faskes.dart';
import '../services/api_service.dart';

class FaskesProvider with ChangeNotifier {
  List<Faskes> _faskes = [];
  List<Faskes> _filteredFaskes = [];
  bool _isLoading = false;
  String _error = '';
  String _searchQuery = '';
  String _selectedType = '';
  String _selectedStatus = '';
  int _currentPage = 1;
  bool _hasMore = true;

  List<Faskes> get faskes => _filteredFaskes;
  bool get isLoading => _isLoading;
  String get error => _error;
  String get searchQuery => _searchQuery;
  String get selectedType => _selectedType;
  String get selectedStatus => _selectedStatus;
  bool get hasMore => _hasMore;

  List<String> get availableTypes => ['Rumah Sakit', 'Puskesmas', 'Apotek'];
  List<String> get availableStatuses => ['Aktif', 'Tidak Aktif'];

  Future<void> loadFaskes({bool refresh = false}) async {
    if (refresh) {
      _currentPage = 1;
      _hasMore = true;
      _faskes.clear();
      _filteredFaskes.clear();
    }

    if (!_hasMore && !refresh) return;

    _isLoading = true;
    _error = '';
    notifyListeners();

    try {
      final result = await ApiService.getFaskes(
        search: _searchQuery.isNotEmpty ? _searchQuery : null,
        type: _selectedType.isNotEmpty ? _selectedType : null,
        status: _selectedStatus.isNotEmpty ? _selectedStatus : null,
        page: _currentPage,
      );

      if (result['success']) {
        final data = result['data'];
        final List<dynamic> faskesData = data['data'] ?? [];

        final newFaskes = faskesData
            .map((json) => Faskes.fromJson(json))
            .toList();

        if (refresh) {
          _faskes = newFaskes;
        } else {
          _faskes.addAll(newFaskes);
        }

        _filteredFaskes = List.from(_faskes);

        // Check if there are more pages
        _hasMore = data['next_page_url'] != null;
        _currentPage++;

        _error = '';
      } else {
        _error = result['message'] ?? 'Gagal memuat data';
      }
    } catch (e) {
      _error = 'Error: $e';
    }

    _isLoading = false;
    notifyListeners();
  }

  void searchFaskes(String query) {
    _searchQuery = query;
    _applyFilters();
  }

  void filterByType(String type) {
    _selectedType = type;
    _applyFilters();
  }

  void filterByStatus(String status) {
    _selectedStatus = status;
    _applyFilters();
  }

  void clearFilters() {
    _searchQuery = '';
    _selectedType = '';
    _selectedStatus = '';
    _applyFilters();
  }

  void _applyFilters() {
    _filteredFaskes = _faskes.where((faskes) {
      bool matchesSearch =
          _searchQuery.isEmpty ||
          faskes.nama.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          faskes.alamat.toLowerCase().contains(_searchQuery.toLowerCase());

      bool matchesType = _selectedType.isEmpty || faskes.type == _selectedType;

      bool matchesStatus =
          _selectedStatus.isEmpty ||
          (_selectedStatus == 'Aktif' && faskes.isActive) ||
          (_selectedStatus == 'Tidak Aktif' && !faskes.isActive);

      return matchesSearch && matchesType && matchesStatus;
    }).toList();

    notifyListeners();
  }

  List<Faskes> getFaskesByType(String type) {
    return _faskes.where((faskes) => faskes.type == type).toList();
  }

  List<Faskes> getFaskesWithCoordinates() {
    return _faskes.where((faskes) => faskes.hasValidCoordinates).toList();
  }

  Map<String, int> getFaskesStats() {
    final stats = <String, int>{};
    for (final faskes in _faskes) {
      stats[faskes.type] = (stats[faskes.type] ?? 0) + 1;
    }
    return stats;
  }

  int getActiveFaskesCount() {
    return _faskes.where((faskes) => faskes.isActive).length;
  }

  int getInactiveFaskesCount() {
    return _faskes.where((faskes) => !faskes.isActive).length;
  }
}
