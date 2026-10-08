<?php

namespace Database\Factories;

use App\Enums\OfferStatus;
use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offer>
 */
class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory()->uniquePiece(),
            'user_id' => User::factory()->customer(),
            'amount' => fake()->randomFloat(2, 10, 200),
            'message' => fake()->optional()->sentence(),
            'status' => OfferStatus::Pending,
            'counter_amount' => null,
            'admin_notes' => null,
            'expires_at' => null,
        ];
    }
}
