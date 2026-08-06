<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * بذر المستخدمين الافتراضيين
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'مدير النظام',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'كاشير 1',
            'username' => 'cashier1',
            'password' => Hash::make('cashier123'),
            'role'     => 'cashier',
        ]);

        User::create([
            'name'     => 'موظف 1',
            'username' => 'staff1',
            'password' => Hash::make('staff123'),
            'role'     => 'staff',
        ]);
    }
}
