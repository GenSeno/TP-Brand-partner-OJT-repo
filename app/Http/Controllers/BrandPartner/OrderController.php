<?php

namespace App\Http\Controllers\BrandPartner;

use App\Enums\BrandPartnerOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartnerOrder;
use App\Services\TpinkLabService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderController extends Controller
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
        $orders = QueryBuilder::for(BrandPartnerOrder::class)
            ->where('brand_partner_id', $this->brandPartner()->id)
            ->with(['lines.product'])
            ->allowedSorts(['reference', 'customer_name', 'status', 'total', 'created_at'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
            ])
            ->latest()
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        $counts = [
            'total' => $this->brandPartner()->orders()->count(),
            'pending' => $this->brandPartner()->orders()->where('status', BrandPartnerOrderStatus::PENDING)->count(),
            'confirmed' => $this->brandPartner()->orders()->where('status', BrandPartnerOrderStatus::CONFIRMED)->count(),
            'completed' => $this->brandPartner()->orders()->where('status', BrandPartnerOrderStatus::COMPLETED)->count(),
        ];

        return Inertia::render('order/index', [
            'orders' => $orders,
            'counts' => $counts,
            'statusOptions' => BrandPartnerOrderStatus::getOptions(),
            'filter' => $request->input('filter', []),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(BrandPartnerOrder $order)
    {
        $this->authorize($order);

        $order->load(['lines.product.images', 'customer']);

        return Inertia::render('order/show', [
            'order' => $order,
        ]);
    }

    /**
     * Confirm the order.
     */
    public function confirm(BrandPartnerOrder $order)
    {
        $this->authorize($order);

        if (!$order->isPending()) {
            return back()->with('error', __('Only pending orders can be confirmed.'));
        }

        $order->confirm();

        (new TpinkLabService)->sendOrderToAdmin($order);

        return back()->with('success', __('Order confirmed successfully.'));
    }

    /**
     * Complete the order.
     */
    public function complete(BrandPartnerOrder $order)
    {
        $this->authorize($order);

        if (!$order->isConfirmed()) {
            return back()->with('error', __('Only confirmed orders can be completed.'));
        }

        $order->complete();

        return back()->with('success', __('Order completed successfully.'));
    }

    /**
     * Cancel the order.
     */
    public function cancel(BrandPartnerOrder $order)
    {
        $this->authorize($order);

        if ($order->isCompleted() || $order->isCancelled()) {
            return back()->with('error', __('This order cannot be cancelled.'));
        }

        $order->cancel();

        return back()->with('success', __('Order cancelled successfully.'));
    }

    /**
     * Authorize that the order belongs to the current brand partner.
     */
    protected function authorize(BrandPartnerOrder $order)
    {
        if ($order->brand_partner_id !== $this->brandPartner()->id) {
            abort(403);
        }
    }
}
