<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductOptionRequest;
use App\Models\ProductOption;
use App\Models\ProductVariant;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductOptionController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $productOptions = QueryBuilder::for(ProductOption::shared())
            ->allowedSorts(['name', 'created_at'])
            ->allowedFilters([
                AllowedFilter::scope('search'),
                AllowedFilter::exact('autoapply'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('admin/product-option/index', [
            'productOptions' => $productOptions,
            'filter' => $request->input('filter', []),
            'default_per_page' => $this->defaultPerPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::modal('admin/product-option/create', [
            'external' => $request->query('external', false),
        ])->baseRoute('admin.product-option.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductOptionRequest $request)
    {
        $productOption = DB::transaction(function () use ($request) {
            $productOption = ProductOption::create([
                'name' => str($request->name)->singular()->title(),
                'shared' => true,
                'autoapply' => $request->autoapply,
                'position' => $request->position,
            ]);

            foreach ($request->values as $valueData) {
                $productOption->values()->create([
                    'value' => $valueData['value'],
                    'label' => $valueData['label']
                ]);
            }

            if ($productOption->autoapply) {
                ProductVariant::checkAndGenerate();
            }

            return $productOption;
        });

        return response()->json([
            'option' => $productOption,
            'message' => __('crud.created', ['record' => 'Product option'])
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductOption $productOption)
    {
        return Inertia::modal('admin/product-option/edit', [
            'option' => $productOption->load('values'),
        ])->baseRoute('admin.product-option.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductOptionRequest $request, ProductOption $productOption)
    {
        DB::transaction(function () use ($request, $productOption) {
            $productOption->update([
                'name' => str($request->name)->singular()->title(),
                'autoapply' => $request->autoapply,
                'position' => $request->position,
            ]);

            $values = [];
            foreach ($request->values as $valueData) {
                $productOption->values()->updateOrCreate([
                    'value' => $valueData['value'],
                ], [
                    'label' => $valueData['label'],
                ]);
                $values[] = $valueData['value'];
            }

            // Remove values that are not in the request
            $values = $productOption->values()->whereNotIn(
                'value',
                $values
            )->delete();

            if ($productOption->autoapply) {
                ProductVariant::checkAndGenerate();
            }
        });

        return response()->json([
            'option' => $productOption->refresh(),
            'message' => __('crud.updated', ['record' => 'Product option'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductOption $productOption)
    {
        if ($productOption->permanent) {
            return response()->json([
                'message' => __('crud.not_allowed', ['record' => 'Product option', 'action' => 'deleted']),
            ], 403);
        }
        $productOption->delete();
        return response()->json([
            'deleted' => [$productOption->id],
            'message' => __('crud.deleted', ['record' => 'Product option']),
        ]);
    }
}
