<?php

namespace Database\Factories;

use App\Enums\OrderLineType;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderLine>
 */
class OrderLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = fake()->numberBetween(1000, 50000);
        $quantity = fake()->numberBetween(1, 10);
        $unitQuantity = 1;
        $subTotal = $unitPrice * $quantity * $unitQuantity;
        $taxTotal = $subTotal * 0.12;
        $total = $subTotal + $taxTotal;

        $purchasable = ProductVariant::query()
            ->whereHas('product', function ($query) {
                $query->where('status', 'published')
                    ->whereNull('deleted_at');
            })
            ->inRandomOrder()
            ->first();

        $meta = [];
        if ($purchasable->values()->where('value', 'WITH-name')->exists()) {
            foreach (range(1, $quantity) as $_) {
                $meta['names'][] = fake()->lastName() . ' #' . fake()->numberBetween(1, 99);
            }
        }

        return [
            'purchasable_type' => $purchasable->getMorphClass(),
            'purchasable_id' => $purchasable->id,
            'type' => fake()->randomElement(OrderLineType::cases())->value,
            'description' => $purchasable->product->name,
            'option' => $purchasable->description,
            'identifier' => $purchasable->sku,
            'unit_price' => $unitPrice,
            'unit_quantity' => $unitQuantity,
            'quantity' => $quantity,
            'sub_total' => $subTotal,
            'discount_total' => 0,
            // 'tax_breakdown' => null,
            'tax_total' => $taxTotal,
            'total' => $total,
            'notes' => fake()->optional(0.2)->sentence(),
            'meta' => $meta,
        ];
    }

    /**
     * Indicate that the order line is for a physical product.
     */
    public function physical(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => OrderLineType::PHYSICAL->value,
        ]);
    }

    /**
     * Indicate that the order line is for a digital product.
     */
    public function digital(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => OrderLineType::DIGITAL->value,
        ]);
    }

    /**
     * Indicate that the order line is for shipping.
     */
    public function shipping(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => OrderLineType::SHIPPING->value,
            'description' => fake()->randomElement(['Standard Shipping', 'Express Shipping', 'Next Day Delivery']),
            'quantity' => 1,
        ]);
    }
}
