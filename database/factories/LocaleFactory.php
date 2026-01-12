<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Locale>
 */
class LocaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = $this->faker->randomElement([
            'en', 'en-GB', 'en-US',
            'es', 'es-ES',
            'fr-FR',
            'de-DE',
            'it-IT',
        ]);

        return [
            'code' => $code,
            'name' => $code,
            'is_active' => true,
        ];
    }
}
