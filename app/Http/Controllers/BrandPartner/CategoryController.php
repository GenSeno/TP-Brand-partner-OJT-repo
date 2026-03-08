<?php

namespace App\Http\Controllers\BrandPartner;

use App\Enums\BrandPartnerCategoryType;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandPartner\CategoryRequest;
use App\Models\BrandPartnerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
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
        $categories = QueryBuilder::for(BrandPartnerCategory::class)
            ->where('brand_partner_id', $this->brandPartner()->id)
            ->withCount('products')
            ->allowedSorts(['name', 'type', 'position', 'created_at'])
            ->allowedFilters([
                AllowedFilter::exact('type'),
                AllowedFilter::exact('enabled'),
                AllowedFilter::scope('search'),
            ])
            ->orderBy('position')
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('category/index', [
            'categories' => $categories,
            'typeOptions' => BrandPartnerCategoryType::getOptions(),
            'filter' => $request->input('filter', []),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('category/create', [
            'typeOptions' => BrandPartnerCategoryType::getOptions(),
        ])->baseRoute('brand-partner.categories.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $category = $this->brandPartner()->categories()->create([
            ...$request->validated(),
            'slug' => $request->slug ?? Str::slug($request->name),
        ]);

        return response()->json([
            'category' => $category,
            'message' => __('crud.created', ['record' => 'Category']),
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BrandPartnerCategory $category)
    {
        $this->authorize($category);

        return Inertia::modal('category/edit', [
            'category' => $category,
            'typeOptions' => BrandPartnerCategoryType::getOptions(),
        ])->baseRoute('brand-partner.categories.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, BrandPartnerCategory $category)
    {
        $this->authorize($category);

        $category->update($request->validated());

        return response()->json([
            'category' => $category->fresh(),
            'message' => __('crud.updated', ['record' => 'Category']),
        ]);
    }

    /**
     * Toggle the enabled status.
     */
    public function toggleStatus(BrandPartnerCategory $category)
    {
        $this->authorize($category);

        $category->update(['enabled' => !$category->enabled]);

        return to_route('brand-partner.categories.index')
            ->with('success', __('crud.updated', ['record' => 'Category']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BrandPartnerCategory $category)
    {
        $this->authorize($category);

        if ($category->products()->exists()) {
            return to_route('brand-partner.categories.index')
                ->with('error', __('Category cannot be deleted because it has products.'));
        }

        $category->delete();

        return to_route('brand-partner.categories.index')
            ->with('success', __('crud.deleted', ['record' => 'Category']));
    }

    /**
     * Authorize that the category belongs to the current brand partner.
     */
    protected function authorize(BrandPartnerCategory $category)
    {
        if ($category->brand_partner_id !== $this->brandPartner()->id) {
            abort(403);
        }
    }
}
