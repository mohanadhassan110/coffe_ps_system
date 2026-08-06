<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول عناصر طلبات الكافيه المستقلة
     */
    public function up(): void
    {
        Schema::create('cafe_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_order_id')->constrained('cafe_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('total_price', 8, 2);
            $table->timestamps();

            $table->index('cafe_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_order_items');
    }
};
