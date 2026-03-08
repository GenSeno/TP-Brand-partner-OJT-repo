<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QuoteStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuoteLineRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Quote;
use App\Models\QuoteLine;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;


class QuoteLineController extends Controller
{
    protected $defaultPerPage = 6;

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Quote $quotation)
    {
        $products = QueryBuilder::for(Product::class)
            ->with(['category', 'options.values'])
            ->allowedFilters([
                AllowedFilter::exact('category', 'category_id'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::modal('admin/quotation/line/add_item', [
            'quotation' => $quotation->load('lines.purchasable'),
            'products' => $products,
            'categories' => fn () => Category::pluck('name', 'id'),
            'filter' => $request->input('filter', []) + [
                'default_per_page' => $this->defaultPerPage,
            ],
        ])->baseRoute('admin.quotation.item', $quotation);
    }

    public function edit(Quote $quotation, QuoteLine $line)
    {

        $line = QuoteLine::with('purchasable.product.category')->find($line->id);

        return Inertia::modal('admin/quotation/line/edit_item', [
            'categories' => Category::enabled()->get(),
            'line' => $line,
            'quotation' => $quotation,
        ])->baseRoute('admin.quotation.item', $quotation);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuoteLineRequest $request, Quote $quotation)
    {
    
        $quoteLines = DB::transaction(function () use ($quotation, $request) {
            $product_id = $request->input('product_id');
            $printing_option_id = $request->input('printing_option_id');
            $printing_option_value = $request->input('printing_option_value');
            $lines = [];
            $purchasableType = 'App\Models\ProductVariant';
            foreach ($request->input('sizes') as $sizeRow) {

                $lineData = [
                    'quantity' => $sizeRow['quantity'],
                    'purchase_price' => $sizeRow['price'] * 100,
                    'total' => $sizeRow['quantity'] * $sizeRow['price'] * 100,
                    'purchasable_type' => $purchasableType,
                    'purchasable_id' => $sizeRow['variant_id'],
                    'meta' => [
                        'names' => $sizeRow['names'] ?? [],
                        'size' => $sizeRow['size_label'],
                        'product_id' => $product_id,
                        'custom_dimension' => $request->input('custom_dimension'),
                        'printing_option_id' => $printing_option_id,
                        'printing_option_value' => $printing_option_value,
                    ],
                ];

                $lines[] = $quotation->lines()->create($lineData);
            }

            // Update quotation status if it's still 'request'
            if ($quotation->status->value === 'request') {
                $quotation->update([
                    'status' => QuoteStatus::DRAFT,
                ]);
            }

            $quotation->recalculateTotals();

            return $lines;
        });

        return response()->json([
            'quote_lines' => $quoteLines,
            'message' => 'Items added successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quotation, QuoteLine $line)
    {
        $data = $request->input('quote_lines')[0] ?? [];
        $line->update([
            'quantity' => $data['quantity'] ?? $line->quantity,
            'purchase_price' => $data['purchase_price'] ?? $line->purchase_price * 100,
            'meta' => $data['meta'] ?? $line->meta,
        ]);

        // Recalculate totals
        $quotation->recalculateTotals();

        return redirect()->route('admin.quotation.item', $quotation->id)
            ->with('success', 'Item updated successfully.');
    }

    /**
     * Delete a quotation line.
     */
    public function destroy(QuoteLine $line)
    {

        $quotation = $line->quote;

        // Delete the quote line
        $line->delete();

        $quotation->recalculateTotals();

        // Redirect back with a success message
        return redirect()
            ->route('admin.quotation.item', $quotation->id)
            ->with('success', 'Item deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'message' => 'No items selected',
                'deleted' => [],
            ], 400);
        }

        $lines = QuoteLine::whereIn('id', $ids)->get();

        QuoteLine::whereIn('id', $ids)->delete();

        // Recalculate totals for affected quotes
        $lines->pluck('quote_id')->unique()->each(function ($quoteId) {
            $quote = Quote::find($quoteId);
            if ($quote) {
                $quote->recalculateTotals();
            }
        });

        return response()->json([
            'message' => 'Item(s) deleted successfully!',
            'deleted' => $ids,
        ]);
    }

    public function edit_product(Quote $quotation, Product $product)
    {
        // // Get all quote lines for this product in this quotation.
        // // Filter directly on purchasable columns — querying via whereHas('purchasable')
        // // with a `product_id` condition fails when purchasable_type is Product because
        // // the `products` table has no `product_id` column (it lives on `product_variants`).
        // $lines = QuoteLine::with(['purchasable'])
        //     ->where('quote_id', $quotation->id)
        //     ->where(function ($query) use ($product) {
        //         $query->where(function ($q) use ($product) {
        //             // purchasable IS the product directly
        //             $q->where('purchasable_type', 'App\\Models\\Product')
        //               ->where('purchasable_id', $product->id);
        //         })->orWhere(function ($q) use ($product) {
        //             // purchasable is a variant of this product.
        //             // whereHas() on a MorphTo doesn't respect morph-type constraints,
        //             // so use whereIn with a direct subquery on product_variants instead.
        //             $variantIds = ProductVariant::where('product_id', $product->id)
        //                 ->pluck('id');

        //             $q->where('purchasable_type', 'App\\Models\\ProductVariant')
        //               ->whereIn('purchasable_id', $variantIds);
        //         });
        //     })
        //     ->get();

        // // Eager-load product.category only for ProductVariant purchasables.
        // $variantPurchasables = $lines
        //     ->where('purchasable_type', 'App\\Models\\ProductVariant')
        //     ->pluck('purchasable')
        //     ->filter();

        // if ($variantPurchasables->isNotEmpty()) {
        //     $variantPurchasables->loadMissing('product.category');
        // }
         $lines = QuoteLine::with('purchasable.product.category')
            ->where('quote_id', $quotation->id)
            ->whereHas('purchasable', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->get();

        return Inertia::modal('admin/quotation/line/edit_item', [
            'categories' => Category::enabled()->get(),
            'lines' => $lines,
            'quotation' => $quotation,
            'product' => $product,
        ])->baseRoute('admin.quotation.item', $quotation);
    }

    public function update_bulk(Request $request, Quote $quotation, Product $product)
    {
        $quoteLinesData = $request->input('quote_lines', []);

        foreach ($quoteLinesData as $data) {
            $line = QuoteLine::find($data['id']);
            if (! $line) {
                continue;
            }

            $line->update([
                'quantity' => $data['quantity'],
                'purchase_price' => $data['purchase_price'] * 100,
                'meta' => $data['meta'] ?? $line->meta,
                'total' => $data['quantity'] * $data['purchase_price'] * 100,
            ]);
        }

        // Recalculate totals for the quotation
        $quotation->recalculateTotals();

        return response()->json([
            'message' => 'Items updated successfully.',
            'quote_lines' => $quoteLinesData,
        ]);
    }

    
}
