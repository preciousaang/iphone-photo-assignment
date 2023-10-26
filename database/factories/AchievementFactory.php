<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'type' => fake()->randomElement(['comment', 'lesson']),
            'name' => fake()->name(),
            'number_to_achieve' => fake()->numberBetween(1, 20),
        ];
    }
}
