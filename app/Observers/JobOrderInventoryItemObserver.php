<?php

namespace App\Observers;

use App\Enums\InventoryMovementType;
use App\Models\JobOrder;
use App\Models\JobOrderInventoryItem;
use DB;
use Illuminate\Database\QueryException;

class JobOrderInventoryItemObserver
{
    /**
     * Handle the JobOrderInventoryItem "created" event.
     */
    public function created(JobOrderInventoryItem $item): void
    {
        if ($item->amount_used > 0) {
            $item->inventoryItem->movements()->create([
                'source_type' => JobOrder::class,
                'source_id' => $item->job_order_id,
                'type' => InventoryMovementType::DEDUCTION,
                'adjustment' => false,
                'amount' => $item->amount_used,
                'notes' => 'Used in Job Order #' . $item->job_order_id,
            ]);
        }
    }

    /**
     * Handle the JobOrderInventoryItem "deleting" event.
     * Reverses the stock deduction when a usage record is removed.
     */
    public function deleting(JobOrderInventoryItem $item): void
    {
        if ($item->amount_used > 0) {
            $item->inventoryItem->movements()->create([
                'source_type' => JobOrder::class,
                'source_id' => $item->job_order_id,
                'type' => InventoryMovementType::ADDITION,
                'adjustment' => false,
                'amount' => $item->amount_used,
                'notes' => 'Reverted usage in Job Order #' . $item->job_order_id,
            ]);
        }
    }

    /**
     * Handle the JobOrderInventoryItem "updated" event.
     */
    public function updated(JobOrderInventoryItem $item): void
    {
        if ($item->wasChanged('amount_used') === false) {
            return;
        }

        try {
            DB::transaction(function () use ($item) {
                $previous = (float) $item->getOriginal('amount_used');
                $current = (float) $item->amount_used;
                $delta = $current - $previous;

                // Use loose == to handle float 0.0 vs int 0
                if ($delta == 0) {
                    return;
                }

                if ($delta > 0 && $item->inventoryItem->current_stock < $delta) {
                    if (config('inventory.stop_on_insufficient_stock')) {
                        throw new \LogicException('Insufficient stock');
                    }
                }

                $item->inventoryItem->movements()->create([
                    'source_type' => JobOrder::class,
                    'source_id' => $item->job_order_id,
                    'type' => $delta > 0
                        ? InventoryMovementType::DEDUCTION
                        : InventoryMovementType::ADDITION,
                    'adjustment' => false,
                    'amount' => abs($delta),
                    'notes' => $delta > 0
                        ? 'Used in Job Order #' . $item->job_order_id
                        : 'Reverted usage in Job Order #' . $item->job_order_id,
                ]);
            });
        } catch (QueryException $e) {
            if ($e->getCode() === '1205') {
                throw new \RuntimeException('Inventory is busy. Try again.');
            }

            throw $e;
        }
    }
}
