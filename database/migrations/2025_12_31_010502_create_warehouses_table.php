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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->boolean('isprincipal')->default(false);
            $table->foreignId('inchargeOf')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['warehouse', 'store', 'tank'])->default('warehouse');
            $table->timestamps();
            
            // Indexes
            $table->index('code');
            $table->index('isprincipal');
            $table->index('type');
            $table->index('inchargeOf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
