<?php

namespace App\Http\Controllers\BrandPartner;

use App\Enums\BrandPartnerEventStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandPartner\EventRequest;
use App\Models\BrandPartnerEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EventController extends Controller
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
        $events = QueryBuilder::for(BrandPartnerEvent::class)
            ->where('brand_partner_id', $this->brandPartner()->id)
            ->withCount('products')
            ->allowedSorts(['name', 'status', 'start_date', 'end_date', 'created_at'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('enabled'),
                AllowedFilter::scope('search'),
            ])
            ->latest()
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('event/index', [
            'events' => $events,
            'statusOptions' => BrandPartnerEventStatus::getOptions(),
            'filter' => $request->input('filter', []),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('event/create', [
            'statusOptions' => BrandPartnerEventStatus::getOptions(),
        ])->baseRoute('brand-partner.events.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {
        $event = $this->brandPartner()->events()->create([
            ...$request->validated(),
            'slug' => $request->slug ?? Str::slug($request->name),
        ]);

        return response()->json([
            'event' => $event,
            'message' => __('crud.created', ['record' => 'Event']),
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BrandPartnerEvent $event)
    {
        $this->authorize($event);

        return Inertia::modal('event/edit', [
            'event' => $event,
            'statusOptions' => BrandPartnerEventStatus::getOptions(),
        ])->baseRoute('brand-partner.events.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, BrandPartnerEvent $event)
    {
        $this->authorize($event);

        $event->update($request->validated());

        return response()->json([
            'event' => $event->fresh(),
            'message' => __('crud.updated', ['record' => 'Event']),
        ]);
    }

    /**
     * Toggle the enabled status.
     */
    public function toggleStatus(BrandPartnerEvent $event)
    {
        $this->authorize($event);

        $event->update(['enabled' => !$event->enabled]);

        return to_route('brand-partner.events.index')
            ->with('success', __('crud.updated', ['record' => 'Event']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BrandPartnerEvent $event)
    {
        $this->authorize($event);

        if ($event->products()->exists()) {
            return to_route('brand-partner.events.index')
                ->with('error', __('Event cannot be deleted because it has products.'));
        }

        $event->delete();

        return to_route('brand-partner.events.index')
            ->with('success', __('crud.deleted', ['record' => 'Event']));
    }

    /**
     * Authorize that the event belongs to the current brand partner.
     */
    protected function authorize(BrandPartnerEvent $event)
    {
        if ((int) $event->brand_partner_id !== (int) $this->brandPartner()->id) {
            abort(403);
        }
    }
}
