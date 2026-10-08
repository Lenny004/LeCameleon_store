<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::query()->create([
            'key' => 'store.name',
            'value' => ['en' => 'Le Cameleon'],
        ]);

        Setting::query()->create([
            'key' => 'store.currency',
            'value' => ['code' => 'USD', 'symbol' => '$'],
        ]);

        Setting::query()->create([
            'key' => 'store.shipping',
            'value' => ['flat_rate' => 12.00, 'free_threshold' => 200.00],
        ]);

        Setting::query()->create([
            'key' => 'store.contact',
            'value' => ['email' => 'hello@lecameleon.store', 'phone' => '+1-555-0199'],
        ]);
    }
}
