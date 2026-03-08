<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InventoryMovementType;
use App\Enums\InventoryType;
use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryAdjustmentRequest;
use App\Http\Requests\InventoryItemRequest;
use App\Models\InventoryItem;
use App\Models\UnitMeasure;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InventoryItemController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $inventoryItems = QueryBuilder::for(InventoryItem::class)
            ->with(['unitMeasure'])
            ->withCount('movements')
            ->allowedSorts(['item_name', 'type', 'current_stock', 'created_at'])
            ->defaultSort(['item_name'])
            ->allowedFilters([
                AllowedFilter::exact('type'),
                AllowedFilter::exact('enabled'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('admin/inventory-item/index', [
            'inventoryItems' => $inventoryItems,
            'inventoryTypes' => fn() => InventoryType::getOptions(),
            'filter' => $request->input('filter', []) + [
                'default_per_page' => $this->defaultPerPage,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('admin/inventory-item/create', [
            'inventoryTypes' => InventoryType::getOptions(false),
            'unitMeasures' => UnitMeasure::query()
                ->enabled()
                ->orderBy('name')
                ->getOptions('name', 'code'),
        ])->baseRoute('admin.inventory-item.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventoryItemRequest $request)
    {
        $inventoryItem = InventoryItem::create($request->validated());

        return response()->json([
            'inventoryItem' => $inventoryItem->load('unitMeasure'),
            'message' => 'Inventory item created successfully.',
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryItem $inventoryItem)
    {
        return Inertia::modal('admin/inventory-item/edit', [
            'inventoryItem' => $inventoryItem->load('unitMeasure'),
            'inventoryTypes' => InventoryType::getOptions(false),
            'unitMeasures' => UnitMeasure::query()
                ->enabled()
                ->orderBy('name')
                ->getOptions('name', 'code'),
        ])->baseRoute('admin.inventory-item.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InventoryItemRequest $request, InventoryItem $inventoryItem)
    {
        $inventoryItem->update($request->validated());

        return response()->json([
            'inventoryItem' => $inventoryItem->load('unitMeasure'),
            'message' => 'Inventory item updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryItem $inventoryItem)
    {
        // Check if item has any movements
        if ($inventoryItem->movements()->exists()) {
            return response()->json([
                'message' => 'Cannot delete this item because it has inventory movements.',
                'deleted' => [],
            ], 403); // Forbidden
        }

        // If no movements, delete the item
        $inventoryItem->delete();

        return response()->json([
            'message' => 'Inventory item deleted successfully.',
            'deleted' => [$inventoryItem->id],
        ]);
    }

    /**
     * Show the adjustment modal
     */
    public function adjustStock(InventoryItem $inventoryItem)
    {
        return Inertia::modal('admin/inventory-item/adjust-stock', [
            'inventoryItem' => $inventoryItem->load('unitMeasure'),
            'movementTypes' => InventoryMovementType::getOptions(false),
            'recentMovements' => $inventoryItem->movements()
                ->adjustment()
                ->latest()
                ->take(5)
                ->get(),
        ])->baseRoute('admin.inventory-item.index');
    }

    /**
     * Show full movement history for an inventory item
     */
    public function history(Request $request, InventoryItem $inventoryItem)
    {
        $movements = $inventoryItem->movements()
            ->with('source')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::modal('admin/inventory-item/history', [
            'inventoryItem' => $inventoryItem->load('unitMeasure'),
            'movements' => $movements,
        ])->baseRoute('admin.inventory-item.index');
    }

    /**
     * Process stock adjustment
     */
    public function processAdjustment(InventoryAdjustmentRequest $request, InventoryItem $inventoryItem)
    {
        DB::transaction(function () use ($request, $inventoryItem) {
            $inventoryItem->movements()->create(
                $request->only(['type', 'amount', 'notes']) + [
                    'adjustment' => true,
                ]
            );
        });

        return response()->json([
            'inventoryItem' => $inventoryItem->fresh()->load('unitMeasure'),
            'message' => 'Stock adjusted successfully.',
        ]);
    }
}
