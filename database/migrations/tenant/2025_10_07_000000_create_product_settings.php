<?php

use Illuminate\Database\Migrations\Migration;
use Ingenius\Core\Facades\Settings;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Settings::set('products', 'auto_sku_generation', true);
        Settings::set('products', 'sku_prefix', 'SKU-');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Settings::forget('products', 'auto_sku_generation');
        Settings::forget('products', 'sku_prefix');
    }
};