<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::factory()
            ->count(10)
            ->has(Address::factory()->state(['type' => 'invoice']))
            ->has(Address::factory()->state(['type' => 'delivery']))
            ->create();
    }
}
