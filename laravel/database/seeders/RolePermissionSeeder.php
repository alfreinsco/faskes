<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management permissions
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Role management permissions
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Permission management permissions
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',

            // Category management permissions
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            // Report permissions
            'view reports',
            'export reports',

            // Faskes management permissions
            'view faskes',
            'create faskes',
            'edit faskes',
            'delete faskes',

            // Dashboard permissions
            'view dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());
    }
}
