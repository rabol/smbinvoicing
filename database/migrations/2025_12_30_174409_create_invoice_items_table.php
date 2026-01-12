<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();

            $table->unsignedInteger('position')->default(1);

            $table->string('name');
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('unit')->nullable();

            $table->decimal('quantity')->default(1);
            $table->decimal('unit_price_amount')->default(0);     // net minor units
            $table->decimal('discount_amount')->default(0);       // net minor units
            $table->decimal('tax_amount', 8, 3)->default(0);            // minor units
            $table->decimal('line_subtotal_amount', 8, 3)->default(0);  // net minor units
            $table->decimal('line_total_amount', 8, 3)->default(0);     // gross minor units
            $table->decimal('shipping_amount', 8, 3)->default(0);  // total shipping
            $table->decimal('charges_amount', 8, 3)->default(0);   // total charges
            $table->decimal('tax_rate', 8, 3)->default(0); // e.g. 21.0000

            $table->timestamps();

            $table->index(['invoice_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
