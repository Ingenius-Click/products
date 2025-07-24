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
            $table->string('name');
            $table->string('sku');
            $table->text('description')->nullable();
            $table->boolean('visible')->default(true);
            $table->integer('regular_price');
            $table->integer('sale_price');
            $table->boolean('handle_stock')->default(true);
            $table->integer('stock')->default(0);
            $table->integer('stock_for_sale')->default(0);
            $table->string('unit_of_measurement')->nullable();
            $table->string('short_description');
            $table->timestamps();
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
