<?php

namespace App\Http\Controllers\BrandPartner;

use App\Enums\BrandPartnerProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandPartner\ProductRequest;
use App\Models\BrandPartnerProduct;
use App\Models\BrandPartnerProductOption;
use App\Services\TpinkLabService;
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

        $categories = $this->getCategoryValues();
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
        $categories = $this->getCategoryValues();
        $collections = $this->getCollectionValues();

        return Inertia::modal('product/create', [
            'categories' => $categories,
            'collections' => $collections,
        ])->baseRoute('brand-partner.products.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $validated['track_stock'] = true;
            $validated['meta'] = array_merge(
                (array) ($validated['meta'] ?? []),
                ['variants' => $request->input('variants', [])],
            );
            $product = $this->brandPartner()->products()->create([...$validated,
                'slug' => $request->slug ?? Str::slug($request->name),
                'price' => (int) (($request->price ?? 0) * 100),
                'compare_price' => $request->compare_price ? (int) ($request->compare_price * 100) : null,
                'status' => config('store.brand_partner_product_approval', true)
                    ? \App\Enums\BrandPartnerProductStatus::DRAFT
                    : \App\Enums\BrandPartnerProductStatus::PUBLISHED,
                'approval_status' => config('store.brand_partner_product_approval', true) ? 'pending' : 'approved',
                'approval_notes' => null,
                'approved_at' => config('store.brand_partner_product_approval', true) ? null : now(),
            ]);

            return $product;
        });

        // Notify TPInkAdmin about the new product (non-blocking; errors are logged)
        app(TpinkLabService::class)->submitProductForApproval($product);

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

        $product->load(['category', 'event', 'images', 'collection']);
        $categories = $this->getCategoryValues();
        $events = $this->brandPartner()->events()->enabled()->get();
        $collections = $this->getCollectionValues();
        $colors = $this->getColorValues();
        $sizes = $this->getSizeValues();

        return Inertia::modal('product/edit', [
            'product' => $product,
            'categories' => $categories,
            'events' => $events,
            'collections' => $collections,
            'colorOptions' => $colors,
            'sizeOptions' => $sizes,
            'statusOptions' => BrandPartnerProductStatus::getOptions(),
        ])->baseRoute('brand-partner.products.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, BrandPartnerProduct $product)
    {
        $this->authorize($product);

        // Prevent publishing without approval (unless approval feature is disabled)
        $approvalEnabled = config('store.brand_partner_product_approval', true);
        if ($request->status === 'published' && $approvalEnabled && ! $product->canBePublished()) {
            return response()->json([
                'message' => 'Product must be approved by TPInkAdmin before it can be published.',
                'errors' => ['status' => ['Product requires TPInkAdmin approval before publishing.']],
            ], 422);
        }

        DB::transaction(function () use ($request, $product) {
            $data = $request->validated();
            $data['track_stock'] = true;
            $data['price'] = (int) ($request->price * 100);
            $data['compare_price'] = $request->compare_price ? (int) ($request->compare_price * 100) : null;
            $data['meta'] = array_merge(
                (array) ($data['meta'] ?? []),
                ['variants' => $request->input('variants', [])],
            );

            $product->update($data);
        });

        // Sync published status to TPInkAdmin so the front store reflects the change
        if ($request->status === 'published') {
            app(TpinkLabService::class)->syncProductStatus($product->fresh());
        }

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
     * Get the collection option values for the current brand partner.
     */
    protected function getCollectionValues(): \Illuminate\Support\Collection
    {
        $option = BrandPartnerProductOption::where('brand_partner_id', $this->brandPartner()->id)
            ->where('name', 'Collection')
            ->first();

        return $option ? $option->values()->orderBy('position')->get() : collect();
    }

    /**
     * Get the category option values for the current brand partner.
     */
    protected function getCategoryValues(): \Illuminate\Support\Collection
    {
        $option = BrandPartnerProductOption::where('brand_partner_id', $this->brandPartner()->id)
            ->where('name', 'Category')
            ->first();

        return $option ? $option->values()->orderBy('position')->get() : collect();
    }

    /**
     * Get the color option values for the current brand partner.
     */
    protected function getColorValues(): \Illuminate\Support\Collection
    {
        $option = BrandPartnerProductOption::where('brand_partner_id', $this->brandPartner()->id)
            ->where('name', 'Color')
            ->first();

        return $option ? $option->values()->orderBy('position')->get() : collect();
    }

    /**
     * Get the size option values for the current brand partner.
     */
    protected function getSizeValues(): \Illuminate\Support\Collection
    {
        $option = BrandPartnerProductOption::where('brand_partner_id', $this->brandPartner()->id)
            ->where('name', 'Size')
            ->first();

        return $option ? $option->values()->orderBy('position')->get() : collect();
    }

    /**
     * Authorize that the product belongs to the current brand partner.
     */
    protected function authorize(BrandPartnerProduct $product)
    {
        if ((int) $product->brand_partner_id !== (int) $this->brandPartner()->id) {
            abort(403);
        }
    }
}
