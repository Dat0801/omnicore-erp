<?php

namespace Database\Factories;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Product\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['physical', 'service', 'digital']);

        $data = [
            'name' => $this->faker->words(3, true), // fallback since commerce might not be loaded or syntax is wrong
            'sku' => $this->faker->unique()->bothify('SKU-####-????'),
            'barcode' => $this->faker->ean13(),
            'type' => $type,
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'cost_price' => $this->faker->randomFloat(2, 5, 800),
            'product_category_id' => ProductCategory::inRandomOrder()->first()?->id ?? ProductCategory::factory(),
            'is_active' => $this->faker->boolean(80),
            'allow_backorders' => $this->faker->boolean(20),
            'tags' => $this->faker->words(3, false), // Array of words
        ];

        if ($type === 'physical') {
            $data['unit'] = $this->faker->randomElement(['pcs', 'kg', 'box']);
            $data['weight'] = $this->faker->randomFloat(2, 0.1, 10);
            $data['dimensions'] = [
                'length' => $this->faker->numberBetween(10, 100),
                'width' => $this->faker->numberBetween(10, 100),
                'height' => $this->faker->numberBetween(5, 50),
            ];
        }

        return $data;
    }
}
