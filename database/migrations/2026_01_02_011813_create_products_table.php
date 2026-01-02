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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('ean13')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->foreignId('tax_id')->nullable();
            $table->string('unit')->nullable();
            $table->boolean('isactive')->default(true);
            $table->boolean('onPromo')->default(false);
            $table->boolean('isFeatured')->default(false);
            $table->timestamps();
            
            // Indexes
            $table->index('code');
            $table->index('name');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('isactive');
            $table->index('onPromo');
            $table->index('isFeatured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
