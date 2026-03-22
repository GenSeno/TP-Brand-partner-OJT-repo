<?php

namespace App\Http\Controllers\BrandPartner\ProductOption;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandPartner\CollectionRequest;
use App\Models\BrandPartnerCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CollectionController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Get the authenticated brand partner.
     */
    protected function brandPartner()
    {
        return Auth::guard('brand_partner')->user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $collections = QueryBuilder::for(BrandPartnerCollection::class)
            ->where('brand_partner_id', $this->brandPartner()->id)
            ->allowedSorts(['name', 'created_at'])
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->orderBy('name')
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('product-option/collection/index', [
            'collections' => $collections,
            'filter' => $request->input('filter', []),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('product-option/collection/create')
            ->baseRoute('brand-partner.product-options.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CollectionRequest $request)
    {
        $collection = $this->brandPartner()->collections()->create($request->validated());

        return response()->json([
            'collection' => $collection,
            'message' => __('crud.created', ['record' => 'Collection']),
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BrandPartnerCollection $collection)
    {
        $this->authorize($collection);

        return Inertia::modal('product-option/collection/edit', [
            'collection' => $collection,
        ])->baseRoute('brand-partner.product-options.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CollectionRequest $request, BrandPartnerCollection $collection)
    {
        $this->authorize($collection);

        $collection->update($request->validated());

        return response()->json([
            'collection' => $collection->fresh(),
            'message' => __('crud.updated', ['record' => 'Collection']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BrandPartnerCollection $collection)
    {
        $this->authorize($collection);

        $collection->delete();

        return to_route('brand-partner.product-options.index')
            ->with('success', __('crud.deleted', ['record' => 'Collection']));
    }

    /**
     * Authorize that the collection belongs to the current brand partner.
     */
    protected function authorize(BrandPartnerCollection $collection)
    {
        if ((int) $collection->brand_partner_id !== (int) $this->brandPartner()->id) {
            abort(403);
        }
    }
}
