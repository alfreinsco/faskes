<x-layouts.app>
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('permissions.index') }}"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Kembali
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $permission->name }}</h1>
                        <p class="text-gray-600 mt-1">Detail informasi permission</p>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('permissions.edit', $permission) }}"
                        class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Permission
                    </a>
                </div>
            </div>
        </div>

        <!-- Permission Status Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-20 h-20 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                            <i class="fas fa-key text-white text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $permission->name }}</h2>
                            <p class="text-gray-600 flex items-center">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Guard: {{ $permission->guard_name }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-key mr-2"></i>
                            Permission
                        </span>

                        <div class="text-sm text-gray-500 mt-2">
                            Module: {{ explode(' ', $permission->name)[1] ?? 'other' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permission Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Basic Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Permission Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-sm"></i>
                            </div>
                            Informasi Permission
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Permission</label>
                                <p class="text-gray-900 font-medium">{{ $permission->name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Guard Name</label>
                                <p class="text-gray-900">{{ $permission->guard_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Module</label>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ explode(' ', $permission->name)[1] ?? 'other' }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Permission ID</label>
                                <p class="text-gray-900 font-mono text-sm">{{ $permission->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roles Using This Permission -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3">
                                <i class="fas fa-user-shield text-sm"></i>
                            </div>
                            Roles yang Menggunakan Permission Ini
                        </h3>

                        @if ($permission->roles->count() > 0)
                            <div class="space-y-3">
                                @foreach ($permission->roles as $role)
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
                                <p class="text-gray-500">Tidak ada role yang menggunakan permission ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Permission Usage Stats -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-3">
                                <i class="fas fa-chart-bar text-sm"></i>
                            </div>
                            Usage Statistics
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $permission->roles->count() }}</div>
                                <div class="text-sm text-gray-500">Roles</div>
                            </div>

                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ $permission->created_at->diffInDays(now()) }}</div>
                                <div class="text-sm text-gray-500">Days Old</div>
                            </div>

                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ $permission->updated_at->diffInDays($permission->created_at) }}</div>
                                <div class="text-sm text-gray-500">Days Since Update</div>
                            </div>
                        </div>
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
                                    <p class="font-medium text-gray-900">Permission dibuat</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $permission->created_at->format('d M Y, H:i:s') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Last Updated -->
                            @if ($permission->updated_at != $permission->created_at)
                                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                        <i class="fas fa-edit text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Permission diperbarui</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $permission->updated_at->format('d M Y, H:i:s') }}
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
                            <a href="{{ route('permissions.edit', $permission) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Permission
                            </a>

                            <button
                                onclick="konfirmasiHapus('{{ route('permissions.destroy', $permission->id) }}', '{{ $permission->name }}')"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus Permission
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Permission Stats -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3">
                                <i class="fas fa-chart-bar text-sm"></i>
                            </div>
                            Permission Stats
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Module</span>
                                <span class="font-medium">
                                    {{ explode(' ', $permission->name)[1] ?? 'other' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Guard</span>
                                <span class="font-medium">
                                    {{ $permission->guard_name }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Roles</span>
                                <span class="font-medium">
                                    {{ $permission->roles->count() }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Created</span>
                                <span class="font-medium text-sm">
                                    {{ $permission->created_at->format('d M Y') }}
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
                                <span class="text-gray-600">Permission ID</span>
                                <span class="font-mono text-xs">{{ $permission->id }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Guard Type</span>
                                <span>{{ $permission->guard_name }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Last Updated</span>
                                <span>{{ $permission->updated_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</x-layouts.app>
