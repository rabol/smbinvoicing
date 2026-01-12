<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('payment_term_id')->nullable()->constrained('payment_terms')->restrictOnDelete();

            $table->string('number', 50)->unique();
            $table->date('issue_date');
            $table->date('due_date')->nullable();

            $table->string('status', 20)->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('voided_at')->nullable();

            // Customer snapshot
            $table->string('customer_name');
            $table->string('customer_tax_id')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();

            // Billing address snapshot
            $table->string('bill_to_line_1')->nullable();
            $table->string('bill_to_line_2')->nullable();
            $table->string('bill_to_postal_code', 32)->nullable();
            $table->string('bill_to_city')->nullable();
            $table->string('bill_to_state')->nullable();
            $table->string('bill_to_country')->nullable();

            // Totals in minor units (e.g. cents)
            $table->decimal('subtotal_amount', 8, 3)->default(0);   // net
            $table->decimal('tax_total_amount', 8, 3)->default(0);  // vat
            $table->decimal('total_amount', 8, 3)->default(0);      // gross
            $table->decimal('total_discount_amount', 8, 3)->default(0);
            $table->decimal('total_shipping_amount', 8, 3)->default(0);  // total shipping
            $table->decimal('total_charges_amount', 8, 3)->default(0);   // total charges

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
