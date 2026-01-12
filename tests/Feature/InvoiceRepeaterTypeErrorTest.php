<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
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

it('does not throw TypeError when updating repeater item in existing invoice', function () {
    $customer = Customer::factory()->create();
    $invoice = Invoice::create([
        'customer_id' => $customer->id,
        'currency_id' => Currency::first()->id,
        'payment_term_id' => PaymentTerm::first()->id,
        'number' => 'INV-001',
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
        'status' => \App\Enums\InvoiceStatus::Draft,
        'customer_name' => $customer->name,
        'bill_to_line_1' => 'Street 1',
        'bill_to_postal_code' => '12345',
        'bill_to_city' => 'City',
        'bill_to_country' => 'Country',
    ]);

    $item = InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'name' => 'Original Item',
        'description' => 'Original Description',
        'quantity' => 1,
        'unit_price_amount' => 10000, // $100.00
        'tax_rate' => 20,
        'line_subtotal_amount' => 10000,
        'tax_amount' => 2000,
        'line_total_amount' => 12000,
    ]);

    Livewire::test(App\Filament\Resources\Invoices\Pages\EditInvoice::class, [
        'record' => $invoice->getRouteKey(),
    ])
        ->set('data.items.0.quantity', 2)
        ->assertHasNoErrors();
});
