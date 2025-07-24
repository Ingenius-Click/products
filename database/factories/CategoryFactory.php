<?php

namespace Ingenius\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ingenius\Products\Models\Category;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(rand(1, 3), true),
            'description' => $this->faker->paragraph(),
            'parent_id' => null, // Default to null for top-level categories
        ];
    }

    /**
     * Configure the factory to create a category with a parent.
     */
    public function withParent($parentId = null): self
    {
        return $this->state(function (array $attributes) use ($parentId) {
            return [
                'parent_id' => $parentId,
            ];
        });
    }
}
