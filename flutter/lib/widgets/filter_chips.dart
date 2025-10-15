import 'package:flutter/material.dart';

class FilterChips extends StatefulWidget {
  final Function(String) onTypeSelected;
  final Function(String) onStatusSelected;
  final VoidCallback onClearFilters;

  const FilterChips({
    super.key,
    required this.onTypeSelected,
    required this.onStatusSelected,
    required this.onClearFilters,
  });

  @override
  State<FilterChips> createState() => _FilterChipsState();
}

class _FilterChipsState extends State<FilterChips> {
  String? _selectedType;
  String? _selectedStatus;

  void _selectType(String type) {
    setState(() {
      _selectedType = _selectedType == type ? null : type;
    });
    widget.onTypeSelected(_selectedType ?? '');
  }

  void _selectStatus(String status) {
    setState(() {
      _selectedStatus = _selectedStatus == status ? null : status;
    });
    widget.onStatusSelected(_selectedStatus ?? '');
  }

  void _clearFilters() {
    setState(() {
      _selectedType = null;
      _selectedStatus = null;
    });
    widget.onClearFilters();
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Filter header
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'TIPE FASKES',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: Colors.black87,
              ),
            ),
            if (_selectedType != null || _selectedStatus != null)
              TextButton(
                onPressed: _clearFilters,
                style: TextButton.styleFrom(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 4,
                  ),
                  minimumSize: Size.zero,
                  tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                ),
                child: Text(
                  'Hapus Filter',
                  style: TextStyle(
                    color: Colors.cyan[600],
                    fontSize: 12,
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ),
          ],
        ),

        const SizedBox(height: 8),
        const SizedBox(height: 8),
        Wrap(
          spacing: 8,
          runSpacing: 8,
          children: [
            _buildTypeChip('Rumah Sakit', '🏥', Colors.red),
            _buildTypeChip('Puskesmas', '🏥', Colors.blue),
            _buildTypeChip('Apotek', '💊', Colors.green),
          ],
        ),

        const SizedBox(height: 16),

        // Status filters
        // const Text(
        //   'Status',
        //   style: TextStyle(
        //     fontSize: 14,
        //     fontWeight: FontWeight.w500,
        //     color: Colors.black87,
        //   ),
        // ),
        // const SizedBox(height: 8),
        // Wrap(
        //   spacing: 8,
        //   runSpacing: 8,
        //   children: [
        //     _buildStatusChip('Aktif', Icons.check_circle, Colors.green),
        //     _buildStatusChip('Tidak Aktif', Icons.cancel, Colors.red),
        //   ],
        // ),
      ],
    );
  }

  Widget _buildTypeChip(String type, String icon, Color color) {
    final isSelected = _selectedType == type;

    return GestureDetector(
      onTap: () => _selectType(type),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? color.withOpacity(0.1) : Colors.grey[100],
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: isSelected ? color : Colors.grey[300]!,
            width: isSelected ? 2 : 1,
          ),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(icon, style: const TextStyle(fontSize: 16)),
            const SizedBox(width: 6),
            Text(
              type,
              style: TextStyle(
                color: isSelected ? color : Colors.grey[700],
                fontWeight: isSelected ? FontWeight.w600 : FontWeight.w500,
                fontSize: 12,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatusChip(String status, IconData icon, Color color) {
    final isSelected = _selectedStatus == status;

    return GestureDetector(
      onTap: () => _selectStatus(status),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? color.withOpacity(0.1) : Colors.grey[100],
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: isSelected ? color : Colors.grey[300]!,
            width: isSelected ? 2 : 1,
          ),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 16, color: isSelected ? color : Colors.grey[600]),
            const SizedBox(width: 6),
            Text(
              status,
              style: TextStyle(
                color: isSelected ? color : Colors.grey[700],
                fontWeight: isSelected ? FontWeight.w600 : FontWeight.w500,
                fontSize: 12,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
