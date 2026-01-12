<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\PaymentTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nnjeim\World\Models\Currency;

uses(RefreshDatabase::class);

it('can create an invoice', function () {
    $customer = Customer::factory()->create();
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
    $paymentTerm = PaymentTerm::factory()->create();

    $invoice = Invoice::create([
        'customer_id' => $customer->id,
        'currency_id' => $currency->id,
        'payment_term_id' => $paymentTerm->id,
        'number' => 'INV-001',
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
        'status' => 'draft',
        'customer_name' => 'Test Customer',
        'total_amount' => 100.00,
    ]);

    expect($invoice)->toBeInstanceOf(Invoice::class)
        ->and($invoice->number)->toBe('INV-001');
});

it('belongs to a customer', function () {
    $customer = Customer::factory()->create();
    $invoice = Invoice::create([
        'customer_id' => $customer->id,
        'currency_id' => Currency::create([
            'country_id' => 1,
            'name' => 'US Dollar',
            'code' => 'USD',
            'precision' => 2,
            'symbol' => '$',
            'symbol_native' => '$',
            'symbol_first' => 1,
            'decimal_mark' => '.',
            'thousands_separator' => ',',
        ])->id,
        'payment_term_id' => PaymentTerm::factory()->create()->id,
        'number' => 'INV-002',
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
        'status' => 'draft',
        'customer_name' => 'Test Customer',
        'total_amount' => 100.00,
    ]);

    expect($invoice->customer)->toBeInstanceOf(Customer::class)
        ->and($invoice->customer->id)->toBe($customer->id);
});

it('casts dates correctly', function () {
    $invoice = Invoice::create([
        'customer_id' => Customer::factory()->create()->id,
        'currency_id' => Currency::create([
            'country_id' => 1,
            'name' => 'US Dollar',
            'code' => 'USD',
            'precision' => 2,
            'symbol' => '$',
            'symbol_native' => '$',
            'symbol_first' => 1,
            'decimal_mark' => '.',
            'thousands_separator' => ',',
        ])->id,
        'payment_term_id' => PaymentTerm::factory()->create()->id,
        'number' => 'INV-003',
        'issue_date' => '2024-01-15',
        'due_date' => '2024-02-15',
        'status' => 'draft',
        'customer_name' => 'Test Customer',
        'total_amount' => 100.00,
    ]);

    expect($invoice->issue_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($invoice->due_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

it('generates unique invoice id', function () {
    $invoiceId = Invoice::generateUniqueInvoiceId();

    expect($invoiceId)->toBeString()
        ->and(strlen($invoiceId))->toBeGreaterThan(0);
});

it('updates status to PartialPaid when invoice has partial payment', function () {
    $customer = Customer::create([
        'type' => 'company',
        'status' => 'active',
        'name' => 'Test Customer',
        'email' => 'test@example.com',
    ]);

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

    $invoice = Invoice::create([
        'customer_id' => $customer->id,
        'currency_id' => $currency->id,
        'payment_term_id' => PaymentTerm::factory()->create()->id,
        'number' => 'INV-PARTIAL-001',
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
        'status' => 'invoiced',
        'customer_name' => 'Test Customer',
        'total_amount' => '1000.000',
    ]);

    // Record a partial payment (50% of total)
    $invoice->payments()->create([
        'amount' => '500.000',
        'payment_method' => 'bank_transfer',
        'payment_date' => now(),
    ]);

    $invoice->updatePaymentStatus();

    expect($invoice->fresh()->status->value)->toBe('partial_paid')
        ->and($invoice->fresh()->paid_at)->toBeNull();
});

it('updates status to Paid when invoice is fully paid', function () {
    $customer = Customer::create([
        'type' => 'company',
        'status' => 'active',
        'name' => 'Test Customer 2',
        'email' => 'test2@example.com',
    ]);

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

    $invoice = Invoice::create([
        'customer_id' => $customer->id,
        'currency_id' => $currency->id,
        'payment_term_id' => PaymentTerm::factory()->create()->id,
        'number' => 'INV-PAID-001',
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
        'status' => 'invoiced',
        'customer_name' => 'Test Customer',
        'total_amount' => '1000.000',
    ]);

    // Record a full payment
    $invoice->payments()->create([
        'amount' => '1000.000',
        'payment_method' => 'bank_transfer',
        'payment_date' => now(),
    ]);

    $invoice->updatePaymentStatus();

    expect($invoice->fresh()->status->value)->toBe('paid')
        ->and($invoice->fresh()->paid_at)->not->toBeNull();
});

it('updates status to Paid with multiple partial payments', function () {
    $customer = Customer::create([
        'type' => 'company',
        'status' => 'active',
        'name' => 'Test Customer 3',
        'email' => 'test3@example.com',
    ]);

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

    $invoice = Invoice::create([
        'customer_id' => $customer->id,
        'currency_id' => $currency->id,
        'payment_term_id' => PaymentTerm::factory()->create()->id,
        'number' => 'INV-MULTI-001',
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
        'status' => 'invoiced',
        'customer_name' => 'Test Customer',
        'total_amount' => '1000.000',
    ]);

    // Record first partial payment
    $invoice->payments()->create([
        'amount' => '300.000',
        'payment_method' => 'bank_transfer',
        'payment_date' => now(),
    ]);

    $invoice->updatePaymentStatus();
    expect($invoice->fresh()->status->value)->toBe('partial_paid');

    // Record second partial payment
    $invoice->payments()->create([
        'amount' => '400.000',
        'payment_method' => 'cash',
        'payment_date' => now(),
    ]);

    $invoice->updatePaymentStatus();
    expect($invoice->fresh()->status->value)->toBe('partial_paid');

    // Record final payment to complete
    $invoice->payments()->create([
        'amount' => '300.000',
        'payment_method' => 'credit_card',
        'payment_date' => now(),
    ]);

    $invoice->updatePaymentStatus();
    expect($invoice->fresh()->status->value)->toBe('paid')
        ->and($invoice->fresh()->paid_at)->not->toBeNull();
});

it('handles overpayment correctly', function () {
    $customer = Customer::create([
        'type' => 'company',
        'status' => 'active',
        'name' => 'Test Customer 4',
        'email' => 'test4@example.com',
    ]);

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

    $invoice = Invoice::create([
        'customer_id' => $customer->id,
        'currency_id' => $currency->id,
        'payment_term_id' => PaymentTerm::factory()->create()->id,
        'number' => 'INV-OVER-001',
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
        'status' => 'invoiced',
        'customer_name' => 'Test Customer',
        'total_amount' => '1000.000',
    ]);

    // Record an overpayment
    $invoice->payments()->create([
        'amount' => '1200.000',
        'payment_method' => 'bank_transfer',
        'payment_date' => now(),
    ]);

    $invoice->updatePaymentStatus();

    expect($invoice->fresh()->status->value)->toBe('paid')
        ->and($invoice->fresh()->paid_at)->not->toBeNull();
});

it('correctly identifies partially paid invoice', function () {
    $customer = Customer::create([
        'type' => 'company',
        'status' => 'active',
        'name' => 'Test Customer 5',
        'email' => 'test5@example.com',
    ]);

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

    $invoice = Invoice::create([
        'customer_id' => $customer->id,
        'currency_id' => $currency->id,
        'payment_term_id' => PaymentTerm::factory()->create()->id,
        'number' => 'INV-CHECK-001',
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
        'status' => 'invoiced',
        'customer_name' => 'Test Customer',
        'total_amount' => '1000.000',
    ]);

    expect($invoice->isPartiallyPaid())->toBe(false)
        ->and($invoice->isFullyPaid())->toBe(false);

    // Add partial payment
    $invoice->payments()->create([
        'amount' => '500.000',
        'payment_method' => 'bank_transfer',
        'payment_date' => now(),
    ]);

    expect($invoice->isPartiallyPaid())->toBe(true)
        ->and($invoice->isFullyPaid())->toBe(false);

    // Complete payment
    $invoice->payments()->create([
        'amount' => '500.000',
        'payment_method' => 'bank_transfer',
        'payment_date' => now(),
    ]);

    expect($invoice->isPartiallyPaid())->toBe(false)
        ->and($invoice->isFullyPaid())->toBe(true);
});
