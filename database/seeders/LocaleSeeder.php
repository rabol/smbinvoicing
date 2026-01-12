<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Seeder;
use Locale as PhpLocale;
use ResourceBundle;

class LocaleSeeder extends Seeder
{
    public function run(): void
    {
        if (! class_exists(ResourceBundle::class)) {
            throw new \RuntimeException('PHP intl extension is required to seed locales.');
        }

        $locales = ResourceBundle::getLocales('');

        foreach ($locales as $code) {
            // Normalize underscore to dash (Laravel / BCP-47 style)
            $normalized = str_replace('_', '-', $code);

            // Try to get a human-readable name
            $name = PhpLocale::getDisplayName($code, $code);

            Locale::updateOrCreate(
                ['code' => $normalized],
                [
                    'name' => $name ?: $normalized,
                    'is_active' => true,
                ]
            );
        }
    }
}
