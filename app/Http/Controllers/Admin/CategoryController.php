<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = QueryBuilder::for(Category::class)
            ->allowedSorts(['name', 'slug', 'created_at'])
            ->allowedFilters([
                AllowedFilter::exact('enabled'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('admin/category/index', [
            'categories' => $categories,
            'filter' => $request->input('filter', []),
            'default_per_page' => $this->defaultPerPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('admin/category/create')->baseRoute('admin.category.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $category = DB::transaction(function () use ($request) {
            $category = Category::create($request->only(['name', 'slug', 'enabled']));
            if ($request->hasFile('logo')) {
                $category
                    ->addMediaFromRequest('logo')
                    ->toMediaCollection('logo');
            }
            return $category;
        });

        return response()->json([
            'category' => $category,
            'message' => __('crud.created', ['record' => 'Category'])
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return Inertia::modal('admin/category/edit', [
            'category' => $category,
        ])->baseRoute('admin.category.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->only(['name', 'slug', 'enabled']);
        $category->update($data);
        if ($request->hasFile('logo')) {
            $category
                ->addMediaFromRequest('logo')
                ->toMediaCollection('logo');
        } elseif ($request->input('logo_removed')) {
            $category
                ->clearMediaCollection('logo');
        }

        return response()->json([
            'category' => $category,
            'message' => __('crud.updated', ['record' => 'Category'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([
            'deleted' => [$category->id],
            'message' => __('crud.deleted', ['record' => 'Category']),
        ]);
    }

    public function getProducts(Category $category)
    {
        return $category->products()
            ->where('status', 'published')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
