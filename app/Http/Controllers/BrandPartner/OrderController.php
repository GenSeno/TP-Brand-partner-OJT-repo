<?php

namespace App\Http\Controllers\BrandPartner;

use App\Enums\BrandPartnerOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartnerOrder;
use App\Models\BrandPartnerProduct;
use App\Services\TpinkLabService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Show the form for creating a new order manually.
     */
    public function create()
    {
        $products = BrandPartnerProduct::where('brand_partner_id', $this->brandPartner()->id)
            ->published()
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'colors', 'sizes']);

        return Inertia::modal('order/create', [
            'products' => $products,
        ])->baseRoute('brand-partner.orders.index');
    }

    /**
     * Store a manually created order (confirmed, no API push).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'placed_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'integer', 'exists:brand_partner_products,id'],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
            'lines.*.color' => ['nullable', 'string', 'max:255'],
            'lines.*.size' => ['nullable', 'string', 'max:255'],
        ]);

        $order = DB::transaction(function () use ($validated) {
            $order = $this->brandPartner()->orders()->create([
                'customer_name' => $validated['customer_name'],
                'company_name' => $validated['company_name'] ?? null,
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'address_line1' => $validated['address_line1'] ?? null,
                'address_line2' => $validated['address_line2'] ?? null,
                'barangay' => $validated['barangay'] ?? null,
                'city' => $validated['city'] ?? null,
                'province' => $validated['province'] ?? null,
                'postcode' => $validated['postcode'] ?? null,
                'placed_at' => $validated['placed_at'] ?? now(),
                'notes' => $validated['notes'] ?? null,
                'status' => BrandPartnerOrderStatus::CONFIRMED,
                'sub_total' => 0,
                'tax_total' => 0,
                'total' => 0,
            ]);

            foreach ($validated['lines'] as $line) {
                $product = BrandPartnerProduct::find($line['product_id']);
                $unitPrice = (int) round($line['unit_price'] * 100);
                $meta = [];
                if (! empty($line['color'])) {
                    $meta['color'] = $line['color'];
                }
                if (! empty($line['size'])) {
                    $meta['size'] = $line['size'];
                }
                $order->lines()->create([
                    'product_id' => $line['product_id'],
                    'product_name' => $product->name,
                    'quantity' => $line['quantity'],
                    'unit_price' => $unitPrice,
                    'total' => $unitPrice * $line['quantity'],
                    'meta' => empty($meta) ? null : $meta,
                ]);

                $product->decrementStock($line['quantity'], $line['color'] ?? null, $line['size'] ?? null);
            }

            return $order->fresh();
        });

        return response()->json([
            'order' => $order,
            'message' => 'Order created successfully.',
        ], 201);
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
     * Show the form for editing an order.
     */
    public function edit(BrandPartnerOrder $order)
    {
        $this->authorize($order);

        $order->load(['lines.product']);

        return Inertia::modal('order/edit', [
            'order' => $order,
            'joStatusOptions' => [
                'pending' => 'Pending',
                'processing' => 'Processing',
                'ready' => 'Ready for Pickup',
                'delivered' => 'Delivered',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ],
        ])->baseRoute('brand-partner.orders.show', $order);
    }

    /**
     * Update the order.
     */
    public function update(Request $request, BrandPartnerOrder $order)
    {
        $this->authorize($order);

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'placed_at' => ['nullable', 'date'],
            'jo_number' => ['nullable', 'string', 'max:100'],
            'jo_status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $order->update($validated);

        return response()->json([
            'order' => $order->fresh(),
            'message' => 'Order updated successfully.',
        ]);
    }

    /**
     * Show the receive payment modal.
     */
    public function receivePaymentForm(BrandPartnerOrder $order)
    {
        $this->authorize($order);

        return Inertia::modal('order/receive-payment', [
            'order' => $order,
        ])->baseRoute('brand-partner.orders.show', $order);
    }

    /**
     * Update the payment status of an order.
     */
    public function receivePayment(Request $request, BrandPartnerOrder $order)
    {
        $this->authorize($order);

        $request->validate([
            'payment_status' => ['required', 'string', 'in:unpaid,partially_paid,paid,cancelled'],
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return response()->json([
            'order' => $order->fresh(),
            'message' => 'Payment status updated successfully.',
        ]);
    }

    /**
     * Send the order directly to TPInkAdmin (third-party API).
     */
    public function sendToAdmin(BrandPartnerOrder $order)
    {
        $this->authorize($order);

        (new TpinkLabService)->sendOrderToAdmin($order);

        return back()->with('success', 'Order sent to TPInkAdmin successfully.');
    }

    /**
     * Confirm the order.
     */
    public function confirm(BrandPartnerOrder $order)
    {
        $this->authorize($order);

        if (! $order->isPending()) {
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

        if (! $order->isConfirmed()) {
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
        if ((int) $order->brand_partner_id !== (int) $this->brandPartner()->id) {
            abort(403);
        }
    }
}
