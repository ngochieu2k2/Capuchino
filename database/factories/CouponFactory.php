<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    public function definition(): array
    {

        $startDate = $this->faker->dateTimeBetween('-1 week', 'now');
        $endDate = (clone $startDate)->modify('+1 month');

        return [
            'code' => strtoupper(Str::random(8)),
            'discount' => $this->faker->numberBetween(10000, 350000), // Giảm từ 10k - 50k
            'min_order_amount' => $this->faker->numberBetween(1000000, 5000000), // Có thể null
            'usage_limit' => $this->faker->numberBetween(10, 100),
            'user_limit' => $this->faker->numberBetween(1, 3),
            'used_count' => $this->faker->numberBetween(1, 10),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $this->faker->boolean(80), // 90% active
        ];
    }
}
