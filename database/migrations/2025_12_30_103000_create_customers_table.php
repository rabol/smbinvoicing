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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('type', 20)->default('company');   // person|company
            $table->string('status', 20)->default('active'); // active|inactive|blocked

            $table->string('name');                 // person name OR primary contact

            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('registration_code')->nullable();
            // References (as requested)
            $table->foreignId('locale_id')->nullable()->constrained('locales')->restrictOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->restrictOnDelete();
            $table->foreignId('timezone_id')->nullable()->constrained('timezones')->restrictOnDelete();
            $table->foreignId('payment_term_id')->nullable()->constrained('payment_terms')->restrictOnDelete();

            // Archive instead of delete
            $table->timestamp('archived_at')->nullable()->index();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
