<?php

namespace App\Traits;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasStaffTracking
{
    /**
     * Boot the trait.
     */
    protected static function bootHasStaffTracking(): void
    {
        static::creating(function ($model) {
            if (auth()->guard('staff')->check()) {
                $model->created_by = $model->created_by ?? auth()->guard('staff')->id();
                $model->updated_by = auth()->guard('staff')->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->guard('staff')->check()) {
                $model->updated_by = auth()->guard('staff')->id();
            }
        });
    }

    /**
     * Get the staff member who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    /**
     * Get the staff member who last updated the record.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }
}
