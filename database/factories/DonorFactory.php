<?php

namespace Database\Factories;

use App\Models\Donor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donor>
 */
class DonorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'fname' => fake()->firstName(),
            'lname' => fake()->lastName(),
            'password' => bcrypt('password'),
            'bloodType' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'address' => fake()->address(),
            'gender' => fake()->randomElement(['Male', 'Female', 'Other']),
            'birthDate' => fake()->date(),
            'profilePicture' => fake()->imageUrl(),
        ];
    }
}
