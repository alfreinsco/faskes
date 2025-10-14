<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center space-x-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Permission</h1>
                    <p class="text-gray-600 mt-1">Ubah informasi permission "{{ $permission->name }}"</p>
                </div>
                <a href="{{ route('permissions.index') }}"
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

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form action="{{ route('permissions.update', $permission) }}" method="POST" class="p-6 space-y-8">
                @csrf
                @method('PUT')

                <!-- Permission Information -->
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-3">
                                <i class="fas fa-key text-sm"></i>
                            </div>
                            Informasi Permission
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Permission Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Permission <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $permission->name) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @error('name') border-red-300 @enderror"
                                placeholder="Contoh: view devices, create users" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Gunakan format: action resource (contoh: view devices, create users)
                            </p>
                        </div>

                        <!-- Guard Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Guard Name <span class="text-red-500">*</span>
                            </label>
                            <select name="guard_name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @error('guard_name') border-red-300 @enderror"
                                required>
                                <option value="">Pilih Guard</option>
                                <option value="web"
                                    {{ old('guard_name', $permission->guard_name) == 'web' ? 'selected' : '' }}>Web
                                </option>
                                <option value="api"
                                    {{ old('guard_name', $permission->guard_name) == 'api' ? 'selected' : '' }}>API
                                </option>
                            </select>
                            @error('guard_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Guard yang akan digunakan untuk permission ini
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <div class="flex space-x-2">
                        <a href="{{ route('permissions.show', $permission) }}"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Detail
                        </a>
                        <button type="button"
                            onclick="konfirmasiHapus('{{ route('permissions.destroy', $permission->id) }}', '{{ $permission->name }}')"
                            class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Permission
                        </button>
                    </div>

                    <div class="flex space-x-4">
                        <a href="{{ route('permissions.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.querySelector('input[name="name"]').value.trim();
            const guard = document.querySelector('select[name="guard_name"]').value;

            if (!name) {
                e.preventDefault();
                SweetAlert.error('Error!', 'Nama permission harus diisi');
                return;
            }

            if (!guard) {
                e.preventDefault();
                SweetAlert.error('Error!', 'Guard name harus dipilih');
                return;
            }

            // Show loading
            SweetAlert.loading('Menyimpan...', 'Perubahan sedang disimpan');
        });
    </script>
</x-layouts.app>
