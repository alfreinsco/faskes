# SMUB - Sistem Manajemen Unit Bisnis

Sistem Manajemen Unit Bisnis (SMUB) adalah aplikasi web yang dikembangkan untuk mengelola unit bisnis di Universitas Pattimura. Aplikasi ini memungkinkan pengelolaan transaksi, inventaris, staf, dan laporan keuangan secara terintegrasi.

## 🚀 Fitur Utama

### 1. **Autentikasi & Otorisasi**

-   Sistem login dengan role-based access control
-   4 level akses: Super Admin, Admin Unit, Staff, Akuntansi
-   Otorisasi berdasarkan unit bisnis yang dikelola

### 2. **Manajemen Unit Bisnis**

-   CRUD unit bisnis dengan kategori: Penyewaan, Pelatihan, Kantin, Percetakan, Layanan Lainnya
-   Penugasan manager untuk setiap unit
-   Status aktif/non-aktif

### 3. **Manajemen Transaksi**

-   Input transaksi per unit bisnis
-   Status: Lunas, DP (Down Payment), Tertunda
-   Upload bukti pembayaran
-   Generate nomor transaksi otomatis

### 4. **Manajemen Keuangan**

-   Pencatatan pendapatan otomatis dari transaksi
-   Input pengeluaran manual dengan kategori
-   Laporan laba/rugi per unit
-   Grafik pendapatan dan pengeluaran

### 5. **Manajemen Inventaris & Aset**

-   Kategori: Aset Fisik dan Stok Barang
-   Tracking status: Baik, Rusak Ringan, Rusak Berat, Tidak Berfungsi
-   Catatan maintenance dan tanggal terakhir maintenance
-   Generate kode inventaris otomatis

### 6. **Manajemen SDM**

-   Daftar staf per unit bisnis
-   Informasi posisi, kontak, dan tanggal bergabung
-   Status aktif/non-aktif

### 7. **Dashboard & Laporan**

-   Dashboard dengan statistik real-time
-   Grafik pendapatan dan pengeluaran 6 bulan terakhir
-   Transaksi terbaru
-   Statistik per unit bisnis
-   Export laporan ke PDF/Excel (coming soon)

## 🛠️ Teknologi yang Digunakan

### Backend

-   **Laravel 11** - PHP Framework
-   **MySQL** - Database
-   **Spatie Laravel Permission** - Role & Permission Management

### Frontend

-   **Tailwind CSS** - CSS Framework
-   **DaisyUI** - UI Component Library
-   **FontAwesome** - Icons
-   **SweetAlert2** - Notifications & Confirmations
-   **Chart.js** - Data Visualization

### Development Tools

-   **Vite** - Asset Bundling
-   **Laravel Mix** - Frontend Build Tool

## 📋 Persyaratan Sistem

-   PHP 8.2+
-   MySQL 8.0+
-   Node.js 18+
-   Composer
-   NPM/Yarn

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd smub
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smub
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migration & Seeder

```bash
php artisan migrate:fresh --seed
```

### 6. Build Assets

```bash
npm run build
# atau untuk development
npm run dev
```

### 7. Jalankan Server

```bash
php artisan serve
```

Aplikasi akan tersedia di `http://localhost:8000`

## 👥 Default Login

**Super Admin:**

-   Email: `alfreinsco@gmail.com`
-   Password: `alfreinsco@gmail.com`

## 🎨 Palet Warna

Aplikasi menggunakan palet warna yang konsisten:

-   **Primary**: `#0891b2` (Cyan-600)
-   **Secondary**: `#64748b` (Slate-500)
-   **Accent**: `#0e7490` (Cyan-700)
-   **Neutral**: `#1e293b` (Slate-800)

## 📁 Struktur Database

### Tabel Utama

-   `users` - Data pengguna
-   `business_units` - Unit bisnis
-   `transactions` - Transaksi
-   `inventories` - Inventaris & aset
-   `staff` - Data staf

### Tabel Spatie Permission

-   `roles` - Role pengguna
-   `permissions` - Permission
-   `model_has_roles` - Relasi user-role
-   `role_has_permissions` - Relasi role-permission

## 🔐 Sistem Otorisasi

### Roles

1. **Super Admin**: Akses penuh ke semua fitur
2. **Admin Unit**: Mengelola unit yang ditugaskan
3. **Staff**: Input transaksi dan inventaris
4. **Akuntansi**: View dan edit data keuangan

### Permissions

-   `view-*`, `create-*`, `edit-*`, `delete-*` untuk setiap modul
-   `view dashboard`, `view-reports`, `export-reports`

## 📊 Fitur Dashboard

### Statistik Cards

-   Total Pendapatan Bulan Ini
-   Total Pengeluaran Bulan Ini
-   Laba Bersih Bulan Ini
-   Jumlah Aset Aktif

### Grafik

-   Grafik Pendapatan 6 Bulan Terakhir (Line Chart)
-   Grafik Pengeluaran 6 Bulan Terakhir (Bar Chart)

### Tabel

-   Transaksi Terbaru (5 transaksi terakhir)
-   Statistik Per Unit Bisnis

## 🚀 Deployment

### Production Setup

1. Set `APP_ENV=production` di `.env`
2. Set `APP_DEBUG=false` di `.env`
3. Jalankan `php artisan config:cache`
4. Jalankan `php artisan route:cache`
5. Jalankan `php artisan view:cache`
6. Build assets dengan `npm run build`

### Web Server Configuration

-   Pastikan document root mengarah ke folder `public`
-   Konfigurasi URL rewriting untuk Laravel
-   Set proper permissions untuk folder `storage` dan `bootstrap/cache`

## 🐛 Troubleshooting

### Common Issues

1. **Permission Error**: Pastikan folder `storage` dan `bootstrap/cache` writable
2. **Database Connection**: Periksa konfigurasi database di `.env`
3. **Asset Not Loading**: Jalankan `npm run build` atau `npm run dev`
4. **Migration Error**: Pastikan database kosong atau jalankan `php artisan migrate:fresh`

### Log Files

-   Application logs: `storage/logs/laravel.log`
-   Web server logs: Sesuai konfigurasi web server

## 📝 Changelog

### v1.0.0 (2025-01-09)

-   Initial release
-   Core features implementation
-   Dashboard with charts
-   CRUD operations for all modules
-   Role-based access control
-   Responsive design

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

## 👨‍💻 Developer

**Alfreinsco**

-   Email: alfreinsco@gmail.com
-   GitHub: [@alfreinsco](https://github.com/alfreinsco)

## 🙏 Acknowledgments

-   Laravel Framework
-   Spatie Laravel Permission
-   Tailwind CSS & DaisyUI
-   FontAwesome
-   SweetAlert2
-   Chart.js
