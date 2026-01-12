<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Locale;
use App\Models\PaymentTerm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nnjeim\World\Models\Currency;
use Nnjeim\World\Models\Timezone;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['person', 'company']),
            'status' => 'active',

            'name' => $this->faker->company(),
            'email' => $this->faker->optional()->safeEmail(),

            // ✅ ALWAYS valid, no garbage like 50357---1593135
            'phone' => $this->faker->optional()->numerify('+## (###) ###-####'),

            'tax_id' => $this->faker->optional()->randomElement([
                'DE'.$this->faker->numerify('#########'),
                'FR'.$this->faker->bothify('??#########'),
                'ES'.$this->faker->bothify('?########'),
                'IT'.$this->faker->numerify('###########'),
            ]),

            'locale_id' => Locale::query()->inRandomOrder()->value('id'),
            'currency_id' => Currency::query()->inRandomOrder()->value('id'),
            'timezone_id' => Timezone::query()->inRandomOrder()->value('id'),
            'payment_term_id' => PaymentTerm::query()->inRandomOrder()->value('id'),

            'archived_at' => null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function company(): static
    {
        return $this->state(fn () => [
            'type' => 'company',
            'name' => $this->faker->company(),
        ]);
    }
}
