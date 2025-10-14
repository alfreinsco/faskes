@php
    // Determine module type and data based on route
    $moduleType = 'faskes';
    $moduleName = 'Fasilitas Kesehatan';
    $moduleIcon = 'fas fa-hospital';
    $moduleColor = 'cyan';
    $item = $item ?? ($rumahSakit ?? ($puskesmas ?? ($apotek ?? null)));

    if (request()->routeIs('rumah-sakit.*')) {
        $moduleType = 'rumah-sakit';
        $moduleName = 'Rumah Sakit';
        $moduleIcon = 'fas fa-hospital-alt';
        $moduleColor = 'red';
        $item = $rumahSakit ?? $item;
    } elseif (request()->routeIs('puskesmas.*')) {
        $moduleType = 'puskesmas';
        $moduleName = 'Puskesmas';
        $moduleIcon = 'fas fa-clinic-medical';
        $moduleColor = 'blue';
        $item = $puskesmas ?? $item;
    } elseif (request()->routeIs('apotek.*')) {
        $moduleType = 'apotek';
        $moduleName = 'Apotek';
        $moduleIcon = 'fas fa-pills';
        $moduleColor = 'green';
        $item = $apotek ?? $item;
    }
@endphp

<x-layouts.app>
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $item->nama }}</h1>
                        <p class="text-gray-600 mt-1">Detail informasi {{ strtolower($moduleName) }}</p>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route($moduleType . '.edit', $item) }}"
                        class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit {{ $moduleName }}
                    </a>
                    <form action="{{ route($moduleType . '.toggle-status', $item) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 {{ $item->is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas {{ $item->is_active ? 'fa-pause' : 'fa-play' }} mr-2"></i>
                            {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <a href="{{ route($moduleType . '.index') }}"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Faskes Status Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                            @if ($item->gambar)
                                <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->nama }}"
                                    class="w-full h-full object-cover rounded-full">
                            @else
                                <i class="fas fa-hospital text-white text-3xl"></i>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $item->nama }}</h2>
                            <div class="flex items-center space-x-2 mt-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->type == 'Puskesmas' ? 'bg-blue-100 text-blue-800' : ($item->type == 'Rumah Sakit' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $item->type }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas {{ $item->is_active ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                    {{ $item->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Informasi Dasar
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nama Faskes</label>
                        <p class="text-gray-900 font-medium">{{ $item->nama }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tipe Faskes</label>
                        <p class="text-gray-900">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->type == 'Puskesmas' ? 'bg-blue-100 text-blue-800' : ($item->type == 'Rumah Sakit' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800') }}">
                                {{ $item->type }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Alamat</label>
                        <p class="text-gray-900">{{ $item->alamat ?: 'Tidak ada alamat' }}</p>
                    </div>
                    @if ($item->waktu_buka && $item->waktu_tutup)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Jam Operasional</label>
                            <p class="text-gray-900">{{ $item->waktu_buka }} - {{ $item->waktu_tutup }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Layanan -->
            @if ($item->layanan && count($item->layanan) > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-list mr-2 text-cyan-600"></i>
                        Layanan
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($item->layanan as $layanan)
                            @if ($layanan)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-cyan-100 text-cyan-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    {{ $layanan }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-phone mr-2 text-green-600"></i>
                    Informasi Kontak
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nomor Telepon</label>
                        <p class="text-gray-900">
                            @if ($item->no_telp)
                                <a href="tel:{{ $item->no_telp }}" class="text-cyan-600 hover:text-cyan-900">
                                    {{ $item->no_telp }}
                                </a>
                            @else
                                <span class="text-gray-400">Tidak ada nomor telepon</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-gray-900">
                            @if ($item->email)
                                <a href="mailto:{{ $item->email }}" class="text-cyan-600 hover:text-cyan-900">
                                    {{ $item->email }}
                                </a>
                            @else
                                <span class="text-gray-400">Tidak ada email</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Website</label>
                        <p class="text-gray-900">
                            @if ($item->website)
                                <a href="{{ $item->website }}" target="_blank"
                                    class="text-cyan-600 hover:text-cyan-900">
                                    {{ $item->website }}
                                    <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            @else
                                <span class="text-gray-400">Tidak ada website</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            @if ($item->latitude || $item->longitude)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>
                        Informasi Lokasi
                    </h3>
                    <div class="space-y-4">
                        @if ($item->latitude)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Latitude</label>
                                <p class="text-gray-900 font-mono">{{ $item->latitude }}</p>
                            </div>
                        @endif
                        @if ($item->longitude)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Longitude</label>
                                <p class="text-gray-900 font-mono">{{ $item->longitude }}</p>
                            </div>
                        @endif
                        @if ($item->latitude && $item->longitude)
                            <div class="space-y-4">
                                <!-- Map Display -->
                                <div>
                                    <label class="text-sm font-medium text-gray-500 mb-2 block">Peta Lokasi</label>
                                    <div id="map" class="w-full h-64 rounded-lg border border-gray-300"></div>
                                </div>

                                <!-- External Map Links -->
                                <div class="flex flex-wrap gap-2">
                                    <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}"
                                        target="_blank"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                                        <i class="fas fa-map mr-2"></i>
                                        Google Maps
                                    </a>
                                    <a href="https://www.openstreetmap.org/?mlat={{ $item->latitude }}&mlon={{ $item->longitude }}&zoom=15"
                                        target="_blank"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                                        <i class="fas fa-globe mr-2"></i>
                                        OpenStreetMap
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- System Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-cog mr-2 text-gray-600"></i>
                    Informasi Sistem
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <p class="text-gray-900">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas {{ $item->is_active ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                {{ $item->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Dibuat</label>
                        <p class="text-gray-900">{{ $item->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Diperbarui</label>
                        <p class="text-gray-900">{{ $item->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">ID</label>
                        <p class="text-gray-900 font-mono">{{ $item->id }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-tools mr-2 text-purple-600"></i>
                Aksi
            </h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route($moduleType . '.edit', $item) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i>
                    Edit {{ $moduleName }}
                </a>
                <form action="{{ route($moduleType . '.toggle-status', $item) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 {{ $item->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas {{ $item->is_active ? 'fa-pause' : 'fa-play' }} mr-2"></i>
                        {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <form action="{{ route($moduleType . '.destroy', $item) }}" method="POST" class="inline"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus faskes ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Faskes
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if ($item->latitude && $item->longitude)
        <x-slot name="scripts">
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize map centered on faskes location
                    const map = L.map('map').setView([{{ $item->latitude }}, {{ $item->longitude }}], 15);

                    // Add OpenStreetMap tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);

                    // Get faskes type and create appropriate icon
                    @php
                        $iconMap = [
                            'Apotek' => 'icon-apotek.png',
                            'Puskesmas' => 'icon-puskesmas.png',
                            'Rumah Sakit' => 'icon-rumah-sakit.png',
                        ];
                        $iconFile = $iconMap[$item->type] ?? 'icon-apotek.png';
                    @endphp

                    const iconUrl = '{{ asset('images/icons/' . $iconFile) }}';
                    console.log('Loading icon from:', iconUrl);

                    const customIcon = L.icon({
                        iconUrl: iconUrl,
                        iconSize: [32, 32],
                        iconAnchor: [16, 32],
                        popupAnchor: [0, -32]
                    });

                    // Add marker for faskes location
                    const marker = L.marker([{{ $item->latitude }}, {{ $item->longitude }}], {
                        icon: customIcon
                    }).addTo(map);

                    // Add popup with faskes information
                    marker.bindPopup(`
                    <div class="text-center">
                        <h3 class="font-semibold text-lg">{{ $item->nama }}</h3>
                        <p class="text-sm text-gray-600">{{ $item->alamat }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $item->type }}</p>
                    </div>
                `).openPopup();
                });
            </script>
        </x-slot>
    @endif
</x-layouts.app>
