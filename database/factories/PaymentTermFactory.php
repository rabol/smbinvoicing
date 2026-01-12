<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentTerm>
 */
class PaymentTermFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $days = $this->faker->randomElement([0, 7, 14, 30, 60]);

        return [
            'name' => $days === 0 ? 'Due on receipt' : "Net {$days}",
            'days' => $days,
            'is_active' => true,
        ];
    }
}
