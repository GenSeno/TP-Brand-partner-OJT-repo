<?php

namespace App\Models;

use App\Lunar\Traits\LogsActivity;
use App\Observers\JobOrderStageObserver;
use App\States\JobOrderState\JobOrderState;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $job_order_id
 * @property int|null $operator_id
 * @property JobOrderState $state
 * @property ?\Illuminate\Support\Carbon $started_at
 * @property ?\Illuminate\Support\Carbon $completed_at
 * @property ?\Illuminate\Support\Carbon $due_at
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property Staff|null $operator
 */
#[ObservedBy(JobOrderStageObserver::class)]
class JobOrderStage extends Model
{
    use LogsActivity;

    protected $guarded = [];

    protected static $recordEvents = ['updated'];

    protected $casts = [
        'state' => JobOrderState::class,
        'completed_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    protected $appends = [
        'permission',
    ];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function operator()
    {
        return $this->belongsTo(Staff::class, 'operator_id');
    }

    public function complete()
    {
        if ($this->completed_at)
            return false;

        return $this->forceFill([
            'completed_at' => now(),
        ])->save();
    }

    public function permission(): Attribute
    {
        return Attribute::get(
            fn() => $this->state->getPermission()
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotNull('started_at')->whereNull('completed_at');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('completed_at');
    }

    public static function getActiveStages($job_order_id): Collection
    {
        $key = "job_order_{$job_order_id}_active_stages";

        if (!cache()->has($key)) {
            $stages = self::query()
                ->active()
                ->where('job_order_id', $job_order_id)
                ->orderBy('started_at')
                ->get();

            cache()->forever($key, $stages);
        }

        return cache()->get($key, collect());
    }

    public static function getActiveStagePermissions($job_order_id): Collection
    {
        return self::getActiveStages($job_order_id)->map(
            fn($stage) => $stage->state->getPermission()
        );
    }
}
