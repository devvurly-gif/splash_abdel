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
        Schema::create('numbering_systems', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('domain');
            $table->string('type');
            $table->string('template');
            $table->integer('next_trick')->default(1);
            $table->boolean('isActive')->default(true);
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['domain', 'type']);
            $table->index('isActive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('numbering_systems');
    }
};
