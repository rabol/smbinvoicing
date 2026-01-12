<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PaymentTerm;
use Illuminate\Database\Seeder;

class PaymentTermSeeder extends Seeder
{
    public function run(): void
    {
        $terms = [
            ['name' => 'Due on receipt', 'days' => 0],
            ['name' => 'Net 7',          'days' => 7],
            ['name' => 'Net 14',         'days' => 14],
            ['name' => 'Net 30',         'days' => 30],
            ['name' => 'Net 60',         'days' => 60],
        ];

        foreach ($terms as $term) {
            PaymentTerm::updateOrCreate(
                ['days' => $term['days']],
                [
                    'name' => $term['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
