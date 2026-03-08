<?php

namespace App\Observers;

use App\Actions\GenerateReference;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\InvoiceService;
use App\States\JobOrderState\Cancelled;

class OrderObserver
{
    /**
     * Handle the Order "creating" event.
     */
    public function creating(Order $order): void
    {
        $owner = $order->orderable;
        $order->new_customer = Order::where('orderable_type', get_class($owner))
            ->where('orderable_id', $owner->getKey())
            ->doesntExist();

        $order->sub_total ??= 0;
        $order->discount_total ??= 0;
        $order->shipping_total ??= 0;
        $order->tax_total ??= 0;
        $order->total ??= 0;
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $order->update([
            'reference' => GenerateReference::run($order->id, 'generator.sales_order.reference_format'),
        ]);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status') && $order->getRawOriginal('status') !== null) {
            $order->logStatus(
                new: $order->status->value,
                previous: $order->getOriginal('status')->value
            );

            if ($order->status->is(OrderStatus::CANCELLED)) {
                $order->jobOrder->update(['cancelled_at' => now()]);
            }
        }
    }

    /**
     * Handle the Order "saving" event.
     */
    public function saving(Order $order): void
    {
        if ($order->shipping_breakdown !== null) {
            $order->shipping_total = $order->shipping_breakdown->items->sum(fn($item) => $item->price->value);
        } else {
            $order->shipping_total = 0;
        }
    }

    /**
     * Handle the Order "saved" event.
     */
    public function saved(Order $order): void
    {
        if ($order->invoices()->exists()) {
            (new InvoiceService)->resyncInvoiceFromOrder($order->refresh());
        }
    }
}
