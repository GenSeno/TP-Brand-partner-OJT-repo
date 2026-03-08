<?php

namespace Database\Seeders;

use App\Models\JobOrder;
use App\Models\JobOrderStage;
use App\States\JobOrderState\Approval;
use App\States\JobOrderState\Artist;
use App\States\JobOrderState\NewOrder;
use Illuminate\Database\Seeder;

class AddApprovalStageToJobOrderStages extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobOrderIds = JobOrder::whereIn('current_state', [
            NewOrder::$name,
            Artist::$name,
        ])->pluck('id');

        foreach ($jobOrderIds as $jobOrderId) {
            JobOrderStage::firstOrCreate([
                'job_order_id' => $jobOrderId,
                'state' => Approval::$name,
            ]);
        }
    }
}
