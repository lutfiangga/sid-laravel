<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');
        $roles = Role::all();

        // Create standard test user if not exists
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User (SuperAdmin)',
                'password' => $password,
                'email_verified_at' => now(),
            ]
        );
        $testUser->assignRole('SuperAdmin');

        // Create a user for each role
        foreach ($roles as $role) {
            $slug = strtolower($role->name);
            $user = User::firstOrCreate(
                ['email' => "{$slug}@example.com"],
                [
                    'name' => "{$role->name} User",
                    'password' => $password,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($role);
        }
    }
}
