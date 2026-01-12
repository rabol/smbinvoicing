<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Random total between 3 and 10
        $total = rand(3, 10);

        // Split roughly in half
        $taxableCount = (int) ceil($total / 2);
        $nonTaxableCount = $total - $taxableCount;

        // Create taxable products
        Product::factory()
            ->count($taxableCount)
            ->state(fn () => ['taxable' => true])
            ->create();

        // Create non-taxable products
        Product::factory()
            ->count($nonTaxableCount)
            ->nonTaxable()
            ->create();
    }
}
