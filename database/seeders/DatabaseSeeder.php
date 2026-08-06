<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DeviceSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
