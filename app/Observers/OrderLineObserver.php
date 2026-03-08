<?php

namespace App\Observers;

use App\Models\OrderLine;
use App\Models\ProductVariant;
use App\Services\OrderPrintLineService;

class OrderLineObserver
{
    /**
     * Handle the OrderLine "creating" event.
     */
    public function creating(OrderLine $orderLine): void
    {
        if ($orderLine->purchasable instanceof ProductVariant) {
            $orderLine->description = $orderLine->purchasable->product->name;
            $orderLine->option = $orderLine->purchasable->description;
            $orderLine->identifier = $orderLine->purchasable->sku;
            $orderLine->unit_quantity ??= 1;
            $orderLine->sub_total = $orderLine->unit_price->value * $orderLine->quantity;
            $orderLine->tax_total ??= 0;
            $orderLine->total = $orderLine->sub_total->value + $orderLine->tax_total->value;
        }
    }

    /**
     * Handle the OrderLine "saved" event.
     */
    public function saved(OrderLine $orderLine): void
    {
        if ($orderLine->purchasable instanceof ProductVariant) {
            $orderLine->purchasable->checkTransactions();
        }

        $orderLine->order->recalculate();

        app(OrderPrintLineService::class)->createPrintLinesFromOrder(
            order: $orderLine->order,
            printableType: $orderLine->order->getMorphClass(),
            printableId: $orderLine->order->id,
        );
    }

    /**
     * Handle the OrderLine "deleted" event.
     */
    public function deleted(OrderLine $orderLine): void
    {
        if ($orderLine->purchasable instanceof ProductVariant) {
            $orderLine->purchasable->checkTransactions();
        }

        $orderLine->order->recalculate();

        app(OrderPrintLineService::class)->createPrintLinesFromOrder(
            order: $orderLine->order,
            printableType: $orderLine->order->getMorphClass(),
            printableId: $orderLine->order->id,
        );
    }
}
