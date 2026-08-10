<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;

/**
 * بذر أجهزة البلايستيشن والألعاب
 */
class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $devices = [
            ['name' => 'بلايستيشن 5 - 01', 'type' => 'ps5', 'hourly_rate' => 40.00],
            ['name' => 'بلايستيشن 5 - 02', 'type' => 'ps5', 'hourly_rate' => 40.00],
            ['name' => 'بلايستيشن 5 - 03', 'type' => 'ps5', 'hourly_rate' => 40.00],
            ['name' => 'بلايستيشن 4 - 01', 'type' => 'ps4', 'hourly_rate' => 25.00],
            ['name' => 'بلايستيشن 4 - 02', 'type' => 'ps4', 'hourly_rate' => 25.00],
            ['name' => 'غرفة VIP 1',        'type' => 'ps5', 'hourly_rate' => 80.00],
            ['name' => 'في آر - 01',        'type' => 'vr',  'hourly_rate' => 60.00],
            ['name' => 'بلياردو - 01',      'type' => 'billiard', 'hourly_rate' => 30.00],
        ];

        foreach ($devices as $device) {
            Device::create($device);
        }
    }
}
