<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\PaymentTerm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Nnjeim\World\Models\Currency;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    // Ensure we have some base data
    if (PaymentTerm::count() === 0) {
        PaymentTerm::create(['name' => 'Net 30', 'days' => 30]);
    }

    if (Currency::count() === 0) {
        Currency::create([
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
    }
});

it('can render invoice resource create page', function () {
    Livewire::test(App\Filament\Resources\Invoices\Pages\CreateInvoice::class)
        ->assertSuccessful();
});

it('can autofill customer data', function () {
    $customer = Customer::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'tax_id' => 'VAT123',
    ]);

    Livewire::test(App\Filament\Resources\Invoices\Pages\CreateInvoice::class)
        ->set('data.customer_id', $customer->id)
        ->assertSet('data.customer_name', 'John Doe')
        ->assertSet('data.customer_email', 'john@example.com')
        ->assertSet('data.customer_tax_id', 'VAT123');
});

it('calculates line totals correctly', function () {
    Livewire::test(App\Filament\Resources\Invoices\Pages\CreateInvoice::class)
        ->fillForm([
            'items' => [
                [
                    'name' => 'Test Item',
                    'quantity' => 2,
                    'unit_price_major' => 100,
                    'discount_major' => 10,
                    'tax_rate' => 20,
                ],
            ],
        ])
        ->assertSet('data.items.0.line_subtotal_major', 190) // (2 * 100) - 10
        ->assertSet('data.items.0.tax_amount_major', 38)     // 190 * 0.2
        ->assertSet('data.items.0.line_total_major', 228);   // 190 + 38
});

it('forces zero tax for B2B customers', function () {
    $customer = Customer::factory()->create([
        'tax_id' => 'B2B-TAX-ID',
    ]);

    Livewire::test(App\Filament\Resources\Invoices\Pages\CreateInvoice::class)
        ->set('data.customer_id', $customer->id)
        ->fillForm([
            'items' => [
                [
                    'name' => 'Test Item',
                    'quantity' => 1,
                    'unit_price_major' => 100,
                    'tax_rate' => 21,
                ],
            ],
        ])
        ->assertSet('data.items.0.tax_rate', 0)
        ->assertSet('data.items.0.tax_amount_major', 0)
        ->assertSet('data.items.0.line_total_major', 100);
});
