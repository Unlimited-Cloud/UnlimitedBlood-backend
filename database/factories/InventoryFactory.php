<?php

namespace Database\Factories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bloodType' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'donationType' => fake()->randomElement(['Whole Blood', 'Plasma', 'Platelets']),
            'quantity' => fake()->numberBetween(1, 1000),
            'price' => fake()->numberBetween(1, 1000),
        ];
    }
}
