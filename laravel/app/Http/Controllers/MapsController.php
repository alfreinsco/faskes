<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faskes;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MapsController extends Controller
{
    /**
     * Display the maps page with health facilities
     */
    public function index(Request $request)
    {
        try {
            // Get query parameters for filtering
            $type = $request->get('type');
            $search = $request->get('search');

            // Build query
            $query = Faskes::active();

            // Filter by type if provided
            if ($type && $type !== 'all') {
                $query->where('type', $type);
            }

            // Search by name or address if provided
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('alamat', 'like', "%{$search}%");
                });
            }

            $faskes = $query->get();

            // Transform data for map markers
            $healthFacilities = $faskes->filter(function ($faskes) {
                // Only include facilities with valid coordinates
                return $faskes->latitude && $faskes->longitude &&
                       is_numeric($faskes->latitude) && is_numeric($faskes->longitude);
            })->map(function ($faskes) {
                return [
                    'id' => $faskes->id,
                    'name' => $faskes->nama,
                    'address' => $faskes->alamat,
                    'phone' => $faskes->no_telp,
                    'email' => $faskes->email,
                    'website' => $faskes->website,
                    'type' => $faskes->type,
                    'services' => $faskes->layanan,
                    'opening_hours' => $faskes->waktu_buka,
                    'closing_hours' => $faskes->waktu_tutup,
                    'image' => $faskes->gambar,
                    'lat' => (float) $faskes->latitude,
                    'lng' => (float) $faskes->longitude
                ];
            });

            // Get unique types for filter dropdown
            $types = Faskes::active()
                ->whereNotNull('type')
                ->distinct()
                ->pluck('type')
                ->filter()
                ->values();

            return view('maps', [
                'faskes' => $faskes,
                'healthFacilities' => $healthFacilities,
                'types' => $types,
                'selectedType' => $type,
                'searchQuery' => $search,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in MapsController@index: ' . $e->getMessage());

            return view('maps', [
                'faskes' => collect(),
                'healthFacilities' => collect(),
                'types' => collect(),
                'selectedType' => null,
                'searchQuery' => null,
                'error' => 'Terjadi kesalahan saat memuat data peta.'
            ]);
        }
    }

    /**
     * Get health facilities data as JSON for API calls
     */
    public function getFacilities(Request $request): JsonResponse
    {
        try {
            $type = $request->get('type');
            $search = $request->get('search');
            $bounds = $request->get('bounds'); // For map bounds filtering

            $query = Faskes::active();

            if ($type && $type !== 'all') {
                $query->where('type', $type);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('alamat', 'like', "%{$search}%");
                });
            }

            // Filter by map bounds if provided
            if ($bounds && is_array($bounds)) {
                $query->whereBetween('latitude', [$bounds['south'], $bounds['north']])
                      ->whereBetween('longitude', [$bounds['west'], $bounds['east']]);
            }

            $facilities = $query->get()->filter(function ($faskes) {
                return $faskes->latitude && $faskes->longitude &&
                       is_numeric($faskes->latitude) && is_numeric($faskes->longitude);
            })->map(function ($faskes) {
                return [
                    'id' => $faskes->id,
                    'name' => $faskes->nama,
                    'address' => $faskes->alamat,
                    'phone' => $faskes->no_telp,
                    'email' => $faskes->email,
                    'website' => $faskes->website,
                    'type' => $faskes->type,
                    'services' => $faskes->layanan,
                    'opening_hours' => $faskes->waktu_buka,
                    'closing_hours' => $faskes->waktu_tutup,
                    'image' => $faskes->gambar,
                    'lat' => (float) $faskes->latitude,
                    'lng' => (float) $faskes->longitude
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $facilities,
                'count' => $facilities->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in MapsController@getFacilities: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data fasilitas kesehatan.',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get facility details by ID
     */
    public function getFacilityDetails($id): JsonResponse
    {
        try {
            $facility = Faskes::active()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $facility->id,
                    'name' => $facility->nama,
                    'address' => $facility->alamat,
                    'phone' => $facility->no_telp,
                    'email' => $facility->email,
                    'website' => $facility->website,
                    'type' => $facility->type,
                    'services' => $facility->layanan,
                    'opening_hours' => $facility->waktu_buka,
                    'closing_hours' => $facility->waktu_tutup,
                    'image' => $facility->gambar,
                    'lat' => (float) $facility->latitude,
                    'lng' => (float) $facility->longitude
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in MapsController@getFacilityDetails: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Fasilitas kesehatan tidak ditemukan.'
            ], 404);
        }
    }
}
