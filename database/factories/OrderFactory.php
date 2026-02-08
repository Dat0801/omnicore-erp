<?php

namespace Database\Factories;

use App\Modules\Order\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'total_amount' => $this->faker->randomFloat(2, 10, 5000),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'source' => $this->faker->randomElement(['WebStore', 'POS Terminal']),
            'source_id' => $this->faker->uuid(),
            'warehouse_id' => 1, // Assuming ID 1 exists, or random
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->unique()->safeEmail(),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
