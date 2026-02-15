<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Purchasing\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = \App\Modules\Purchasing\Models\Supplier::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'tax_id' => $this->faker->bothify('TAX-####-###'),
            'address' => $this->faker->address(),
            'is_active' => true,
        ];
    }
}
