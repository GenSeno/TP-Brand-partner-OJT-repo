<?php

namespace Database\Factories;

use App\Enums\JobOrderUrgency;
use App\Models\Order;
use App\States\JobOrderState\Cancelled;
use App\States\JobOrderState\Completed;
use App\States\JobOrderState\JobOrderState;
use App\States\JobOrderState\NewOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobOrder>
 */
class JobOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $order = Order::placed()->inRandomOrder()->first();
        $orderedAt = $order->placed_at;
        $leadTime = fake()->numberBetween(3, 14); // 3-14 days lead time
        $dueAt = (clone $orderedAt)->modify("+{$leadTime} days");
        $estimatedDelivery = (clone $dueAt)->modify('+1 day');

        return [
            'order_id' => $order->id,
            'urgency_flag' => fake()->randomElement(JobOrderUrgency::cases())->value,
            'current_state' => fake()->randomElement(JobOrderState::allOrdered()->except(Cancelled::$name)),
            'lead_time' => $leadTime,
            'estimated_delivery' => $estimatedDelivery,
            'ordered_at' => $orderedAt,
            'due_at' => $dueAt,
        ];
    }

    /**
     * Indicate that the job order is urgent/rush.
     */
    public function rush(): static
    {
        return $this->state(fn(array $attributes) => [
            'urgency_flag' => JobOrderUrgency::RUSH,
            'lead_time' => fake()->numberBetween(1, 3),
        ]);
    }

    /**
     * Indicate that the job order is priority.
     */
    public function priority(): static
    {
        return $this->state(fn(array $attributes) => [
            'urgency_flag' => JobOrderUrgency::PRIORITY,
            'lead_time' => fake()->numberBetween(3, 7),
        ]);
    }

    /**
     * Indicate that the job order is normal priority.
     */
    public function normal(): static
    {
        return $this->state(fn(array $attributes) => [
            'urgency_flag' => JobOrderUrgency::NORMAL,
            'lead_time' => fake()->numberBetween(7, 14),
        ]);
    }

    /**
     * Indicate that the job order is newly created.
     */
    public function newOrder(): static
    {
        return $this->state(fn(array $attributes) => [
            'current_state' => NewOrder::class,
        ]);
    }

    /**
     * Indicate that the job order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'current_state' => Completed::class,
        ]);
    }
}
