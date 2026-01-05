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
        Schema::table('voucher_templates', function (Blueprint $table) {
            // Drop owner_id and add business_id
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
            $table->foreignId('business_id')->nullable()->after('id')->constrained('businesses')->onDelete('cascade');
        });
        
        // Make business_id non-nullable after existing records are handled
        // In production, you would migrate existing data first
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_templates', function (Blueprint $table) {
            // Revert back to owner_id
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
            $table->foreignId('owner_id')->after('id')->constrained('users')->onDelete('cascade');
        });
    }
};
