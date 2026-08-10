<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول المنتجات - مع تتبع المخزون وسعر الشراء والبيع
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete(); // التصنيف
            $table->string('name'); // اسم المنتج بالعربية
            $table->decimal('purchase_price', 8, 2)->default(0); // سعر الشراء
            $table->decimal('sale_price', 8, 2); // سعر البيع
            $table->integer('stock_quantity')->default(0); // الكمية المتاحة
            $table->integer('min_stock_alert')->default(5); // حد التنبيه للمخزون
            $table->timestamps();

            $table->index('category_id');
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
