<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('users.index') }}" class="btn btn-ghost btn-circle">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah User Baru</h1>
                <p class="text-gray-600">Tambahkan pengguna baru ke sistem</p>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- User Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-600"></i>
                        Informasi User
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Nama Lengkap <span
                                        class="text-red-500">*</span></span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="input input-bordered w-full @error('name') input-error @enderror"
                                placeholder="Contoh: John Doe" required>
                            @error('name')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Email <span class="text-red-500">*</span></span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="input input-bordered w-full @error('email') input-error @enderror"
                                placeholder="Contoh: john@example.com" required>
                            @error('email')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Password <span class="text-red-500">*</span></span>
                            </label>
                            <input type="password" name="password"
                                class="input input-bordered w-full @error('password') input-error @enderror"
                                placeholder="Minimal 8 karakter" required>
                            @error('password')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Konfirmasi Password <span
                                        class="text-red-500">*</span></span>
                            </label>
                            <input type="password" name="password_confirmation"
                                class="input input-bordered w-full @error('password_confirmation') input-error @enderror"
                                placeholder="Ulangi password" required>
                            @error('password_confirmation')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Info Note -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Role dan permission dapat diatur setelah user dibuat melalui halaman detail user.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Preview -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-eye mr-2 text-indigo-600"></i>
                        Preview User
                    </h3>

                    <div class="bg-gray-50 p-6 rounded-lg border-2 border-dashed border-gray-300">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-16 h-16 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                                <span class="text-white font-bold text-2xl" id="previewInitial">U</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-lg" id="previewName">Nama User</h4>
                                <p class="text-gray-600" id="previewEmail">email@example.com</p>
                                <div class="flex flex-wrap gap-2 mt-2" id="previewRoles">
                                    <span class="badge badge-primary">Role</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('users.index') }}" class="btn btn-ghost">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>

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
                const name = nameInput.value || 'Nama User';
                previewName.textContent = name;
                previewInitial.textContent = name.charAt(0).toUpperCase();

                // Update email
                previewEmail.textContent = emailInput.value || 'email@example.com';

                // Update roles
                const selectedRoles = Array.from(roleCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => {
                        const roleName = checkbox.parentElement.querySelector('.font-medium').textContent;
                        return `<span class="badge badge-primary">${roleName}</span>`;
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
            const password = document.querySelector('input[name="password"]').value;
            const passwordConfirmation = document.querySelector('input[name="password_confirmation"]').value;

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

            if (!password) {
                e.preventDefault();
                SweetAlert.error('Error!', 'Password harus diisi');
                return;
            }

            if (password !== passwordConfirmation) {
                e.preventDefault();
                SweetAlert.error('Error!', 'Konfirmasi password tidak sesuai');
                return;
            }

            // Show loading
            SweetAlert.loading('Menyimpan...', 'User sedang disimpan');
        });
    </script>
</x-layouts.app>
