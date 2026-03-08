<?php

namespace App\Observers;

use App\Actions\GenerateReference;
use App\Models\Payment;

class PaymentObserver
{
    /**
     * Handle the Payment "creating" event.
     */
    public function creating(Payment $payment): void
    {
        $payment->created_by = auth()->id();
    }

    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        if (!$payment->internal_reference) {
            $payment->update([
                'internal_reference' => GenerateReference::run($payment->id, 'generator.payment.reference_format'),
            ]);
        }
    }

    /**
     * Handle the Payment "saved" event.
     */
    public function saved(Payment $payment): void
    {
        $payment->checkOrderInvoice();
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        $payment->checkOrderInvoice();
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        $payment->checkOrderInvoice();
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        $payment->checkOrderInvoice();
    }
}
