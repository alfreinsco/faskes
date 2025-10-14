<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Role: {{ $role->name }}</h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap role dan permissions</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('roles.edit', $role) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                            </path>
                        </svg>
                        Edit Role
                    </a>
                    <a href="{{ route('roles.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Role Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Basic Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Role</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ID Role</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $role->id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                            <p class="text-sm text-gray-900 font-semibold">{{ $role->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Guard Name</label>
                            <p class="text-sm text-gray-900">{{ $role->guard_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dibuat</label>
                            <p class="text-sm text-gray-900">{{ $role->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diperbarui</label>
                            <p class="text-sm text-gray-900">{{ $role->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                    <div class="space-y-3">
                        <a href="{{ route('roles.edit', $role) }}"
                            class="w-full flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                </path>
                            </svg>
                            Edit Role
                        </a>
                        <button
                            onclick="konfirmasiHapus('{{ route('roles.destroy', $role->id) }}', '{{ $role->name }}', 'Hapus Role', 'Role yang dihapus tidak dapat dikembalikan!')"
                            class="w-full flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd">
                                </path>
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Hapus Role
                        </button>
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Permissions ({{ $role->permissions->count() }})
                        </h3>
                        @if ($role->permissions->count() > 0)
                            <div class="flex space-x-2">
                                <button onclick="selectAllPermissions()"
                                    class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-md hover:bg-blue-200 transition-colors duration-200">
                                    Pilih Semua
                                </button>
                                <button onclick="deselectAllPermissions()"
                                    class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors duration-200">
                                    Batal Pilih
                                </button>
                            </div>
                        @endif
                    </div>

                    @if ($role->permissions->count() > 0)
                        @php
                            // Ambil semua action yang ada dari permissions role ini
                            $allActions = collect();
                            $groupedPermissions = $role->permissions->groupBy(function ($permission) {
                                return explode(' ', $permission->name)[1] ?? 'other';
                            });

                            foreach ($groupedPermissions as $modulePermissions) {
                                $actions = $modulePermissions
                                    ->map(function ($permission) {
                                        $parts = explode(' ', $permission->name);
                                        return $parts[0] ?? 'other';
                                    })
                                    ->unique();
                                $allActions = $allActions->merge($actions);
                            }
                            $allActions = $allActions->unique()->sort()->values();
                        @endphp

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Module
                                        </th>
                                        @foreach ($allActions as $action)
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ ucfirst($action) }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($groupedPermissions as $module => $modulePermissions)
                                        @php
                                            $groupedByAction = $modulePermissions->groupBy(function ($permission) {
                                                $parts = explode(' ', $permission->name);
                                                return $parts[0] ?? 'other';
                                            });
                                        @endphp

                                        <tr class="hover:bg-gray-50">
                                            <!-- Module Name -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-cyan-100 flex items-center justify-center mr-3">
                                                        <i
                                                            class="fas fa-{{ $module === 'users' ? 'users' : ($module === 'roles' ? 'user-shield' : ($module === 'permissions' ? 'key' : 'cog')) }} text-cyan-600 text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 capitalize">
                                                            {{ $module }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $modulePermissions->count() }} permissions</div>
                                                    </div>
                                                </div>
                                            </td>

                                            @foreach ($allActions as $action)
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    @if ($groupedByAction->has($action))
                                                        @foreach ($groupedByAction[$action] as $permission)
                                                            <div class="flex items-center justify-center">
                                                                <input type="checkbox" checked disabled
                                                                    class="rounded border-gray-300
                                                                    @if ($action === 'view') text-cyan-600
                                                                    @elseif($action === 'create') text-green-600
                                                                    @elseif($action === 'update') text-yellow-600
                                                                    @elseif($action === 'delete') text-red-600
                                                                    @else text-purple-600 @endif">
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <span class="text-gray-400 text-xs">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-shield-alt text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada permissions</h3>
                            <p class="text-gray-500 mb-4">Role ini belum memiliki permissions yang ditetapkan</p>
                            <a href="{{ route('roles.edit', $role) }}"
                                class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                    </path>
                                </svg>
                                Tambah Permissions
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Users with this Role -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Users dengan Role Ini
                        ({{ $role->users->count() }})</h3>

                    @if ($role->users->count() > 0)
                        <div class="space-y-3">
                            @foreach ($role->users as $user)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-8 w-8 rounded-full bg-cyan-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-cyan-600">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('users.show', $user) }}"
                                        class="text-cyan-600 hover:text-cyan-900 text-sm font-medium">
                                        Lihat User
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <i class="fas fa-users text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Tidak ada user yang menggunakan role ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function selectAllPermissions() {
            document.querySelectorAll('input[type="checkbox"]:not([disabled])').forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectAllPermissions() {
            document.querySelectorAll('input[type="checkbox"]:not([disabled])').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    </script>
</x-layouts.app>
