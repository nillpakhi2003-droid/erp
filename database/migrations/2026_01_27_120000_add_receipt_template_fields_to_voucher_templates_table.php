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
            // POS Receipt Template Fields
            $table->string('receipt_header_text', 500)->nullable()->after('watermark_text');
            $table->string('receipt_footer_text', 500)->nullable()->after('receipt_header_text');
            $table->string('receipt_paper_size', 10)->default('80mm')->after('receipt_footer_text');
            $table->boolean('receipt_show_logo')->default(true)->after('receipt_paper_size');
            $table->boolean('receipt_show_customer')->default(true)->after('receipt_show_logo');
            $table->boolean('receipt_show_payment_method')->default(true)->after('receipt_show_customer');
            $table->string('receipt_font_size', 10)->default('12px')->after('receipt_show_payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_templates', function (Blueprint $table) {
            $table->dropColumn([
                'receipt_header_text',
                'receipt_footer_text',
                'receipt_paper_size',
                'receipt_show_logo',
                'receipt_show_customer',
                'receipt_show_payment_method',
                'receipt_font_size',
            ]);
        });
    }
};
