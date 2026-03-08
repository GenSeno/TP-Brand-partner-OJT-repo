<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::query()->inRandomOrder()->value('id'),
            'name' => fake()->words(fake()->numberBetween(2, 4), true),
            'description' => fake()->optional(0.7)->sentences(fake()->numberBetween(1, 3), true),
            'status' => fake()->randomElement(ProductStatus::cases())->value,
        ];
    }

    /**
     * Indicate that the product is published.
     */
    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => ProductStatus::PUBLISHED,
        ]);
    }

    /**
     * Indicate that the product is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => ProductStatus::DISABLED,
        ]);
    }
}
