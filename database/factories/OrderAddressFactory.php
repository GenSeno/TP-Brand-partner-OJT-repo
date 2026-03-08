<?php

namespace Database\Factories;

use App\Enums\AddressType;
use App\Enums\PersonTitle;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderAddress>
 */
class OrderAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = Country::inRandomOrder()->first();

        return [
            'country_id' => $country->id,
            'title' => fake()->optional(0.3)->randomElement(PersonTitle::cases())?->value,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company_name' => fake()->optional(0.4)->company(),
            'line1' => fake()->streetAddress(),
            'line2' => fake()->optional(0.3)->secondaryAddress(),
            'barangay' => fake()->optional(0.8)->city(),
            'city' => fake()->city(),
            'province' => $country->states()->inRandomOrder()->value('name'),
            'postcode' => fake()->postcode(),
            'delivery_instructions' => fake()->optional(0.2)->sentence(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'type' => AddressType::SHIPPING,
            'shipping_option' => fake()->optional(0.7)->randomElement(['Standard', 'Express', 'Same Day']),
        ];
    }

    /**
     * Indicate that this is a shipping address.
     */
    public function shipping(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => AddressType::SHIPPING,
            'shipping_option' => fake()->randomElement(['Standard', 'Express', 'Same Day']),
        ]);
    }

    /**
     * Indicate that this is a billing address.
     */
    public function billing(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => AddressType::BILLING,
            'shipping_option' => null,
        ]);
    }
}
