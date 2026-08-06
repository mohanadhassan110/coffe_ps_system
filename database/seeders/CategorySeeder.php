<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * بذر تصنيفات المنتجات
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'مشروبات ساخنة',
            'مشروبات باردة',
            'عصائر',
            'تسالي وسناكس',
            'وجبات',
        ];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}
