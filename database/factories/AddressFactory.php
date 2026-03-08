<?php

namespace Database\Factories;

use App\Enums\AddressType;
use App\Enums\PersonTitle;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = Country::inRandomOrder()->first();
        $state = $country->states()->inRandomOrder()->first();

        return [
            'country_id' => $country->id,
            'type' => fake()->randomElement(AddressType::cases()),
            'title' => fake()->optional(0.3)->randomElement(PersonTitle::cases())?->value,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company_name' => fake()->optional(0.4)->company(),
            'line1' => fake()->streetAddress(),
            'line2' => fake()->optional(0.3)->secondaryAddress(),
            'barangay' => fake()->optional(0.8)->city(),
            'city' => $state?->name ?? fake()->city(),
            'province' => $state?->name ?? fake()->city(),
            'postcode' => fake()->postcode(),
            'delivery_instructions' => fake()->optional(0.2)->sentence(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'default' => fake()->boolean(30),
        ];
    }

    public function shipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AddressType::SHIPPING,
        ]);
    }

    public function billing(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AddressType::BILLING,
        ]);
    }

    public function home(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AddressType::HOME,
        ]);
    }

    public function office(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AddressType::OFFICE,
            'company_name' => fake()->company(),
        ]);
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'default' => true,
        ]);
    }
}
