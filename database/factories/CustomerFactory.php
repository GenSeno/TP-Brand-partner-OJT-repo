<?php

namespace Database\Factories;

use App\Enums\PersonTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->optional(0.3)->randomElement(PersonTitle::cases())?->value,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company_name' => fake()->optional(0.6)->company(),
            'vat_no' => fake()->optional(0.4)->numerify('###-###-###-###-V'),
            'enabled' => fake()->boolean(85),
        ];
    }

    /**
     * Indicate that the customer is enabled.
     */
    public function enabled(): static
    {
        return $this->state(fn(array $attributes) => [
            'enabled' => true,
        ]);
    }

    /**
     * Indicate that the customer is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn(array $attributes) => [
            'enabled' => false,
        ]);
    }

    /**
     * Indicate that the customer is a business (has company name).
     */
    public function business(): static
    {
        return $this->state(fn(array $attributes) => [
            'company_name' => fake()->company(),
            'vat_no' => fake()->numerify('VAT-###-###-###'),
        ]);
    }

    /**
     * Indicate that the customer is an individual (no company name).
     */
    public function individual(): static
    {
        return $this->state(fn(array $attributes) => [
            'company_name' => null,
            'vat_no' => null,
        ]);
    }
}
