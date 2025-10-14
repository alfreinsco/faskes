<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Permission::with('roles');

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Module filter
        if ($request->filled('module')) {
            $query->where('name', 'like', '%'.$request->module.'%');
        }

        $permissions = $query->orderBy('name')->paginate(20);

        $groupedPermissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[1] ?? 'other';
        });

        return view('permissions.index', compact('permissions', 'groupedPermissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'required|string|max:255',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);

        return redirect()->route('permissions.index')
            ->with('swal', [
                'title' => 'Berhasil!',
                'text' => 'Permission berhasil ditambahkan.',
                'icon' => 'success',
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');

        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$permission->id,
            'guard_name' => 'required|string|max:255',
        ]);

        $permission->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);

        return redirect()->route('permissions.edit', $permission)
            ->with('swal', [
                'title' => 'Berhasil!',
                'text' => 'Permission berhasil diperbarui.',
                'icon' => 'success',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is being used by roles
        if ($permission->roles()->count() > 0) {
            return redirect()->back()
                ->with('swal', [
                    'title' => 'Error!',
                    'text' => 'Permission tidak dapat dihapus karena sedang digunakan oleh '.$permission->roles()->count().' role.',
                    'icon' => 'error',
                ]);
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('swal', [
                'title' => 'Berhasil!',
                'text' => 'Permission berhasil dihapus.',
                'icon' => 'success',
            ]);
    }

    /**
     * Bulk create permissions
     */
    public function bulkCreate()
    {
        return view('permissions.bulk-create');
    }

    /**
     * Store bulk permissions
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'permissions' => 'required|string',
            'guard_name' => 'required|string|max:255',
        ]);

        $permissions = array_filter(array_map('trim', explode("\n", $request->permissions)));
        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($permissions as $permission) {
            if (empty($permission)) {
                continue;
            }

            try {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $request->guard_name,
                ]);
                $created++;
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = "Error creating '{$permission}': ".$e->getMessage();
            }
        }

        $message = "{$created} permission berhasil dibuat";
        if ($skipped > 0) {
            $message .= ", {$skipped} permission dilewati";
        }

        return redirect()->route('permissions.index')
            ->with('swal', [
                'title' => 'Berhasil!',
                'text' => $message,
                'icon' => 'success',
            ]);
    }
}
