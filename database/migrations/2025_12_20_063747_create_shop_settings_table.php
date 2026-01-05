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
        Schema::create('shop_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('shop_name')->default('My Shop');
            $table->string('shop_logo')->nullable();
            $table->string('primary_color')->default('#3b82f6'); // Blue
            $table->string('secondary_color')->default('#10b981'); // Green
            $table->string('accent_color')->default('#f59e0b'); // Orange
            $table->string('text_color')->default('#1f2937'); // Dark gray
            $table->string('font_family')->default('Inter');
            $table->text('custom_css')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_settings');
    }
};
