<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RumahSakitController;
use App\Http\Controllers\PuskesmasController;
use App\Http\Controllers\ApotekController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MapsController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/maps', [MapsController::class, 'index'])->name('maps');
Route::get('/api/facilities', [MapsController::class, 'getFacilities'])->name('api.facilities');
Route::get('/api/facilities/{id}', [MapsController::class, 'getFacilityDetails'])->name('api.facility.details');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:view dashboard');


    // Rumah Sakit Management
    Route::resource('rumah-sakit', RumahSakitController::class)->parameters(['rumah-sakit' => 'rumahSakit']);
    Route::patch('rumah-sakit/{rumahSakit}/toggle-status', [RumahSakitController::class, 'toggleStatus'])->name('rumah-sakit.toggle-status');

    // Puskesmas Management
    Route::resource('puskesmas', PuskesmasController::class)->parameters(['puskesmas' => 'puskesmas']);
    Route::patch('puskesmas/{puskesmas}/toggle-status', [PuskesmasController::class, 'toggleStatus'])->name('puskesmas.toggle-status');

    // Apotek Management
    Route::resource('apotek', ApotekController::class)->parameters(['apotek' => 'apotek']);
    Route::patch('apotek/{apotek}/toggle-status', [ApotekController::class, 'toggleStatus'])->name('apotek.toggle-status');

    // User Management
    Route::resource('users', UserController::class)
        ->middleware(['permission:view users', 'permission:create users', 'permission:edit users', 'permission:delete users']);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('users/{user}/assign-roles', [UserController::class, 'assignRoles'])->name('users.assign-roles');
    Route::post('users/{user}/assign-roles', [UserController::class, 'storeRoles'])->name('users.store-roles');
    Route::get('users/{user}/assign-permissions', [UserController::class, 'assignPermissions'])->name('users.assign-permissions');
    Route::post('users/{user}/assign-permissions', [UserController::class, 'storePermissions'])->name('users.store-permissions');
    Route::get('users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
    Route::post('users/{user}/change-password', [UserController::class, 'storePassword'])->name('users.store-password');

    // Role Management
    Route::resource('roles', RoleController::class)
        ->middleware(['permission:view roles', 'permission:create roles', 'permission:edit roles', 'permission:delete roles']);

    // Permission Management
    Route::resource('permissions', PermissionController::class)
        ->middleware(['permission:view permissions', 'permission:create permissions', 'permission:edit permissions', 'permission:delete permissions']);
    Route::get('permissions/bulk/create', [PermissionController::class, 'bulkCreate'])->name('permissions.bulk-create');
    Route::post('permissions/bulk/store', [PermissionController::class, 'bulkStore'])->name('permissions.bulk-store');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
