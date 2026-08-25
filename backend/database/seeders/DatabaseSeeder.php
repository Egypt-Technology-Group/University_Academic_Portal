<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles and Permissions setup
        $roles = [
            'super-admin',
            'college-admin',
            'faculty',
            'student',
            'admissions-officer',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Create Super Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@university.edu.eg'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('SuperAdmin@2025!'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('super-admin');

        // 2. Call Domain Seeders
        $this->call([
            AcademicSeeder::class,
            ContentAndAdmissionsSeeder::class,
        ]);
    }
}
