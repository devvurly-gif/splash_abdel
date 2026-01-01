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
        Schema::create('document_headers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            
            // Classification
            $table->enum('domain', ['sale', 'purchase', 'stock']);
            $table->string('type'); // invoice, delivery_note, return, transfer, adjustment, etc.
            
            // Relations externes
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->string('related_entity_type')->nullable(); // polymorphic
            $table->unsignedBigInteger('related_entity_id')->nullable(); // polymorphic
            
            // Dates
            $table->date('document_date');
            $table->date('due_date')->nullable();
            
            // Statut
            $table->enum('status', [
                'draft', 
                'validated', 
                'completed', 
                'cancelled',
                'archived'
            ])->default('draft');
            
            // Totaux
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            // Métadonnées
            $table->text('notes')->nullable();
            $table->string('reference')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['domain', 'type', 'status']);
            $table->index(['document_date', 'status']);
            $table->index('warehouse_id');
            $table->index(['related_entity_type', 'related_entity_id']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_headers');
    }
};
