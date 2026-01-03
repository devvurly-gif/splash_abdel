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
        Schema::table('warehouses', function (Blueprint $table) {
            // Drop old status column if it exists
            if (Schema::hasColumn('warehouses', 'status')) {
                $table->dropColumn('status');
            }
        });
        
        Schema::table('warehouses', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('warehouses', 'isprincipal')) {
                $table->boolean('isprincipal')->default(false)->after('title');
            }
        });
        
        Schema::table('warehouses', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouses', 'inchargeOf')) {
                $table->foreignId('inchargeOf')->nullable()->after('isprincipal')->constrained('users')->onDelete('set null');
            }
        });
        
        Schema::table('warehouses', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouses', 'type')) {
                $table->enum('type', ['warehouse', 'store', 'tank'])->default('warehouse')->after('inchargeOf');
            }
        });
        
        // Add indexes in separate calls (using try-catch to handle if indexes already exist)
        try {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->index('isprincipal');
            });
        } catch (\Exception $e) {
            // Index might already exist, ignore
        }
        
        try {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->index('type');
            });
        } catch (\Exception $e) {
            // Index might already exist, ignore
        }
        
        try {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->index('inchargeOf');
            });
        } catch (\Exception $e) {
            // Index might already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['isprincipal']);
            $table->dropIndex(['type']);
            $table->dropIndex(['inchargeOf']);
            
            // Drop foreign key constraint
            $table->dropForeign(['inchargeOf']);
            
            // Drop columns
            $table->dropColumn(['isprincipal', 'inchargeOf', 'type']);
            
            // Restore old status column
            $table->boolean('status')->default(true)->after('title');
        });
    }
};
