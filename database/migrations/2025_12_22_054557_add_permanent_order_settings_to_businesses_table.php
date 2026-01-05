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
        Schema::table('businesses', function (Blueprint $table) {
            $table->boolean('enable_permanent_orders')->default(false)->after('is_active');
            $table->boolean('enable_credit_system')->default(false)->after('enable_permanent_orders');
            $table->decimal('credit_limit', 12, 2)->nullable()->after('enable_credit_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['enable_permanent_orders', 'enable_credit_system', 'credit_limit']);
        });
    }
};
