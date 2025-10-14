<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'alfreinsco@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('alfreinsco@gmail.com'),
                'phone' => '081318812027',
                'address' => 'Universitas Pattimura',
                'is_active' => true,
            ]
        );

        $superAdmin->assignRole('admin');
    }
}
