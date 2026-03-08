<?php

namespace App\Models;

use App\Enums\JobOrderUrgency;
use App\Lunar\Traits\LogsActivity;
use App\Observers\JobOrderObserver;
use App\States\JobOrderState as Stage;
use App\States\JobOrderState\JobOrderState;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\ModelStates\HasStates;
use Spatie\ModelStates\HasStatesContract;

/**
 * @property int $id
 * @property int $order_id
 * @property ?string $reference
 * @property JobOrderUrgency $urgency_flag
 * @property JobOrderState $current_state
 * @property int $lead_time
 * @property ?\Illuminate\Support\Carbon $estimated_delivery
 * @property ?\Illuminate\Support\Carbon $ordered_at
 * @property ?\Illuminate\Support\Carbon $due_at
 * @property ?\Illuminate\Support\Carbon $cancelled_at
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
#[ObservedBy(JobOrderObserver::class)]
class JobOrder extends Model implements HasMedia, HasStatesContract
{
    /** @use HasFactory<\Database\Factories\JobOrderFactory> */
    use HasFactory;

    use HasStates;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'urgency_flag' => JobOrderUrgency::class,
        'current_state' => JobOrderState::class,
        'meta' => AsArrayObject::class,
        'cancelled_at' => 'datetime',
    ];

    protected $appends = [
        'current_state_data',
        'urgency_flag_data',
        'can_produce',
        'current_general_state',
        'total_quantity',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function stages()
    {
        return $this->hasMany(JobOrderStage::class);
    }

    public function products()
    {
        return $this->hasMany(JobOrderProduct::class);
    }

    public function productions()
    {
        return $this->hasMany(JobOrderProduction::class);
    }

    public function notes()
    {
        return $this->morphMany(ModelNote::class, 'model');
    }

    public function materials()
    {
        return $this->hasMany(JobOrderInventoryItem::class);
    }

    public function inventoryItems()
    {
        return $this->hasManyThrough(
            InventoryItem::class,
            JobOrderInventoryItem::class
        );
    }

    public function currentStateData(): Attribute
    {
        return Attribute::get(fn() => [
            'name' => $this->current_state->getMorphClass(),
            'label' => $this->current_state->getLabel(),
            'icon' => $this->current_state->getIcon(),
            'color' => $this->current_state->getColor(),
            'class' => $this->current_state::class,
            'next' => $this->current_state->getNext(),
        ]);
    }

    public function urgencyFlagData(): Attribute
    {
        return Attribute::get(fn() => [
            'value' => $this->urgency_flag->value,
            'label' => $this->urgency_flag->getLabel(),
            'color' => $this->urgency_flag->getColor(),
            'icon' => $this->urgency_flag->getIcon(),
        ]);
    }

    public function canProduce(): Attribute
    {
        return Attribute::get(
            fn() => !$this->current_state->in(
                Stage\NewOrder::class,
                Stage\Artist::class,
                Stage\Approval::class,
                Stage\Cancelled::class,
                Stage\Completed::class,
            ) && $this->stages()->whereNotNull('started_at')->exists()
        );
    }

    public function currentGeneralState(): Attribute
    {
        return Attribute::get(
            fn() => $this->current_state->is(Stage\Completed::class)
            ? 'Delivered'
            : 'Production'
        );
    }

    public function totalQuantity(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->order) {
                return 0;
            }

            return $this->order->lines->sum('quantity');
        });
    }

    public function productionsByLine(OrderLine $orderLine, Collection $stages): Collection
    {
        $productions = $this->productions()
            ->with('staff')
            ->where('order_line_id', $orderLine->id)
            // ->whereIn('state', $stages)
            ->get()
            ->groupBy('state');

        $startedStages = $this->stages()
            ->whereNotNull('started_at')
            ->whereNotIn('state', [Stage\NewOrder::$name, Stage\Artist::$name, Stage\Approval::$name, Stage\Completed::$name])
            // ->whereIn('state', $stages)
            ->orderBy('started_at')
            ->get();

        $result = collect();
        $previousProduced = $orderLine->quantity;

        foreach ($startedStages as $stage) {
            $morphClass = $stage->state->getMorphClass();
            $items = $productions->get($morphClass, collect());
            $total = $items->sum('quantity');
            $received = $previousProduced;
            $balance = max(0, $received - $total);

            if ($stages->contains($morphClass)) {
                $result[$morphClass] = [
                    'label' => $stage->state->getLabel() ?? '',
                    'items' => $items,
                    'total' => $total,
                    'received' => $received,
                    'balance' => $balance,
                ];
            }

            $previousProduced = $total;
        }

        return $result;
    }

    public function canBeCancelled(): bool
    {
        return true;
        // return $this->current_state->in(
        //     Stage\NewOrder::class,
        //     Stage\Artist::class,
        // );
    }

    public function scopeUrgent(Builder $query, JobOrderUrgency|string ...$urgency_flag): Builder
    {
        return $query->whereIn('urgency_flag', array_map(
            fn($flag) => $flag instanceof JobOrderUrgency ? $flag->value : $flag,
            $urgency_flag
        ));
    }

    public function scopeUrgentFirst(Builder $query): Builder
    {
        return $query->orderBy('urgency_flag');
    }

    public function scopeStage(Builder $query, $stage): Builder
    {
        $progress = request()->input('misc.progress', 'pending');

        if ($progress === 'cancelled') {
            $query->where('current_state', Stage\Cancelled::$name);
        }

        return $query->when($stage, fn($q) => $q->where('current_state', $stage)
            ->orWhereHas('stages', function ($q) use ($stage, $progress) {
                $q->where('state', $stage);

                if ($progress === 'pending') {
                    $q->whereNotNull('started_at')->whereNull('completed_at');
                }

                if ($progress === 'completed') {
                    $q->whereNotNull('completed_at');
                }
            }));
    }

    public function scopeAuthorized(Builder $query, Staff $staff): Builder
    {
        $stages = JobOrderState::getPermittedStates($staff)->keys();

        return $query->whereIn('current_state', $stages)
            ->orWhere(function ($q) use ($stages) {
                $q->whereHas('stages', function ($q) use ($stages) {
                    $q->whereIn('state', $stages)
                        ->whereNotNull('started_at')
                        ->whereNull('completed_at');
                });

            });
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value)) {
            return $query;
        }

        return $query->where(function ($q) use ($value) {
            $q->where('reference', 'like', "%{$value}%");
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')
            ->useDisk('private');
    }

    public static function getDefaultLogExcept(): array
    {
        return [
            'reference',
            'current_state',
            'urgency_flag',
        ];
    }
}
