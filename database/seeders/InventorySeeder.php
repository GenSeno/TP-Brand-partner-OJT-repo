<?php

namespace Database\Seeders;

use App\Enums\InventoryMovementType;
use App\Models\InventoryItem;
use DB;

class InventorySeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inventories = $this->getSeedData('inventory');

        DB::transaction(function () use ($inventories) {
            $inventories->each(function ($inventory) {
                foreach ($inventory->items as $item) {
                    $inventoryItem = InventoryItem::firstOrCreate([
                        'type' => $inventory->type,
                        'item_name' => $item->item_name,
                    ], [
                        'uom_code' => $item->uom_code,
                    ]);

                    if (app()->environment('local', 'testing') && !$inventoryItem->movements()->exists()) {
                        $inventoryItem->movements()->create([
                            'type' => InventoryMovementType::ADDITION,
                            'adjustment' => false,
                            'amount' => $item->stock,
                            'notes' => 'Initial stock entry',
                        ]);
                    }
                }
            });
        });
    }
}
