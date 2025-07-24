<?php

namespace Ingenius\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ingenius\Products\Models\Product;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'sku' => $this->faker->unique()->randomNumber(6),
            'description' => $this->faker->sentence,
            'visible' => $this->faker->boolean,
            'regular_price' => $this->faker->numberBetween(1000, 10000),
            'sale_price' => $this->faker->numberBetween(1000, 10000),
            'handle_stock' => $this->faker->boolean,
            'stock' => $this->faker->numberBetween(0, 1000),
            'stock_for_sale' => $this->faker->numberBetween(0, 1000),
            'unit_of_measurement' => $this->faker->word,
            'short_description' => $this->faker->sentence,
        ];
    }
}
