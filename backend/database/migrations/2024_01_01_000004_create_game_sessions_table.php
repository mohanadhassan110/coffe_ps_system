<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول الجلسات (الأساسي) - يربط الأجهزة بالمبيعات ويحسب التكلفة
     * يدعم الجلسات المفتوحة والمدفوعة مسبقاً
     */
    public function up(): void
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete(); // الجهاز (null = طلب كافيه فقط)
            $table->foreignId('user_id')->constrained('users'); // الكاشير المسؤول
            $table->string('client_name')->nullable(); // اسم العميل (اختياري)

            // نوع الجلسة
            $table->enum('session_type', ['open', 'pre_paid'])->default('open'); // مفتوح أو مدفوع مسبقاً
            $table->integer('pre_paid_minutes')->nullable(); // عدد الدقائق المدفوعة مسبقاً

            // التوقيت
            $table->timestamp('start_time')->useCurrent();
            $table->timestamp('end_time')->nullable();

            // الحسابات المالية
            $table->decimal('playstation_total', 10, 2)->default(0); // إجمالي وقت البلايستيشن
            $table->decimal('cafe_total', 10, 2)->default(0); // إجمالي طلبات الكافيه
            $table->decimal('total_amount', 10, 2)->default(0); // المجموع الكلي
            $table->decimal('discount', 10, 2)->default(0); // الخصم
            $table->decimal('final_amount', 10, 2)->default(0); // المبلغ النهائي

            // الدفع والحالة
            $table->enum('payment_method', ['cash', 'vodafone_cash', 'card'])->default('cash'); // طريقة الدفع
            $table->enum('status', ['active', 'closed', 'cancelled'])->default('active'); // حالة الجلسة

            $table->softDeletes();
            $table->timestamps();

            // فهارس للأداء
            $table->index('status');
            $table->index('device_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
