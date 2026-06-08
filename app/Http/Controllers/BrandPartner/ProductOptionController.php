<?php

namespace App\Http\Controllers\BrandPartner;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandPartner\ProductOptionRequest;
use App\Models\BrandPartnerProductOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductOptionController extends Controller
{
    protected $defaultPerPage = 10;

    protected function brandPartner()
    {
        return Auth::guard('brand_partner')->user();
    }

    public function index(Request $request)
    {
        $productOptions = QueryBuilder::for(BrandPartnerProductOption::class)
            ->where('brand_partner_id', $this->brandPartner()->id)
            ->allowedSorts(['name', 'created_at'])
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->orderBy('position')
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('product-option/index', [
            'productOptions' => $productOptions,
            'filter' => $request->input('filter', []),
            'default_per_page' => $this->defaultPerPage,
        ]);
    }

    public function create()
    {
        return Inertia::modal('product-option/create')
            ->baseRoute('brand-partner.product-options.index');
    }

    public function store(ProductOptionRequest $request)
    {
        $option = DB::transaction(function () use ($request) {
            $option = $this->brandPartner()->productOptions()->create([
                'name' => $request->name,
                'position' => $request->position,
            ]);

            foreach ($request->values as $i => $valueData) {
                $option->values()->create([
                    'label' => $valueData['label'],
                    'value' => $valueData['value'] ?? null,
                    'position' => $i + 1,
                ]);
            }

            return $option;
        });

        return response()->json([
            'option' => $option->load('values'),
            'message' => __('crud.created', ['record' => 'Product option']),
        ], 201);
    }

    public function edit(BrandPartnerProductOption $productOption)
    {
        $this->authorize($productOption);

        return Inertia::modal('product-option/edit', [
            'option' => $productOption->load('values'),
        ])->baseRoute('brand-partner.product-options.index');
    }

    public function update(ProductOptionRequest $request, BrandPartnerProductOption $productOption)
    {
        $this->authorize($productOption);

        DB::transaction(function () use ($request, $productOption) {
            $productOption->update([
                'name' => $request->name,
                'position' => $request->position,
            ]);

            $keptLabels = [];
            foreach ($request->values as $i => $valueData) {
                $productOption->values()->updateOrCreate(
                    ['label' => $valueData['label']],
                    ['value' => $valueData['value'] ?? null, 'position' => $i + 1],
                );
                $keptLabels[] = $valueData['label'];
            }

            $productOption->values()->whereNotIn('label', $keptLabels)->delete();
        });

        return response()->json([
            'option' => $productOption->fresh('values'),
            'message' => __('crud.updated', ['record' => 'Product option']),
        ]);
    }

    public function destroy(BrandPartnerProductOption $productOption)
    {
        $this->authorize($productOption);

        $productOption->delete();

        return to_route('brand-partner.product-options.index')
            ->with('success', __('crud.deleted', ['record' => 'Product option']));
    }

    protected function authorize(BrandPartnerProductOption $productOption): void
    {
        if ((int) $productOption->brand_partner_id !== (int) $this->brandPartner()->id) {
            abort(403);
        }
    }
}
