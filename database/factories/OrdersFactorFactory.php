<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrdersFactorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => $this->faker->numberBetween(1, 100),
            "product_id" => $this->faker->numberBetween(1, 100),
            "quantity" => $this->faker->numberBetween(1, 10),
            "total_price" => $this->faker->randomFloat(2, 10, 1000),
            "status" => $this->faker->randomElement(['pending', 'completed', 'canceled']),
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
}
