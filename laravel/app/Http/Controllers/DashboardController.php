<?php

namespace App\Http\Controllers;

use App\Models\Faskes;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics for all faskes
        $totalFaskes = Faskes::count();
        $activeFaskes = Faskes::where('is_active', true)->count();
        $inactiveFaskes = Faskes::where('is_active', false)->count();

        // Get statistics by type
        $rumahSakit = Faskes::where('type', 'Rumah Sakit')->count();
        $rumahSakitActive = Faskes::where('type', 'Rumah Sakit')->where('is_active', true)->count();

        $puskesmas = Faskes::where('type', 'Puskesmas')->count();
        $puskesmasActive = Faskes::where('type', 'Puskesmas')->where('is_active', true)->count();

        $apotek = Faskes::where('type', 'Apotek')->count();
        $apotekActive = Faskes::where('type', 'Apotek')->where('is_active', true)->count();

        // Get recent faskes
        $recentFaskes = Faskes::withTrashed()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get faskes with layanan
        $faskesWithLayanan = Faskes::whereNotNull('layanan')
            ->where('layanan', '!=', '[]')
            ->count();

        // Get total users
        $totalUsers = User::count();

        // Get faskes by month for chart
        $faskesByMonth = Faskes::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = $faskesByMonth[$i] ?? 0;
        }

        // Get faskes with coordinates for map
        $faskesForMap = Faskes::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '')
            ->get();

        return view('dashboard', compact(
            'totalFaskes',
            'activeFaskes',
            'inactiveFaskes',
            'rumahSakit',
            'rumahSakitActive',
            'puskesmas',
            'puskesmasActive',
            'apotek',
            'apotekActive',
            'recentFaskes',
            'faskesWithLayanan',
            'totalUsers',
            'monthlyData',
            'faskesForMap'
        ));
    }
}
