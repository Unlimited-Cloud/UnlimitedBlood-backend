<?php

namespace Database\Seeders;

use App\Models\Camps;
use App\Models\Donations;
use App\Models\Donor;
use App\Models\Inventory;
use App\Models\Organizations;
use App\Models\Requests;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{

    public function run(): void
    {
        Organizations::factory()->create([
            'name' => 'Test Organization1',
            'phoneNumber' => '3333333333',
            'user_id' => 4,
        ]);

        Organizations::factory()->create([
            'name' => 'Test Organization2',
            'phoneNumber' => '2222222222',
            'user_id' => 5,
        ]);

        Donor::factory()->create([
            'phoneNumber' => '0909090909',
            'password' => bcrypt('password'),
            'user_id' => 2
        ]);

        Donor::factory()->create([
            'phoneNumber' => '1111111111',
            'password' => bcrypt('password'),
            'user_id' => 3
        ]);

        Inventory::factory()->create([
            'organizationId' => 1,
            'bloodType' => 'A+',
            'donationType' => 'Whole Blood',
            'quantity' => 100,
            'price' => 100

        ]);

        Inventory::factory()->create([

            'organizationId' => 2,
            'bloodType' => 'B+',
            'donationType' => 'Whole Blood',
            'quantity' => 200,
            'price' => 200

        ]);

        Requests::factory()->create([
            'phoneNumber' => '1111111111',
        ]);

        Requests::factory()->create([
            'phoneNumber' => '0909090909',

        ]);

        Camps::factory()->create([
            'name' => 'Test Camp1',
            'attendees' => 0,
            'organizationId' => 1,
        ]);

        Donations::factory()->create([
            'organizationId' => 1,
            'campId' => 1,
            'phoneNumber' => '1111111111',

        ]);
    }
}
