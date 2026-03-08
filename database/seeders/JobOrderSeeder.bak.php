<?php

namespace Database\Seeders;

use App\Models\JobOrder;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\ProductVariant;
use App\States\JobOrderState\Cancelled;
use App\States\JobOrderState\JobOrderState;
use App\States\JobOrderState\NewOrder;
use Carbon\Carbon;
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
                $stages = JobOrderState::allOrdered()->except(Cancelled::$name);

                $jobOrders = JobOrder::factory()->count(10)->create();

                foreach ($jobOrders as $jobOrder) {
                    $lines = OrderLine::where('purchasable_type', ProductVariant::class)
                        ->where('order_id', $jobOrder->order_id)
                        ->get();

                    $product_ids = ProductVariant::query()
                        ->whereIn('id', $lines->pluck('purchasable_id'))
                        ->pluck('product_id')
                        ->unique();

                    $product_ids->each(function ($product_id) use ($jobOrder) {
                        $jobOrder->products()->create([
                            'product_id' => $product_id,
                        ]);
                    });

                    if (!$jobOrder->current_state->is(NewOrder::class)) {
                        $completedStages = $stages->takeWhile(fn($_, $state) => $state !== $jobOrder->current_state->getValue());

                        foreach ($completedStages as $state => $class) {
                            $process = $jobOrder->stages()->firstWhere('state', $state);
                            if (!$process)
                                continue;

                            $startedAt = $process->started_at ?? fake()->dateTimeBetween($jobOrder->ordered_at, '+3 days');
                            $completedAt = fake()->dateTimeBetween($startedAt, Carbon::parse($startedAt)->addWeeks(1));
                            $dueAt = fake()->dateTimeBetween($completedAt, Carbon::parse($completedAt)->addWeeks(2));

                            $process->update([
                                'started_at' => $startedAt,
                                'completed_at' => $process->completed_at ?? $completedAt,
                                'due_at' => $process->due_at ?? $dueAt,
                            ]);

                            $nextState = $class::getNext();

                            if ($nextState) {
                                $nextStage = $jobOrder->stages()->firstWhere('state', $nextState::$name);
                                $nextStage?->update([
                                    'started_at' => $completedAt,
                                ]);
                            }

                            $lines->each(function ($line) use ($jobOrder, $state) {
                                $jobOrder->productions()->create([
                                    'order_line_id' => $line->id,
                                    'state' => $state,
                                    'quantity' => $line->quantity,
                                ]);
                            });
                        }

                        // Ensure the current stage is started (handles cases where
                        // predecessor stages have no stage row, e.g. new-order → artist)
                        $currentStage = $jobOrder->stages()->firstWhere('state', $jobOrder->current_state->getValue());
                        if ($currentStage && !$currentStage->started_at) {
                            $currentStage->update([
                                'started_at' => fake()->dateTimeBetween($jobOrder->ordered_at, '+3 days'),
                            ]);
                        }
                    }
                }
            });
        }
    }
}
