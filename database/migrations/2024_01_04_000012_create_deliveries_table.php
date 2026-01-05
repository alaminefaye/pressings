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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['pickup', 'delivery']);
            
            $table->foreignId('address_id')->constrained()->onDelete('cascade');
            $table->dateTime('scheduled_at');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'failed'])->default('pending');
            
            $table->text('signature')->nullable(); // Base64 de la signature
            $table->string('photo')->nullable(); // Preuve de livraison
            $table->text('notes')->nullable();
            
            $table->timestamps();

            // Indexes
            $table->index('driver_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};

