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
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('reserved_quantity', 15, 3)->default(0);
            $table->decimal('available_quantity', 15, 3)
                  ->virtualAs('quantity - reserved_quantity');
            
            $table->foreignId('last_movement_id')
                  ->nullable()
                  ->constrained('journal_stock')->onDelete('set null');
            $table->date('last_movement_date')->nullable();
            
            $table->timestamps();
            
            $table->unique(['warehouse_id', 'product_id']);
            $table->index('quantity');
            $table->index('available_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
    }
};
