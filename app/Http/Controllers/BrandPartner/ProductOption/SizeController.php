<?php

namespace App\Http\Controllers\BrandPartner\ProductOption;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandPartner\SizeRequest;
use App\Models\BrandPartnerSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SizeController extends Controller
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
        $sizes = QueryBuilder::for(BrandPartnerSize::class)
            ->where('brand_partner_id', $this->brandPartner()->id)
            ->allowedSorts(['name', 'created_at'])
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->orderBy('name')
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('product-option/size/index', [
            'sizes' => $sizes,
            'filter' => $request->input('filter', []),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('product-option/size/create')
            ->baseRoute('brand-partner.product-options.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SizeRequest $request)
    {
        $size = $this->brandPartner()->sizes()->create($request->validated());

        return response()->json([
            'size' => $size,
            'message' => __('crud.created', ['record' => 'Size']),
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BrandPartnerSize $size)
    {
        $this->authorize($size);

        return Inertia::modal('product-option/size/edit', [
            'size' => $size,
        ])->baseRoute('brand-partner.product-options.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SizeRequest $request, BrandPartnerSize $size)
    {
        $this->authorize($size);

        $size->update($request->validated());

        return response()->json([
            'size' => $size->fresh(),
            'message' => __('crud.updated', ['record' => 'Size']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BrandPartnerSize $size)
    {
        $this->authorize($size);

        $size->delete();

        return to_route('brand-partner.product-options.index')
            ->with('success', __('crud.deleted', ['record' => 'Size']));
    }

    /**
     * Authorize that the size belongs to the current brand partner.
     */
    protected function authorize(BrandPartnerSize $size)
    {
        if ((int) $size->brand_partner_id !== (int) $this->brandPartner()->id) {
            abort(403);
        }
    }
}
