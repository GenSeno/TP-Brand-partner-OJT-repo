<?php

namespace App\Models;

use App\Lunar\Traits\LogsActivity;
use App\States\JobOrderState\Approval;
use App\States\JobOrderState\Artist;
use App\States\JobOrderState\Completed;
use App\States\JobOrderState\JobOrderState;
use App\States\JobOrderState\NewOrder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $job_order_id
 * @property int $product_id
 * @property ?int $inventory_item_id
 * @property ?string $file_path
 * @property ?string $notes
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class JobOrderProduct extends Model
{
    use LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'meta' => AsArrayObject::class,
    ];

    protected $appends = [
        'order_lines',
    ];

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)
            ->withTrashed();
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function orderLines(): Attribute
    {
        return Attribute::get(
            function () {
                $jobOrder = $this->jobOrder;

                if (!$jobOrder) {
                    return OrderLine::query()->whereRaw('1 = 0')->get();
                }

                /** @var Staff $staff */
                $staff = auth('staff')->user();
                $stages = JobOrderState::getPermittedStates($staff)->keys();

                $orderLines = OrderLine::with([
                    'productions' => function ($query) use ($jobOrder, $stages) {
                        $query->where('job_order_id', $jobOrder->id)
                            ->whereIn('state', $stages);
                    },
                    'assignments' => function ($query) use ($stages) {
                        $query->with('staff')
                            ->whereIn('state', $stages);
                    },
                ])
                    ->where('order_id', $jobOrder->order_id)
                    ->whereHasMorph('purchasable', ProductVariant::class, function ($query) {
                        $query->where('product_id', $this->product_id);
                    })
                    ->get();

                $startedStages = $jobOrder->stages()
                    ->whereNotNull('started_at')
                    ->whereNotIn('state', [NewOrder::$name, Artist::$name, Approval::$name, Completed::$name])
                    // ->whereIn('state', $stages)
                    ->orderBy('started_at')
                    ->get();

                return $orderLines->map(function ($line) use ($stages, $startedStages) {
                    $productionsByState = [];
                    $previousProduced = $line->quantity;

                    foreach ($startedStages as $stage) {
                        $morphClass = $stage->state->getMorphClass();
                        $producedInStage = $line->productions
                            ->where('state', $morphClass)
                            ->sum('quantity');

                        if ($stages->contains($morphClass)) {
                            $productionsByState[$morphClass] = [
                                'label' => $stage->state->getLabel() ?? '',
                                'quantity' => $producedInStage,
                                'received' => $previousProduced,
                                'meta' => $line->productions
                                    ->where('state', $morphClass)
                                    ->pluck('meta')
                                    ->filter()
                                    ->reduce(function ($carry, $meta) {
                                        return array_merge_recursive($carry ?? [], $meta ?? []);
                                    }, []),
                            ];
                        }

                        $previousProduced = $producedInStage;
                    }

                    $line->productions_by_state = $productionsByState;

                    return $line;
                });
            }
        );
    }
}
