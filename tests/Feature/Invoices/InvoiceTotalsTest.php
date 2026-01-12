<?php

declare(strict_types=1);

use App\Filament\Resources\Invoices\Pages\CreateInvoice;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\PaymentTerm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Nnjeim\World\Models\Currency;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());

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

it('saves subtotal_amount, tax_total_amount, and total_amount correctly', function () {
    $customer = Customer::factory()->create();

    Livewire::test(CreateInvoice::class)
        ->fillForm([
            'customer_id' => $customer->id,
            'currency_id' => Currency::first()->id,
            'payment_term_id' => PaymentTerm::first()->id,
            'number' => 'INV-001',
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'items' => [
                [
                    'name' => 'Test Item',
                    'description' => 'Description',
                    'quantity' => 2,
                    'unit_price_amount' => 100, // 2 * 100 = 200
                    'discount_amount' => 10,   // 200 - 10 = 190 (subtotal)
                    'tax_rate' => 20,           // 190 * 0.2 = 38 (tax)
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $invoice = Invoice::first();

    expect($invoice->subtotal_amount)->toBe(190.0)
        ->and($invoice->tax_total_amount)->toBe(38.0)
        ->and($invoice->total_amount)->toBe(228.0);
});
