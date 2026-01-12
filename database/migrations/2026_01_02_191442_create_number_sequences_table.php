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
        Schema::create('number_sequences', function (Blueprint $table) {
            $table->id();

            $table->string('type')->unique();
            $table->string('pattern');
            $table->string('prefix')->nullable();
            $table->string('postfix')->nullable();
            $table->string('delimiter')->default('-');
            $table->string('reset_frequency')->nullable();
            $table->json('placeholders')->nullable();
            $table->date('date')->nullable();
            $table->unsignedInteger('ordinal_number')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_sequences');
    }
};
