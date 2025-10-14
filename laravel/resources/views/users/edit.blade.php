<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center space-x-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Pengguna</h1>
                    <p class="text-gray-600 mt-1">Ubah informasi pengguna "{{ $user->name }}"</p>
                </div>
                <a href="{{ route('users.index') }}"
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
            <form action="{{ route('users.update', $user) }}" method="POST" class="p-6 space-y-8">
                @csrf
                @method('PUT')

                <!-- User Information -->
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            Informasi Pengguna
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @error('name') border-red-300 @enderror"
                                placeholder="Contoh: John Doe" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @error('email') border-red-300 @enderror"
                                placeholder="Contoh: john@example.com" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-sm"></i>
                            </div>
                            Informasi Tambahan
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Created Info -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Dibuat</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    {{ $user->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                        </div>

                        <!-- Last Updated Info -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Terakhir Diperbarui</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    {{ $user->updated_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Verification Status -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Status Email</h4>
                        <div class="flex items-center">
                            @if ($user->email_verified_at)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Terverifikasi
                                </span>
                                <span class="ml-2 text-sm text-gray-500">
                                    {{ $user->email_verified_at->format('d M Y, H:i') }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Belum Terverifikasi
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Preview -->
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center mr-3">
                                <i class="fas fa-eye text-sm"></i>
                            </div>
                            Preview Pengguna
                        </h3>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border-2 border-dashed border-gray-300">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-16 h-16 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                                <span class="text-white font-bold text-2xl"
                                    id="previewInitial">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-lg" id="previewName">{{ $user->name }}</h4>
                                <p class="text-gray-600" id="previewEmail">{{ $user->email }}</p>
                                <div class="flex flex-wrap gap-2 mt-2" id="previewRoles">
                                    @foreach ($user->roles as $role)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">{{ $role->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <div class="flex space-x-2">
                        <a href="{{ route('users.show', $user) }}"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Detail
                        </a>
                        @if ($user->id !== auth()->id())
                            <button type="button" onclick="deleteUser('{{ $user->id }}', '{{ $user->name }}')"
                                class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus Pengguna
                            </button>
                        @endif
                    </div>

                    <div class="flex space-x-4">
                        <a href="{{ route('users.index') }}"
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
        // Real-time preview
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.querySelector('input[name="name"]');
            const emailInput = document.querySelector('input[name="email"]');
            const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');

            const previewName = document.getElementById('previewName');
            const previewEmail = document.getElementById('previewEmail');
            const previewInitial = document.getElementById('previewInitial');
            const previewRoles = document.getElementById('previewRoles');

            function updatePreview() {
                // Update name and initial
                const name = nameInput.value || 'Nama Pengguna';
                previewName.textContent = name;
                previewInitial.textContent = name.charAt(0).toUpperCase();

                // Update email
                previewEmail.textContent = emailInput.value || 'email@example.com';

                // Update roles
                const selectedRoles = Array.from(roleCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => {
                        const roleName = checkbox.parentElement.querySelector('.font-medium').textContent;
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">${roleName}</span>`;
                    });

                previewRoles.innerHTML = selectedRoles.length > 0 ?
                    selectedRoles.join('') :
                    '<span class="text-gray-500 text-sm">Tidak ada role</span>';
            }

            nameInput.addEventListener('input', updatePreview);
            emailInput.addEventListener('input', updatePreview);
            roleCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updatePreview);
            });

            // Initial preview update
            updatePreview();
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.querySelector('input[name="name"]').value.trim();
            const email = document.querySelector('input[name="email"]').value.trim();

            if (!name) {
                e.preventDefault();
                SweetAlert.error('Error!', 'Nama lengkap harus diisi');
                return;
            }

            if (!email) {
                e.preventDefault();
                SweetAlert.error('Error!', 'Email harus diisi');
                return;
            }

            // Show loading
            SweetAlert.loading('Menyimpan...', 'Perubahan sedang disimpan');
        });

        // Delete user
        function deleteUser(userId, userName) {
            SweetAlert.deleteConfirm(
                'Hapus Pengguna',
                `Apakah Anda yakin ingin menghapus pengguna "${userName}"? Data yang dihapus tidak dapat dikembalikan.`
            ).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteForm');
                    form.action = `/users/${userId}`;
                    form.submit();
                }
            });
        }
    </script>
</x-layouts.app>
