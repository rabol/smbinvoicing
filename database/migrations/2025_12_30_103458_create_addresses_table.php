<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();

            $table->string('type', 20); // company|invoice|delivery
            $table->string('label')->nullable();

            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('postal_code', 32)->nullable();

            // nnjeim/world
            $table->foreignId('country_id')->constrained('countries')->restrictOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->restrictOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->restrictOnDelete();

            // Fallback text when a city/state isn't selected or postcodes don't map cleanly
            $table->string('state_text')->nullable();
            $table->string('city_text')->nullable();

            $table->string('phone')->nullable();
            $table->timestamps();

            // If exactly one per type:
            $table->unique(['customer_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
