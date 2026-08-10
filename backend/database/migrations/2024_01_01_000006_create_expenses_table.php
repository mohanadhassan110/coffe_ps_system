<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول المصروفات اليومية - إيجار، فواتير، مشتريات، إلخ
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // المستخدم الذي أضاف المصروف
            $table->decimal('amount', 10, 2); // المبلغ
            $table->string('reason'); // سبب المصروف بالعربية
            $table->date('date'); // تاريخ المصروف
            $table->timestamps();

            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
