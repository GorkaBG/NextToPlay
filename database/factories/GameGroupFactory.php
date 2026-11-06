<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GameGroup;
use App\Models\User;

/**
 * @extends Factory<GameGroup>
 */
class GameGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'groupname' => implode(' ', array_slice(explode(' ', fake()->bs()), 0, 2)),
            'color' => fake()->hexColor(),
            'icon' => null,
            'order' => 1,
        ];
    }
}
