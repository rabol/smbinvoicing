<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'type' => $this->faker->randomElement(['company', 'invoice', 'delivery']),
            'label' => $this->faker->optional()->word(),

            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->optional()->secondaryAddress(),
            'postal_code' => $this->faker->postcode(),

            // Use existing world data if present
            'country_id' => DB::table('countries')->inRandomOrder()->value('id'),
            'state_id' => DB::table('states')->inRandomOrder()->value('id'),
            'city_id' => DB::table('cities')->inRandomOrder()->value('id'),

            'state_text' => null,
            'city_text' => null,

            'phone' => $this->faker->optional()->phoneNumber(),
        ];
    }
}
