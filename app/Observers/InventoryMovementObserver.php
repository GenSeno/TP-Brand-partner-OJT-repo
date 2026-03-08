<?php

namespace App\Observers;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use DB;

class InventoryMovementObserver
{
    /**
     * Handle the InventoryMovement "created" event.
     */
    public function created(InventoryMovement $movement): void
    {
        DB::afterCommit(function () use ($movement) {
            DB::transaction(function () use ($movement) {
                $item = $movement->item()->lockForUpdate()->first();

                if (
                    $movement->type->is(InventoryMovementType::DEDUCTION)
                    && $item->current_stock < $movement->amount
                ) {
                    if (config('inventory.stop_on_insufficient_stock')) {
                        throw new \LogicException('Insufficient stock');
                    }
                }

                if ($movement->type->is(InventoryMovementType::ADDITION)) {
                    $item->increment('current_stock', $movement->amount);
                } else {
                    $item->decrement('current_stock', $movement->amount);
                }
            });
        });
    }
}
