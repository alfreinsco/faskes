<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Role: {{ $role->name }}</h1>
                    <p class="text-gray-600 mt-1">Ubah informasi role dan permissions</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('roles.show', $role) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Lihat Detail
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

        <!-- Role Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Role</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID Role</label>
                    <p class="text-sm text-gray-900">{{ $role->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                    <p class="text-sm text-gray-900">{{ $role->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dibuat</label>
                    <p class="text-sm text-gray-900">{{ $role->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('roles.update', $role) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Role Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Role <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="Masukkan nama role" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Permissions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Permissions Saat Ini
                    </label>
                    <div class="bg-gray-50 rounded-lg p-4">
                        @if ($role->permissions->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach ($role->permissions as $permission)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Tidak ada permissions yang ditetapkan</p>
                        @endif
                    </div>
                </div>

                <!-- Permissions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Pilih Permissions Baru <span class="text-gray-500">(Opsional)</span>
                    </label>

                    <!-- Select All/None Buttons -->
                    <div class="flex space-x-2 mb-4">
                        <button type="button" onclick="selectAllPermissions()"
                            class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-md hover:bg-blue-200 transition-colors duration-200">
                            <i class="fas fa-check-square mr-1"></i>
                            Pilih Semua
                        </button>
                        <button type="button" onclick="deselectAllPermissions()"
                            class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors duration-200">
                            <i class="fas fa-square mr-1"></i>
                            Batal Pilih
                        </button>
                    </div>

                    <!-- Permissions Table -->
                    @php
                        // Ambil semua action yang ada dari semua permissions
                        $allActions = collect();
                        foreach ($permissions as $modulePermissions) {
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
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($permissions as $module => $modulePermissions)
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
                                                        <label class="flex items-center justify-center">
                                                            <input type="checkbox" name="permissions[]"
                                                                value="{{ $permission->name }}"
                                                                class="permission-checkbox module-{{ $module }} action-{{ $action }} rounded border-gray-300
                                                                @if ($action === 'view') text-cyan-600 focus:ring-cyan-500
                                                                @elseif($action === 'create') text-green-600 focus:ring-green-500
                                                                @elseif($action === 'update') text-yellow-600 focus:ring-yellow-500
                                                                @elseif($action === 'delete') text-red-600 focus:ring-red-500
                                                                @else text-purple-600 focus:ring-purple-500 @endif"
                                                                {{ in_array($permission->name, old('permissions', $role->permissions->pluck('name')->toArray())) ? 'checked' : '' }}>
                                                        </label>
                                                    @endforeach
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                        @endforeach

                                        <!-- Module Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-1">
                                                <button type="button"
                                                    onclick="selectModulePermissions('{{ $module }}')"
                                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <span class="text-gray-300">|</span>
                                                <button type="button"
                                                    onclick="deselectModulePermissions('{{ $module }}')"
                                                    class="text-xs text-gray-600 hover:text-gray-800 font-medium">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @error('permissions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('roles.index') }}"
                        class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectAllPermissions() {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectAllPermissions() {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        function selectModulePermissions(module) {
            document.querySelectorAll(`.module-${module}`).forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectModulePermissions(module) {
            document.querySelectorAll(`.module-${module}`).forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    </script>
</x-layouts.app>
