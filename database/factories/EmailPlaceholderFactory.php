<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailPlaceholder>
 */
class EmailPlaceholderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word(),
            'label' => $this->faker->words(3, true),
            'category' => $this->faker->randomElement(['customer', 'invoice', 'company', 'general']),
            'description' => $this->faker->sentence(),
            'resolver_class' => null,
            'resolver_method' => null,
            'applicable_to' => null,
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
}
