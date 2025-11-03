<x-layouts.app>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Aplikasi Mobile</h1>
            </div>

            @if (count($files) > 0)
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama File</th>
                                <th>Ukuran</th>
                                <th>Tanggal Modifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($files as $index => $file)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-file-archive text-cyan-600"></i>
                                            <span class="font-medium text-gray-900">{{ $file['name'] }}</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($file['size'] / 1024 / 1024, 2) }} MB</td>
                                    <td>{{ date('d M Y H:i', $file['modified']) }}</td>
                                    <td>
                                        <a href="{{ $file['path'] }}" download
                                            class="btn btn-sm btn-primary bg-cyan-600 hover:bg-cyan-700 text-white">
                                            <i class="fas fa-download mr-2"></i>
                                            Unduh
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-folder-open text-gray-400 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">Tidak ada file tersedia</p>
                    <p class="text-gray-400 text-sm mt-2">File APK akan muncul di sini setelah diunggah ke folder
                        release</p>
                </div>
            @endif
        </div>
    </div>
    <x-slot name="scripts">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Aplikasi Mobile - {{ count($files) }} file tersedia');
            });
        </script>
    </x-slot>
</x-layouts.app>
