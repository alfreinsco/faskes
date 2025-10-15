# FASKES Mobile App

Aplikasi mobile Flutter untuk menampilkan data fasilitas kesehatan (Faskes) yang terintegrasi dengan API Laravel.

## Features

### 🏥 **Home Screen**
- **Statistik Dashboard**: Menampilkan total faskes, faskes aktif, dan statistik per tipe
- **Search & Filter**: Pencarian berdasarkan nama/alamat dan filter berdasarkan tipe dan status
- **Faskes List**: Daftar faskes dengan card design yang menarik
- **Pull to Refresh**: Refresh data dengan gesture pull down
- **Infinite Scroll**: Load more data secara otomatis saat scroll

### 🗺️ **Map Screen**
- **Interactive Map**: Peta interaktif menggunakan Flutter Map
- **Custom Markers**: Marker dengan icon dan warna berbeda untuk setiap tipe faskes
- **Popup Info**: Informasi detail faskes saat tap marker
- **Auto Fit Bounds**: Tombol untuk menampilkan semua marker
- **Real-time Data**: Data faskes real-time dari API

### 📱 **Detail Screen**
- **Complete Information**: Informasi lengkap faskes
- **Contact Actions**: Tap untuk call, email, dan buka website
- **Layanan Display**: Daftar layanan dengan chip design
- **Location Info**: Koordinat latitude dan longitude
- **Status Indicator**: Indikator status aktif/tidak aktif

## Tech Stack

- **Flutter**: 3.9.2+
- **State Management**: Provider
- **HTTP Client**: http package
- **Maps**: flutter_map + latlong2
- **Caching**: cached_network_image
- **URL Launcher**: url_launcher
- **Date Formatting**: intl

## Project Structure

```
lib/
├── main.dart                 # Entry point
├── models/
│   └── faskes.dart          # Faskes data model
├── services/
│   └── api_service.dart     # API service layer
├── providers/
│   └── faskes_provider.dart # State management
├── screens/
│   ├── home_screen.dart     # Home screen
│   ├── map_screen.dart      # Map screen
│   └── faskes_detail_screen.dart # Detail screen
└── widgets/
    ├── faskes_card.dart     # Faskes card widget
    ├── search_bar_widget.dart # Search bar widget
    ├── filter_chips.dart    # Filter chips widget
    └── stats_card.dart      # Stats card widget
```

## API Integration

Aplikasi terintegrasi dengan API Laravel yang menyediakan endpoint:

- `GET /api/faskes` - Mendapatkan data faskes dengan pagination
- Query parameters:
  - `search` - Pencarian berdasarkan nama/alamat
  - `type` - Filter berdasarkan tipe (Rumah Sakit, Puskesmas, Apotek)
  - `status` - Filter berdasarkan status (aktif/tidak aktif)
  - `page` - Halaman untuk pagination

## Setup & Installation

1. **Install Dependencies**
   ```bash
   flutter pub get
   ```

2. **Configure API URL**
   Edit `lib/services/api_service.dart` dan sesuaikan `baseUrl` dengan server Laravel Anda:
   ```dart
   static const String baseUrl = 'http://your-server.com/api';
   ```

3. **Run the App**
   ```bash
   flutter run
   ```

## Features Detail

### 🎨 **UI/UX Design**
- **Material Design 3**: Menggunakan Material Design 3 terbaru
- **Responsive Layout**: Tampilan optimal di berbagai ukuran layar
- **Color Coding**: Warna berbeda untuk setiap tipe faskes
- **Smooth Animations**: Animasi halus dan transisi yang menarik
- **Loading States**: Loading indicator dan skeleton loading

### 🔍 **Search & Filter**
- **Real-time Search**: Pencarian real-time saat mengetik
- **Multiple Filters**: Filter berdasarkan tipe dan status
- **Clear Filters**: Tombol untuk menghapus semua filter
- **Visual Feedback**: Indikator visual untuk filter aktif

### 📊 **Data Management**
- **State Management**: Menggunakan Provider untuk state management
- **Caching**: Cache image dan data untuk performa optimal
- **Error Handling**: Handling error dengan pesan yang informatif
- **Offline Support**: Dasar untuk support offline (dapat dikembangkan)

### 🗺️ **Map Features**
- **OpenStreetMap**: Menggunakan OpenStreetMap sebagai base layer
- **Custom Markers**: Marker dengan icon dan warna custom
- **Interactive Popups**: Popup dengan informasi lengkap
- **Auto Fit**: Otomatis fit ke semua marker
- **Smooth Navigation**: Navigasi peta yang smooth

## Screenshots

### Home Screen
- Dashboard dengan statistik
- Search bar dan filter chips
- List faskes dengan card design

### Map Screen
- Peta interaktif dengan marker
- Popup informasi faskes
- Tombol kontrol peta

### Detail Screen
- Informasi lengkap faskes
- Action buttons untuk kontak
- Daftar layanan dan lokasi

## Development Notes

### State Management
Aplikasi menggunakan Provider untuk state management dengan struktur:
- `FaskesProvider`: Mengelola data faskes, search, filter, dan pagination
- `ChangeNotifierProvider`: Wrapper untuk provider di main.dart

### API Service
- Centralized API calls di `ApiService`
- Error handling dan response parsing
- Support untuk pagination dan query parameters

### Widget Architecture
- Reusable widgets untuk komponen umum
- Separation of concerns antara UI dan business logic
- Custom widgets untuk fitur spesifik

## Future Enhancements

- [ ] Offline support dengan local database
- [ ] Push notifications
- [ ] Favorites/bookmarks
- [ ] User authentication
- [ ] Reviews and ratings
- [ ] Directions integration
- [ ] Dark mode support
- [ ] Multi-language support

## Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

This project is licensed under the MIT License.