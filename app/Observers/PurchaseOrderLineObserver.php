<?php

namespace App\Observers;

use App\Models\PurchaseOrderLine;

class PurchaseOrderLineObserver
{
    /**
     * Handle the PurchaseOrderLine "saved" event.
     */
    public function saved(PurchaseOrderLine $line): void
    {
        $line->purchaseOrder->calculateTotals();
    }

    /**
     * Handle the PurchaseOrderLine "deleted" event.
     */
    public function deleted(PurchaseOrderLine $line): void
    {
        $line->purchaseOrder->calculateTotals();
    }
}
