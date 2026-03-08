<?php

namespace App\Observers;

use App\Actions\GenerateReference;
use App\Enums\InvoiceType;
use App\Enums\OrderStatus;
use App\Models\Invoice;
use App\Models\Order;
use Number;

class InvoiceObserver
{
    /**
     * Handle the Invoice "creating" event.
     */
    public function creating(Invoice $invoice): void
    {
        $invoice->description = match ($invoice->type) {
            InvoiceType::FINAL_PAYMENT => 'Final Payment',
            InvoiceType::DOWN_PAYMENT => 'Down Payment',
            InvoiceType::INSTALLMENT => $this->getInstallmentDescription($invoice->order),
            default => null,
        };
    }

    protected function getInstallmentDescription(Order $order): string
    {
        $count = $order->invoices()->count();
        return Number::ordinal($count + 1) . ' Payment';
    }

    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        $invoice->update([
            'reference' => GenerateReference::run($invoice->id, 'generator.invoice.reference_format'),
        ]);

        $order = $invoice->order;
        if ($order->status->is(OrderStatus::PENDING)) {
            $order->updateQuietly(['status' => OrderStatus::UNPAID]);
        }
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        if ($invoice->order->wasChanged('status')) {
            $invoice->order->logStatus(
                new: $invoice->status->value,
                previous: $invoice->getOriginal('status')->value
            );
        }
    }
}
