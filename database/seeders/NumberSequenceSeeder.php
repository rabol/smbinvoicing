<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NumberSequence;
use Illuminate\Database\Seeder;

class NumberSequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'pattern' => 'INV--{day}-{number}',
            'prefix' => 'INV',
            'delimiter' => '-',
            'reset_frequency' => 'yearly',
            'placeholders' => [
                'day',
                'number',
            ],
            'date' => now(),
            'ordinal_number' => 1,
            'type' => 'invoice',
        ];
        NumberSequence::createPattern($data);

        NumberSequence::create($data);
        $data = [
            'pattern' => 'PRD-{number}',
            'prefix' => 'PRD',
            'delimiter' => '-',
            'reset_frequency' => 'yearly',
            'placeholders' => [
                'number',
            ],
            'date' => now(),
            'ordinal_number' => 1,
            'type' => 'product',
        ];

        NumberSequence::create($data);
    }
}
