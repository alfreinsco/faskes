<x-layouts.app>
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-gray-600 mt-1">Detail informasi pengguna</p>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('users.edit', $user) }}"
                        class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Pengguna
                    </a>
                    @if ($user->id !== auth()->id())
                        <button onclick="toggleStatus('{{ $user->id }}', {{ $user->is_active ? 'true' : 'false' }})"
                            class="inline-flex items-center px-4 py-2 {{ $user->is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas {{ $user->is_active ? 'fa-pause' : 'fa-play' }} mr-2"></i>
                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    @endif
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
        </div>

        <!-- User Status Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                            <span class="text-white font-bold text-3xl">
                                {{ substr($user->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                            <p class="text-gray-600 flex items-center">
                                <i class="fas fa-envelope mr-2"></i>
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        @if ($user->is_active)
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                Aktif
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-2"></i>
                                Pending
                            </span>
                        @endif

                        @if ($user->last_login_at)
                            <div class="text-sm text-gray-500 mt-2">
                                Terakhir login: {{ $user->last_login_at->diffForHumans() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- User Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Basic Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-sm"></i>
                            </div>
                            Informasi Pengguna
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                                <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                                <p class="text-gray-900">{{ $user->email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                                <div class="flex items-center">
                                    @if ($user->is_active)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">User ID</label>
                                <p class="text-gray-900 font-mono text-sm">{{ $user->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roles Assignment -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3">
                                <i class="fas fa-user-shield text-sm"></i>
                            </div>
                            Roles Assignment
                        </h3>

                        @if ($user->roles->count() > 0)
                            <div class="space-y-3">
                                @foreach ($user->roles as $role)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center mr-3">
                                                <i class="fas fa-shield-alt text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $role->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $role->permissions->count() }}
                                                    permissions</p>
                                            </div>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                            Role
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-user-slash text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">Pengguna belum memiliki role</p>
                                <a href="{{ route('users.assign-roles', $user) }}"
                                    class="inline-flex items-center px-3 py-2 mt-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Assign Role
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Direct Permissions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-3">
                                <i class="fas fa-key text-sm"></i>
                            </div>
                            Direct Permissions
                        </h3>

                        @if ($user->permissions->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach ($user->permissions as $permission)
                                    <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                                        <i class="fas fa-key text-gray-400 mr-2"></i>
                                        <span class="text-sm text-gray-900">{{ $permission->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-key text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">Pengguna tidak memiliki direct permissions</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3">
                                <i class="fas fa-history text-sm"></i>
                            </div>
                            Activity Log
                        </h3>

                        <div class="space-y-4">
                            <!-- Created -->
                            <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                <div
                                    class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                    <i class="fas fa-plus text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Pengguna dibuat</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $user->created_at->format('d M Y, H:i:s') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Last Updated -->
                            @if ($user->updated_at != $user->created_at)
                                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                        <i class="fas fa-edit text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Pengguna diperbarui</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $user->updated_at->format('d M Y, H:i:s') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Email Verified -->
                            @if ($user->email_verified_at)
                                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                    <div
                                        class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                        <i class="fas fa-check-circle text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Email terverifikasi</p>
                                        <p class="text-sm text-gray-500">
                                            {{ @$user?->email_verified_at?->format('d M Y, H:i:s') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Last Login -->
                            @if ($user->last_login_at)
                                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                    <div
                                        class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center">
                                        <i class="fas fa-sign-in-alt text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Terakhir login</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $user->last_login_at->format('d M Y, H:i:s') }}
                                            ({{ $user->last_login_at->diffForHumans() }})
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mr-3">
                                <i class="fas fa-bolt text-sm"></i>
                            </div>
                            Quick Actions
                        </h3>

                        <div class="space-y-3">
                            <a href="{{ route('users.edit', $user) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Pengguna
                            </a>

                            <a href="{{ route('users.assign-roles', $user) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                                <i class="fas fa-user-shield mr-2"></i>
                                Atur Role
                            </a>

                            <a href="{{ route('users.assign-permissions', $user) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                                <i class="fas fa-key mr-2"></i>
                                Atur Permission
                            </a>

                            <a href="{{ route('users.change-password', $user) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                                <i class="fas fa-lock mr-2"></i>
                                Ubah Password
                            </a>

                            @if ($user->id !== auth()->id())
                                <button
                                    onclick="toggleStatus('{{ $user->id }}', {{ $user->is_active ? 'true' : 'false' }})"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 {{ $user->is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-lg transition-colors duration-200">
                                    <i class="fas {{ $user->is_active ? 'fa-pause' : 'fa-play' }} mr-2"></i>
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>

                                <button onclick="deleteUser('{{ $user->id }}', '{{ $user->name }}')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    <i class="fas fa-trash mr-2"></i>
                                    Hapus Pengguna
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Stats -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3">
                                <i class="fas fa-chart-bar text-sm"></i>
                            </div>
                            User Stats
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Status</span>
                                <span
                                    class="font-medium {{ $user->is_active ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Pending' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Roles</span>
                                <span class="font-medium">
                                    {{ $user->roles->count() }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Permissions</span>
                                <span class="font-medium">
                                    {{ $user->permissions->count() }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Member Since</span>
                                <span class="font-medium text-sm">
                                    {{ $user->created_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center mr-3">
                                <i class="fas fa-cog text-sm"></i>
                            </div>
                            System Info
                        </h3>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">User ID</span>
                                <span class="font-mono text-xs">{{ substr($user->id, 0, 8) }}...</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Email Status</span>
                                <span>{{ $user->is_active ? 'Verified' : 'Unverified' }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Last Login</span>
                                <span>{{ $user->last_login_at ? $user->last_login_at->format('d M Y') : 'Never' }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Dibuat</span>
                                <span>{{ $user->created_at->format('d M Y H:i') }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Terakhir Diupdate</span>
                                <span>{{ $user->updated_at->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="toggleStatusForm" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
    </form>

    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        // Toggle status
        function toggleStatus(userId, currentStatus) {
            const action = currentStatus ? 'dinonaktifkan' : 'diaktifkan';

            SweetAlert.confirm(
                'Konfirmasi Status',
                `Apakah Anda yakin ingin ${action} pengguna ini?`
            ).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('toggleStatusForm');
                    form.action = `/users/${userId}/toggle-status`;
                    form.submit();
                }
            });
        }

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
