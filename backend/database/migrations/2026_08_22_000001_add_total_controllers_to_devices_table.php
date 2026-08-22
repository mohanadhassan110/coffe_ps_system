<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة عمود إجمالي أذرع التحكم المتوفرة فعلياً لكل جهاز
     * Add total physical controllers available per console device
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->unsignedTinyInteger('total_controllers')->default(4)->after('status'); // عدد الأذرع المادية الكلية
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('total_controllers');
        });
    }
};
