<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center space-x-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Bulk Create Permissions</h1>
                    <p class="text-gray-600 mt-1">Tambahkan banyak permissions sekaligus</p>
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
            <form action="{{ route('permissions.bulk-store') }}" method="POST" class="p-6 space-y-8">
                @csrf

                <!-- Permission Information -->
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-3">
                                <i class="fas fa-key text-sm"></i>
                            </div>
                            Informasi Permissions
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Guard Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Guard Name <span class="text-red-500">*</span>
                            </label>
                            <select name="guard_name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @error('guard_name') border-red-300 @enderror"
                                required>
                                <option value="">Pilih Guard</option>
                                <option value="web" {{ old('guard_name', 'web') == 'web' ? 'selected' : '' }}>Web
                                </option>
                                <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>API</option>
                            </select>
                            @error('guard_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Guard yang akan digunakan untuk semua permissions
                            </p>
                        </div>

                        <!-- Preview Count -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Permissions</label>
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                                <span class="text-sm text-gray-600" id="permissionCount">0 permissions</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Jumlah permissions yang akan dibuat
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Permissions List -->
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3">
                                <i class="fas fa-list text-sm"></i>
                            </div>
                            Daftar Permissions
                        </h3>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Permissions (satu per baris) <span class="text-red-500">*</span>
                        </label>
                        <textarea name="permissions" rows="15"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @error('permissions') border-red-300 @enderror"
                            placeholder="Masukkan permissions, satu per baris:&#10;view devices&#10;create devices&#10;edit devices&#10;delete devices&#10;view users&#10;create users&#10;edit users&#10;delete users"
                            required>{{ old('permissions') }}</textarea>
                        @error('permissions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Masukkan nama permission, satu per baris. Baris kosong akan diabaikan.
                        </p>
                    </div>
                </div>

                <!-- Preview Permissions -->
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center mr-3">
                                <i class="fas fa-eye text-sm"></i>
                            </div>
                            Preview Permissions
                        </h3>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border-2 border-dashed border-gray-300">
                        <div id="previewPermissions" class="space-y-2">
                            <p class="text-gray-500 text-center">Masukkan permissions untuk melihat preview</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('permissions.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Buat Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Real-time preview
        document.addEventListener('DOMContentLoaded', function() {
            const permissionsTextarea = document.querySelector('textarea[name="permissions"]');
            const permissionCount = document.getElementById('permissionCount');
            const previewPermissions = document.getElementById('previewPermissions');

            function updatePreview() {
                const permissions = permissionsTextarea.value
                    .split('\n')
                    .map(p => p.trim())
                    .filter(p => p.length > 0);

                // Update count
                permissionCount.textContent = `${permissions.length} permissions`;

                // Update preview
                if (permissions.length === 0) {
                    previewPermissions.innerHTML =
                        '<p class="text-gray-500 text-center">Masukkan permissions untuk melihat preview</p>';
                } else {
                    const previewHtml = permissions.map(permission => `
                        <div class="flex items-center space-x-2 p-2 bg-white rounded border">
                            <i class="fas fa-key text-purple-500"></i>
                            <span class="text-sm font-mono">${permission}</span>
                        </div>
                    `).join('');
                    previewPermissions.innerHTML = previewHtml;
                }
            }

            permissionsTextarea.addEventListener('input', updatePreview);

            // Initial preview update
            updatePreview();
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const guard = document.querySelector('select[name="guard_name"]').value;
            const permissions = document.querySelector('textarea[name="permissions"]').value.trim();

            if (!guard) {
                e.preventDefault();
                SweetAlert.error('Error!', 'Guard name harus dipilih');
                return;
            }

            if (!permissions) {
                e.preventDefault();
                SweetAlert.error('Error!', 'Daftar permissions harus diisi');
                return;
            }

            const permissionList = permissions.split('\n').filter(p => p.trim().length > 0);
            if (permissionList.length === 0) {
                e.preventDefault();
                SweetAlert.error('Error!', 'Minimal satu permission harus diisi');
                return;
            }

            // Show loading
            SweetAlert.loading('Menyimpan...', `${permissionList.length} permissions sedang dibuat`);
        });
    </script>
</x-layouts.app>
