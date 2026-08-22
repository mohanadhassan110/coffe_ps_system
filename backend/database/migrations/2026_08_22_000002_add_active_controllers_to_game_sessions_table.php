<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة عمود عدد الأذرع النشطة المستخدمة في الجلسة
     * Add active (in-use) controllers count per game session
     */
    public function up(): void
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->unsignedTinyInteger('active_controllers')->default(2)->after('pre_paid_minutes'); // الأذرع المستخدمة (افتراضي: لعب جماعي)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->dropColumn('active_controllers');
        });
    }
};
