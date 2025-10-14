@php
    // Determine module type and data based on route
    $moduleType = 'faskes';
    $moduleName = 'Fasilitas Kesehatan';
    $moduleIcon = 'fas fa-hospital';
    $moduleColor = 'cyan';
    $data = $faskes ?? ($rumahSakit ?? ($puskesmas ?? ($apotek ?? collect())));

    if (request()->routeIs('rumah-sakit.*')) {
        $moduleType = 'rumah-sakit';
        $moduleName = 'Rumah Sakit';
        $moduleIcon = 'fas fa-hospital-alt';
        $moduleColor = 'red';
        $data = $rumahSakit ?? collect();
    } elseif (request()->routeIs('puskesmas.*')) {
        $moduleType = 'puskesmas';
        $moduleName = 'Puskesmas';
        $moduleIcon = 'fas fa-clinic-medical';
        $moduleColor = 'blue';
        $data = $puskesmas ?? collect();
    } elseif (request()->routeIs('apotek.*')) {
        $moduleType = 'apotek';
        $moduleName = 'Apotek';
        $moduleIcon = 'fas fa-pills';
        $moduleColor = 'green';
        $data = $apotek ?? collect();
    }
@endphp

<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Data {{ $moduleName }}</h1>
                    <p class="text-gray-600 mt-1">Kelola data {{ strtolower($moduleName) }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route($moduleType . '.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah {{ $moduleName }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-cyan-100 text-cyan-600">
                        <i class="{{ $moduleIcon }} text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total {{ $moduleName }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $data->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $data->where('is_active', true)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-exclamation-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tidak Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $data->where('is_active', false)->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route($moduleType . '.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari {{ $moduleName }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari berdasarkan nama, alamat, telepon, atau email..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif
                            </option>
                        </select>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="mt-auto">
                        <div class="flex justify-start space-x-3">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-search mr-2"></i>
                                Cari
                            </button>
                            <a href="{{ route($moduleType . '.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                                <i class="fas fa-undo mr-2"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if ($data->count() > 0)
                <!-- Table Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Hasil Pencarian ({{ $data->total() }} data)
                        </h3>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Gambar</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama {{ $moduleName }}</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Alamat</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kontak</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($data as $index => $item)
                                <tr class="hover:bg-gray-50"></tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $data->firstItem() + $index }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($item->gambar)
                                        <div class="h-12 w-12 rounded-lg overflow-hidden">
                                            <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->nama }}"
                                                class="h-full w-full object-cover">
                                        </div>
                                    @else
                                        <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="{{ $moduleIcon }} text-gray-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $item->nama }}</div>
                                        @if ($item->waktu_buka && $item->waktu_tutup)
                                            <div class="text-sm text-gray-500">
                                                {{ $item->waktu_buka }} - {{ $item->waktu_tutup }}
                                            </div>
                                        @endif
                                        @if ($item->layanan && count($item->layanan) > 0)
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach (array_slice($item->layanan, 0, 2) as $layanan)
                                                    @if ($layanan)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-cyan-100 text-cyan-800">
                                                            {{ $layanan }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                                @if (count($item->layanan) > 2)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                        +{{ count($item->layanan) - 2 }} lainnya
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        {{ $item->alamat ?: '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        @if ($item->no_telp)
                                            <div class="text-gray-600">{{ $item->no_telp }}</div>
                                        @endif
                                        @if ($item->email)
                                            <div class="text-gray-500">{{ $item->email }}</div>
                                        @endif
                                        @if (!$item->no_telp && !$item->email)
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route($moduleType . '.toggle-status', $item) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }} transition-colors duration-200">
                                            <i class="fas {{ $item->is_active ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                            {{ $item->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route($moduleType . '.show', $item) }}"
                                            class="text-cyan-600 hover:text-cyan-900 transition-colors duration-200"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route($moduleType . '.edit', $item) }}"
                                            class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route($moduleType . '.toggle-status', $item) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="text-cyan-600 hover:text-cyan-900 transition-colors duration-200">
                                                <i
                                                    class="fas {{ $item->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} mr-1"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route($moduleType . '.destroy', $item) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus {{ strtolower($moduleName) }} ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $data->appends(request()->query())->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <i class="{{ $moduleIcon }} text-4xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada {{ strtolower($moduleName) }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if (request()->hasAny(['search', 'status']))
                            Tidak ada {{ strtolower($moduleName) }} yang sesuai dengan kriteria pencarian.
                        @else
                            Belum ada {{ strtolower($moduleName) }} yang terdaftar. Mulai dengan menambahkan
                            {{ strtolower($moduleName) }} pertama.
                        @endif
                    </p>
                    <div class="mt-6">
                        @if (request()->hasAny(['search', 'status']))
                            <a href="{{ route($moduleType . '.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 mr-3">
                                <i class="fas fa-undo mr-2"></i>
                                Lihat Semua {{ $moduleName }}
                            </a>
                        @endif
                        <a href="{{ route($moduleType . '.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah {{ $moduleName }}
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Map View -->
        @if ($data->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marked-alt mr-2 text-cyan-600"></i>
                    Peta Lokasi {{ $moduleName }}
                </h3>
                <div id="map" class="w-full h-96 rounded-lg border border-gray-300"></div>
            </div>
        @endif
    </div>

    <x-slot name="scripts">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if ($data->count() > 0)
                    // Initialize map
                    const map = L.map('map').setView([-3.6561, 128.1664], 11);

                    // Add OpenStreetMap tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);

                    // Icon mapping
                    const iconMap = {
                        'Apotek': '{{ asset('images/icons/icon-apotek.png') }}?v=' + Date.now(),
                        'Puskesmas': '{{ asset('images/icons/icon-puskesmas.png') }}?v=' + Date.now(),
                        'Rumah Sakit': '{{ asset('images/icons/icon-rumah-sakit.png') }}?v=' + Date.now()
                    };

                    console.log('Icon Map:', iconMap);

                    // Add markers for each item
                    @foreach ($data as $item)
                        @if ($item->latitude && $item->longitude)
                            const icon{{ $loop->index }} = L.icon({
                                iconUrl: iconMap['{{ $item->type }}'] || iconMap['Apotek'],
                                iconSize: [32, 32],
                                iconAnchor: [16, 32],
                                popupAnchor: [0, -32]
                            });

                            const marker{{ $loop->index }} = L.marker([{{ $item->latitude }},
                                {{ $item->longitude }}
                            ], {
                                icon: icon{{ $loop->index }}
                            }).addTo(map);

                            marker{{ $loop->index }}.bindPopup(`
                            <div class="text-center">
                                <h3 class="font-semibold text-lg">{{ $item->nama }}</h3>
                                <p class="text-sm text-gray-600">{{ $item->alamat }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $item->type }}</p>
                                <div class="mt-2">
                                    <a href="{{ route($moduleType . '.show', $item) }}"
                                        class="inline-flex items-center px-3 py-1 bg-cyan-600 text-white text-xs rounded hover:bg-cyan-700" style="color: white;">
                                        <i class="fas fa-eye mr-1"></i>
                                        Detail
                                    </a>
                                </div>
                            </div>
                        `);
                        @endif
                    @endforeach
                @endif
            });
        </script>
    </x-slot>
</x-layouts.app>
