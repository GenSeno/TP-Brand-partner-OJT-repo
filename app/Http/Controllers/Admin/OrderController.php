<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Str;

class OrderController extends Controller
{
    protected $defaultPerPage = 10;

    public function index(Request $request)
    {
        $orders = QueryBuilder::for(Order::class)
            ->with(['billingAddress', 'orderable', 'jobOrder'])
            ->withSum('lines', 'quantity')
            ->allowedSorts(['reference', 'created_at', 'total'])
            ->defaultSort('-created_at')
            ->allowedFilters([
                AllowedFilter::scope('search'),
                AllowedFilter::exact('status'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        // counts for cards
        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $counts = [
            'pending' => $statusCounts->get(OrderStatus::PENDING->value, 0),
            'unpaid' => $statusCounts->get(OrderStatus::UNPAID->value, 0),
            'partially_paid' => $statusCounts->get(OrderStatus::PARTIALLY_PAID->value, 0),
            'completed' => $statusCounts->get(OrderStatus::COMPLETED->value, 0),
        ];

        return Inertia::render('admin/order/index', [
            'orders' => $orders,
            'filter' => $request->input('filter', []) + [
                'default_per_page' => $this->defaultPerPage,
            ],
            'counts' => $counts,
            'statusOptions' => OrderStatus::getOptions(),
        ]);
    }

    public function create()
    {
        return Inertia::modal('admin/order/create', [
            'customers' => Customer::orderByName()->getOptions('full_name', 'id'),
            'countries' => Country::get(['id', 'name', 'emoji']),
        ])->baseRoute('admin.order.index');
    }

    public function store(OrderRequest $request)
    {
        $customerId = $request->input('customer_id');

        // If no customer_id provided, create a new customer from address data
        if (! $customerId && $request->has('address')) {
            $addressData = $request->input('address');
            $customer = Customer::create([
                'title' => $addressData['title'] ?? null,
                'first_name' => $addressData['first_name'],
                'last_name' => $addressData['last_name'],
                'company_name' => $addressData['company_name'] ?? null,
            ]);
            $customerId = $customer->id;
        }

        $data = [
            'orderable_type' => Customer::class,
            'orderable_id' => $customerId,
            'notes' => $request->input('notes'),
            'status' => $request->input('status', OrderStatus::getDefault()),
            'currency_code' => Currency::getDefault()->code,
        ];

        $order = Order::create($data);

        // Create billing address
        if ($request->has('address')) {
            $billingData = collect($request->address)->only([
                'line1',
                'line2',
                'city',
                'province',
                'barangay',
                'postcode',
                'country_id',
                'email',
                'phone',
                'title',
                'first_name',
                'last_name',
                'company_name',
            ])->toArray() + [
                'type' => 'billing',
                'meta' => [
                    'need' => $request->input('need'),
                    'notes' => $request->address['notes'] ?? null,
                    'shipping_option' => $request->input('shipping_option', 'delivery'),
                ],
            ];
            $billingAddress = $order->addresses()->create($billingData);
        }

        // Create shipping address
        if ($request->has('shipping') && $request->input('shipping')) {
            // Different shipping address provided
            $shippingData = $request->input('shipping');
            $shippingData['type'] = 'shipping';
            $order->addresses()->create($shippingData);
        }
        // elseif (isset($billingAddress)) {
        //     // No different shipping address, use billing address as shipping
        //     $shippingData = $billingAddress->toArray();
        //     unset($shippingData['id'], $shippingData['created_at'], $shippingData['updated_at']);
        //     $shippingData['type'] = 'shipping';
        //     $order->addresses()->create($shippingData);
        // }

        return response()->json([
            'order' => $order->load('billingAddress', 'shippingAddress', 'orderable'),
            'message' => __('crud.created', ['record' => 'Sales Order']),
        ], 201);
    }

    public function show(Order $order)
    {
        $order->append('billing_summary')
            ->load([
                'billingAddress.country',
                'shippingAddress.country',
                'orderable',
                'printLines.product',
                'invoices',
                'media',
            ]);

        return Inertia::render('admin/order/show', [
            'order' => $order,
            'canCreateJobOrder' => $order->canCreateJobOrder(),
            'activities' => fn () => $order->activities()->latest()->with('causer')->take(100)->get(),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'placed_at' => ['nullable', 'date'],
            'expected_delivery' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'reason' => ['nullable', 'string', 'max:5000'],
        ]);

        $reason = $data['reason'] ?? null;
        unset($data['reason']);

        $order->update($data);

        if ($reason) {
            $order->logNote("Edit reason: {$reason}");
        }

        return to_route('admin.order.show', ['order' => $order->id])
            ->with('success', __('crud.updated', ['record' => 'Sales Order']));
    }

    public function cancel(Request $request, Order $order)
    {
        if (! $order->canBeCancelled()) {
            return response()->json([
                'message' => __('This order cannot be cancelled.'),
            ], 400);
        }

        $request->validate([
            'reason' => ['required', 'string', 'max:5000'],
        ]);

        $order->update(['status' => OrderStatus::CANCELLED]);
        $order->logNote("Cancel reason: {$request->input('reason')}");

        return response()->json([
            'message' => __('Order cancelled successfully.'),
        ]);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'deleted' => [$order->id],
            'message' => __('crud.deleted', ['record' => 'Sales Order']),
        ]);
    }

    public function createJobOrder(Order $order)
    {
        if (! $order->canCreateJobOrder()) {
            return response()->json([
                'message' => __('Cannot create Job Order from this Sales Order.'),
            ], 400);
        }

        $jobOrder = $order->createJobOrder();

        return response()->json([
            'job_order' => $jobOrder->load('order'),
            'message' => __('Job Order created successfully from Sales Order.'),
        ]);
    }

    public function createInvoice(InvoiceService $invoiceService, Order $order)
    {
        if (! $order->canCreateInvoice()) {
            return response()->json([
                'message' => __('Cannot create Billing from this Sales Order.'),
            ], 400);
        }

        $invoice = $invoiceService->createFromOrder($order);

        return response()->json([
            'invoice' => $invoice->load('order'),
            'message' => __('Billing created successfully from Sales Order.'),
        ]);
    }

    public function addNote(Request $request, Order $order)
    {
        $request->validate([
            'note' => ['required', 'string', 'max:5000'],
        ]);

        $order->logNote($request->input('note'));

        return response()->json([
            'message' => __('Note added successfully.'),
        ], 201);
    }

    public function upload(Request $request, Order $order)
    {
        $config = config('file-attachments');

        $request->validate([
            'files.*' => [
                'required',
                Rule::file()
                    ->max($config['max']['default'])
                    ->types($config['allowed_extensions']['default']),
            ],
        ]);

        $uploaded = array_map(function ($file) use ($order) {
            $originalName = $file->getClientOriginalName();
            $cleanName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();

            $finalFileName = "{$order->reference}_{$cleanName}.{$extension}";

            $media = $order
                ->addMedia($file)
                ->usingFileName($finalFileName)
                ->toMediaCollection('files', 'private');

            return [
                'id' => $media->id,
                'name' => $finalFileName,
                'path' => $media->getPathRelativeToRoot(),
                'original_url' => $media->getUrl(),
                'extension' => $extension,
                'size' => $media->size,
                'created_at' => $media->created_at->toDateTimeString(),
            ];
        }, $request->file('files', []));

        return response()->json([
            'files' => $uploaded,
        ], 201);
    }

    public function fetchByStatus($status)
    {
        $statuses = explode(',', $status);

        $orders = Order::with(['billingAddress', 'orderable'])
            ->whereIn('status', $statuses)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function destroyMedia(Order $order, $mediaId)
    {
        $media = $order->media()->findOrFail($mediaId);
        $media->delete();

        return response()->noContent();
    }
}
