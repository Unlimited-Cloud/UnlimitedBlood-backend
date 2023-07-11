<?php

namespace Database\Factories;

use App\Models\Donations;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donations>
 */
class DonationsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bloodType' => fake()->randomElement(['Blood', 'Plasma', 'Platelets']),
            'quantity' => fake()->randomDigit(),
            'bloodGroup' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'donationDate' => fake()->date(),
            'upperBP' => fake()->randomDigit(),
            'lowerBP' => fake()->randomDigit(),
            'weight' => fake()->randomDigit(),
            'notes' => fake()->text(),
        ];
    }
}
