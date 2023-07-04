<?php

namespace Database\Factories;

use App\Models\Camps;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Camps>
 */
class CampsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'address' => fake()->address(),
            'startDate' => fake()->date(),
            'endDate' => fake()->date(),
            'attendees' => 0,
            'pictures' => fake()->imageUrl(),
        ];
    }
}
