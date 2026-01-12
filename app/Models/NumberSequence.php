<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NumberSequenceResetFrequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'delimiter',
        'ordinal_number',
        'pattern',
        'placeholders',
        'postfix',
        'prefix',
        'reset_frequency',
        'type',
    ];

    public function casts(): array
    {
        return [
            'date' => 'date',
            'delimiter' => 'string',
            'ordinal_number' => 'integer',
            'pattern' => 'string',
            'placeholders' => 'array',
            'postfix' => 'string',
            'prefix' => 'string',
            'reset_frequency' => NumberSequenceResetFrequency::class,
            'type' => 'string',
        ];
    }

    public static function createPattern(array $data): array
    {
        // Normalize delimiter
        $delimiter = blank($data['delimiter'] ?? null)
            ? ''
            : $data['delimiter'];

        $parts = [];

        // Add prefix as first part (if any)
        if (filled($data['prefix'] ?? null)) {
            $parts[] = $data['prefix'];
        }

        // Add placeholders as parts
        foreach ($data['placeholders'] ?? [] as $placeholder) {
            if (blank($placeholder)) {
                continue;
            }

            $parts[] = '{'.$placeholder.'}';
        }

        // Join everything with the delimiter
        $data['pattern'] = implode($delimiter, $parts);

        return $data;
    }
}
