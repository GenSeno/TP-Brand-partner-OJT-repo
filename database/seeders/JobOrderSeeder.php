<?php

namespace Database\Seeders;

use App\Enums\JobOrderUrgency;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\States\JobOrderState\NewOrder;
use DB;
use Illuminate\Database\Seeder;

class JobOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('local', 'testing')) {
            DB::transaction(function () {
                $orders = Order::where('status', '!=', OrderStatus::CANCELLED)
                    ->doesntHave('jobOrder')
                    ->inRandomOrder()
                    ->limit(5)
                    ->get();

                if ($orders->isEmpty()) {
                    $this->command->info('No orders found. Please seed orders before seeding job orders.');
                    return;
                }

                $orders->each(function ($order) {
                    $jobOrder = $order->jobOrder;
                    if (!$jobOrder) {
                        $orderedAt = $order->placed_at;
                        $leadTime = fake()->numberBetween(3, 14); // 3-14 days lead time
                        $dueAt = (clone $orderedAt)->modify("+{$leadTime} days");
                        $estimatedDelivery = (clone $dueAt)->modify('+1 day');

                        $jobOrder = $order->createJobOrder([
                            'urgency_flag' => fake()->randomElement(JobOrderUrgency::cases())->value,
                            'current_state' => NewOrder::$name,
                            'lead_time' => $leadTime,
                            'estimated_delivery' => $estimatedDelivery,
                            'ordered_at' => $orderedAt,
                            'due_at' => $dueAt,
                        ]);
                    }
                });
            });
        }
    }
}
