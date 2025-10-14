<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - FASKES</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-base-200">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 flex items-center justify-center rounded-full bg-cyan-600 text-white">
                    <img src="{{ asset('img/logo-faskes.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-base-content">
                    FASKES
                </h2>
                <p class="mt-2 text-sm text-base-content/70">
                    Fasilitas Kesehatan
                </p>
            </div>

            <!-- Login Form -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title justify-center mb-6">Masuk ke Akun Anda</h3>

                    @if ($errors->any())
                        <div class="alert alert-error mb-4">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <div>
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Email</span>
                            </label>
                            <input type="email" name="email"
                                class="input input-bordered w-full @error('email') input-error @enderror"
                                value="{{ old('email') }}" placeholder="Masukkan email" required autofocus>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Password</span>
                            </label>
                            <input type="password" name="password"
                                class="input input-bordered w-full @error('password') input-error @enderror"
                                placeholder="Masukkan password" required>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">Ingat saya</span>
                                <input type="checkbox" name="remember"
                                    class="checkbox border-cyan-600 text-cyan-600 input-xs">
                            </label>
                        </div>

                        <div class="form-control">
                            <button type="submit" class="btn btn-primary w-full">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Masuk
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-sm text-base-content/70">
                <p>&copy; {{ date('Y') }} FASKES - Fasilitas Kesehatan</p>
            </div>
        </div>
    </div>
</body>

</html>
