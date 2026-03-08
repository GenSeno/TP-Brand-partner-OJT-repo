<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderLineType;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderItemRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use DB;

class OrderItemController extends Controller
{
    protected $defaultPerPage = 6;

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Order $order)
    {
        $products = QueryBuilder::for(Product::class)
            ->with(['category', 'options.values'])
            ->allowedFilters([
                AllowedFilter::exact('category', 'category_id'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::modal('admin/order/item/create', [
            'order' => $order->load('printLines.product'),
            'products' => $products,
            'categories' => fn() => Category::pluck('name', 'id'),
            'filter' => $request->input('filter', []) + [
                'default_per_page' => $this->defaultPerPage,
            ],
        ])->baseRoute('admin.order.show', ['order' => $order->id]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderItemRequest $request, Order $order)
    {
        $quoteLines = DB::transaction(function () use ($order, $request) {
            $product_id = $request->input('product_id');
            $printing_option_id = $request->input('printing_option_id');
            $printing_option_value = $request->input('printing_option_value');
            $lines = [];
            $purchasableType = 'App\Models\ProductVariant';
            foreach ($request->input('sizes') as $sizeRow) {

            $sizeLabel = $sizeRow['size_label'];
            $customDimension = null;

            // Save custom_dimension only if size label is exactly "custom" (case-insensitive)
            if (strcasecmp($sizeLabel, 'Custom') === 0) {
                $customDimension = $request->input('custom_dimension');
            }
                $lineData = [
                    'purchasable_type' => $purchasableType,
                    'purchasable_id' => $sizeRow['variant_id'],
                    'type' => OrderLineType::PHYSICAL->value,
                    'unit_price' => $sizeRow['price'] * 100,
                    'quantity' => $sizeRow['quantity'],
                    'meta' => [
                        'names' => $sizeRow['names'] ?? [],
                        'size' => $sizeRow['size_label'],
                        'custom_dimension' => $customDimension,
                    ],
                ];

                $lines[] = $order->lines()->create($lineData);
            }
        });

        return response()->json([
            'message' => __('Item added to Sales Order successfully.'),
        ]);
    }

     public function store1(OrderItemRequest $request, Order $order)
    {
        $lineData = [
            ...$request->validated(),
            'price' => $request->input('price', 0) * 100,
            'quantity' => $request->input('quantity') ?? 1,
        ];

        $variantQuery = ProductVariant::where('product_id', $lineData['product_id']);

        if (!empty($lineData['values'])) {
            foreach ($lineData['values'] as $optionId => $value) {
                $variantQuery->whereHas('values', function ($q) use ($value) {
                    $q->where('product_option_values.value', $value);
                });
            }
        }

        $variant = $variantQuery->first();

        if (!$variant) {
            abort(404, __('Product variant not found with selected options.'));
        }

        $lineExists = $order->lines()->where([
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $variant->id
        ])->exists();

        if ($lineExists) {
            abort(409, __('This item is already added to the Sales Order.'));
        }

        $meta = array_filter([
            'names'            => $lineData['names'] ?? null,
            'custom_dimension' => $lineData['custom_dimension'] ?? null,
        ]);

        $order->lines()->create([
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $variant->id,
            'type' => OrderLineType::PHYSICAL->value,
            'unit_price' => $lineData['price'],
            'quantity' => $lineData['quantity'],
            'meta' => $meta,
        ]);

        return response()->json([
            'message' => __('Item added to Sales Order successfully.'),
        ]);
    }

    /**
     * Show the form for editing a single order line.
     */
    public function edit(Order $order, OrderLine $item)
    {
        $item->load('purchasable.product.category');

        return Inertia::modal('admin/order/item/edit-line', [
            'order' => $order,
            'line' => $item,
        ])->baseRoute('admin.order.show', ['order' => $order->id]);
    }

    /**
     * Update a single order line.
     */
    public function update(Request $request, Order $order, OrderLine $item)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'meta' => ['nullable', 'array'],
            'reason' => ['required', 'string', 'max:5000'],
        ]);

        $item->unit_price = (int) ($request->input('price') * 100);
        $item->quantity = $request->input('quantity');
        $item->sub_total = (int) ($request->input('price') * 100) * $request->input('quantity');
        $item->total = $item->sub_total->value;
        $item->meta = $request->input('meta', $item->meta);
        $item->save();

        $order->logNote("Item edited: {$request->input('reason')}");

        return response()->json([
            'message' => __('Item updated successfully.'),
        ]);
    }

    /**
     * Delete a single order line.
     */
    public function destroy(Request $request, Order $order, OrderLine $item)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:5000'],
        ]);

        $item->delete();

        $order->logNote("Item deleted: {$request->input('reason')}");

        return response()->json([
            'message' => __('Item deleted successfully.'),
        ]);
    }

    /**
     * Show the form for editing product lines.
     */
    public function editProduct(Order $order, Product $product)
    {
        $lines = $order->lines()
            ->whereHasMorph('purchasable', ProductVariant::class, function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->with(['purchasable.product.category'])
            ->get();

        return Inertia::modal('admin/order/item/edit-product', [
            'order' => $order,
            'product' => $product->load('category'),
            'lines' => $lines,
        ])->baseRoute('admin.order.show', ['order' => $order->id]);
    }

    /**
     * Bulk update order lines for a product.
     */
    public function updateBulk(Request $request, Order $order, Product $product)
    {
        $request->validate([
            'order_lines' => ['required', 'array', 'min:1'],
            'order_lines.*.id' => ['required', 'integer', 'exists:order_lines,id'],
            'order_lines.*.quantity' => ['required', 'integer', 'min:1'],
            'order_lines.*.price' => ['required', 'numeric', 'min:0'],
            'order_lines.*.meta' => ['nullable', 'array'],
            'reason' => ['required', 'string', 'max:5000'],
        ]);

        foreach ($request->input('order_lines') as $data) {
            $line = OrderLine::find($data['id']);
            if (!$line || $line->order_id !== $order->id) {
                continue;
            }

            $line->unit_price = (int) ($data['price'] * 100);
            $line->quantity = $data['quantity'];
            $line->sub_total = (int) ($data['price'] * 100) * $data['quantity'];
            $line->total = $line->sub_total->value;
            $line->meta = $data['meta'] ?? $line->meta;
            $line->save();
        }

        $order->logNote("Item edited: {$request->input('reason')}");

        return response()->json([
            'message' => __('Items updated successfully.'),
        ]);
    }

    /**
     * Bulk destroy order lines for a product.
     */
    public function bulkDestroy(Request $request, Order $order)
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'reason' => ['required', 'string', 'max:5000'],
        ]);

        $lines = $order->lines()
            ->whereHasMorph('purchasable', ProductVariant::class, function ($q) use ($request) {
                $q->where('product_id', $request->input('product_id'));
            })
            ->get();

        // Delete individually so OrderLineObserver fires for each
        foreach ($lines as $line) {
            $line->delete();
        }

        $order->logNote("Item deleted: {$request->input('reason')}");

        return response()->json([
            'message' => __('Item deleted successfully.'),
        ]);
    }
}
