<?php

namespace Database\Seeders;

use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::query()->create([
            'code' => 'WELCOME10',
            'type' => CouponType::Percent,
            'value' => 10,
            'min_order_amount' => 50,
            'max_uses' => 100,
            'used_count' => 0,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonths(6),
            'is_active' => true,
        ]);

        Coupon::query()->create([
            'code' => 'VINTAGE25',
            'type' => CouponType::Fixed,
            'value' => 25,
            'min_order_amount' => 150,
            'max_uses' => 50,
            'used_count' => 0,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonths(3),
            'is_active' => true,
        ]);

        Coupon::query()->create([
            'code' => 'ARCHIVE15',
            'type' => CouponType::Percent,
            'value' => 15,
            'min_order_amount' => 75,
            'max_uses' => null,
            'used_count' => 0,
            'starts_at' => now()->subDay(),
            'ends_at' => null,
            'is_active' => true,
        ]);
    }
}
