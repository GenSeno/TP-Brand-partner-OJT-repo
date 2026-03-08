<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = QueryBuilder::for(Product::class)
            ->with('category')
            ->allowedSorts(['name'])
            ->allowedFilters([
                AllowedFilter::exact('category', 'category_id'),
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('admin/product/index', [
            'products' => $products,
            'statuses' => fn() => ProductStatus::getOptions(),
            'categories' => fn() => Category::pluck('name', 'id'),
            'filter' => $request->input('filter', []),
            'default_per_page' => $this->defaultPerPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('admin/product/create', [
            'categories' => fn() => Category::enabled()->getOptions('name', 'id'),
        ])->baseRoute('admin.product.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = DB::transaction(function () use ($request) {
            $product = Product::create($request->only(['name', 'category_id', 'description']) + [
                'status' => ProductStatus::PUBLISHED
            ]);

            if ($request->hasFile('image')) {
                $product->addMediaFromRequest('image')
                    ->toMediaCollection('image');
            }

            return $product;
        });

        return response()->json([
            'product' => $product,
            'message' => __('crud.created', ['record' => 'Product'])
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::modal('admin/product/edit', [
            'product' => $product,
            'categories' => fn() => Category::enabled()->getOptions('name', 'id'),
            'statuses' => fn() => ProductStatus::getOptions(false),
        ])->baseRoute('admin.product.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $product->update($request->only([
                'name',
                'category_id',
                'description',
                'status',
            ]));

            if ($request->hasFile('image')) {
                $product->addMediaFromRequest('image')
                    ->toMediaCollection('image');
            }
        });

        return response()->json([
            'product' => $product->refresh(),
            'message' => __('crud.updated', ['record' => 'Product'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            'deleted' => [$product->id],
            'message' => __('crud.deleted', ['record' => 'Product']),
        ]);
    }


    public function getVariants(Product $product)
    {
        return $product->variants()->get();
    }

    public function toggleStatus(Product $product)
    {
        $product->toggleStatus();

        return response()->json([
            'product' => $product,
            'message' => __('crud.updated', ['record' => 'Product']),
        ]);
    }

    public function variantsByOption(Product $product, $valueId)
    {
         // Get all variant IDs linked to this option value via the pivot table
        $variantIds = \DB::table('product_option_value_product_variant')
            ->where('value_id', $valueId)
            ->pluck('variant_id');

        // Get variants of the product that are in the list
        $variants = $product->variants()
            ->whereIn('id', $variantIds)
            ->with('values') // eager load values if needed
            ->get();

        return response()->json($variants);
    }
}
