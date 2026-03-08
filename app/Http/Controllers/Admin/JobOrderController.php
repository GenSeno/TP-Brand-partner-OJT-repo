<?php

namespace App\Http\Controllers\Admin;

use App\Constants\StaffPermission;
use App\Enums\InventoryType;
use App\Enums\JobOrderUrgency;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobOrderInventoryRequest;
use App\Http\Requests\JobOrderProductionRequest;
use App\Http\Requests\JobOrderRequest;
use App\Http\Requests\JobOrderSewerAssignmentRequest;
use App\Models\InventoryItem;
use App\Models\JobOrder;
use App\Models\JobOrderStage;
use App\Models\OrderLine;
use App\Models\Staff;
use App\States\JobOrderState\Approval;
use App\States\JobOrderState\Artist;
use App\States\JobOrderState\Cancelled;
use App\States\JobOrderState\Completed;
use App\States\JobOrderState\Dispatching;
use App\States\JobOrderState\HeatPress;
use App\States\JobOrderState\JobOrderState;
use App\States\JobOrderState\NewOrder;
use App\States\JobOrderState\Packing;
use App\States\JobOrderState\Printing;
use App\States\JobOrderState\Sewing;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class JobOrderController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JobOrder::urgentFirst()
            ->authorized($request->user('staff'));

        $jobOrders = QueryBuilder::for($query)
            ->with(['order.orderable', 'order.lines', 'stages.operator'])
            ->allowedSorts(['reference', 'ordered_at', 'due_at', AllowedSort::field('customer', 'customer.first_name')])
            ->defaultSort(['ordered_at'])
            ->allowedFilters([
                AllowedFilter::exact('urgency_flag'),
                AllowedFilter::callback('stage', function (Builder $query, $value) use ($request) {
                    $progress = $request->input('misc.progress', 'pending');
                    $this->stageProgressFilter($query, $progress, $value);
                })->default('all'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('admin/job-order/index', [
            'jobOrders' => $jobOrders,
            'urgencyFlags' => fn() => JobOrderUrgency::getOptions(),
            'states' => function () use ($request) {
                $states = JobOrderState::getPermittedStates(
                    staff: $request->user('staff'),
                    except: [Cancelled::class, Completed::class]
                );

                $stageCounts = JobOrderStage::query()
                    ->selectRaw('job_order_stages.state, COUNT(*) as count')
                    ->join('job_orders', 'job_order_stages.job_order_id', '=', 'job_orders.id')
                    ->whereNotNull('job_order_stages.started_at')
                    ->whereNull('job_order_stages.completed_at')
                    ->whereIn('job_order_stages.state', $states->keys())
                    ->whereNull('job_orders.cancelled_at')
                    ->groupBy('job_order_stages.state')
                    ->pluck('count', 'job_order_stages.state');

                $tabs = $states->mapWithKeys(function ($class, $state) use ($stageCounts) {
                    $count = match ($state) {
                        NewOrder::$name => JobOrder::query()
                            ->where('current_state', NewOrder::$name)
                            ->whereNull('cancelled_at')
                            ->count(),
                        // Completed::$name => JobOrder::query()
                        //     ->where('current_state', Completed::$name)
                        //     ->whereNull('cancelled_at')
                        //     ->count(),
                        default => $stageCounts[$state] ?? 0,
                    };

                    return [
                        $state => [
                            'label' => $class::getLabel(),
                            'icon' => $class::getIcon(),
                            'color' => $class::getColor(),
                            'count' => $count,
                        ],
                    ];
                });

                return $tabs->all();
            },
            'total' => fn() => ['all' => $jobOrders->total()] + JobOrderUrgency::collection()
                ->mapWithKeys(fn($urgency) => [
                    $urgency->value => $jobOrders->where('urgency_flag', $urgency)->count(),
                ])->all(),
            'filter' => $request->input('filter', []) + [
                'default_per_page' => $this->defaultPerPage,
            ],
            'misc' => $request->input('misc', []),
        ]);
    }

    protected function stageProgressFilter(Builder $query, $progress = 'pending', $stage = 'all')
    {
        if ($progress === 'cancelled') {
            $query->whereNotNull('cancelled_at');
        } else {
            $query->whereNull('cancelled_at');
        }

        if ($stage === NewOrder::$name) {
            $query->where('current_state', $stage);
        } elseif ($stage !== 'all') {
            $query->whereHas('stages', function ($q) use ($stage, $progress) {
                $q->where('state', $stage);

                if ($progress === 'pending' || $progress === 'cancelled') {
                    $q->whereNotNull('started_at')->whereNull('completed_at');
                }

                if ($progress === 'completed') {
                    $q->whereNotNull('completed_at');
                }
            });
        } elseif ($progress === 'completed') {
            $query->where('current_state', Completed::$name);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, JobOrder $jobOrder)
    {
        $staff = $this->isStaffAuthorized(
            $request->user('staff'),
            $jobOrder
        );

        $viewingStage = null;
        if ($stage = $request->query('stage', $jobOrder->current_state->getMorphClass())) {
            if (JobOrderState::all()->has($stage)) {
                $viewingStage = $stage;
            }
        }

        return Inertia::modal('admin/job-order/edit', [
            'jobOrder' => $jobOrder->load([
                'order.orderable',
                'order.currency',
                'products.product',
                'products.inventoryItem',
                'stages.operator',
                'materials',
            ]),
            'viewingStage' => $viewingStage,
            'notes' => fn() => $jobOrder->notes()->with('author')->get(),
            'activities' => fn() => $jobOrder->activities()->latest()->with('causer')->take(100)->get(),
            'nextStage' => function () use ($jobOrder, $viewingStage) {
                if ($viewingStage) {
                    $stateClass = JobOrderState::all()->get($viewingStage);

                    return $stateClass ? $stateClass::getNextLabel() : null;
                }

                return $jobOrder->current_state->getNextLabel();
            },
            'urgencyFlags' => fn() => JobOrderUrgency::getOptions(),
            'fabrics' => function () {
                return InventoryItem::type(InventoryType::FABRIC)->available()->get();
            },
            'isCancelled' => fn() => $jobOrder->cancelled_at !== null,
            'states' => fn() => JobOrderState::allOrdered()->mapWithKeys(fn($class, $state) => [
                $state => [
                    'label' => $class::getLabel(),
                    'icon' => $class::getIcon(),
                    'color' => $class::getColor(),
                    'permission' => $class::getPermission(),
                ],
            ])->all(),
            'materials' => function () use ($jobOrder, $staff, $viewingStage) {
                $stages = JobOrderStage::getActiveStages($jobOrder->id);

                return $stages->map(function ($stage) use ($staff, $viewingStage) {
                    $stageName = $stage->state->getMorphClass();

                    // When viewing a specific stage, only show that stage's inventory table.
                    if ($viewingStage && $stageName !== $viewingStage) {
                        return null;
                    }

                    $permission = $stage->state->getPermission();
                    if ($staff->can($permission)) {
                        $materials = [
                            'state' => $stage->state->getLabel(),
                            'stage' => $stageName,
                        ];

                        return match ($stageName) {
                            Printing::$name => $materials + [
                                'type' => 'multiple',
                                'items' => InventoryItem::type(InventoryType::SUBLI_PAPER)->get(),
                            ],
                            HeatPress::$name => $materials + [
                                'type' => 'multiple',
                                'items' => InventoryItem::type(InventoryType::FABRIC)->get(),
                            ],
                            Sewing::$name => $materials + [
                                'type' => 'multiple',
                                'items' => InventoryItem::type(InventoryType::THREAD)->get(),
                            ],
                            Packing::$name => $materials + [
                                'type' => 'multiple',
                                'items' => InventoryItem::type(InventoryType::PACKAGING)->get(),
                            ],
                            default => null
                        };
                    }
                })->filter()->values();
            },
            'canSubmitToNextStage' => function () use ($jobOrder, $staff, $viewingStage) {
                // When viewing a specific stage, only allow submission if current_state matches
                if ($viewingStage && $viewingStage !== $jobOrder->current_state->getMorphClass()) {
                    return false;
                }

                $currentState = $jobOrder->current_state;

                if ($jobOrder->cancelled_at !== null || $currentState instanceof Completed) {
                    return false;
                }

                if ($currentState instanceof NewOrder) {
                    return $this->canSubmitNewOrder($jobOrder);
                }

                if ($currentState instanceof Artist || $currentState instanceof Approval) {
                    return $this->canSubmitArtistStage($jobOrder);
                }

                if ($currentState instanceof Dispatching) {
                    if (!$this->canSubmitProductionStage($jobOrder, $staff)) {
                        return false;
                    }
                    return $this->canSubmitDispatchingStage($jobOrder);
                }

                if (!$this->canSubmitProductionStage($jobOrder, $staff)) {
                    return false;
                }

                return $this->validateInventoryUsage($jobOrder)['valid'];
            },
        ])->baseRoute('admin.job-order.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobOrderRequest $request, JobOrder $jobOrder)
    {
        if ($jobOrder->cancelled_at !== null) {
            return response()->json([
                'message' => 'Cancelled job orders cannot be edited.',
            ], 422);
        }

        $staff = $this->isStaffAuthorized(
            $request->user('staff'),
            $jobOrder
        );

        DB::transaction(function () use ($request, $jobOrder, $staff) {
            $jobOrder->update([
                'urgency_flag' => $request->input('urgency_flag'),
            ]);

            if ($staff->can(StaffPermission::MANAGE_JO_DEADLINES)) {
                foreach ($request->input('deadlines', []) as $deadlineData) {
                    $stage = $jobOrder->stages()->where('state', $deadlineData['state'])->first();
                    if ($stage) {
                        $stage->update([
                            'due_at' => $deadlineData['due_at'],
                        ]);
                    }
                }
            }

            if (
                $staff->can(StaffPermission::MANAGE_JO_NEW_ORDER_TASKS) ||
                $staff->can(StaffPermission::MANAGE_JO_ARTIST_TASKS)
            ) {
                foreach ($request->input('products', []) as $productData) {
                    $product = $jobOrder->products()->find($productData['id']);
                    if ($product) {
                        if ($staff->can(StaffPermission::MANAGE_JO_NEW_ORDER_TASKS)) {
                            $product->inventory_item_id = $productData['inventory_item_id'];
                            $product->notes = $productData['notes'];
                        }

                        if ($staff->can(StaffPermission::MANAGE_JO_ARTIST_TASKS)) {
                            $product->file_path = $productData['file_path'];
                        }

                        $product->save();
                    }
                }
            }

            if (
                $staff->can(StaffPermission::MANAGE_JO_DISPATCHING_TASKS) &&
                $request->has('dispatching')
            ) {
                $jobOrder->update([
                    'meta' => array_merge(
                        $jobOrder->meta ? $jobOrder->meta->toArray() : [],
                        $request->input('dispatching', [])
                    ),
                ]);
            }
        });

        return response()->json([
            'jobOrder' => $jobOrder,
            'message' => 'Job Order updated successfully.',
        ]);
    }

    public function setProduced(Request $request, JobOrder $jobOrder, OrderLine $orderLine)
    {
        if ($jobOrder->cancelled_at !== null) {
            return response()->json([
                'message' => 'Cannot set production for a cancelled job order.',
            ], 422);
        }

        $staff = $this->isStaffAuthorized(
            $request->user('staff'),
            $jobOrder
        );

        $viewingStage = $request->query('stage');

        if ($viewingStage === Sewing::$name) {
            $productions = $jobOrder->productionsByLine($orderLine, collect([Sewing::$name]));
            if ($productions->isEmpty()) {
                abort(404, 'No producible stages found for this order line.');
            }

            return Inertia::modal('admin/job-order/production/sewer-production', [
                'jobOrder' => $jobOrder,
                'orderLine' => $orderLine->load('purchasable'),
                'productions' => $productions,
                'sewers' => fn() => $orderLine->assignments()
                    ->where('state', Sewing::$name)
                    ->with('staff')
                    ->get(),
            ]);
        }

        $stages = JobOrderState::getPermittedStates($staff)->keys();
        $productions = $jobOrder->productionsByLine($orderLine, $stages);

        if ($productions->isEmpty()) {
            abort(404, 'No producible stages found for this order line.');
        }

        return Inertia::modal('admin/job-order/set-produced', [
            'jobOrder' => $jobOrder,
            'orderLine' => $orderLine->load('purchasable'),
            'productions' => $productions,
            'viewingStage' => $viewingStage,
            'sewers' => $viewingStage === Sewing::$name
                ? Staff::permission(Sewing::$permission)->get()->map(fn($s) => [
                    'id' => $s->id,
                    'full_name' => $s->full_name,
                ])
                : null,
        ]);
    }

    public function completeProduced(Request $request, JobOrder $jobOrder, OrderLine $orderLine)
    {
        if ($jobOrder->cancelled_at !== null) {
            return response()->json([
                'message' => 'Cannot complete production for a cancelled job order.',
            ], 422);
        }

        $staff = $this->isStaffAuthorized(
            $request->user('staff'),
            $jobOrder
        );

        $stages = JobOrderState::getPermittedStates($staff)->keys();
        $productions = $jobOrder->productionsByLine($orderLine, $stages);

        if ($productions->isEmpty()) {
            abort(404, 'No producible stages found for this order line.');
        }

        $viewingStage = $request->query('stage');

        return Inertia::modal('admin/job-order/complete-produced', [
            'jobOrder' => $jobOrder,
            'orderLine' => $orderLine->load('purchasable'),
            'productions' => $productions,
            'viewingStage' => $viewingStage,
        ]);
    }

    public function updateProduced(JobOrderProductionRequest $request, JobOrder $jobOrder)
    {
        if ($jobOrder->cancelled_at !== null) {
            return response()->json([
                'message' => 'Cannot update production for a cancelled job order.',
            ], 422);

        }
        $staff = $this->isStaffAuthorized(
            $request->user('staff'),
            $jobOrder
        );

        DB::transaction(function () use ($request, $jobOrder) {
            $jobOrder->productions()->create(
                [
                    ...$request->only(['order_line_id', 'state', 'quantity', 'meta']),
                    'staff_id' => $request->input('staff_id', $request->user('staff')->id)
                ]
            );

            // Auto-start the next production stage
            $this->autoStartNextStage($jobOrder, $request->input('state'));

            // Check and complete any stages that are now finished
            $this->checkAndCompleteStages($jobOrder);
        });

        return response()->noContent();
    }

    public function setInventoryUsage(JobOrderInventoryRequest $request, JobOrder $jobOrder)
    {
        if ($jobOrder->cancelled_at !== null) {
            return response()->json([
                'message' => 'Cannot set inventory usage for a cancelled job order.',
            ], 422);
        }

        $this->isStaffAuthorized(
            $request->user('staff'),
            $jobOrder
        );

        $currentStage = $jobOrder->current_state->getMorphClass();

        // Prevent editing once the stage has been completed
        $stageRecord = $jobOrder->stages()->where('state', $currentStage)->first();
        if ($stageRecord && $stageRecord->completed_at) {
            return response()->json(['message' => 'This stage is already completed.'], 422);
        }

        DB::transaction(function () use ($request, $jobOrder, $currentStage) {
            // Collect valid submitted IDs (ignore empty dropdown rows)
            $submittedItemIds = collect($request->input('items', []))
                ->pluck('inventory_item_id')
                ->filter()
                ->values();

            // Delete items that were removed from the list.
            // The deleting observer fires for each, creating a reversal ADDITION movement.
            $jobOrder->materials()
                ->where('stage', $currentStage)
                ->whereNotIn('inventory_item_id', $submittedItemIds)
                ->each(fn($material) => $material->delete());

            foreach ($request->input('items', []) as $item) {
                if (empty($item['inventory_item_id'])) {
                    continue;
                }

                $jobOrder->materials()->updateOrCreate(
                    ['inventory_item_id' => $item['inventory_item_id']],
                    [
                        'stage' => $currentStage,
                        // ?: 0 catches both null and empty string from the frontend
                        'amount_used' => $item['amount_used'] ?: 0,
                    ]
                );
            }
        });

        return response()->noContent();
    }

    public function submitStage(Request $request, JobOrder $jobOrder)
    {
        if ($jobOrder->cancelled_at !== null) {
            return response()->json([
                'message' => 'Cannot submit a cancelled job order.',
            ], 422);
        }

        $staff = $this->isStaffAuthorized(
            $request->user('staff'),
            $jobOrder
        );

        $currentState = $jobOrder->current_state;

        // Validate submission based on current state
        if ($currentState instanceof NewOrder) {
            if (!$this->canSubmitNewOrder($jobOrder)) {
                return response()->json([
                    'message' => 'Please set all stage deadlines and product fabrics before submitting.',
                ], 422);
            }
        } elseif ($currentState instanceof Artist || $currentState instanceof Approval) {
            if (!$this->canSubmitArtistStage($jobOrder)) {
                return response()->json([
                    'message' => 'Please upload all required design files before submitting.',
                ], 422);
            }
        } elseif ($currentState instanceof Completed) {
            return response()->json([
                'message' => 'This job order is already completed.',
            ], 422);
        } else {
            if (!$this->canSubmitProductionStage($jobOrder, $staff)) {
                return response()->json([
                    'message' => 'Please record at least one production before submitting.',
                ], 422);
            }

            $inventoryValidation = $this->validateInventoryUsage($jobOrder);
            if (!$inventoryValidation['valid']) {
                return response()->json([
                    'message' => $inventoryValidation['message'],
                ], 422);
            }
        }

        DB::transaction(function () use ($jobOrder, $staff) {
            $currentState = $jobOrder->current_state;

            // For non-production stages, complete them directly since validation already passed
            if (
                $currentState instanceof NewOrder
                || $currentState instanceof Artist
                || $currentState instanceof Approval
                || $this->isStageComplete($jobOrder)
            ) {
                $this->completeCurrentStage($jobOrder, $staff->id);
            }

            // Transition to next state and start it
            $this->transitionToNextStage($jobOrder);
        });

        return response()->json([
            'jobOrder' => $jobOrder->fresh()->load([
                'order.orderable',
                'products.product',
                'products.inventoryItem',
                'stages',
                'materials',
            ]),
            'message' => 'Job order submitted successfully.',
        ]);
    }

    public function addNote(Request $request, JobOrder $jobOrder)
    {
        $request->validate([
            'note' => ['required', 'string', 'max:5000'],
        ]);

        $jobOrder->logNote($request->input('note'));

        return response()->json([
            'message' => __('Note added successfully.'),
        ], 201);
    }

    protected function canSubmitNewOrder(JobOrder $jobOrder): bool
    {
        $deadlinesCount = $jobOrder->stages()->count();
        $hasDeadlines = $deadlinesCount > 0 &&
            $jobOrder->stages()->whereNotNull('due_at')->count() === $deadlinesCount;

        $productsCount = $jobOrder->products()->count();
        $hasFabrics = $productsCount > 0 &&
            $jobOrder->products()->whereNotNull('inventory_item_id')->count() === $productsCount;

        return $hasDeadlines && $hasFabrics;
    }

    protected function canSubmitArtistStage(JobOrder $jobOrder): bool
    {
        $productsCount = $jobOrder->products()->count();

        return $productsCount > 0 &&
            $jobOrder->products()->whereNotNull('file_path')->count() === $productsCount;
    }

    protected function canSubmitDispatchingStage(JobOrder $jobOrder): bool
    {
        $meta = $jobOrder->meta;

        if (
            empty($meta['dispatching_type']) ||
            empty($meta['delivery_method']) ||
            empty($meta['reference_number']) ||
            !in_array($meta['add_delivery_fee'] ?? '', ['yes', 'no'])
        ) {
            return false;
        }

        $order = $jobOrder->order->loadMissing('currency');

        return $order->billing_summary['amount_unbilled']->value === 0;
    }

    protected function canSubmitProductionStage(JobOrder $jobOrder, Staff $staff): bool
    {
        if (!$staff->admin) {
            $stages = JobOrderState::getPermittedStates($staff);
            if ($stages->isEmpty() || !$stages->contains($jobOrder->current_state->getNext())) {
                return false;
            }
        }

        return $jobOrder->productions()
            ->where('state', $jobOrder->current_state)
            ->exists();
    }

    protected function validateInventoryUsage(JobOrder $jobOrder): array
    {
        $currentState = $jobOrder->current_state;
        $stateName = $currentState->getMorphClass();

        // Only validate for stages that require inventory
        $requiresInventory = match ($stateName) {
            Printing::$name, HeatPress::$name, Sewing::$name, Packing::$name => true,
            default => false,
        };

        if (!$requiresInventory) {
            return ['valid' => true];
        }

        // Determine inventory type and validation mode
        $config = match ($stateName) {
            Printing::$name => [
                'type' => InventoryType::SUBLI_PAPER,
                'mode' => 'input', // all items must have quantity
                'message' => 'Please set the sublimation paper usage quantity.',
            ],
            HeatPress::$name => [
                'type' => InventoryType::FABRIC,
                'mode' => 'multiple', // at least one item must have quantity
                'message' => 'Please set at least one fabric usage.',
            ],
            Sewing::$name => [
                'type' => InventoryType::THREAD,
                'mode' => 'multiple',
                'message' => 'Please set at least one thread usage.',
            ],
            Packing::$name => [
                'type' => InventoryType::PACKAGING,
                'mode' => 'multiple',
                'message' => 'Please set at least one packaging material usage.',
            ],
        };

        // Get available inventory items for this type
        $availableItems = InventoryItem::type($config['type'])->get();

        if ($availableItems->isEmpty()) {
            return ['valid' => true]; // Skip validation if no items available
        }

        $materialsQuery = fn() => $jobOrder->materials()
            ->where('stage', $stateName)
            ->whereHas('inventoryItem', fn($q) => $q->where('type', $config['type']));

        if ($config['mode'] === 'input') {
            // Every available item must have a saved record.
            // 0 is acceptable — the record just must exist (not be left blank/unsaved).
            $recordedCount = $materialsQuery()->count();

            if ($recordedCount < $availableItems->count()) {
                return [
                    'valid' => false,
                    'message' => $config['message'],
                ];
            }
        } else {
            // At least one item must have a quantity > 0 recorded.
            $hasUsage = $materialsQuery()->where('amount_used', '>', 0)->exists();

            if (!$hasUsage) {
                return [
                    'valid' => false,
                    'message' => $config['message'],
                ];
            }
        }

        return ['valid' => true];
    }

    protected function isStageComplete(JobOrder $jobOrder): bool
    {
        $currentState = $jobOrder->current_state->getMorphClass();

        // Get all order lines for this job order
        $orderLines = $jobOrder->order->lines;

        // Check if all order lines have complete production for current state
        foreach ($orderLines as $line) {
            $totalProduced = $jobOrder->productions()
                ->where('order_line_id', $line->id)
                ->where('state', $currentState)
                ->sum('quantity');

            if ($totalProduced < $line->quantity) {
                return false;
            }
        }

        return true;
    }

    protected function completeCurrentStage(JobOrder $jobOrder, ?int $operatorId = null): void
    {
        $currentStage = $jobOrder->stages()
            ->where('state', $jobOrder->current_state->getMorphClass())
            ->whereNull('completed_at')
            ->first();

        if ($currentStage) {
            $currentStage->update([
                'started_at' => $currentStage->started_at ?? now(),
                'completed_at' => now(),
                'operator_id' => $operatorId,
            ]);
        }
    }

    protected function transitionToNextStage(JobOrder $jobOrder): void
    {
        $nextState = $jobOrder->current_state->getNext();

        if (!$nextState) {
            return;
        }

        $jobOrder->current_state->transitionTo($nextState);

        $nextStage = $jobOrder->stages()
            ->where('state', $nextState::getMorphClass())
            ->whereNull('started_at')
            ->first();

        if ($nextStage) {
            $nextStage->update([
                'started_at' => now(),
            ]);
        }
    }

    protected function checkAndCompleteStages(JobOrder $jobOrder): void
    {
        // Get all active (started but not completed) stages
        $activeStages = $jobOrder->stages()
            ->whereNotNull('started_at')
            ->whereNull('completed_at')
            ->get();

        foreach ($activeStages as $stage) {
            if ($this->isStageCompleteByState($jobOrder, $stage->state)) {
                $stage->update([
                    'completed_at' => now(),
                ]);
            }
        }
    }

    protected function isStageCompleteByState(JobOrder $jobOrder, $state): bool
    {
        // Get all order lines for this job order
        $orderLines = $jobOrder->order->lines;

        // Check if all order lines have complete production for the given state
        foreach ($orderLines as $line) {
            $totalProduced = $jobOrder->productions()
                ->where('order_line_id', $line->id)
                ->where('state', $state)
                ->sum('quantity');

            if ($totalProduced < $line->quantity) {
                return false;
            }
        }

        return true;
    }

    protected function autoStartNextStage(JobOrder $jobOrder, string $stateName): void
    {
        $stateClass = JobOrderState::all()->get($stateName);
        if (!$stateClass) {
            return;
        }

        $nextStateClass = $stateClass::getNext();
        if (!$nextStateClass) {
            return;
        }

        $nextStateName = $nextStateClass::getMorphClass();

        // Only auto-start production stages
        if (
            in_array($nextStateName, [
                NewOrder::$name,
                Approval::$name,
                Artist::$name,
                Completed::$name,
                Cancelled::$name,
            ])
        ) {
            return;
        }

        $jobOrder->stages()
            ->where('state', $nextStateName)
            ->whereNull('started_at')
            ->update(['started_at' => now()]);

        if ($jobOrder->current_state->canTransitionTo($nextStateClass)) {
            $jobOrder->current_state->transitionTo($nextStateClass);
        }
    }

    public function assignArtist(Request $request, JobOrder $jobOrder)
    {
        $staff = $request->user('staff');

        if (!$staff->admin && !$staff->can(StaffPermission::MANAGE_JO_ARTIST_TASKS)) {
            abort(403);
        }

        $artistStage = $jobOrder->stages()->where('state', Artist::$name)->first();

        if (!$artistStage) {
            return response()->json(['message' => 'Artist stage not found.'], 404);
        }

        if ($artistStage->operator_id && $artistStage->operator_id !== $staff->id) {
            return response()->json(['message' => 'You can only assign or unassign yourself.'], 403);
        }

        if ($artistStage->operator_id === $staff->id) {
            $artistStage->update(['operator_id' => null]);
            $message = 'Unassigned successfully.';
        } else {
            $artistStage->update(['operator_id' => $staff->id]);
            $message = 'Assigned successfully.';
        }

        return response()->json([
            'message' => $message,
            'stage' => $artistStage->fresh()->load('operator'),
        ]);
    }

    public function assignSewerView(Request $request, OrderLine $orderLine)
    {
        $staff = $request->user('staff');
        if (!$staff->admin && !$staff->can(StaffPermission::MANAGE_JO_SEWING_TASKS)) {
            abort(403);
        }

        return Inertia::modal('admin/job-order/assign-sewer', [
            'orderLine' => $orderLine,
            'sewers' => fn() => Staff::role('Sewer')->getOptions('full_name'),
            'currentAssignments' => fn() => $orderLine->assignments()
                ->where('state', Sewing::$name)
                ->with('staff')
                ->get()
                ->map(fn($a) => [
                    'id' => $a->id,
                    'quantity' => $a->quantity,
                    'staff_id' => $a->staff_id,
                    'sewer_name' => $a->staff->full_name,
                ]),
            'total' => fn() => $orderLine->quantity,
        ]);
    }

    public function assignSewer(JobOrderSewerAssignmentRequest $request, OrderLine $orderLine)
    {
        $staff = $request->user('staff');
        if (!$staff->admin && !$staff->can(StaffPermission::MANAGE_JO_SEWING_TASKS)) {
            abort(403);
        }

        $orderLine->assignments()->where('state', Sewing::$name)->delete();
        $assignments = $request->input('assignments', []);

        foreach ($assignments as $assignment) {
            $orderLine->assignments()->create([
                'staff_id' => $assignment['sewer_id'],
                'quantity' => $assignment['quantity'],
                'state' => Sewing::$name,
            ]);
        }

        return response()->json([
            'message' => 'Sewers assigned successfully.',
        ], 201);
    }

    protected function isStaffAuthorized(Staff $staff, JobOrder $jobOrder)
    {
        if (!$staff->admin) {
            $permissions = JobOrderStage::getActiveStagePermissions($jobOrder->id);
            if (!$staff->hasAnyPermission($permissions)) {
                abort(403);
            }
        }

        return $staff;
    }
}
