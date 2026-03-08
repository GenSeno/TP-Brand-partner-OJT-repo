<?php

namespace App\Observers;

use App\Actions\GenerateReference;
use App\Models\PurchaseOrder;

class PurchaseOrderObserver
{
    /**
     * Handle the PurchaseOrder "created" event.
     */
    public function created(PurchaseOrder $purchaseOrder): void
    {
        $purchaseOrder->update([
            'reference' => GenerateReference::run($purchaseOrder->id, 'generator.purchase_order.reference_format'),
        ]);
    }
}
