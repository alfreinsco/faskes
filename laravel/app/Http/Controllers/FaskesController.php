<?php

namespace App\Http\Controllers;

use App\Models\Faskes;
use Illuminate\Http\Request;

class FaskesController extends Controller
{
    public function index(Request $request)
    {
        $faskes = Faskes::query();
        $faskes = $faskes->active();

        if ($request->has('search') && $request->search != '') {
            $faskes = $faskes->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->has('type') && $request->type != '') {
            $faskes = $faskes->where('type', $request->type);
        }

        if ($request->has('status') && $request->status != '') {
            $faskes = $faskes->where('is_active', $request->status);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data faskes berhasil diambil',
            'data' => $faskes->paginate(10)
        ]);
    }
}
