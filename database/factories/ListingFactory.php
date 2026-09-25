<?php

namespace Database\Factories;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Listing>
 */
class ListingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'price' => fake()->randomFloat(2, 50000, 5000000),
            'type' => fake()->randomElement(['rent', 'sale', 'shortlet']),
            'bedrooms' => fake()->numberBetween(0, 5),
            'location' => fake()->city(),
            'latitude' => fake()->latitude(-90, 90),
            'longitude' => fake()->longitude(-180, 180),
            'agent_id' => User::factory(),
        ];
    }
}
