<?php

namespace App\Observers;

use App\Models\JobOrderStage;

class JobOrderStageObserver
{
    /**
     * Handle the JobOrderStage "updated" event.
     */
    public function updated(JobOrderStage $jobOrderStage): void
    {
        if ($jobOrderStage->wasChanged(['started_at', 'completed_at'])) {
            $key = "job_order_{$jobOrderStage->job_order_id}_active_stages";
            if (cache()->has($key)) {
                cache()->forget($key);
            }
        }
    }
}
