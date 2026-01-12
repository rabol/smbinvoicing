<?php

declare(strict_types=1);

use App\Models\Address;
use App\Models\ContactPerson;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Locale;
use App\Models\PaymentTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nnjeim\World\Models\Currency;
use Nnjeim\World\Models\Timezone;

uses(RefreshDatabase::class);

it('can create a customer using factory', function () {
    $customer = Customer::factory()->create();

    expect($customer)->toBeInstanceOf(Customer::class)
        ->and($customer->name)->not->toBeEmpty();
});

it('has fillable attributes', function () {
    $customer = Customer::factory()->create([
        'name' => 'Test Customer',
        'email' => 'test@example.com',
        'phone' => '1234567890',
        'tax_id' => 'TAX123',
    ]);

    expect($customer->name)->toBe('Test Customer')
        ->and($customer->email)->toBe('test@example.com')
        ->and($customer->phone)->toBe('1234567890')
        ->and($customer->tax_id)->toBe('TAX123');
});

it('belongs to a locale', function () {
    $locale = Locale::factory()->create();
    $customer = Customer::factory()->create(['locale_id' => $locale->id]);

    expect($customer->locale)->toBeInstanceOf(Locale::class)
        ->and($customer->locale->id)->toBe($locale->id);
});

it('belongs to a currency', function () {
    $currency = Currency::create([
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

    $customer = Customer::factory()->create(['currency_id' => $currency->id]);

    expect($customer->currency)->toBeInstanceOf(Currency::class)
        ->and($customer->currency->id)->toBe($currency->id);
});

it('belongs to a timezone', function () {
    $timezone = Timezone::create([
        'country_id' => 1,
        'name' => 'America/New_York',
    ]);

    $customer = Customer::factory()->create(['timezone_id' => $timezone->id]);

    expect($customer->timezone)->toBeInstanceOf(Timezone::class)
        ->and($customer->timezone->id)->toBe($timezone->id);
});

it('belongs to a payment term', function () {
    $paymentTerm = PaymentTerm::factory()->create();
    $customer = Customer::factory()->create(['payment_term_id' => $paymentTerm->id]);

    expect($customer->paymentTerm)->toBeInstanceOf(PaymentTerm::class)
        ->and($customer->paymentTerm->id)->toBe($paymentTerm->id);
});

it('has many addresses', function () {
    $customer = Customer::factory()->create();
    Address::factory()->count(3)->create(['customer_id' => $customer->id]);

    expect($customer->addresses)->toHaveCount(3)
        ->and($customer->addresses->first())->toBeInstanceOf(Address::class);
});

it('has one company address', function () {
    $customer = Customer::factory()->create();
    $companyAddress = Address::factory()->create([
        'customer_id' => $customer->id,
        'type' => 'company',
    ]);

    expect($customer->companyAddress)->toBeInstanceOf(Address::class)
        ->and($customer->companyAddress->id)->toBe($companyAddress->id);
});

it('has one invoice address', function () {
    $customer = Customer::factory()->create();
    $invoiceAddress = Address::factory()->create([
        'customer_id' => $customer->id,
        'type' => 'invoice',
    ]);

    expect($customer->invoiceAddress)->toBeInstanceOf(Address::class)
        ->and($customer->invoiceAddress->id)->toBe($invoiceAddress->id);
});

it('has one delivery address', function () {
    $customer = Customer::factory()->create();
    $deliveryAddress = Address::factory()->create([
        'customer_id' => $customer->id,
        'type' => 'delivery',
    ]);

    expect($customer->deliveryAddress)->toBeInstanceOf(Address::class)
        ->and($customer->deliveryAddress->id)->toBe($deliveryAddress->id);
});

it('has many contact people', function () {
    $customer = Customer::factory()->create();
    ContactPerson::factory()->count(2)->create([
        'contactable_id' => $customer->id,
        'contactable_type' => Customer::class,
    ]);

    expect($customer->contactPeople)->toHaveCount(2)
        ->and($customer->contactPeople->first())->toBeInstanceOf(ContactPerson::class);
});

it('has many invoices', function () {
    $customer = Customer::factory()->create();
    Invoice::factory()->count(3)->create(['customer_id' => $customer->id]);

    expect($customer->invoices)->toHaveCount(3)
        ->and($customer->invoices->first())->toBeInstanceOf(Invoice::class);
});

it('can scope active customers', function () {
    Customer::factory()->create(['status' => 'active', 'archived_at' => null]);
    Customer::factory()->create(['status' => 'inactive']);
    Customer::factory()->create(['status' => 'active', 'archived_at' => now()]);

    $activeCustomers = Customer::active()->get();

    expect($activeCustomers)->toHaveCount(1);
});

it('casts archived_at as datetime', function () {
    $customer = Customer::factory()->create([
        'archived_at' => '2024-01-01 12:00:00',
    ]);

    expect($customer->archived_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});
