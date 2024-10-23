<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a superadmin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);

        // Create a permission if it doesn't exist
        $managePermissions = Permission::firstOrCreate(['name' => 'manage_permissions']);

        // Assign the permission to the superadmin role
        $superAdminRole->givePermissionTo($managePermissions);

        // Create a superadmin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('Admin@123'), // Change 'password' to the desired password
        ]);

        $superAdmin->assignRole($superAdminRole);
    }
}
