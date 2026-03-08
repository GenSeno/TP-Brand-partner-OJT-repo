<?php

namespace Database\Seeders;

use App\Models\JobOrder;
use App\Models\JobOrderInventoryItem;
use App\Models\JobOrderProduction;
use App\Models\JobOrderStage;
use App\States\JobOrderState\HeatPress;
use App\States\JobOrderState\Sewing;
use Illuminate\Database\Seeder;

class JobOrderRevision extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update Job Orders and Job Order Stages with the new state names
        JobOrder::where('current_state', 'heat-press')
            ->update(['current_state' => HeatPress::$name]);

        JobOrder::where('current_state', 'cutting/sewing')
            ->update(['current_state' => Sewing::$name]);


        // Update Job Order Stages with the new state names
        JobOrderStage::where('state', 'heat-press')
            ->update(['state' => HeatPress::$name]);

        JobOrderStage::where('state', 'cutting/sewing')
            ->update(['state' => Sewing::$name]);


        // Update Job Order Inventory Items with the new stage names
        JobOrderInventoryItem::where('stage', 'heat-press')
            ->update(['stage' => HeatPress::$name]);

        JobOrderInventoryItem::where('stage', 'cutting/sewing')
            ->update(['stage' => Sewing::$name]);


        // Update Job Order Productions with the new state names
        JobOrderProduction::where('state', 'heat-press')
            ->update(['state' => HeatPress::$name]);

        JobOrderProduction::where('state', 'cutting/sewing')
            ->update(['state' => Sewing::$name]);

        JobOrder::select('id')->orderBy('id')->each(function (JobOrder $jobOrder) {
            cache()->forget("job_order_{$jobOrder->id}_active_stages");
        });
    }
}
