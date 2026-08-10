<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول طلبات الكافيه المستقلة (طاولات وتيك أواي)
     */
    public function up(): void
    {
        Schema::create('cafe_orders', function (Blueprint $table) {
            $table->id();
            $table->enum('order_type', ['table', 'takeaway'])->default('table'); // طاولة أو تيك أواي
            $table->string('table_number')->nullable(); // رقم/اسم الطاولة
            $table->string('client_name')->nullable(); // اسم العميل
            $table->foreignId('user_id')->constrained('users'); // الكاشير المسؤول
            
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2)->default(0);

            $table->enum('payment_method', ['cash', 'vodafone_cash', 'card'])->nullable();
            $table->enum('status', ['open', 'completed', 'cancelled'])->default('open');

            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('order_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_orders');
    }
};
