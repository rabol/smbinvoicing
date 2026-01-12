<?php

declare(strict_types=1);

use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Models\Customer;
use App\Models\Locale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Nnjeim\World\Models\Country;
use Nnjeim\World\Models\Currency;
use Nnjeim\World\Models\Timezone;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    Locale::factory()->create(['id' => 1]);
    Currency::create([
        'id' => 1,
        'country_id' => 1,
        'name' => 'US Dollar',
        'code' => 'USD',
        'precision' => 2,
        'symbol' => '$',
        'symbol_native' => '$',
        'symbol_first' => 1,
        'decimal_mark' => '.',
        'thousands_separator' => ',',
    ]);
    Timezone::create([
        'id' => 1,
        'country_id' => 1,
        'name' => 'UTC',
        'abbr' => 'UTC',
        'offset' => 0,
        'dst' => 0,
    ]);
    Country::create([
        'id' => 1,
        'name' => 'United States',
        'iso2' => 'US',
        'iso3' => 'USA',
        'phone_code' => '1',
        'region' => 'Americas',
        'subregion' => 'Northern America',
    ]);
});

it('can render customer resource create page', function () {
    Livewire::test(CreateCustomer::class)
        ->assertSuccessful();
});

it('can create a customer with addresses', function () {
    $component = Livewire::test(CreateCustomer::class);

    $repeaterItems = $component->get('data.addresses');
    $uuid = array_key_first($repeaterItems);

    $component
        ->fillForm([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'type' => 'company',
            'status' => 'active',
            'locale_id' => 1,
            'currency_id' => 1,
            'timezone_id' => 1,
            "addresses.{$uuid}" => [
                'type' => 'company',
                'address_line_1' => '123 Main St',
                'postal_code' => '12345',
                'country_id' => 1,
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $customer = Customer::first();
    expect($customer->name)->toBe('John Doe');
    expect($customer->addresses)->toHaveCount(1);
    expect($customer->addresses->first()->address_line_1)->toBe('123 Main St');
});
