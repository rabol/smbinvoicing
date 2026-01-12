<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => implode(' ', $this->faker->unique()->words(3)),
            'description' => $this->faker->paragraph(),
            'taxable' => $this->faker->boolean(70), // most products taxable
            'unit' => $this->faker->randomElement(['pcs', 'hour', 'kg', 'item', null]),
            'price' => $this->faker->randomFloat(3, 0, 999),
        ];
    }

    /**
     * State: non-taxable product.
     */
    public function nonTaxable(): static
    {
        return $this->state(fn () => [
            'taxable' => false,
        ]);
    }

    /**
     * State: service-like product (hourly).
     */
    public function service(): static
    {
        return $this->state(fn () => [
            'unit' => 'hour',
        ]);
    }
}
