<?php

declare(strict_types=1);

use App\Classes\AddressFormatter;

test('formats Danish address correctly', function () {
    $address = [
        'company' => 'Test Company',
        'name' => 'John Doe',
        'street' => 'Main Street 123',
        'city' => 'Copenhagen',
        'postal_code' => '1000',
    ];

    $formatted = AddressFormatter::format($address, 'DK');

    expect($formatted)->toBe("Test Company\nJohn Doe\nMain Street 123\n1000 Copenhagen\nDENMARK");
});

test('formats US address correctly', function () {
    $address = [
        'company' => 'Acme Corp',
        'name' => 'Jane Smith',
        'street' => '456 Oak Avenue',
        'street_2' => 'Suite 200',
        'city' => 'New York',
        'state' => 'NY',
        'postal_code' => '10001',
    ];

    $formatted = AddressFormatter::format($address, 'US');

    expect($formatted)->toBe("Acme Corp\nJane Smith\n456 Oak Avenue\nSuite 200\nNew York, NY 10001\nUSA");
});

test('formats German address correctly', function () {
    $address = [
        'company' => 'Deutsche Firma GmbH',
        'name' => 'Hans Mueller',
        'street' => 'Hauptstrasse 45',
        'city' => 'Berlin',
        'postal_code' => '10115',
    ];

    $formatted = AddressFormatter::format($address, 'DE');

    expect($formatted)->toBe("Deutsche Firma GmbH\nHans Mueller\nHauptstrasse 45\n10115 Berlin\nGERMANY");
});

test('formats Canadian address correctly', function () {
    $address = [
        'company' => 'Maple Corp',
        'name' => 'Pierre Leblanc',
        'street' => '789 Maple Road',
        'street_2' => 'Apt 5B',
        'city' => 'Toronto',
        'province' => 'ON',
        'postal_code' => 'M5H 2N2',
    ];

    $formatted = AddressFormatter::format($address, 'CA');

    expect($formatted)->toBe("Maple Corp\nPierre Leblanc\n789 Maple Road\nApt 5B\nToronto, ON M5H 2N2\nCANADA");
});

test('formats Italian address with province correctly', function () {
    $address = [
        'company' => 'Azienda Italiana SRL',
        'name' => 'Giuseppe Rossi',
        'street' => 'Via Roma 25',
        'city' => 'Milan',
        'province' => 'MI',
        'postal_code' => '20121',
    ];

    $formatted = AddressFormatter::format($address, 'IT');

    expect($formatted)->toBe("Azienda Italiana SRL\nGiuseppe Rossi\nVia Roma 25\n20121 Milan (MI)\nITALY");
});

test('formats Spanish address with province correctly', function () {
    $address = [
        'company' => 'Empresa Española SA',
        'name' => 'Carlos Garcia',
        'street' => 'Calle Mayor 10',
        'city' => 'Barcelona',
        'province' => 'B',
        'postal_code' => '08001',
    ];

    $formatted = AddressFormatter::format($address, 'ES');

    expect($formatted)->toBe("Empresa Española SA\nCarlos Garcia\nCalle Mayor 10\n08001 Barcelona (B)\nSPAIN");
});

test('formats Latvian address with LV prefix correctly', function () {
    $address = [
        'name' => 'Janis Berzins',
        'street' => 'Brivibas iela 123',
        'city' => 'Riga',
        'postal_code' => '1001',
    ];

    $formatted = AddressFormatter::format($address, 'LV');

    expect($formatted)->toBe("Janis Berzins\nBrivibas iela 123\nLV-1001 Riga\nLATVIA");
});

test('formats Lithuanian address with LT prefix correctly', function () {
    $address = [
        'name' => 'Jonas Petraitis',
        'street' => 'Gedimino pr. 45',
        'city' => 'Vilnius',
        'postal_code' => '01109',
    ];

    $formatted = AddressFormatter::format($address, 'LT');

    expect($formatted)->toBe("Jonas Petraitis\nGedimino pr. 45\nLT-01109 Vilnius\nLITHUANIA");
});

test('formats Irish address correctly', function () {
    $address = [
        'company' => 'Irish Company Ltd',
        'name' => "Sean O'Brien",
        'street' => 'O\'Connell Street',
        'street_2' => 'Floor 3',
        'city' => 'Dublin',
        'postal_code' => 'D01 F5P2',
    ];

    $formatted = AddressFormatter::format($address, 'IE');

    expect($formatted)->toBe("Irish Company Ltd\nSean O'Brien\nO'Connell Street\nFloor 3\nDublin\nD01 F5P2\nIRELAND");
});

test('formats Maltese address correctly', function () {
    $address = [
        'name' => 'Mario Borg',
        'street' => 'Republic Street 45',
        'city' => 'Valletta',
        'postal_code' => 'VLT 1234',
    ];

    $formatted = AddressFormatter::format($address, 'MT');

    expect($formatted)->toBe("Mario Borg\nRepublic Street 45\nValletta VLT 1234\nMALTA");
});

test('formats address with custom separator', function () {
    $address = [
        'name' => 'John Doe',
        'street' => 'Main Street 123',
        'city' => 'Copenhagen',
        'postal_code' => '1000',
    ];

    $formatted = AddressFormatter::format($address, 'DK', ', ');

    expect($formatted)->toBe('John Doe, Main Street 123, 1000 Copenhagen, DENMARK');
});

test('handles missing optional fields gracefully', function () {
    $address = [
        'name' => 'John Doe',
        'city' => 'Copenhagen',
        'postal_code' => '1000',
    ];

    $formatted = AddressFormatter::format($address, 'DK');

    expect($formatted)->toBe("John Doe\n1000 Copenhagen\nDENMARK");
});

test('handles address without company', function () {
    $address = [
        'name' => 'Private Person',
        'street' => 'Home Street 1',
        'city' => 'Amsterdam',
        'postal_code' => '1012',
    ];

    $formatted = AddressFormatter::format($address, 'NL');

    expect($formatted)->toBe("Private Person\nHome Street 1\n1012 Amsterdam\nNETHERLANDS");
});

test('handles unknown country code with default format', function () {
    $address = [
        'company' => 'Global Company',
        'name' => 'John Doe',
        'street' => 'Unknown Street 99',
        'city' => 'Unknown City',
        'postal_code' => '99999',
        'country' => 'Unknown Country',
    ];

    $formatted = AddressFormatter::format($address, 'XX');

    expect($formatted)->toBe("Global Company\nJohn Doe\nUnknown Street 99\nUnknown City\n99999\nUNKNOWN COUNTRY");
});

test('handles unknown country code without country field', function () {
    $address = [
        'name' => 'John Doe',
        'street' => 'Unknown Street 99',
        'city' => 'Unknown City',
        'postal_code' => '99999',
    ];

    $formatted = AddressFormatter::format($address, 'XX');

    expect($formatted)->toBe("John Doe\nUnknown Street 99\nUnknown City\n99999\nXX");
});

test('country code is case insensitive', function () {
    $address = [
        'name' => 'John Doe',
        'street' => 'Main Street 123',
        'city' => 'Copenhagen',
        'postal_code' => '1000',
    ];

    $formattedLower = AddressFormatter::format($address, 'dk');
    $formattedUpper = AddressFormatter::format($address, 'DK');
    $formattedMixed = AddressFormatter::format($address, 'Dk');

    expect($formattedLower)->toBe($formattedUpper)
        ->and($formattedMixed)->toBe($formattedUpper);
});

test('formats all EU countries correctly', function () {
    $countries = [
        'AT' => 'AUSTRIA',
        'BE' => 'BELGIUM',
        'BG' => 'BULGARIA',
        'HR' => 'CROATIA',
        'CY' => 'CYPRUS',
        'CZ' => 'CZECH REPUBLIC',
        'DK' => 'DENMARK',
        'EE' => 'ESTONIA',
        'FI' => 'FINLAND',
        'FR' => 'FRANCE',
        'DE' => 'GERMANY',
        'GR' => 'GREECE',
        'HU' => 'HUNGARY',
        'IE' => 'IRELAND',
        'IT' => 'ITALY',
        'LV' => 'LATVIA',
        'LT' => 'LITHUANIA',
        'LU' => 'LUXEMBOURG',
        'MT' => 'MALTA',
        'NL' => 'NETHERLANDS',
        'PL' => 'POLAND',
        'PT' => 'PORTUGAL',
        'RO' => 'ROMANIA',
        'SK' => 'SLOVAKIA',
        'SI' => 'SLOVENIA',
        'ES' => 'SPAIN',
        'SE' => 'SWEDEN',
    ];

    foreach ($countries as $code => $name) {
        $address = [
            'name' => 'Test Person',
            'street' => 'Test Street 1',
            'city' => 'Test City',
            'postal_code' => '12345',
        ];

        $formatted = AddressFormatter::format($address, $code);

        expect($formatted)->toContain($name);
    }
});

test('handles empty address array', function () {
    $formatted = AddressFormatter::format([], 'DK');

    expect($formatted)->toContain('DENMARK');
});
