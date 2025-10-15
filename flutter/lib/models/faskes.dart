class Faskes {
  final int id;
  final String nama;
  final String alamat;
  final String? noTelp;
  final String? email;
  final String? website;
  final String? gambar;
  final String? waktuBuka;
  final String? waktuTutup;
  final String type;
  final List<String>? layanan;
  final String? latitude;
  final String? longitude;
  final bool isActive;
  final DateTime createdAt;
  final DateTime updatedAt;

  Faskes({
    required this.id,
    required this.nama,
    required this.alamat,
    this.noTelp,
    this.email,
    this.website,
    this.gambar,
    this.waktuBuka,
    this.waktuTutup,
    required this.type,
    this.layanan,
    this.latitude,
    this.longitude,
    required this.isActive,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Faskes.fromJson(Map<String, dynamic> json) {
    return Faskes(
      id: json['id'],
      nama: json['nama'],
      alamat: json['alamat'],
      noTelp: json['no_telp'],
      email: json['email'],
      website: json['website'],
      gambar: json['gambar'],
      waktuBuka: json['waktu_buka'],
      waktuTutup: json['waktu_tutup'],
      type: json['type'],
      layanan: json['layanan'] != null ? List<String>.from(json['layanan']) : null,
      latitude: json['latitude'],
      longitude: json['longitude'],
      isActive: json['is_active'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama': nama,
      'alamat': alamat,
      'no_telp': noTelp,
      'email': email,
      'website': website,
      'gambar': gambar,
      'waktu_buka': waktuBuka,
      'waktu_tutup': waktuTutup,
      'type': type,
      'layanan': layanan,
      'latitude': latitude,
      'longitude': longitude,
      'is_active': isActive,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }

  String get typeIcon {
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

  String get typeColor {
    switch (type) {
      case 'Rumah Sakit':
        return '#EF4444';
      case 'Puskesmas':
        return '#3B82F6';
      case 'Apotek':
        return '#10B981';
      default:
        return '#6B7280';
    }
  }

  bool get hasValidCoordinates {
    if (latitude == null || longitude == null) return false;
    final lat = double.tryParse(latitude!);
    final lng = double.tryParse(longitude!);
    return lat != null && lng != null && lat != 0 && lng != 0;
  }
}
