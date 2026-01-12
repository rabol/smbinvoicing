<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeZone>
 */
class TimeZoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'UTC',
            'Europe/Madrid',
            'Europe/Berlin',
            'Europe/London',
            'America/New_York',
        ]);

        return [
            'name' => $name,
            'label' => $name,
            'is_active' => true,
        ];
    }
}
