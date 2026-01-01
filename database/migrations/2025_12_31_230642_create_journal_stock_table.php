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
        Schema::create('journal_stock', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            
            // Type de mouvement
            $table->enum('movement_type', [
                'sale_invoice',
                'sale_delivery', 
                'sale_return',
                'purchase_invoice',
                'purchase_receipt',
                'purchase_return',
                'transfer_in',
                'transfer_out',
                'adjustment_increase',
                'adjustment_decrease',
                'manual_entry',
                'manual_exit'
            ]);
            
            // Lien vers le document
            $table->foreignId('document_header_id')->nullable()
                  ->constrained('document_headers')->onDelete('set null');
            $table->foreignId('document_line_id')->nullable()
                  ->constrained('document_lines')->onDelete('set null');
            
            // Dépôt et produit
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            
            // Mouvement
            $table->decimal('quantity', 15, 3); // signed: +entrée, -sortie
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->date('movement_date');
            
            // Référence
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            // Indexes pour performance
            $table->index(['warehouse_id', 'product_id', 'movement_date']);
            $table->index(['movement_type', 'movement_date']);
            $table->index('document_header_id');
            $table->index('document_line_id');
            $table->index('movement_date');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_stock');
    }
};
