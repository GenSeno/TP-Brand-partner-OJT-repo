<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandPartnerRequest;
use App\Models\BrandPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BrandPartnerController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $brandPartners = QueryBuilder::for(BrandPartner::class)
            ->withCount(['products', 'orders'])
            ->allowedSorts(['name', 'created_at', 'status'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        $counts = [
            'total' => BrandPartner::count(),
            'pending' => BrandPartner::where('status', BrandPartnerStatus::PENDING)->count(),
            'active' => BrandPartner::where('status', BrandPartnerStatus::ACTIVE)->count(),
            'suspended' => BrandPartner::where('status', BrandPartnerStatus::SUSPENDED)->count(),
        ];

        return Inertia::render('admin/brand-partner/index', [
            'brandPartners' => $brandPartners,
            'counts' => $counts,
            'filter' => $request->input('filter', []),
            'statusOptions' => BrandPartnerStatus::getOptions(),
            'default_per_page' => $this->defaultPerPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::modal('admin/brand-partner/create', [
            'statusOptions' => BrandPartnerStatus::getOptions(),
        ])->baseRoute('admin.brand-partners.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandPartnerRequest $request)
    {
        $brandPartner = DB::transaction(function () use ($request) {
            $brandPartner = BrandPartner::create([
                ...$request->only([
                    'name',
                    'slug',
                    'email',
                    'contact_person',
                    'phone',
                    'address',
                    'description',
                    'status',
                ]),
                'password' => Hash::make($request->password),
            ]);

            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $extension = $request->file('logo')->getClientOriginalExtension();
                $timestamp = now()->format('Ymd_His');

                $brandPartner->addMedia($request->file('logo'))
                    ->usingFileName("{$brandPartner->id}_{$timestamp}.{$extension}")
                    ->toMediaCollection('logo');
            }

            return $brandPartner;
        });

        return response()->json([
            'brandPartner' => $brandPartner,
            'message' => __('crud.created', ['record' => 'Brand Partner']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BrandPartner $brandPartner)
    {
        $brandPartner->load(['products', 'orders' => fn($q) => $q->latest()->limit(10)]);
        $brandPartner->loadCount(['products', 'orders', 'categories', 'events']);

        return Inertia::render('admin/brand-partner/show', [
            'brandPartner' => $brandPartner,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BrandPartner $brandPartner)
    {
        return Inertia::modal('admin/brand-partner/edit', [
            'brandPartner' => $brandPartner,
            'statusOptions' => BrandPartnerStatus::getOptions(),
        ])->baseRoute('admin.brand-partners.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandPartnerRequest $request, BrandPartner $brandPartner)
    {
        DB::transaction(function () use ($request, $brandPartner) {
            $data = $request->only([
                'name',
                'slug',
                'email',
                'contact_person',
                'phone',
                'address',
                'description',
                'status',
            ]);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $brandPartner->update($data);

            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $extension = $request->file('logo')->getClientOriginalExtension();
                $timestamp = now()->format('Ymd_His');

                $brandPartner->clearMediaCollection('logo');
                $brandPartner->addMedia($request->file('logo'))
                    ->usingFileName("{$brandPartner->id}_{$timestamp}.{$extension}")
                    ->toMediaCollection('logo');
            }

            if ($request->boolean('logo_removed')) {
                $brandPartner->clearMediaCollection('logo');
            }
        });

        return response()->json([
            'brandPartner' => $brandPartner->fresh(),
            'message' => __('crud.updated', ['record' => 'Brand Partner']),
        ]);
    }

    /**
     * Approve a brand partner.
     */
    public function approve(BrandPartner $brandPartner)
    {
        $brandPartner->update(['status' => BrandPartnerStatus::ACTIVE]);

        return to_route('admin.brand-partners.index')
            ->with('success', __('Brand Partner approved successfully.'));
    }

    /**
     * Suspend a brand partner.
     */
    public function suspend(BrandPartner $brandPartner)
    {
        $brandPartner->update(['status' => BrandPartnerStatus::SUSPENDED]);

        return to_route('admin.brand-partners.index')
            ->with('success', __('Brand Partner suspended successfully.'));
    }

    /**
     * Toggle status between active and suspended.
     */
    public function toggleStatus(BrandPartner $brandPartner)
    {
        $newStatus = $brandPartner->status === BrandPartnerStatus::ACTIVE
            ? BrandPartnerStatus::SUSPENDED
            : BrandPartnerStatus::ACTIVE;

        $brandPartner->update(['status' => $newStatus]);

        return to_route('admin.brand-partners.index')
            ->with('success', __('crud.updated', ['record' => 'Brand Partner']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BrandPartner $brandPartner)
    {
        if ($brandPartner->orders()->exists()) {
            return to_route('admin.brand-partners.index')
                ->with('error', __('Brand Partner cannot be deleted because it has orders.'));
        }

        $brandPartner->delete();

        return to_route('admin.brand-partners.index')
            ->with('success', __('crud.deleted', ['record' => 'Brand Partner']));
    }
}
