<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrderRequest;
use App\Http\Requests\PurchaseOrderReceivedRequest;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Currency;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Carbon;
class PurchaseOrderController extends Controller
{
    protected $defaultPerPage = 10;

    public function index(Request $request)
    {
        $purchaseOrders = QueryBuilder::for(PurchaseOrder::class)
            ->with('supplier')
            ->allowedSorts(['reference', 'created_at'])
            ->allowedFilters([
                AllowedFilter::partial('reference'), // search by reference
                AllowedFilter::scope('search'),
                AllowedFilter::exact('supplier_id'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('admin/purchase-order/index', [
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'filter' => $request->input('filter', []) + [
                'default_per_page' => $this->defaultPerPage,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::modal('admin/purchase-order/create', [
            'suppliers' => Supplier::orderBy('name')->getOptions('name', 'id'),
            'inventory' => InventoryItem::orderBy('item_name')->getOptions('item_name', 'id'),
        ])->baseRoute('admin.purchase-order.index');
    }

   public function store(PurchaseOrderRequest $request)
    {
        $data = $request->only([
            'supplier_id',
            'order_date',
            'expected_delivery_date',
            'notes',
            'status',
        ]);
        $data['currency_code'] = Currency::getDefault()->code;
        $po = PurchaseOrder::create($data);
     
        if ($request->filled('lines')) {
            foreach ($request->input('lines') as $line) {
                 
                // Ensure proper values
                $inventory_item_id = $line['inventory_item_id'] ?? null;
                $unit_quantity = $line['unit_price'] ?? null;
                $unit_price = isset($line['unit_price']) ? (int) round($line['unit_price'] * 100) : 0;
                $quantity = isset($line['quantity']) ? (int) $line['quantity'] : 0;
                $description = $line['description'] ?? null;
                // Only create line if inventory_item_id and quantity exist
                if ($inventory_item_id && $quantity > 0) {
                    $po->lines()->create([
                        'inventory_item_id' => $inventory_item_id,
                        'description' => $description,
                        'purchase_order_id' => $po->id,
                        'unit_price' => $unit_price,
                        'unit_quantity' => $unit_quantity,
                        'quantity' => $quantity,
                        'total' => $unit_price * $quantity
                    ]);
                }
            }

            $po->refresh();
            $po->calculateTotals();
        }

        $po->load('lines');

        return response()->json([
            'purchaseOrder' => $po,
            'message' => __('crud.created', ['record' => 'Purchase Order']),
        ], 201);
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('lines');

        return Inertia::modal('admin/purchase-order/edit', [
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => Supplier::orderBy('name')->getOptions('name', 'id'),
            'inventory' => InventoryItem::orderBy('item_name')->getOptions('item_name', 'id')
        ])->baseRoute('admin.purchase-order.index');
    }

    public function update(PurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
         if ($purchaseOrder->status->value === 'received') {
            return response()->json([
                'purchaseOrder' => $purchaseOrder,
                'message' => 'Cannot update a received purchase order.'
            ], 403);
        }


        $data = $request->only([
            'supplier_id',
            'order_date',
            'expected_delivery_date',
            'notes',
            'status',
        ]);

        // ensure currency remains the default (or keep existing)
        $data['currency_code'] = Currency::getDefault()->code;

        $purchaseOrder->update($data);

        // handle deleted lines
        $deleted = $request->input('deleted_line_ids', []);
        if (!empty($deleted)) {
            $purchaseOrder->lines()->whereIn('id', $deleted)->delete();
        }

        // handle lines create/update
        foreach ($request->input('lines', []) as $line) {

            $unitPriceDecimal = isset($line['unit_price'])
                ? (float) $line['unit_price']
                : 0;

            $unitPriceCents = (int) round($unitPriceDecimal * 100);

            $quantity = isset($line['quantity'])
                ? (int) $line['quantity']
                : 0;

            $total = $unitPriceCents * $quantity;

            if (!empty($line['id'])) {

                $l = $purchaseOrder->lines()->find($line['id']);

                if ($l) {
                    $l->update([
                        'inventory_item_id' => $line['inventory_item_id'] ?? $l->inventory_item_id,
                        'description'       => $line['description'] ?? $l->description,
                        'unit_price'        => $unitPriceCents,
                        'unit_quantity'     => $unitPriceDecimal,
                        'quantity'          => $quantity,
                        'total'             => $total,
                    ]);
                }

            } else {

                $purchaseOrder->lines()->create([
                    'inventory_item_id' => $line['inventory_item_id'] ?? null,
                    'description'       => $line['description'] ?? null,
                    'unit_price'        => $unitPriceCents,
                    'unit_quantity'     => $unitPriceDecimal,
                    'quantity'          => $quantity,
                    'total'             => $total,
                ]);
            }
        }

        // recalc totals
        $purchaseOrder->refresh();
        $purchaseOrder->calculateTotals();

        $purchaseOrder->load('lines');

        return response()->json([
            'purchaseOrder' => $purchaseOrder,
            'message' => __('crud.updated', ['record' => 'Purchase Order']),
        ]);
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status->value === 'received') {
            return response()->json([
                'purchaseOrder' => $purchaseOrder,
                'message' => 'Cannot update a received purchase order.'
            ], 403);
        }

        $purchaseOrder->delete();

        return response()->json([
            'deleted' => [$purchaseOrder->id],
            'message' => __('crud.deleted', ['record' => 'Purchase Order']),
        ]);
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('lines');
        
        return Inertia::modal('admin/purchase-order/received', [
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => Supplier::orderBy('name')->getOptions('name', 'id'),
            'inventory' => InventoryItem::orderBy('item_name')->getOptions('item_name', 'id'),
        ])->baseRoute('admin.purchase-order.index');
    }

    public function received(PurchaseOrderReceivedRequest $request, PurchaseOrder $purchaseOrder)
    {
        $data = $request->only([
            'date_received',
            'notes',
        ]);

        if (!empty($data['date_received'])) {
            $data['date_received'] = Carbon::parse($data['date_received'])
                ->format('Y-m-d H:i:s');
        }

        $data['status'] = 'received';
        $data['currency_code'] = Currency::getDefault()->code;

        $purchaseOrder->update($data);

        foreach ($request->input('lines', []) as $line) {

            $unitPriceDecimal = isset($line['unit_price'])
                ? (float) $line['unit_price']
                : 0;

            $unitPriceCents = (int) round($unitPriceDecimal * 100);

            $quantity = isset($line['qty_received'])
                ? (int) $line['qty_received']
                : 0;

            $total = $unitPriceCents * $quantity;

            if (!empty($line['id'])) {

                $l = $purchaseOrder->lines()->find($line['id']);

                if ($l) {
                    $l->update([
                        'unit_price'   => $unitPriceCents,
                        'qty_received' => $quantity,
                        'total'        => $total,
                    ]);
                }
            }
        }

        $purchaseOrder->refresh();
        $purchaseOrder->calculateTotals();
        $purchaseOrder->load('lines');

        return response()->json([
            'purchaseOrder' => $purchaseOrder,
            'message' => __('crud.updated', ['record' => 'Received Purchase Order']),
        ]);
    }
}
