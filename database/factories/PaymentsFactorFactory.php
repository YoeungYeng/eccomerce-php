<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PaymentsFactorFactory extends Factory
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
            "order_id" => $this->faker->numberBetween(1, 100),
            "amount" => $this->faker->randomFloat(2, 10, 1000),
            "status" => $this->faker->randomElement(['pending', 'completed', 'failed']),
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
}
