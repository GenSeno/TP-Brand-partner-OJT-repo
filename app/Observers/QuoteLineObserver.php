<?php

namespace App\Observers;

use App\Models\ProductVariant;
use App\Models\QuoteLine;

class QuoteLineObserver
{
    /**
     * Handle the QuoteLine "created" event.
     */
    public function saved(QuoteLine $quoteLine): void
    {
        if ($quoteLine->purchasable instanceof ProductVariant) {
            $quoteLine->purchasable->checkTransactions();
        }
    }

    /**
     * Handle the QuoteLine "deleted" event.
     */
    public function deleted(QuoteLine $quoteLine): void
    {
        if ($quoteLine->purchasable instanceof ProductVariant) {
            $quoteLine->purchasable->checkTransactions();
        }
    }
}
