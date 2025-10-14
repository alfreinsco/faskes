<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFaskesRequest;
use App\Http\Requests\UpdateFaskesRequest;
use App\Models\Faskes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RumahSakitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Faskes::where('type', 'Rumah Sakit');

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

        $rumahSakit = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('faskes.index', compact('rumahSakit'));
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
        $data['type'] = 'Rumah Sakit'; // Force type to Rumah Sakit

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time().'_'.Str::slug($data['nama']).'.'.$image->getClientOriginalExtension();
            $imagePath = $image->storeAs('faskes', $imageName, 'public');
            $data['gambar'] = $imagePath;
        }

        Faskes::create($data);

        return redirect()->route('rumah-sakit.index')
            ->with('success', 'Rumah Sakit berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Faskes $rumahSakit)
    {
        // Ensure it's a Rumah Sakit
        if ($rumahSakit->type !== 'Rumah Sakit') {
            abort(404);
        }

        return view('faskes.show', compact('rumahSakit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faskes $rumahSakit)
    {
        // Ensure it's a Rumah Sakit
        if ($rumahSakit->type !== 'Rumah Sakit') {
            abort(404);
        }

        return view('faskes.edit', compact('rumahSakit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFaskesRequest $request, Faskes $rumahSakit)
    {
        // Ensure it's a Rumah Sakit
        if ($rumahSakit->type !== 'Rumah Sakit') {
            abort(404);
        }

        $data = $request->validated();
        $data['type'] = 'Rumah Sakit'; // Force type to Rumah Sakit

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($rumahSakit->gambar && Storage::disk('public')->exists($rumahSakit->gambar)) {
                Storage::disk('public')->delete($rumahSakit->gambar);
            }

            $image = $request->file('gambar');
            $imageName = time().'_'.Str::slug($data['nama']).'.'.$image->getClientOriginalExtension();
            $imagePath = $image->storeAs('faskes', $imageName, 'public');
            $data['gambar'] = $imagePath;
        }

        $rumahSakit->update($data);

        return redirect()->route('rumah-sakit.edit', $rumahSakit)
            ->with(['swal' => [
                'title' => 'Berhasil!',
                'text' => 'Rumah Sakit berhasil diperbarui.',
                'icon' => 'success',
            ]]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faskes $rumahSakit)
    {
        // Ensure it's a Rumah Sakit
        if ($rumahSakit->type !== 'Rumah Sakit') {
            abort(404);
        }

        // Delete image if exists
        if ($rumahSakit->gambar && Storage::disk('public')->exists($rumahSakit->gambar)) {
            Storage::disk('public')->delete($rumahSakit->gambar);
        }

        $rumahSakit->delete();

        return redirect()->route('rumah-sakit.index')
            ->with('success', 'Rumah Sakit berhasil dihapus.');
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleStatus(Faskes $rumahSakit)
    {
        // Ensure it's a Rumah Sakit
        if ($rumahSakit->type !== 'Rumah Sakit') {
            abort(404);
        }

        $rumahSakit->update(['is_active' => ! $rumahSakit->is_active]);

        $status = $rumahSakit->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Rumah Sakit berhasil {$status}.");
    }
}
