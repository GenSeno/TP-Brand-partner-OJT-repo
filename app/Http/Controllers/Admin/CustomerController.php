<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\Country;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\CountryResource;

class CustomerController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = QueryBuilder::for(Customer::class)
            ->allowedFilters([
                AllowedFilter::exact('enabled'),
                AllowedFilter::scope('search'),
            ])
            ->defaultSort('-created_at')
            ->with([
                'addresses' => fn($query) => $query->where('default', 1)
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        // Overall counts, ignoring filters and pagination
        $counts = [
            'total' => Customer::count(),
            'active' => Customer::enabled()->count(),
            'inactive' => Customer::disabled()->count(),
        ];

        return Inertia::render('admin/customer/index', [
            'customers' => $customers,
            'counts' => $counts,
            'filter' => $request->input('filter', []) + [
                'default_per_page' => $this->defaultPerPage
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('admin/customer/create', [
            'countries' => function () {
                $countries = Country::all(['id', 'name', 'emoji', 'iso2', 'phonecode']);
                return [
                    'data' => $countries,
                    'default' => $countries->firstWhere('iso2', 'PH'),
                ];
            }
        ])->baseRoute('admin.customer.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        $customer = DB::transaction(function () use ($request) {

            $customer = Customer::create($request->only([
                'title',
                'first_name',
                'last_name',
                'company_name',
                'enabled'
            ]));

            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $extension = $request->file('avatar')->getClientOriginalExtension();
                $timestamp = now()->format('Ymd_His');

                $customer->addMedia($request->file('avatar'))
                    ->usingFileName("{$customer->id}{$timestamp}.{$extension}")
                    ->toMediaCollection('customers');
            }

            $customer->addresses()->create(
                array_merge(
                    $request->address,
                    [
                        'type' => 'billing',
                        'default' => 1,
                    ]
                )
            );

            if ($request->has('shipping')) {
                $customer->addresses()->create(
                    array_merge(
                        $request->shipping,
                        [
                            'type' => 'shipping',
                            'default' => 0,
                        ]
                    )
                );

            }
            // Load relationships
            $customer->load(['addresses', 'media']);

            return $customer;
        });

        return response()->json([
            'customer' => $customer,
            'message' => __('crud.created', ['record' => 'Customer'])
        ], 201);
    }



    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $customer->load([
            'billing',
            'shipping',
        ]);

        return Inertia::modal('admin/customer/edit', [
            'customer' => $customer,
            'countries' => function () {
                $countries = Country::all(['id', 'name', 'emoji', 'iso2', 'phonecode']);
                return [
                    'data' => $countries,
                    'default' => $countries->firstWhere('iso2', 'PH'),
                ];
            }
        ])->baseRoute('admin.customer.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        DB::transaction(function () use ($request, $customer) {

            $customer->update($request->only([
                'title',
                'first_name',
                'last_name',
                'company_name',
                'enabled'
            ]));

            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $extension = $request->file('avatar')->getClientOriginalExtension();
                $timestamp = now()->format('Ymd_His');

                // Clear previous avatar in the 'customers' collection
                $customer->clearMediaCollection('customers');

                // Add new avatar
                $customer->addMedia($request->file('avatar'))
                    ->usingFileName("{$customer->id}_{$timestamp}.{$extension}")
                    ->toMediaCollection('customers');
            }

            // Remove avatar if requested
            if ($request->boolean('avatar_removed')) {
                $customer->clearMediaCollection('customers');
            }
            // Default address
            $address = $customer->billing()->first();

            if ($address) {
                $address->update($request->address);
            } else {
                $customer->addresses()->create(array_merge($request->address, [
                    'type' => 'billing',
                    'default' => 1,
                ]));
            }

            if ($request->has('shipping')) {
                $shipping = $customer->shipping()->first();
                if ($shipping) {
                    $shipping->update($request->shipping);
                } else {
                    $customer->addresses()->create(array_merge($request->shipping, [
                        'type' => 'shipping',
                        'default' => 0,
                    ]));
                }
            } else {
                $customer->addresses()
                    ->where('type', 'shipping')
                    ->delete();
            }

        });

        return response()->json([
            'customer' => $customer->fresh(['addresses', 'media']),
            'message' => 'Customer updated successfully.',
        ]);
    }


    public function toggleStatus(Customer $customer)
    {
        $customer->enabled = !$customer->enabled;
        $customer->save();

        return to_route('admin.customer.index')
            ->with('success', __('crud.updated', ['record' => 'Customer']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        if (!$customer->is_deletable) { // use the accessor
            return to_route('admin.customer.index')
                ->with('error', __('Customer cannot be deleted because it has orders or quotations.'));
        }

        $customer->delete();
        return to_route('admin.customer.index')
            ->with('success', __('crud.deleted', ['record' => 'Customer']));
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return to_route('admin.customer.index');
        }

        $customers = Customer::whereIn('id', $ids)->get()->filter(fn($customer) => $customer->is_deletable);

        if ($customers->isEmpty()) {
            return to_route('admin.customer.index')
                ->with('error', __('No selected customers can be deleted.'));
        }

        foreach ($customers as $customer) {
            $customer->delete(); // triggers deleting event in Customer model
        }

        return to_route('admin.customer.index')
            ->with('success', __('crud.deleted', ['record' => 'Customer(s)']));
    }

    public function search($search) // $search comes from the URL
    {
        if (!$search) {
            return response()->json([]);
        }

        $customers = Customer::where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json($customers);
    }


}
