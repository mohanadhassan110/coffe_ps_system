<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول الأجهزة - بلايستيشن، VR، بلياردو
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الجهاز مثل: بلايستيشن 5 - 01
            $table->enum('type', ['ps4', 'ps5', 'vr', 'billiard']); // نوع الجهاز
            $table->decimal('hourly_rate', 8, 2); // سعر الساعة
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available'); // الحالة
            $table->timestamps();

            $table->index('status'); // فهرس للبحث السريع حسب الحالة
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
