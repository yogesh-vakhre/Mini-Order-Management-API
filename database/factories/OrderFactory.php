<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'quantity' => $this->faker->numberBetween(1, 3),
            'total_price' => $this->faker->randomFloat(2, 1000, 20000),
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipped', 'delivered']),
        ];
    }
}
