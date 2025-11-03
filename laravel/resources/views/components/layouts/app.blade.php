<!doctype html>
<html lang="id" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'FASKES - Fasilitas Kesehatan' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo-faskes-biru.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <div class="drawer lg:drawer-open">
        <!-- Mobile menu toggle -->
        <input id="drawer-toggle" type="checkbox" class="drawer-toggle" />

        <!-- Page content -->
        <div class="drawer-content flex flex-col">
            <!-- Top Navigation -->
            <div class="navbar bg-white shadow-sm border-b border-gray-200">
                <div class="flex-none lg:hidden">
                    <label for="drawer-toggle" class="btn btn-square btn-ghost">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </label>
                </div>

                <div class="flex-1">
                    <h1 class="text-xl font-semibold text-gray-800">{{ $pageTitle ?? 'Dashboard' }}</h1>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    @method('POST')
                    <button onclick="return confirm('Apakah Anda yakin ingin keluar?')"
                        class="btn btn-danger bg-red-500 text-white hover:bg-red-600 rounded-full">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </button>
                </form>
            </div>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                @if (session('success'))
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error mb-4">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        <!-- Sidebar -->
        <div class="drawer-side">
            <label for="drawer-toggle" class="drawer-overlay"></label>
            <aside class="w-64 min-h-full bg-gradient-to-b from-cyan-600 to-cyan-700 text-white">
                <!-- Logo -->
                <div class="p-6 border-b border-cyan-500">
                    <div class="flex items-center space-x-3">
                        <div class="w-15 h-15 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('img/logo-faskes-biru.png') }}" alt="Logo"
                                class="w-full h-full object-contain" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold">FASKES</h2>
                            <p class="text-cyan-200 text-sm">Fasilitas Kesehatan</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="p-4">
                    <ul class="space-y-2">
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-cyan-100' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z">
                                    </path>
                                </svg>
                                <span class="font-medium">Dashboard</span>
                            </a>
                        </li>

                        <!-- Rumah Sakit -->
                        <li>
                            <a href="{{ route('rumah-sakit.index') }}"
                                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors duration-200 {{ request()->routeIs('rumah-sakit.*') ? 'bg-white/20 text-white' : 'text-cyan-100' }}">
                                <i class="fas fa-hospital-alt"></i>
                                <span class="font-medium">Rumah Sakit</span>
                            </a>
                        </li>

                        <!-- Puskesmas -->
                        <li>
                            <a href="{{ route('puskesmas.index') }}"
                                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors duration-200 {{ request()->routeIs('puskesmas.*') ? 'bg-white/20 text-white' : 'text-cyan-100' }}">
                                <i class="fas fa-clinic-medical"></i>
                                <span class="font-medium">Puskesmas</span>
                            </a>
                        </li>

                        <!-- Apotek -->
                        <li>
                            <a href="{{ route('apotek.index') }}"
                                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors duration-200 {{ request()->routeIs('apotek.*') ? 'bg-white/20 text-white' : 'text-cyan-100' }}">
                                <i class="fas fa-pills"></i>
                                <span class="font-medium">Apotek</span>
                            </a>
                        </li>

                        <!-- Divider -->
                        <li class="pt-4">
                            <div class="border-t border-cyan-500"></div>
                        </li>

                        @can('view users')
                            <!-- Management Users -->
                            <li>
                                <a href="{{ route('users.index') }}"
                                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors duration-200 {{ request()->routeIs('users.*') ? 'bg-white/20 text-white' : 'text-cyan-100' }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                        </path>
                                    </svg>
                                    <span class="font-medium">Data Pengguna</span>
                                </a>
                            </li>
                        @endcan

                        <!-- Relase App -->
                        <li>
                            <a href="{{ route('aplikasi') }}"
                                class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors duration-200 {{ request()->routeIs('release-app') ? 'bg-white/20 text-white' : 'text-cyan-100' }}">
                                <i class="fas fa-download"></i>
                                <span class="font-medium">Aplikasi Mobile</span>
                            </a>
                        </li>


                        {{-- @can('view roles')
                            <!-- Management Roles -->
                            <li>
                                <a href="{{ route('roles.index') }}"
                                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors duration-200 {{ request()->routeIs('roles.*') ? 'bg-white/20 text-white' : 'text-cyan-100' }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                                            clip-rule="evenodd"></path>
                                        <path
                                            d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z">
                                        </path>
                                    </svg>
                                    <span class="font-medium">Role</span>
                                </a>
                            </li>
                        @endcan

                        @can('view permissions')
                            <!-- Permission Management -->
                            <li>
                                <a href="{{ route('permissions.index') }}"
                                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors duration-200 {{ request()->routeIs('permissions.*') ? 'bg-white/20 text-white' : 'text-cyan-100' }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">Permission</span>
                                </a>
                            </li>
                        @endcan --}}
                    </ul>
                </nav>

                <!-- User Info -->
                <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-cyan-500">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-cyan-200">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Leaflet JS -->
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle SweetAlert from session
            @if (session('swal'))
                const swalData = @json(session('swal'));
                Swal.fire({
                    title: swalData.title,
                    text: swalData.text,
                    icon: swalData.icon,
                    confirmButtonColor: swalData.icon === 'success' ? '#10b981' : swalData.icon ===
                        'error' ? '#ef4444' : swalData.icon === 'warning' ? '#f59e0b' : '#3b82f6',
                    timer: swalData.icon === 'success' ? 3000 : null,
                    timerProgressBar: swalData.icon === 'success' ? true : false,
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>

    {{ $scripts ?? '' }}
</body>

</html>
