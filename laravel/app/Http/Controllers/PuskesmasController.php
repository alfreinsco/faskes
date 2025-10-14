<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFaskesRequest;
use App\Http\Requests\UpdateFaskesRequest;
use App\Models\Faskes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PuskesmasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Faskes::where('type', 'Puskesmas');

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

        $puskesmas = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('faskes.index', compact('puskesmas'));
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
        $data['type'] = 'Puskesmas'; // Force type to Puskesmas

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

        return redirect()->route('puskesmas.index')
            ->with('success', 'Puskesmas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Faskes $puskesmas)
    {
        // Ensure it's a Puskesmas
        if ($puskesmas->type !== 'Puskesmas') {
            abort(404);
        }

        return view('faskes.show', compact('puskesmas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faskes $puskesmas)
    {
        // Ensure it's a Puskesmas
        if ($puskesmas->type !== 'Puskesmas') {
            abort(404);
        }

        return view('faskes.edit', compact('puskesmas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFaskesRequest $request, Faskes $puskesmas)
    {
        // Ensure it's a Puskesmas
        if ($puskesmas->type !== 'Puskesmas') {
            abort(404);
        }

        $data = $request->validated();
        $data['type'] = 'Puskesmas'; // Force type to Puskesmas

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($puskesmas->gambar && Storage::disk('public')->exists($puskesmas->gambar)) {
                Storage::disk('public')->delete($puskesmas->gambar);
            }

            $image = $request->file('gambar');
            $imageName = time().'_'.Str::slug($data['nama']).'.'.$image->getClientOriginalExtension();
            $imagePath = $image->storeAs('faskes', $imageName, 'public');
            $data['gambar'] = $imagePath;
        }

        // Set status
        $data['is_active'] = $request->has('is_active') ? true : false;

        $puskesmas->update($data);

        return redirect()->route('puskesmas.edit', $puskesmas)
            ->with(['swal' => [
                'title' => 'Berhasil!',
                'text' => 'Puskesmas berhasil diperbarui.',
                'icon' => 'success',
            ]]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faskes $puskesmas)
    {
        // Ensure it's a Puskesmas
        if ($puskesmas->type !== 'Puskesmas') {
            abort(404);
        }

        // Delete image if exists
        if ($puskesmas->gambar && Storage::disk('public')->exists($puskesmas->gambar)) {
            Storage::disk('public')->delete($puskesmas->gambar);
        }

        $puskesmas->delete();

        return redirect()->route('puskesmas.index')
            ->with('success', 'Puskesmas berhasil dihapus.');
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleStatus(Faskes $puskesmas)
    {
        // Ensure it's a Puskesmas
        if ($puskesmas->type !== 'Puskesmas') {
            abort(404);
        }

        $puskesmas->update(['is_active' => ! $puskesmas->is_active]);

        $status = $puskesmas->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Puskesmas berhasil {$status}.");
    }
}
