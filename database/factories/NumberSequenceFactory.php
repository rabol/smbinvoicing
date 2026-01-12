<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NumberSequence>
 */
class NumberSequenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->word,
            'pattern' => '{number}',
            'prefix' => 'SEQ',
            'postfix' => '',
            'delimiter' => '-',
            'reset_frequency' => 'never',
            'placeholders' => [],
            'date' => fake()->optional()->date(),
            'ordinal_number' => fake()->randomNumber(),
        ];
    }
}
