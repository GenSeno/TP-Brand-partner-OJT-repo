<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\Country;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SupplierController extends Controller
{
    protected $defaultPerPage = 10;

    public function index(Request $request)
    {
        $suppliers = QueryBuilder::for(Supplier::class)
            ->allowedSorts(['name', 'created_at'])
            ->allowedFilters([
                AllowedFilter::scope('search'),
                AllowedFilter::exact('enabled'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('admin/supplier/index', [
            'suppliers' => $suppliers,
            'filter' => $request->input('filter', []) + [
                'default_per_page' => $this->defaultPerPage,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::modal('admin/supplier/create', [
            'countries' => Country::orderBy('name')->get(),
        ])->baseRoute('admin.supplier.index');
    }

    public function store(SupplierRequest $request)
    {
        $supplier = DB::transaction(function () use ($request) {
            $data = $request->only([
                'country_id',
                'name',
                'contact_person',
                'email',
                'phone',
                'address',
                'city',
                'province',
                'postcode',
                'enabled',
                'notes'
            ]);

            $supplier = Supplier::create($data);

            if ($request->hasFile('logo')) {
                $supplier->addMediaFromRequest('logo')->toMediaCollection('logo');
            }

            return $supplier;
        });

        return response()->json([
            'supplier' => $supplier,
            'message' => __('crud.created', ['record' => 'Supplier']),
        ], 201);
    }

    public function edit(Supplier $supplier)
    {
        return Inertia::modal('admin/supplier/edit', [
            'supplier' => $supplier,
            'countries' => Country::orderBy('name')->get(),
        ])->baseRoute('admin.supplier.index');
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $data = $request->only([
            'country_id',
            'name',
            'contact_person',
            'email',
            'phone',
            'address',
            'city',
            'province',
            'postcode',
            'enabled',
            'notes'
        ]);

        $supplier->update($data);

        if ($request->hasFile('logo')) {
            $supplier->addMediaFromRequest('logo')->toMediaCollection('logo');
        } elseif ($request->input('logo_removed')) {
            $supplier->clearMediaCollection('logo');
        }

        return response()->json([
            'supplier' => $supplier,
            'message' => __('crud.updated', ['record' => 'Supplier']),
        ]);
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->json([
            'deleted' => [$supplier->id],
            'message' => __('crud.deleted', ['record' => 'Supplier']),
        ]);
    }
}
