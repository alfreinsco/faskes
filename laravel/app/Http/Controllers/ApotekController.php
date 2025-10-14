<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFaskesRequest;
use App\Http\Requests\UpdateFaskesRequest;
use App\Models\Faskes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApotekController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Faskes::where('type', 'Apotek');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('no_telp', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        $apotek = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('faskes.index', compact('apotek'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('faskes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFaskesRequest $request)
    {
        $data = $request->validated();
        $data['type'] = 'Apotek'; // Force type to Apotek

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time().'_'.Str::slug($data['nama']).'.'.$image->getClientOriginalExtension();
            $imagePath = $image->storeAs('faskes', $imageName, 'public');
            $data['gambar'] = $imagePath;
        }

        // Set default values
        $data['is_active'] = $request->has('is_active') ? true : false;

        Faskes::create($data);

        return redirect()->route('apotek.index')
            ->with('success', 'Apotek berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Faskes $apotek)
    {
        // Ensure it's an Apotek
        if ($apotek->type !== 'Apotek') {
            abort(404);
        }

        return view('faskes.show', compact('apotek'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faskes $apotek)
    {
        // Ensure it's an Apotek
        if ($apotek->type !== 'Apotek') {
            abort(404);
        }

        return view('faskes.edit', compact('apotek'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFaskesRequest $request, Faskes $apotek)
    {
        // Ensure it's an Apotek
        if ($apotek->type !== 'Apotek') {
            abort(404);
        }

        $data = $request->validated();
        $data['type'] = 'Apotek'; // Force type to Apotek

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($apotek->gambar && Storage::disk('public')->exists($apotek->gambar)) {
                Storage::disk('public')->delete($apotek->gambar);
            }

            $image = $request->file('gambar');
            $imageName = time().'_'.Str::slug($data['nama']).'.'.$image->getClientOriginalExtension();
            $imagePath = $image->storeAs('faskes', $imageName, 'public');
            $data['gambar'] = $imagePath;
        }

        // Set status
        $data['is_active'] = $request->has('is_active') ? true : false;

        $apotek->update($data);

        return redirect()->route('apotek.edit', $apotek)
            ->with(['swal' => [
                'title' => 'Berhasil!',
                'text' => 'Apotek berhasil diperbarui.',
                'icon' => 'success',
            ]]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faskes $apotek)
    {
        // Ensure it's an Apotek
        if ($apotek->type !== 'Apotek') {
            abort(404);
        }

        // Delete image if exists
        if ($apotek->gambar && Storage::disk('public')->exists($apotek->gambar)) {
            Storage::disk('public')->delete($apotek->gambar);
        }

        $apotek->delete();

        return redirect()->route('apotek.index')
            ->with('success', 'Apotek berhasil dihapus.');
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleStatus(Faskes $apotek)
    {
        // Ensure it's an Apotek
        if ($apotek->type !== 'Apotek') {
            abort(404);
        }

        $apotek->update(['is_active' => ! $apotek->is_active]);

        $status = $apotek->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Apotek berhasil {$status}.");
    }
}
