<?php

namespace Database\Factories;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Station>
 */
class StationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'     => fake()->lastName(),
            'address'  => fake()->address(),
            'location' => Point::makeGeodetic(fake()->latitude(), fake()->longitude()),
        ];
    }
}
