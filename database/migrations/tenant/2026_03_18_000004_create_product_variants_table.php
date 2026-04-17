<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ingenius\Products\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->integer('regular_price')->nullable(); // null = inherit from product
            $table->integer('sale_price')->nullable(); // null = inherit from product
            $table->boolean('handle_stock')->default(true);
            $table->integer('stock')->default(0);
            $table->integer('stock_for_sale')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('visible')->default(true);
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
