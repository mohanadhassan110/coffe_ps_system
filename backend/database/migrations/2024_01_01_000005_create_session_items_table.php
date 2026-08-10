<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول عناصر الجلسة - المنتجات المطلوبة في كل جلسة
     */
    public function up(): void
    {
        Schema::create('session_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained('game_sessions')->cascadeOnDelete(); // الجلسة
            $table->foreignId('product_id')->constrained('products'); // المنتج
            $table->integer('quantity'); // الكمية
            $table->decimal('unit_price', 8, 2); // سعر الوحدة وقت الطلب
            $table->decimal('total_price', 8, 2); // الإجمالي
            $table->timestamps();

            $table->index('game_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_items');
    }
};
