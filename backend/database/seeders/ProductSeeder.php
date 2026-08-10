<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * بذر المنتجات - قائمة كافيه مصرية نموذجية
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');

        $products = [
            // مشروبات ساخنة
            ['category' => 'مشروبات ساخنة', 'name' => 'شاي',          'purchase' => 2,  'sale' => 10, 'stock' => 200, 'alert' => 20],
            ['category' => 'مشروبات ساخنة', 'name' => 'قهوة تركي',     'purchase' => 3,  'sale' => 15, 'stock' => 100, 'alert' => 10],
            ['category' => 'مشروبات ساخنة', 'name' => 'نسكافيه',      'purchase' => 4,  'sale' => 15, 'stock' => 150, 'alert' => 15],
            ['category' => 'مشروبات ساخنة', 'name' => 'كابتشينو',     'purchase' => 5,  'sale' => 20, 'stock' => 80,  'alert' => 10],
            ['category' => 'مشروبات ساخنة', 'name' => 'هوت شوكليت',   'purchase' => 6,  'sale' => 20, 'stock' => 60,  'alert' => 10],
            ['category' => 'مشروبات ساخنة', 'name' => 'سحلب',         'purchase' => 5,  'sale' => 15, 'stock' => 50,  'alert' => 10],

            // مشروبات باردة
            ['category' => 'مشروبات باردة', 'name' => 'بيبسي',         'purchase' => 5,  'sale' => 12, 'stock' => 100, 'alert' => 15],
            ['category' => 'مشروبات باردة', 'name' => 'كوكاكولا',      'purchase' => 5,  'sale' => 12, 'stock' => 100, 'alert' => 15],
            ['category' => 'مشروبات باردة', 'name' => 'سفن أب',        'purchase' => 5,  'sale' => 12, 'stock' => 80,  'alert' => 10],
            ['category' => 'مشروبات باردة', 'name' => 'مياه معدنية',   'purchase' => 3,  'sale' => 8,  'stock' => 200, 'alert' => 20],
            ['category' => 'مشروبات باردة', 'name' => 'ريد بول',       'purchase' => 15, 'sale' => 30, 'stock' => 40,  'alert' => 5],

            // عصائر
            ['category' => 'عصائر', 'name' => 'عصير مانجو',     'purchase' => 8,  'sale' => 20, 'stock' => 50,  'alert' => 5],
            ['category' => 'عصائر', 'name' => 'عصير برتقال',    'purchase' => 8,  'sale' => 20, 'stock' => 50,  'alert' => 5],
            ['category' => 'عصائر', 'name' => 'ليمون بالنعناع',  'purchase' => 5,  'sale' => 15, 'stock' => 60,  'alert' => 10],

            // تسالي
            ['category' => 'تسالي وسناكس', 'name' => 'شيبسي كبير',   'purchase' => 8,  'sale' => 15, 'stock' => 80,  'alert' => 10],
            ['category' => 'تسالي وسناكس', 'name' => 'شيبسي صغير',   'purchase' => 4,  'sale' => 8,  'stock' => 120, 'alert' => 15],
            ['category' => 'تسالي وسناكس', 'name' => 'شوكولاتة',     'purchase' => 10, 'sale' => 18, 'stock' => 60,  'alert' => 10],
            ['category' => 'تسالي وسناكس', 'name' => 'بسكويت',       'purchase' => 5,  'sale' => 10, 'stock' => 80,  'alert' => 10],
            ['category' => 'تسالي وسناكس', 'name' => 'فشار',         'purchase' => 3,  'sale' => 10, 'stock' => 50,  'alert' => 10],

            // وجبات
            ['category' => 'وجبات', 'name' => 'ساندوتش جبنة',  'purchase' => 10, 'sale' => 25, 'stock' => 30,  'alert' => 5],
            ['category' => 'وجبات', 'name' => 'كريب نوتيلا',   'purchase' => 12, 'sale' => 30, 'stock' => 25,  'alert' => 5],
            ['category' => 'وجبات', 'name' => 'بيتزا صغيرة',   'purchase' => 15, 'sale' => 35, 'stock' => 20,  'alert' => 5],
        ];

        foreach ($products as $p) {
            Product::create([
                'category_id'     => $categories[$p['category']]->id,
                'name'            => $p['name'],
                'purchase_price'  => $p['purchase'],
                'sale_price'      => $p['sale'],
                'stock_quantity'  => $p['stock'],
                'min_stock_alert' => $p['alert'],
            ]);
        }
    }
}
