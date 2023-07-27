<?php

namespace Database\Seeders;

use App\Models\User;
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
        Permission::create(['name' => 'camps-access']);
        Permission::create(['name' => 'inventory-access']);
        Permission::create(['name' => 'organizations-access']);
        Permission::create(['name' => 'donations-access']);
        Permission::create(['name' => 'glossary-access']);
        Permission::create(['name' => 'user-access']);
        Permission::create(['name' => 'donors-access']);
        Permission::create(['name' => 'organizers-access']);

        // create roles and assign existing permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo('requests-access');
        $admin->givePermissionTo('camps-access');
        $admin->givePermissionTo('inventory-access');
        $admin->givePermissionTo('organizations-access');
        $admin->givePermissionTo('donations-access');
        $admin->givePermissionTo('glossary-access');
        $admin->givePermissionTo('user-access');
        $admin->givePermissionTo('donors-access');
        $admin->givePermissionTo('organizers-access');

        $donor = Role::create(['name' => 'donor']);
        $donor->givePermissionTo('camps-access');
        $donor->givePermissionTo('donations-access');
        $donor->givePermissionTo('requests-access');

        $organizer = Role::create(['name' => 'organizer']);
        $organizer->givePermissionTo('camps-access');
        $organizer->givePermissionTo('donations-access');
        $organizer->givePermissionTo('requests-access');
        $organizer->givePermissionTo('inventory-access');
        $organizer->givePermissionTo('organizers-access');

        $unverified = Role::create(['name' => 'unverified']);
        $unverified->givePermissionTo('organizations-access');

        $user1 = User::factory()->create([
            'name' => 'Test Admin',
            'phoneNumber' => '1234567890',
            'password' => bcrypt('password'),
        ]);
        $user1->assignRole($admin);

        $user2 = User::factory()->create([
            'name' => 'Test Donor1',
            'phoneNumber' => '0909090909',
            'password' => bcrypt('password'),
        ]);
        $user2->assignRole($donor);

        $user3 = User::factory()->create([
            'name' => 'Test Donor2',
            'phoneNumber' => '1111111111',
            'password' => bcrypt('password'),
        ]);
        $user3->assignRole($donor);

        $user4 = User::factory()->create([
            'name' => 'Test Donor3',
            'phoneNumber' => '1212121212',
            'password' => bcrypt('password'),
        ]);
        $user4->assignRole($donor);

        $user5 = User::factory()->create([
            'name' => 'Test Donor4',
            'phoneNumber' => '2323232323',
            'password' => bcrypt('password'),
        ]);
        $user5->assignRole($donor);

        $user6 = User::factory()->create([
            'name' => 'Test Organizer1',
            'phoneNumber' => '1010101010',
            'password' => bcrypt('password'),
        ]);
        $user6->assignRole($organizer);

        $user7 = User::factory()->create([
            'name' => 'Test Organizer2',
            'phoneNumber' => '2020202020',
            'password' => bcrypt('password'),
        ]);
        $user7->assignRole($organizer);

    }
}
