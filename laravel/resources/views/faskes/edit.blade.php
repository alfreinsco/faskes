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
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route($moduleType . '.index') }}" class="btn btn-ghost btn-circle">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit {{ $moduleName }}</h1>
                    <p class="text-gray-600">Perbarui informasi {{ strtolower($moduleName) }}: {{ $item->nama }}</p>
                </div>
            </div>
            <a href="{{ route($moduleType . '.show', $item) }}"
                class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-eye mr-2"></i>
                Detail
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form action="{{ route($moduleType . '.update', $item) }}" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-hospital mr-2 text-blue-600"></i>
                        Informasi Dasar
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Faskes -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Nama Faskes <span
                                        class="text-red-500">*</span></span>
                            </label>
                            <input type="text" name="nama" value="{{ old('nama', $item->nama) }}"
                                class="input input-bordered w-full @error('nama') input-error @enderror"
                                placeholder="Masukkan nama faskes" required>
                            @error('nama')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Tipe Faskes -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Tipe Faskes <span
                                        class="text-red-500">*</span></span>
                            </label>
                            <select name="type"
                                class="select select-bordered w-full @error('type') select-error @enderror" required>
                                <option value="">Pilih tipe faskes</option>
                                <option value="Puskesmas"
                                    {{ old('type', $item->type) == 'Puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                                <option value="Rumah Sakit"
                                    {{ old('type', $item->type) == 'Rumah Sakit' ? 'selected' : '' }}>Rumah Sakit
                                </option>
                                <option value="Apotek" {{ old('type', $item->type) == 'Apotek' ? 'selected' : '' }}>
                                    Apotek</option>
                            </select>
                            @error('type')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Alamat</span>
                        </label>
                        <textarea name="alamat" rows="3"
                            class="textarea textarea-bordered w-full @error('alamat') textarea-error @enderror"
                            placeholder="Masukkan alamat lengkap faskes">{{ old('alamat', $item->alamat) }}</textarea>
                        @error('alamat')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Layanan -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Layanan</span>
                            <span class="label-text-alt text-gray-500">Klik tombol + untuk menambah layanan</span>
                        </label>
                        <div id="layanan-container" class="space-y-2">
                            @if (old('layanan'))
                                @foreach (old('layanan') as $index => $layanan)
                                    <div class="layanan-item flex items-center space-x-2">
                                        <input type="text" name="layanan[]" value="{{ $layanan }}"
                                            class="input input-bordered flex-1 @error('layanan.' . $index) input-error @enderror"
                                            placeholder="Masukkan nama layanan">
                                        <button type="button" onclick="removeLayanan(this)"
                                            class="btn btn-sm btn-error btn-outline">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @elseif($item->layanan && count($item->layanan) > 0)
                                @foreach ($item->layanan as $index => $layanan)
                                    <div class="layanan-item flex items-center space-x-2">
                                        <input type="text" name="layanan[]" value="{{ $layanan }}"
                                            class="input input-bordered flex-1" placeholder="Masukkan nama layanan">
                                        <button type="button" onclick="removeLayanan(this)"
                                            class="btn btn-sm btn-error btn-outline">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="layanan-item flex items-center space-x-2">
                                    <input type="text" name="layanan[]" class="input input-bordered flex-1"
                                        placeholder="Masukkan nama layanan">
                                    <button type="button" onclick="removeLayanan(this)"
                                        class="btn btn-sm btn-error btn-outline">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addLayanan()" class="btn btn-sm btn-outline btn-primary mt-2">
                            <i class="fas fa-plus mr-1"></i>
                            Tambah Layanan
                        </button>
                        @error('layanan')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-phone mr-2 text-green-600"></i>
                        Informasi Kontak
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- No Telepon -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Nomor Telepon</span>
                            </label>
                            <input type="text" name="no_telp" value="{{ old('no_telp', $item->no_telp) }}"
                                class="input input-bordered w-full @error('no_telp') input-error @enderror"
                                placeholder="Contoh: 081234567890">
                            @error('no_telp')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Email</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $item->email) }}"
                                class="input input-bordered w-full @error('email') input-error @enderror"
                                placeholder="Contoh: info@faskes.com">
                            @error('email')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text font-medium">Website</span>
                            </label>
                            <input type="url" name="website" value="{{ old('website', $item->website) }}"
                                class="input input-bordered w-full @error('website') input-error @enderror"
                                placeholder="Contoh: https://www.faskes.com">
                            @error('website')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-purple-600"></i>
                        Informasi Tambahan
                    </h3>

                    <!-- Current Image -->
                    @if ($item->gambar)
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Gambar Saat Ini</span>
                            </label>
                            <div class="flex items-center gap-4">
                                <div class="h-20 w-20 rounded-lg overflow-hidden">
                                    <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->nama }}"
                                        class="h-full w-full object-cover">
                                </div>
                                <div class="text-sm text-gray-600">
                                    <p>Gambar saat ini</p>
                                    <p class="text-xs text-gray-500">Upload gambar baru untuk mengganti</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- New Image -->
                    <div class="form-control">
                        <label class="label">
                            <span
                                class="label-text font-medium">{{ $item->gambar ? 'Gambar Baru' : 'Gambar Faskes' }}</span>
                        </label>
                        <input type="file" name="gambar" accept="image/*"
                            class="file-input file-input-bordered w-full @error('gambar') file-input-error @enderror">
                        <label class="label">
                            <span class="label-text-alt">Format: JPG, PNG, GIF. Maksimal 2MB</span>
                        </label>
                        @error('gambar')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Waktu Buka -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Waktu Buka</span>
                            </label>
                            <input type="time" name="waktu_buka"
                                value="{{ old('waktu_buka', $item->waktu_buka) }}"
                                class="input input-bordered w-full @error('waktu_buka') input-error @enderror">
                            @error('waktu_buka')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Waktu Tutup -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Waktu Tutup</span>
                            </label>
                            <input type="time" name="waktu_tutup"
                                value="{{ old('waktu_tutup', $item->waktu_tutup) }}"
                                class="input input-bordered w-full @error('waktu_tutup') input-error @enderror">
                            @error('waktu_tutup')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>

                    <!-- Map Section -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Lokasi Faskes</span>
                        </label>
                        <div class="space-y-4">
                            <!-- Map Container -->
                            <div id="map" class="w-full h-64 rounded-lg border border-gray-300"></div>

                            <!-- Coordinate Inputs -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Latitude</span>
                                    </label>
                                    <input type="number" name="latitude" id="latitude"
                                        value="{{ old('latitude', $item->latitude) }}" step="any" min="-90"
                                        max="90"
                                        class="input input-bordered w-full @error('latitude') input-error @enderror"
                                        placeholder="Klik pada peta atau masukkan manual">
                                    @error('latitude')
                                        <label class="label">
                                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Longitude</span>
                                    </label>
                                    <input type="number" name="longitude" id="longitude"
                                        value="{{ old('longitude', $item->longitude) }}" step="any"
                                        min="-180" max="180"
                                        class="input input-bordered w-full @error('longitude') input-error @enderror"
                                        placeholder="Klik pada peta atau masukkan manual">
                                    @error('longitude')
                                        <label class="label">
                                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>

                            <!-- Map Instructions -->
                            <div class="text-sm text-gray-600 bg-blue-50 p-3 rounded-lg">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Petunjuk:</strong> Klik pada peta untuk memilih lokasi faskes, atau masukkan
                                koordinat secara manual.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route($moduleType . '.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <a href="{{ route($moduleType . '.show', $item) }}"
                        class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-eye mr-2"></i>
                        Detail
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-check mr-2"></i>
                        Perbarui {{ $moduleName }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            // Layanan management functions
            function addLayanan() {
                const container = document.getElementById('layanan-container');
                const layananItem = document.createElement('div');
                layananItem.className = 'layanan-item flex items-center space-x-2';
                layananItem.innerHTML = `
                    <input type="text" name="layanan[]"
                        class="input input-bordered flex-1"
                        placeholder="Masukkan nama layanan">
                    <button type="button" onclick="removeLayanan(this)"
                        class="btn btn-sm btn-error btn-outline">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                container.appendChild(layananItem);
            }

            function removeLayanan(button) {
                const layananItems = document.querySelectorAll('.layanan-item');
                if (layananItems.length > 1) {
                    button.parentElement.remove();
                } else {
                    // If it's the last item, just clear the input
                    const input = button.parentElement.querySelector('input');
                    input.value = '';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Initialize map centered on Ambon, Indonesia
                const map = L.map('map').setView([-3.6561, 128.1664], 13);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                let marker = null;
                const latitudeInput = document.getElementById('latitude');
                const longitudeInput = document.getElementById('longitude');

                // Function to update marker position
                function updateMarker(lat, lng) {
                    if (marker) {
                        map.removeLayer(marker);
                    }

                    // Get faskes type and create appropriate icon
                    @php
                        $iconMap = [
                            'Apotek' => 'icon-apotek.png',
                            'Puskesmas' => 'icon-puskesmas.png',
                            'Rumah Sakit' => 'icon-rumah-sakit.png',
                        ];
                        $iconFile = $iconMap[$item->type] ?? 'icon-apotek.png';
                    @endphp

                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 13);
                }

                // Function to update inputs
                function updateInputs(lat, lng) {
                    latitudeInput.value = lat.toFixed(6);
                    longitudeInput.value = lng.toFixed(6);
                }

                // Map click event
                map.on('click', function(e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    updateMarker(lat, lng);
                    updateInputs(lat, lng);
                });

                // If there are existing coordinates, show them on map
                const existingLat = parseFloat(latitudeInput.value);
                const existingLng = parseFloat(longitudeInput.value);

                if (!isNaN(existingLat) && !isNaN(existingLng)) {
                    updateMarker(existingLat, existingLng);
                }

                // Input change events
                latitudeInput.addEventListener('change', function() {
                    const lat = parseFloat(this.value);
                    const lng = parseFloat(longitudeInput.value);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        updateMarker(lat, lng);
                    }
                });

                longitudeInput.addEventListener('change', function() {
                    const lat = parseFloat(latitudeInput.value);
                    const lng = parseFloat(this.value);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        updateMarker(lat, lng);
                    }
                });
            });
        </script>
    </x-slot>
</x-layouts.app>
