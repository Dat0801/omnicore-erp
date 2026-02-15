<?php

namespace Database\Factories;

use App\Modules\Product\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Product\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucfirst($this->faker->unique()->words(2, true));

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'parent_id' => null,
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'is_featured' => $this->faker->boolean(20),
            'display_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
