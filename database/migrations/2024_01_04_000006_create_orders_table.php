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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Ex: "ORD-2024-0001"
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Type de livraison
            $table->enum('delivery_type', ['pickup', 'home_delivery'])->default('pickup');
            
            // Adresses
            $table->foreignId('pickup_address_id')->nullable()->constrained('addresses')->onDelete('set null');
            $table->foreignId('delivery_address_id')->nullable()->constrained('addresses')->onDelete('set null');
            
            // Dates
            $table->dateTime('pickup_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
            
            // Statut
            $table->enum('status', [
                'pending',
                'received',
                'washing',
                'ironing',
                'ready',
                'in_delivery',
                'delivered',
                'cancelled'
            ])->default('pending');
            
            // Montants
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Notes
            $table->text('special_instructions')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('client_id');
            $table->index('employee_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

