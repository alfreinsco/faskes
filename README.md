# FASKES - Sistem Informasi Fasilitas Kesehatan

Sistem informasi terintegrasi untuk mengelola dan menampilkan data fasilitas kesehatan (Rumah Sakit, Puskesmas, dan Apotek) dengan fitur pencarian, pemetaan, dan manajemen data melalui web admin dan aplikasi mobile.

## 📋 Daftar Isi

- [Deskripsi](#deskripsi)
- [Fitur Utama](#fitur-utama)
- [Arsitektur Sistem](#arsitektur-sistem)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Proyek](#struktur-proyek)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Penggunaan](#penggunaan)
- [Development](#development)
- [API Documentation](#api-documentation)
- [Contributing](#contributing)
- [License](#license)

## 🎯 Deskripsi

FASKES adalah sistem informasi yang menyediakan:

- **Web Admin Panel**: Interface untuk mengelola data fasilitas kesehatan dengan fitur CRUD lengkap, manajemen user, dan role-based access control
- **Mobile App**: Aplikasi Flutter untuk mencari dan melihat fasilitas kesehatan terdekat dengan peta interaktif dan navigasi

Sistem ini dirancang untuk membantu pengguna menemukan fasilitas kesehatan terdekat dengan mudah melalui aplikasi mobile, sementara admin dapat mengelola data melalui web panel.

## 🚀 Fitur Utama

### Web Admin Panel (Laravel)

- ✅ **Manajemen Fasilitas Kesehatan**
  - CRUD untuk Rumah Sakit, Puskesmas, dan Apotek
  - Upload gambar fasilitas
  - Manajemen layanan dan informasi kontak
  - Status aktif/non-aktif

- ✅ **Dashboard & Statistik**
  - Statistik total faskes per tipe
  - Data visualisasi
  - Quick access ke modul utama

- ✅ **Manajemen User & Role**
  - Role-based access control (RBAC)
  - Permission management
  - User management dengan Spatie Laravel Permission

- ✅ **Peta Interaktif**
  - Peta dengan Leaflet.js
  - Marker untuk setiap fasilitas
  - Routing machine untuk navigasi

- ✅ **Download APK**
  - Halaman untuk download aplikasi mobile
  - List semua versi APK yang tersedia

### Mobile App (Flutter)

- ✅ **Home Screen**
  - Dashboard dengan statistik faskes
  - Search dan filter berdasarkan tipe/status
  - List faskes dengan card design
  - Pull to refresh dan infinite scroll

- ✅ **Map Screen**
  - Peta interaktif dengan Flutter Map
  - Custom markers per tipe faskes
  - Popup informasi detail
  - Auto fit bounds untuk semua marker
  - Real-time location tracking

- ✅ **Detail Screen**
  - Informasi lengkap faskes
  - Action buttons (call, email, website)
  - Daftar layanan
  - Koordinat lokasi

## 🏗️ Arsitektur Sistem

```
┌─────────────────┐
│   Mobile App    │  Flutter (iOS/Android)
│   (Flutter)     │
└────────┬────────┘
         │
         │ HTTP/REST API
         │
┌────────▼────────┐
│  Backend API    │  Laravel 12
│   (Laravel)     │
└────────┬────────┘
         │
         │
┌────────▼────────┐
│    Database     │  MySQL
│   (MySQL)       │
└─────────────────┘
```

## 🛠️ Teknologi yang Digunakan

### Backend (Laravel)

- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum
- **Permission**: Spatie Laravel Permission
- **Frontend Assets**: Tailwind CSS, DaisyUI, Vite
- **Maps**: Leaflet.js, Leaflet Routing Machine
- **Icons**: FontAwesome

### Mobile App (Flutter)

- **Framework**: Flutter 3.9.2+
- **Language**: Dart 3.9.2+
- **State Management**: Provider
- **HTTP Client**: http package
- **Maps**: flutter_map + latlong2
- **Location**: geolocator
- **Caching**: cached_network_image
- **WebView**: webview_flutter

## 📁 Struktur Proyek

```
faskes/
├── laravel/                 # Backend API & Web Admin
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/ # API & Web Controllers
│   │   ├── Models/          # Eloquent Models
│   │   └── Policies/        # Authorization Policies
│   ├── database/
│   │   ├── migrations/      # Database Migrations
│   │   └── seeders/         # Database Seeders
│   ├── resources/
│   │   └── views/           # Blade Templates
│   ├── routes/
│   │   ├── api.php          # API Routes
│   │   └── web.php          # Web Routes
│   └── public/
│       └── release/         # APK Files Storage
│
├── flutter/                 # Mobile Application
│   ├── lib/
│   │   ├── models/          # Data Models
│   │   ├── services/        # API Services
│   │   ├── providers/       # State Management
│   │   ├── screens/         # App Screens
│   │   └── widgets/         # Reusable Widgets
│   ├── assets/              # Images, Fonts, etc.
│   └── android/ios/         # Platform-specific code
│
└── README.md                # This file
```

## 📦 Instalasi

### Prasyarat

- **PHP**: 8.2 atau lebih tinggi
- **Composer**: Dependency manager untuk PHP
- **Node.js**: 18+ dan NPM
- **MySQL**: 8.0 atau lebih tinggi
- **Flutter**: 3.9.2+ (untuk mobile app)
- **Dart**: 3.9.2+ (untuk mobile app)

### 1. Clone Repository

```bash
git clone https://github.com/alfreinsco/faskes.git
cd faskes
```

### 2. Setup Backend (Laravel)

```bash
cd laravel

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=faskes
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations and seeders
php artisan migrate --seed

# Install Node.js dependencies
npm install

# Build assets (production)
npm run build

# Or for development
npm run dev
```

### 3. Setup Mobile App (Flutter)

```bash
cd flutter

# Install Flutter dependencies
flutter pub get

# Configure API URL
# Edit lib/services/api_service.dart
# Set baseUrl to your Laravel API URL
```

### 4. Run Application

**Backend (Laravel):**
```bash
cd laravel
php artisan serve
# Application will be available at http://localhost:8000
```

**Mobile App (Flutter):**
```bash
cd flutter
flutter run
# Or specify device
flutter run -d <device-id>
```

## ⚙️ Konfigurasi

### Backend Configuration (.env)

```env
APP_NAME=FASKES
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=faskes
DB_USERNAME=root
DB_PASSWORD=

# API Configuration
API_BASE_URL=http://localhost:8000/api
```

### Mobile App Configuration

Edit `flutter/lib/services/api_service.dart`:

```dart
static const String baseUrl = 'http://your-server.com/api';
```

Untuk Android emulator, gunakan `10.0.2.2` untuk localhost:
```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

## 📱 Penggunaan

### Web Admin Panel

1. Akses aplikasi di `http://localhost:8000`
2. Login dengan kredensial default (jika ada)
3. Kelola data fasilitas kesehatan melalui menu:
   - **Rumah Sakit**: Manajemen data rumah sakit
   - **Puskesmas**: Manajemen data puskesmas
   - **Apotek**: Manajemen data apotek
   - **Users**: Manajemen pengguna dan role
   - **Aplikasi Mobile**: Download APK aplikasi

### Mobile App

1. Jalankan aplikasi Flutter
2. Berikan permission untuk akses lokasi
3. Gunakan fitur:
   - **Search**: Cari fasilitas kesehatan
   - **Filter**: Filter berdasarkan tipe dan status
   - **Map**: Lihat fasilitas di peta
   - **Detail**: Lihat informasi lengkap

## 🔧 Development

### Backend Development

```bash
cd laravel

# Run development server with hot reload
composer run dev

# Run tests
php artisan test

# Code formatting
./vendor/bin/pint
```

### Mobile App Development

```bash
cd flutter

# Run in debug mode
flutter run

# Run with hot reload
flutter run --hot

# Build APK
flutter build apk --release

# Build iOS
flutter build ios --release

# Run tests
flutter test
```

### Database Seeding

```bash
cd laravel

# Seed sample data
php artisan db:seed --class=FaskesSeeder

# Fresh migration with seed
php artisan migrate:fresh --seed
```

## 📡 API Documentation

### Endpoints

#### Get All Faskes
```
GET /api/faskes
Query Parameters:
  - search: string (optional)
  - type: string (optional) - Puskesmas, Rumah Sakit, Apotek
  - status: string (optional) - active, inactive
  - page: integer (optional)
```

#### Get Faskes Detail
```
GET /api/faskes/{id}
```

#### Create Faskes (Admin Only)
```
POST /api/faskes
Body: JSON with faskes data
```

#### Update Faskes (Admin Only)
```
PUT /api/faskes/{id}
Body: JSON with updated faskes data
```

#### Delete Faskes (Admin Only)
```
DELETE /api/faskes/{id}
```

### Response Format

```json
{
  "success": true,
  "data": {
    "id": 1,
    "nama": "Rumah Sakit Umum",
    "alamat": "Jl. Contoh No. 123",
    "no_telp": "081234567890",
    "email": "rsu@example.com",
    "website": "https://example.com",
    "gambar": "/images/faskes.jpg",
    "waktu_buka": "08:00",
    "waktu_tutup": "17:00",
    "type": "Rumah Sakit",
    "layanan": ["IGD", "Rawat Inap", "Laboratorium"],
    "latitude": "-5.1477",
    "longitude": "119.4327",
    "is_active": true
  }
}
```

## 🧪 Testing

### Backend Tests

```bash
cd laravel
php artisan test
```

### Mobile App Tests

```bash
cd flutter
flutter test
```

## 📝 Default Login

**Super Admin:**
- Email: `alfreinsco@gmail.com`
- Password: `alfreinsco@gmail.com`

*Ganti password setelah first login untuk keamanan*

## 🗺️ Fitur Peta

### Web Admin
- Peta interaktif dengan Leaflet.js
- Marker untuk setiap fasilitas kesehatan
- Routing machine untuk navigasi
- Popup informasi saat klik marker

### Mobile App
- Peta interaktif dengan Flutter Map
- Custom markers berdasarkan tipe
- Real-time location tracking
- Auto-fit bounds untuk semua marker
- Navigasi ke lokasi

## 📦 Build & Release

### Build Mobile App APK

```bash
cd flutter

# Build release APK
flutter build apk --release

# Build app bundle for Play Store
flutter build appbundle --release

# Build iOS
flutter build ios --release
```

APK akan tersedia di `flutter/build/app/outputs/flutter-apk/app-release.apk`

Copy APK ke `laravel/public/release/` untuk akses download melalui web admin.

## 🐛 Troubleshooting

### Backend Issues

**Permission Error:**
```bash
chmod -R 775 storage bootstrap/cache
```

**Database Connection Error:**
- Periksa konfigurasi database di `.env`
- Pastikan MySQL service running

**Asset Not Loading:**
```bash
npm run build
# atau
npm run dev
```

### Mobile App Issues

**API Connection Error:**
- Periksa URL API di `api_service.dart`
- Pastikan backend server running
- Untuk Android emulator, gunakan `10.0.2.2` bukan `localhost`

**Location Permission:**
- Pastikan permission sudah dikonfigurasi di `Info.plist` (iOS) dan `AndroidManifest.xml` (Android)

**Build Error:**
```bash
flutter clean
flutter pub get
flutter run
```

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👨‍💻 Developer

**Alfreinsco**

- Email: alfreinsco@gmail.com
- GitHub: [@alfreinsco](https://github.com/alfreinsco)

## 🙏 Acknowledgments

- **Laravel Framework** - Backend framework
- **Flutter** - Mobile app framework
- **Spatie Laravel Permission** - Permission management
- **Tailwind CSS & DaisyUI** - UI components
- **Leaflet.js** - Web mapping
- **Flutter Map** - Mobile mapping
- **Provider** - State management

## 📚 Dokumentasi Tambahan

- [Laravel Backend README](laravel/README.md)
- [Flutter Mobile App README](flutter/README.md)

---

**FASKES** - Sistem Informasi Fasilitas Kesehatan | Made with ❤️

