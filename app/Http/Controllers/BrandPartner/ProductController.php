<?php

namespace App\Http\Controllers\BrandPartner;

use App\Enums\BrandPartnerProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandPartner\ProductRequest;
use App\Models\BrandPartnerProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
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
        $products = QueryBuilder::for(BrandPartnerProduct::class)
            ->where('brand_partner_id', $this->brandPartner()->id)
            ->with(['category', 'event', 'images'])
            ->allowedSorts(['name', 'price', 'status', 'created_at'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('event_id'),
                AllowedFilter::exact('featured'),
                AllowedFilter::scope('search'),
            ])
            ->latest()
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        $categories = $this->brandPartner()->categories()->enabled()->ordered()->get();
        $events = $this->brandPartner()->events()->enabled()->get();

        return Inertia::render('product/index', [
            'products' => $products,
            'categories' => $categories,
            'events' => $events,
            'statusOptions' => BrandPartnerProductStatus::getOptions(),
            'filter' => $request->input('filter', []),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->brandPartner()->categories()->enabled()->ordered()->get();
        $events = $this->brandPartner()->events()->enabled()->get();

        return Inertia::modal('product/create', [
            'categories' => $categories,
            'events' => $events,
            'statusOptions' => BrandPartnerProductStatus::getOptions(),
        ])->baseRoute('brand-partner.products.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = DB::transaction(function () use ($request) {
            $product = $this->brandPartner()->products()->create([
                ...$request->validated(),
                'slug' => $request->slug ?? Str::slug($request->name),
                'price' => (int) ($request->price * 100),
                'compare_price' => $request->compare_price ? (int) ($request->compare_price * 100) : null,
            ]);

            return $product;
        });

        return response()->json([
            'product' => $product->load(['category', 'event', 'images']),
            'message' => __('crud.created', ['record' => 'Product']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BrandPartnerProduct $product)
    {
        $this->authorize($product);

        $product->load(['category', 'event', 'images', 'orderLines.order']);

        return Inertia::render('product/show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BrandPartnerProduct $product)
    {
        $this->authorize($product);

        $product->load(['category', 'event', 'images']);
        $categories = $this->brandPartner()->categories()->enabled()->ordered()->get();
        $events = $this->brandPartner()->events()->enabled()->get();

        return Inertia::modal('product/edit', [
            'product' => $product,
            'categories' => $categories,
            'events' => $events,
            'statusOptions' => BrandPartnerProductStatus::getOptions(),
        ])->baseRoute('brand-partner.products.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, BrandPartnerProduct $product)
    {
        $this->authorize($product);

        DB::transaction(function () use ($request, $product) {
            $data = $request->validated();
            $data['price'] = (int) ($request->price * 100);
            $data['compare_price'] = $request->compare_price ? (int) ($request->compare_price * 100) : null;

            $product->update($data);
        });

        return response()->json([
            'product' => $product->fresh(['category', 'event', 'images']),
            'message' => __('crud.updated', ['record' => 'Product']),
        ]);
    }

    /**
     * Toggle the product status.
     */
    public function toggleStatus(BrandPartnerProduct $product)
    {
        $this->authorize($product);

        $product->toggleStatus();

        return to_route('brand-partner.products.index')
            ->with('success', __('crud.updated', ['record' => 'Product']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BrandPartnerProduct $product)
    {
        $this->authorize($product);

        if ($product->orderLines()->exists()) {
            return to_route('brand-partner.products.index')
                ->with('error', __('Product cannot be deleted because it has orders.'));
        }

        $product->delete();

        return to_route('brand-partner.products.index')
            ->with('success', __('crud.deleted', ['record' => 'Product']));
    }

    /**
     * Authorize that the product belongs to the current brand partner.
     */
    protected function authorize(BrandPartnerProduct $product)
    {
        if ($product->brand_partner_id !== $this->brandPartner()->id) {
            abort(403);
        }
    }
}
