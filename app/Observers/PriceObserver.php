<?php

namespace App\Observers;

use App\Models\Price;

class PriceObserver
{
    /**
     * Handle the Price "created" event.
     */
    public function saving(Price $price): void
    {
        //
    }

    /**
     * Handle the Price "updated" event.
     */
    public function updated(Price $price): void
    {
        //
    }

    /**
     * Handle the Price "deleted" event.
     */
    public function deleted(Price $price): void
    {
        //
    }

    /**
     * Handle the Price "restored" event.
     */
    public function restored(Price $price): void
    {
        //
    }

    /**
     * Handle the Price "force deleted" event.
     */
    public function forceDeleted(Price $price): void
    {
        //
    }
}
