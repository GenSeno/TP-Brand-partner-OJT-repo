<?php

namespace App\Observers;

use App\Actions\GenerateReference;
use App\Models\JobOrder;
use App\States\JobOrderState\Cancelled;
use App\States\JobOrderState\Completed;
use App\States\JobOrderState\JobOrderState;
use App\States\JobOrderState\NewOrder;

class JobOrderObserver
{
    /**
     * Handle the JobOrder "created" event.
     */
    public function created(JobOrder $jobOrder): void
    {
        $jobOrder->update([
            'reference' => GenerateReference::run($jobOrder->id, 'generator.job_order.reference_format'),
        ]);

        JobOrderState::allOrdered()
            ->except(NewOrder::$name, Completed::$name, Cancelled::$name)
            ->each(function ($state) use ($jobOrder) {
                $jobOrder->stages()->createOrFirst([
                    'state' => $state,
                ]);
            });
    }

    /**
     * Handle the JobOrder "updated" event.
     */
    public function updated(JobOrder $jobOrder): void
    {
        if ($jobOrder->wasChanged('current_state') && $jobOrder->getOriginal('current_state') !== null) {
            $jobOrder->logStatus(
                new: $jobOrder->current_state->getValue(),
                previous: $jobOrder->getOriginal('current_state')->getValue(),
                description: 'state-transition',
            );
        }
        if ($jobOrder->wasChanged('urgency_flag') && $jobOrder->getOriginal('urgency_flag') !== null) {
            $jobOrder->logStatus(
                new: $jobOrder->urgency_flag->value,
                previous: $jobOrder->getOriginal('urgency_flag')->value,
                description: 'urgency-flag-update',
            );
        }
    }
}
