@extends('layout')

@section('title', 'Home - SMUB')
@section('page-title', 'Home')

@section('content')
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="hero bg-base-200 rounded-lg">
            <div class="hero-content text-center">
                <div class="max-w-md">
                    <h1 class="text-5xl font-bold text-primary">SMUB</h1>
                    <p class="py-6 text-lg">
                        Sistem Manajemen Unit Bisnis Universitas Pattimura
                    </p>
                    <p class="text-base-content/70">
                        Kelola unit bisnis, transaksi, inventaris, dan laporan dengan mudah
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="stat bg-base-100 shadow rounded-lg">
                <div class="stat-figure text-primary">
                    <i class="fas fa-building text-3xl"></i>
                </div>
                <div class="stat-title">Unit Bisnis</div>
                <div class="stat-value text-primary">{{ \App\Models\BusinessUnit::count() }}</div>
                <div class="stat-desc">Total unit aktif</div>
            </div>

            <div class="stat bg-base-100 shadow rounded-lg">
                <div class="stat-figure text-success">
                    <i class="fas fa-receipt text-3xl"></i>
                </div>
                <div class="stat-title">Transaksi</div>
                <div class="stat-value text-success">{{ \App\Models\Transaction::count() }}</div>
                <div class="stat-desc">Total transaksi</div>
            </div>

            <div class="stat bg-base-100 shadow rounded-lg">
                <div class="stat-figure text-warning">
                    <i class="fas fa-users text-3xl"></i>
                </div>
                <div class="stat-title">Staf</div>
                <div class="stat-value text-warning">{{ \App\Models\Staff::count() }}</div>
                <div class="stat-desc">Total karyawan</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title mb-4">
                    <i class="fas fa-bolt text-primary"></i>
                    Aksi Cepat
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @can('create transactions')
                        <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-outline">
                            <i class="fas fa-plus mr-2"></i>Tambah Transaksi
                        </a>
                    @endcan

                    @can('create inventories')
                        <a href="{{ route('inventories.create') }}" class="btn btn-info btn-outline">
                            <i class="fas fa-box mr-2"></i>Tambah Inventaris
                        </a>
                    @endcan

                    @can('create staff')
                        <a href="{{ route('staff.create') }}" class="btn btn-success btn-outline">
                            <i class="fas fa-user-plus mr-2"></i>Tambah Staf
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title mb-4">
                    <i class="fas fa-clock text-primary"></i>
                    Aktivitas Terbaru
                </h2>

                <div class="space-y-4">
                    @php
                        $recentTransactions = \App\Models\Transaction::with(['businessUnit', 'creator'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp

                    @if ($recentTransactions->count() > 0)
                        @foreach ($recentTransactions as $transaction)
                            <div class="flex items-center gap-4 p-3 bg-base-200 rounded-lg">
                                <div class="avatar">
                                    <div
                                        class="w-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">{{ $transaction->customer_name }}</div>
                                    <div class="text-sm text-base-content/70">
                                        {{ $transaction->businessUnit->name }} • @currency($transaction->amount)
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-base-content/70">
                                        {{ $transaction->created_at->diffForHumans() }}
                                    </div>
                                    <div class="badge badge-sm">
                                        {{ ucfirst($transaction->status) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-base-content/70">
                            <i class="fas fa-inbox text-4xl mb-4"></i>
                            <p>Belum ada aktivitas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
