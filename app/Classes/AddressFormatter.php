<?php

declare(strict_types=1);

namespace App\Classes;

class AddressFormatter
{
    public static function format(array $a, string $countryCode, string $separator = "\n"): string
    {
        $c = strtoupper($countryCode);

        $name = $a['name'] ?? null;
        $company = $a['company'] ?? null;
        $street = $a['street'] ?? null;
        $street2 = $a['street_2'] ?? null;
        $city = $a['city'] ?? null;
        $state = $a['state'] ?? null;
        $zip = $a['postal_code'] ?? null;
        $province = $a['province'] ?? null;

        $lines = match ($c) {
            'AT' => [$company, $name, $street, $street2, "{$zip} {$city}", 'AUSTRIA'],
            'BE' => [$company, $name, $street, "{$zip} {$city}", 'BELGIUM'],
            'BG' => [$company, $name, $street, "{$zip} {$city}", 'BULGARIA'],
            'HR' => [$company, $name, $street, "{$zip} {$city}", 'CROATIA'],
            'CY' => [$company, $name, $street, "{$zip} {$city}", 'CYPRUS'],
            'CZ' => [$company, $name, $street, "{$zip} {$city}", 'CZECH REPUBLIC'],
            'DK' => [$company, $name, $street, "{$zip} {$city}", 'DENMARK'],
            'EE' => [$company, $name, $street, "{$zip} {$city}", 'ESTONIA'],
            'FI' => [$company, $name, $street, "{$zip} {$city}", 'FINLAND'],
            'FR' => [$company, $name, $street, "{$zip} {$city}", 'FRANCE'],
            'DE' => [$company, $name, $street, "{$zip} {$city}", 'GERMANY'],
            'GR' => [$company, $name, $street, "{$zip} {$city}", 'GREECE'],
            'HU' => [$company, $name, $street, "{$zip} {$city}", 'HUNGARY'],
            'IE' => [$company, $name, $street, $street2, $city, $zip, 'IRELAND'],
            'IT' => [$company, $name, $street, "{$zip} {$city}".($province ? " ({$province})" : ''), 'ITALY'],
            'LV' => [$company, $name, $street, "LV-{$zip} {$city}", 'LATVIA'],
            'LT' => [$company, $name, $street, "LT-{$zip} {$city}", 'LITHUANIA'],
            'LU' => [$company, $name, $street, "{$zip} {$city}", 'LUXEMBOURG'],
            'MT' => [$company, $name, $street, "{$city} {$zip}", 'MALTA'],
            'NL' => [$company, $name, $street, "{$zip} {$city}", 'NETHERLANDS'],
            'PL' => [$company, $name, $street, "{$zip} {$city}", 'POLAND'],
            'PT' => [$company, $name, $street, "{$zip} {$city}", 'PORTUGAL'],
            'RO' => [$company, $name, $street, "{$zip} {$city}", 'ROMANIA'],
            'SK' => [$company, $name, $street, "{$zip} {$city}", 'SLOVAKIA'],
            'SI' => [$company, $name, $street, "{$zip} {$city}", 'SLOVENIA'],
            'ES' => [$company, $name, $street, "{$zip} {$city}".($province ? " ({$province})" : ''), 'SPAIN'],
            'SE' => [$company, $name, $street, "{$zip} {$city}", 'SWEDEN'],
            'US' => [$company, $name, $street, $street2, "{$city}, {$state} {$zip}", 'USA'],
            'CA' => [$company, $name, $street, $street2, "{$city}, {$province} {$zip}", 'CANADA'],
            default => [$company, $name, $street, $city, $zip, strtoupper($a['country'] ?? $countryCode)],
        };

        return implode($separator, array_filter($lines));
    }
}
