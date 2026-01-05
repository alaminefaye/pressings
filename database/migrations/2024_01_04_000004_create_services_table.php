<?php

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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Lavage simple", "Lavage + repassage", etc.
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('duration_hours')->default(24); // Durée estimée en heures
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};

