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
        Schema::create('contact_people', function (Blueprint $table) {
            $table->id();

            $table->nullableMorphs('contactable'); // contactable_id, contactable_type (both nullable)

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('position')->nullable();   // e.g. “Accounting”, “CEO”
            $table->boolean('is_primary')->default(false);
            $table->boolean('add_to_invoice')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_people');
    }
};
