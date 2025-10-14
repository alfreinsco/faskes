<x-layouts.app title="Dashboard - SMUB" page-title="Dashboard">
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-cyan-600 to-blue-600 rounded-lg shadow-sm p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Selamat Datang di Aplikasi FASKES</h1>
                    <p class="text-cyan-100 mt-2">Sistem Manajemen FASKES - Fasilitas Kesehatan</p>
                </div>
                <div class="text-right">
                    <p class="text-cyan-100">Tanggal</p>
                    <p class="text-2xl font-semibold">{{ date('d M Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Main Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Faskes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-hospital text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Faskes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalFaskes }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Faskes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $activeFaskes }}</p>
                    </div>
                </div>
            </div>

            <!-- Inactive Faskes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-exclamation-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tidak Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $inactiveFaskes }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faskes by Type -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Rumah Sakit -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-hospital-alt mr-2 text-red-600"></i>
                        Rumah Sakit
                    </h3>
                    <span class="text-2xl font-bold text-gray-900">{{ $rumahSakit }}</span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Aktif</span>
                        <span class="font-medium text-green-600">{{ $rumahSakitActive }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tidak Aktif</span>
                        <span class="font-medium text-red-600">{{ $rumahSakit - $rumahSakitActive }}</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('rumah-sakit.index') }}"
                        class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-eye mr-1"></i>
                        Lihat Detail
                    </a>
                </div>
            </div>

            <!-- Puskesmas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-clinic-medical mr-2 text-blue-600"></i>
                        Puskesmas
                    </h3>
                    <span class="text-2xl font-bold text-gray-900">{{ $puskesmas }}</span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Aktif</span>
                        <span class="font-medium text-green-600">{{ $puskesmasActive }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tidak Aktif</span>
                        <span class="font-medium text-red-600">{{ $puskesmas - $puskesmasActive }}</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('puskesmas.index') }}"
                        class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-eye mr-1"></i>
                        Lihat Detail
                    </a>
                </div>
            </div>

            <!-- Apotek -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-pills mr-2 text-green-600"></i>
                        Apotek
                    </h3>
                    <span class="text-2xl font-bold text-gray-900">{{ $apotek }}</span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Aktif</span>
                        <span class="font-medium text-green-600">{{ $apotekActive }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tidak Aktif</span>
                        <span class="font-medium text-red-600">{{ $apotek - $apotekActive }}</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('apotek.index') }}"
                        class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-eye mr-1"></i>
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-map-marked-alt mr-2 text-cyan-600"></i>
                    Peta Lokasi Faskes
                </h3>
                <div class="flex space-x-2">
                    <button onclick="fitBounds()"
                        class="inline-flex items-center px-3 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-expand-arrows-alt mr-1"></i>
                        Tampilkan Semua
                    </button>
                    <button onclick="refreshMap()"
                        class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-sync-alt mr-1"></i>
                        Refresh
                    </button>
                </div>
            </div>
            <div id="dashboardMap" class="w-full h-96 rounded-lg border border-gray-300 relative"
                style="min-height: 400px;">
                <div id="mapLoading"
                    class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center z-10">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cyan-600 mx-auto mb-2"></div>
                        <p class="text-sm text-gray-600">Memuat peta...</p>
                    </div>
                </div>
            </div>
            <div id="mapInfo" class="mt-2 text-sm text-gray-600"></div>
        </div>

        <!-- Charts and Additional Info -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Chart -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-cyan-600"></i>
                    Faskes per Bulan ({{ date('Y') }})
                </h3>
                <div class="h-64">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- Recent Faskes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-clock mr-2 text-cyan-600"></i>
                    Faskes Terbaru
                </h3>
                <div class="space-y-3">
                    @forelse($recentFaskes as $faskes)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                @if ($faskes->gambar)
                                    <img src="{{ Storage::url($faskes->gambar) }}" alt="{{ $faskes->nama }}"
                                        class="h-10 w-10 rounded-full object-cover">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-hospital text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $faskes->nama }}</p>
                                <p class="text-sm text-gray-500">{{ $faskes->type }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $faskes->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $faskes->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada faskes</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Additional Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Faskes with Layanan -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-list mr-2 text-cyan-600"></i>
                    Faskes dengan Layanan
                </h3>
                <div class="text-center">
                    <div class="text-3xl font-bold text-cyan-600 mb-2">{{ $faskesWithLayanan }}</div>
                    <p class="text-gray-600">Faskes yang memiliki layanan</p>
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-cyan-600 h-2 rounded-full"
                                style="width: {{ $totalFaskes > 0 ? ($faskesWithLayanan / $totalFaskes) * 100 : 0 }}%">
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ $totalFaskes > 0 ? round(($faskesWithLayanan / $totalFaskes) * 100, 1) : 0 }}% dari
                            total faskes
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-bolt mr-2 text-cyan-600"></i>
                    Aksi Cepat
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('rumah-sakit.create') }}"
                        class="flex items-center p-3 bg-red-50 hover:bg-red-100 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus-circle text-red-600 mr-3"></i>
                        <span class="text-red-800 font-medium">Tambah Rumah Sakit</span>
                    </a>
                    <a href="{{ route('puskesmas.create') }}"
                        class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus-circle text-blue-600 mr-3"></i>
                        <span class="text-blue-800 font-medium">Tambah Puskesmas</span>
                    </a>
                    <a href="{{ route('apotek.create') }}"
                        class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus-circle text-green-600 mr-3"></i>
                        <span class="text-green-800 font-medium">Tambah Apotek</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <style>
            .custom-marker-icon {
                filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
                transition: transform 0.2s ease;
            }

            .custom-marker-icon:hover {
                transform: scale(1.1);
            }

            .leaflet-popup-content-wrapper {
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .leaflet-popup-tip {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
        </style>
        <script>
            let map;
            let markers = [];
            let faskesData = @json($faskesForMap);

            document.addEventListener('DOMContentLoaded', function() {
                // Initialize map
                initMap();

                // Monthly Chart
                const ctx = document.getElementById('monthlyChart').getContext('2d');
                const monthlyChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
                            'Des'
                        ],
                        datasets: [{
                            label: 'Faskes',
                            data: [
                                {{ $monthlyData[1] }}, {{ $monthlyData[2] }},
                                {{ $monthlyData[3] }}, {{ $monthlyData[4] }},
                                {{ $monthlyData[5] }}, {{ $monthlyData[6] }},
                                {{ $monthlyData[7] }}, {{ $monthlyData[8] }},
                                {{ $monthlyData[9] }}, {{ $monthlyData[10] }},
                                {{ $monthlyData[11] }}, {{ $monthlyData[12] }}
                            ],
                            borderColor: 'rgb(6, 182, 212)',
                            backgroundColor: 'rgba(6, 182, 212, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });

            function initMap() {
                // Check if Leaflet is loaded
                if (typeof L === 'undefined') {
                    document.getElementById('mapLoading').innerHTML = `
                        <div class="text-center p-8 text-red-500">
                            <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                            <h3 class="text-lg font-semibold mb-2">Error Memuat Peta</h3>
                            <p class="text-sm">Gagal memuat library peta. Silakan refresh halaman.</p>
                        </div>
                    `;
                    return;
                }

                // Initialize map centered on Indonesia
                map = L.map('dashboardMap').setView([-2.5489, 118.0149], 5);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Add markers for faskes
                addFaskesMarkers();

                // Hide loading
                document.getElementById('mapLoading').style.display = 'none';

                // Update map info
                updateMapInfo();
            }

            function addFaskesMarkers() {
                // Clear existing markers
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];

                if (faskesData.length === 0) {
                    document.getElementById('mapInfo').innerHTML = 'Tidak ada faskes dengan koordinat yang valid';
                    return;
                }

                let bounds = L.latLngBounds();
                let validCount = 0;

                faskesData.forEach(faskes => {
                    const lat = parseFloat(faskes.latitude);
                    const lng = parseFloat(faskes.longitude);

                    if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                        // Determine icon path based on type
                        let iconPath = '/images/icons/icon-puskesmas.png'; // Default
                        if (faskes.type === 'Rumah Sakit') iconPath = '/images/icons/icon-rumah-sakit.png';
                        else if (faskes.type === 'Puskesmas') iconPath = '/images/icons/icon-puskesmas.png';
                        else if (faskes.type === 'Apotek') iconPath = '/images/icons/icon-apotek.png';

                        // Create custom icon using image
                        const customIcon = L.icon({
                            iconUrl: iconPath,
                            iconSize: [32, 32],
                            iconAnchor: [16, 16],
                            popupAnchor: [0, -16],
                            className: 'custom-marker-icon'
                        });

                        // Create popup content
                        let popupContent = `
                            <div class="p-2">
                                <h3 class="font-semibold text-gray-900 mb-2">${faskes.nama}</h3>
                                <p class="text-sm text-gray-600 mb-1"><strong>Tipe:</strong> ${faskes.type}</p>
                                <p class="text-sm text-gray-600 mb-1"><strong>Alamat:</strong> ${faskes.alamat || 'Tidak ada alamat'}</p>
                                <p class="text-sm text-gray-600 mb-1"><strong>No. Telp:</strong> ${faskes.no_telp || 'Tidak ada'}</p>
                                <p class="text-sm text-gray-600 mb-2"><strong>Status:</strong>
                                    <span class="px-2 py-1 rounded text-xs ${faskes.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${faskes.is_active ? 'Aktif' : 'Tidak Aktif'}
                                    </span>
                                </p>
                        `;

                        if (faskes.layanan && faskes.layanan.length > 0) {
                            popupContent += `<p class="text-sm text-gray-600 mb-1"><strong>Layanan:</strong></p>`;
                            popupContent += `<div class="flex flex-wrap gap-1 mb-2">`;
                            faskes.layanan.slice(0, 3).forEach(layanan => {
                                if (layanan) {
                                    popupContent +=
                                        `<span class="px-2 py-1 bg-cyan-100 text-cyan-800 text-xs rounded">${layanan}</span>`;
                                }
                            });
                            if (faskes.layanan.length > 3) {
                                popupContent +=
                                    `<span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">+${faskes.layanan.length - 3} lainnya</span>`;
                            }
                            popupContent += `</div>`;
                        }

                        popupContent += `</div>`;

                        // Add marker to map
                        const marker = L.marker([lat, lng], {
                                icon: customIcon
                            })
                            .addTo(map)
                            .bindPopup(popupContent);

                        markers.push(marker);
                        bounds.extend([lat, lng]);
                        validCount++;
                    }
                });

                // Fit map to show all markers
                if (validCount > 0) {
                    map.fitBounds(bounds, {
                        padding: [20, 20]
                    });
                }

                updateMapInfo(validCount);
            }

            function updateMapInfo(count = null) {
                const totalCount = count !== null ? count : faskesData.length;
                document.getElementById('mapInfo').innerHTML = `Menampilkan ${totalCount} faskes di peta`;
            }

            function fitBounds() {
                if (markers.length > 0) {
                    let bounds = L.latLngBounds();
                    markers.forEach(marker => {
                        bounds.extend(marker.getLatLng());
                    });
                    map.fitBounds(bounds, {
                        padding: [20, 20]
                    });
                }
            }

            function refreshMap() {
                document.getElementById('mapLoading').style.display = 'flex';
                setTimeout(() => {
                    addFaskesMarkers();
                    document.getElementById('mapLoading').style.display = 'none';
                }, 500);
            }
        </script>
    </x-slot>
</x-layouts.app>
