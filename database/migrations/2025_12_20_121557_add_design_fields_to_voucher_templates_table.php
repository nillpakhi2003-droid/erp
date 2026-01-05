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
            $table->string('primary_color')->default('#1e40af')->after('footer_text');
            $table->string('secondary_color')->default('#3b82f6')->after('primary_color');
            $table->string('font_size')->default('13px')->after('secondary_color');
            $table->string('page_margin')->default('5mm')->after('font_size');
            $table->string('logo_url')->nullable()->after('page_margin');
            $table->boolean('show_watermark')->default(false)->after('logo_url');
            $table->string('watermark_text')->default('PAID')->after('show_watermark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_templates', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color',
                'secondary_color',
                'font_size',
                'page_margin',
                'logo_url',
                'show_watermark',
                'watermark_text',
            ]);
        });
    }
};
