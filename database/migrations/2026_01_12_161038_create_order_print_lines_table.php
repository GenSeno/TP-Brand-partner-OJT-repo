<?php

use App\Models\Order;
use App\Models\Quote;
use App\Services\OrderPrintLineService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_print_lines', function (Blueprint $table) {
            $table->id();

            $table->morphs('printable');
            $table->foreignId('product_id')->constrained();
            $table->string('product_name');
            $table->string('sku')->nullable();
            $table->string('uom_code')->default('pc');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedSmallInteger('quantity');
            $table->unsignedInteger('total');
            $table->json('options_payload');
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });

        Order::chunk(100, function ($orders) {
            $service = app(OrderPrintLineService::class);

            foreach ($orders as $order) {
                $service->createPrintLinesFromOrder(
                    order: $order,
                    printableType: Order::class,
                    printableId: $order->id,
                );
            }
        });

        // Quote::chunk(100, function ($quotes) {
        //     $service = app(OrderPrintLineService::class);

        //     foreach ($quotes as $quote) {
        //         $service->createPrintLinesFromOrder(
        //             order: $quote,
        //             printableType: Quote::class,
        //             printableId: $quote->id,
        //         );
        //     }
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_print_lines');
    }
};
