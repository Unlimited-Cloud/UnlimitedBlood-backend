<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'add users']);

        // create roles and assign existing permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo('edit users');
        $admin->givePermissionTo('delete users');
        $admin->givePermissionTo('add users');

        $donor = Role::create(['name' => 'donor']);
        $donor->givePermissionTo('edit users');

        $user = \App\Models\User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@gmail.com'
        ]);
        $user->assignRole($admin);

        $user = \App\Models\User::factory()->create([
            'name' => 'Test Donor',
            'email' => 'donor@gmail.com'
        ]);
        $user->assignRole($donor);

    }
}
