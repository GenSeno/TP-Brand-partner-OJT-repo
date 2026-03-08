<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\OrderLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subTotal = fake()->numberBetween(10000, 100000);
        $discountTotal = fake()->numberBetween(0, $subTotal * 0.2);
        $shippingTotal = fake()->numberBetween(500, 5000);
        $taxTotal = ($subTotal - $discountTotal + $shippingTotal) * 0.12;
        $total = $subTotal - $discountTotal + $shippingTotal + $taxTotal;
        $customer = Customer::query()->inRandomOrder()->first();

        return [
            'orderable_type' => $customer->getMorphClass(),
            'orderable_id' => $customer->id,
            'new_customer' => fake()->boolean(30),
            'status' => fake()->randomElement(OrderStatus::cases())->value,
            'sub_total' => $subTotal,
            'discount_total' => $discountTotal,
            'discount_breakdown' => null,
            'shipping_breakdown' => null,
            'shipping_total' => $shippingTotal,
            'tax_breakdown' => null,
            'tax_total' => $taxTotal,
            'total' => $total,
            'notes' => fake()->optional(0.3)->sentence(),
            'currency_code' => 'PHP',
            'compare_currency_code' => null,
            'exchange_rate' => 1.0,
            'placed_at' => fake()->optional(0.8)->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Indicate that the order is a draft (not placed).
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'placed_at' => null,
            'status' => OrderStatus::PENDING,
        ]);
    }

    /**
     * Indicate that the order is placed.
     */
    public function placed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::UNPAID,
            'placed_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    /**
     * Indicate that the order has associated order lines.
     */
    public function withLines(int $count = 1)
    {
        return $this->has(
            OrderLine::factory()->count($count),
            'lines'
        );
    }
}
