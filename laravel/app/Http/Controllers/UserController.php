<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'permissions']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)->whereNotNull('email_verified_at');
            } elseif ($request->status === 'pending') {
                $query->where('is_active', true)->whereNull('email_verified_at');
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('users.create', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign roles
        if ($request->has('roles')) {
            $user->assignRole($request->roles);
        }

        // Assign permissions
        if ($request->has('permissions')) {
            $user->givePermissionTo($request->permissions);
        }

        return redirect()->route('users.index')
            ->with('swal', [
                'title' => 'Berhasil!',
                'text' => 'User berhasil ditambahkan.',
                'icon' => 'success',
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'permissions']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $user->load(['roles', 'permissions']);

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        $user->update($userData);

        return redirect()->route('users.edit', $user)
            ->with('swal', [
                'title' => 'Berhasil!',
                'text' => 'User berhasil diperbarui.',
                'icon' => 'success',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('swal', [
                    'title' => 'Error!',
                    'text' => 'Anda tidak dapat menghapus akun sendiri.',
                    'icon' => 'error',
                ]);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('swal', [
                'title' => 'Berhasil!',
                'text' => 'User berhasil dihapus.',
                'icon' => 'success',
            ]);
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        // Prevent toggling own account
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('swal', [
                    'title' => 'Error!',
                    'text' => 'Anda tidak dapat mengubah status akun sendiri.',
                    'icon' => 'error',
                ]);
        }

        $user->update([
            'is_active' => $user->is_active ? false : true,
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('swal', [
                'title' => 'Berhasil!',
                'text' => "User berhasil {$status}.",
                'icon' => 'success',
            ]);
    }

    /**
     * Show assign roles form
     */
    public function assignRoles(User $user)
    {
        $roles = Role::all();
        $user->load('roles');

        return view('users.assign-roles', compact('user', 'roles'));
    }

    /**
     * Store assigned roles
     */
    public function storeRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('users.assign-roles', $user)
            ->with('swal', [
                'title' => 'Berhasil!',
                'text' => 'Role user berhasil diperbarui.',
                'icon' => 'success',
            ]);
    }

    /**
     * Show assign permissions form
     */
    public function assignPermissions(User $user)
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[1] ?? 'other';
        });
        $user->load('permissions');

        return view('users.assign-permissions', compact('user', 'permissions'));
    }

    /**
     * Store assigned permissions
     */
    public function storePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        } else {
            $user->syncPermissions([]);
        }

        return redirect()->route('users.assign-permissions', $user)
            ->with('swal', [
                'title' => 'Berhasil!',
                'text' => 'Permission user berhasil diperbarui.',
                'icon' => 'success',
            ]);
    }

    /**
     * Show change password form
     */
    public function changePassword(User $user)
    {
        return view('users.change-password', compact('user'));
    }

    /**
     * Store new password
     */
    public function storePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.show', $user)
            ->with('swal', [
                'title' => 'Berhasil!',
                'text' => 'Password user berhasil diperbarui.',
                'icon' => 'success',
            ]);
    }
}
