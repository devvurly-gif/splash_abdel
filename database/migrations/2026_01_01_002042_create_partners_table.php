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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            
            // Type de tiers
            $table->enum('type', ['customer', 'supplier', 'both'])->default('customer');
            
            // Informations de base
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('tax_id')->nullable(); // Numéro d'identification fiscale
            $table->string('registration_number')->nullable(); // Numéro d'enregistrement
            
            // Contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('website')->nullable();
            
            // Adresse
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            
            // Informations financières
            $table->string('payment_terms')->nullable(); // Ex: "Net 30", "Cash on delivery"
            $table->decimal('credit_limit', 15, 2)->nullable()->default(0);
            $table->decimal('discount_percent', 5, 2)->nullable()->default(0);
            
            // Statut
            $table->boolean('status')->default(true);
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('code');
            $table->index('type');
            $table->index('status');
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
