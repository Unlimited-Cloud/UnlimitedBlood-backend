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
        Permission::create(['name' => 'requests-access']);
        Permission::create(['name' => 'camp-donors-access']);
        Permission::create(['name' => 'camps-access']);
        Permission::create(['name' => 'inventory-access']);
        Permission::create(['name' => 'organizations-access']);
        Permission::create(['name' => 'donations-access']);
        Permission::create(['name' => 'glossary-access']);
        Permission::create(['name' => 'user-access']);
        Permission::create(['name' => 'donor-access']);

        // create roles and assign existing permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo('requests-access');
        $admin->givePermissionTo('camp-donors-access');
        $admin->givePermissionTo('camps-access');
        $admin->givePermissionTo('inventory-access');
        $admin->givePermissionTo('organizations-access');
        $admin->givePermissionTo('donations-access');
        $admin->givePermissionTo('glossary-access');
        $admin->givePermissionTo('user-access');
        $admin->givePermissionTo('donor-access');

        $donor = Role::create(['name' => 'donor']);
        $donor->givePermissionTo('camps-access');
        $donor->givePermissionTo('donations-access');
        $donor->givePermissionTo('requests-access');

        $organization = Role::create(['name' => 'organization']);
        $organization->givePermissionTo('camps-access');
        $organization->givePermissionTo('donations-access');
        $organization->givePermissionTo('requests-access');
        $organization->givePermissionTo('camp-donors-access');
        $organization->givePermissionTo('inventory-access');
        $organization->givePermissionTo('organizations-access');

        $user1 = \App\Models\User::factory()->create([
            'name' => 'Test Admin',
            'phoneNumber' => '1234567890',
            'password' => bcrypt('password'),
        ]);
        $user1->assignRole($admin);

        $user2 = \App\Models\User::factory()->create([
            'name' => 'Test Donor',
            'phoneNumber' => '0909090909',
            'password' => bcrypt('password'),
        ]);
        $user2->assignRole($donor);

        $user3 = \App\Models\User::factory()->create([
            'name' => 'Test Organization',
            'phoneNumber' => '1111111111',
            'password' => bcrypt('password'),
        ]);
        $user3->assignRole($organization);

    }
}
