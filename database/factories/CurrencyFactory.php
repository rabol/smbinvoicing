<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currencies = [
            ['EUR', 'Euro', '€'],
            ['USD', 'US Dollar', '$'],
            ['GBP', 'British Pound', '£'],
        ];

        [$code, $name, $symbol] = $this->faker->randomElement($currencies);

        return [
            'code' => $code,
            'name' => $name,
            'symbol' => $symbol,
            'precision' => 2,
            'is_active' => true,
        ];
    }
}
