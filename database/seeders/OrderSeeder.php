<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderLine;
use DB;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('local', 'testing')) {
            DB::transaction(function () {
                $orders = Order::factory()
                    ->placed()
                    ->count(10)
                    ->create();

                foreach ($orders as $order) {
                    OrderLine::factory()
                        ->physical()
                        ->count(fake()->numberBetween(1, 5))
                        ->create([
                            'order_id' => $order->id,
                        ]);

                    OrderAddress::factory()
                        ->shipping()
                        ->create([
                            'order_id' => $order->id,
                        ]);

                    OrderAddress::factory()
                        ->billing()
                        ->create([
                            'order_id' => $order->id,
                        ]);
                }
            });
        }
    }
}
