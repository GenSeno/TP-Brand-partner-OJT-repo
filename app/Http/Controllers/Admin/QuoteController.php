<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GenerateReference;
use App\Enums\QuoteStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuoteDateRequest;
use App\Http\Requests\QuoteRequest;
use App\Lunar\DataTypes\Price;
use App\Lunar\ValueObjects\Cart\DiscountBreakdown;
use App\Lunar\ValueObjects\Cart\DiscountBreakdownLine;
use App\Lunar\ValueObjects\Cart\ShippingBreakdown;
use App\Lunar\ValueObjects\Cart\ShippingBreakdownItem;
use App\Lunar\ValueObjects\Cart\TaxBreakdown;
use App\Lunar\ValueObjects\Cart\TaxBreakdownAmount;
use App\Mail\QuotationMail;
use App\Enums\OrderStatus;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Sorts\QuoteAddressSort;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class QuoteController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $quotation = QueryBuilder::for(Quote::class)
            ->allowedSorts([
                'reference',
                'status',
                AllowedSort::custom('full_name', new QuoteAddressSort('first_name')),
                AllowedSort::custom('company_name', new QuoteAddressSort('company_name')),
            ])
            ->defaultSort('-created_at')
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
            ])
            ->with([
                'owner',
                'billingAddress',
                'lines',
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        // Overall counts, ignoring filters and pagination
        $statusCounts = Quote::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $counts = [
            'total' => $statusCounts->sum(),
            'new' => $statusCounts->get('request', 0),
            'draft' => $statusCounts->get('draft', 0),
            'completed' => $statusCounts->get('completed', 0),
            'sent' => $statusCounts->get('sent', 0),
            'cancelled' => $statusCounts->get('cancelled', 0),
        ];

        return Inertia::render('admin/quotation/index', [
            'quotation' => $quotation,
            'counts' => $counts,
            'filter' => $request->input('filter', []),
            'default_per_page' => $this->defaultPerPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('admin/quotation/create', [
            'countries' => function () {
                $countries = Country::all(['id', 'name', 'emoji', 'iso2', 'phonecode']);

                return [
                    'data' => $countries,
                    'default' => $countries->firstWhere('iso2', 'PH'),
                ];
            },
        ])->baseRoute('admin.quotation.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuoteRequest $request)
    {
        $quotation = DB::transaction(function () use ($request) {

            if ($request->quotable_id) {
                $quotation = Quote::create(
                    array_merge(
                        $request->only([
                            'quotable_id',
                            'quotable_type',
                        ]),
                        ['currency_id' => Currency::defaultId()]
                    )
                );
            } else {
                $quotation = Quote::create([
                    'currency_id' => Currency::defaultId(),
                ]);
            }

            $quotation->addresses()->create(
                collect($request->address)->only([
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
                        'need' => $request->address['need'],
                        'notes' => $request->address['notes'] ?? null,
                    ],
                ]
            );

            if ($request->has('shipping')) {

                $quotation->addresses()->create(
                    array_merge(
                        $request->shipping,
                        [
                            'type' => 'shipping',
                        ]
                    )
                );
            }

            $quotation->update([
                'reference' => GenerateReference::run($quotation->id, 'generator.quote.reference_format'),
            ]);

            return $quotation;

        });

        return response()->json([
            'quotation' => $quotation->fresh(['addresses', 'lines', 'currency']),
            'message' => __('crud.created', ['record' => 'Customer']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quotation)
    {
        // Load lines with product info
        $quotation->load([
            'billingAddress.country',
            'lines.purchasable',
            'currency',
            'media',
        ]);

        $quoteLines = $quotation->lines()
        ->with(['purchasable.product.media'])
        ->orderBy('created_at') // preserve creation order
        ->get()
        ->filter(fn($line) => $line->purchasable && $line->purchasable->product)
        ->groupBy(fn($line) => $line->purchasable->product->id)
        ->map(fn($lines) => $lines->values()) // convert each product group to array
        ->values() // convert top-level to numeric array
        ->toArray(); // make sure it's an array for Vue

            
        return Inertia::render('admin/quotation/main', [
            'quotation' => $quotation,
            'lines' => $quoteLines,
            'template' => 'template',
            'activities' => fn () => $quotation->activities()->latest()->with('causer')->take(100)->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quotation)
    {
        $quotation->load([
            'billingAddress.country',
            'shippingAddress',
            'lines',
        ]);

        return Inertia::modal('admin/quotation/edit/information', [
            'quotation' => $quotation,
            'countries' => function () {
                $countries = Country::all(['id', 'name', 'emoji', 'iso2', 'phonecode']);

                return [
                    'data' => $countries,
                    'default' => $countries->firstWhere('iso2', 'PH'),
                ];
            },
        ])->baseRoute('admin.quotation.item', $quotation->id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuoteRequest $request, Quote $quotation)
    {

        DB::transaction(function () use ($request, $quotation) {
            // Default address
            $address = $quotation->addresses()->where('type', 'billing')->first();

            if ($address) {
                $address->update(
                    collect($request->address)->only([
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
                            'need' => $request->address['need'],
                            'notes' => $request->address['notes'] ?? null,
                        ],
                    ]
                );

            } else {
                $quotation->addresses()->create(array_merge($request->address, [
                    'type' => 'billing',
                    'meta' => [
                        'need' => $request->address['need'],
                        'notes' => $request->address['notes'] ?? null,
                    ],
                ]));
            }

            if ($request->has('shipping')) {
                $shipping = $quotation->addresses()->where('type', 'shipping')->first();
                if ($shipping) {
                    $shipping->update($request->shipping);
                } else {
                    $quotation->addresses()->create(array_merge($request->shipping, [
                        'type' => 'shipping',
                    ]));
                }
            } else {
                $quotation->addresses()
                    ->where('type', 'shipping')
                    ->delete();
            }
        });

        return response()->json([
            'customer' => $quotation->fresh(['billingAddress.country', 'shippingAddress', 'lines']),
            'message' => 'Quotation updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quotation)
    {
        $quotation->delete();

        return to_route('admin.quotation.index')
            ->with('success', __('crud.deleted', ['record' => 'Quotation']));
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return to_route('admin.quotation.index');
        }

        // Fetch customers and delete individually to trigger model events
        $quotes = Quote::whereIn('id', $ids)->get();
        foreach ($quotes as $quotation) {
            $quotation->delete(); // triggers deleting event in Customer model
        }

        return to_route('admin.quotation.index')
            ->with('success', __('crud.deleted', ['record' => 'Quote(s)']));
    }

    /**
     * Display the add/edit products.
     */
    public function item(Quote $quotation)
    {

        $quotation->load([
            'billingAddress.country',
            'lines.purchasable',
            'currency',
            'media',
        ]);

        $quoteLines = $quotation->lines()
        ->with(['purchasable.product.media'])
        ->orderBy('created_at') // preserve creation order
        ->get()
        ->filter(fn($line) => $line->purchasable && $line->purchasable->product)
        ->groupBy(fn($line) => $line->purchasable->product->id)
        ->map(fn($lines) => $lines->values()) // convert each product group to array
        ->values() // convert top-level to numeric array
        ->toArray(); // make sure it's an array for Vue


        return Inertia::render('admin/quotation/main', [
            'quotation' => $quotation,
            'lines' => $quoteLines,
            'template' => 'item',
            'activities' => fn () => $quotation->activities()->latest()->with('causer')->take(100)->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit_date(Quote $quotation)
    {
        $quotation->load([
            'billingAddress.country',
        ]);

        return Inertia::modal('admin/quotation/edit/date', [
            'quotation' => $quotation,
        ])->baseRoute('admin.quotation.item', $quotation->id);
    }

    public function update_date(QuoteDateRequest $request, Quote $quotation)
    {

        DB::transaction(function () use ($request, $quotation) {

            $quotation->update($request->only([
                'validity_days',
                'expected_delivery',
                'quoted_at',
            ]));

            if ($quotation->status->value === 'request') {
                $quotation->update([
                    'status' => QuoteStatus::DRAFT,
                ]);
            }

        });

        return response()->json([
            'quotation' => $quotation->fresh(),
            'message' => 'Quotation dates updated successfully.',
        ]);
    }

    public function adjustment(Quote $quotation)
    {
        return Inertia::modal('admin/quotation/adj/add_adj', [
            'quotation' => $quotation,
        ])->baseRoute('admin.quotation.item', $quotation);
    }

    public function storeAdjustment(Request $request, Quote $quotation)
    {
        $adjustments = collect($request->adjustment_breakdown ?? []);

        // -----------------------------
        // Shipping Breakdown
        // -----------------------------
        $shippingLines = $adjustments
            ->where('type', 'shipping')
            ->map(function ($row) use ($quotation) {

                $price = new Price((int) $row['amount'] * 100, $quotation->currency);

                return new ShippingBreakdownItem(
                    name: $row['label'],
                    identifier: Str::slug($row['label']),
                    price: $price
                );
            })->values();

        $shippingBreakdown = new ShippingBreakdown($shippingLines);
        $shippingTotal = $shippingLines->sum(fn ($l) => $l->price->value);

        // -----------------------------
        // Tax Breakdown
        // -----------------------------
        $taxLines = $adjustments
            ->where('type', 'tax')
            ->map(function ($row) use ($quotation) {
                $baseAmount = $quotation->lines->sum(fn ($line) => $line->quantity * $line->purchase_price->value);

                $method = $row['method'] ?? 'fixed';

                $amount = $method === 'percentage'
                    ? $baseAmount * ($row['amount'] / 100)
                    : $row['amount'] * 100;

                $price = new Price((int) $amount, $quotation->currency);

                return new TaxBreakdownAmount(
                    price: $price,
                    identifier: $row['label'],
                    description: $price->formatted,
                    percentage: $method === 'percentage' ? (float) $row['amount'] : 0
                );
            })->values();

        $taxBreakdown = new TaxBreakdown($taxLines);
        $taxTotal = $taxLines->sum(fn ($l) => $l->price->value);

        // -----------------------------
        // Discount Breakdown
        // -----------------------------
        $discountLines = $adjustments
            ->where('type', 'discount')
            ->map(function ($row) use ($quotation) {
                $id = Str::slug($row['label']);
                $desc = $row['label'];

                $method = $row['method'] ?? 'fixed';

                return $method === 'percentage'
                    ? DiscountBreakdownLine::percentage(
                        $quotation->lines->sum(fn ($line) => $line->quantity * $line->purchase_price->value),
                        (float) $row['amount'],
                        $quotation->currency,
                        $id,
                        $desc
                    )
                    : DiscountBreakdownLine::fixed(
                        (int) $row['amount'],
                        $quotation->currency,
                        $id,
                        $desc
                    );
            })->values();

        $discountBreakdown = new DiscountBreakdown($discountLines);
        $discountTotal = $discountLines->sum(fn ($l) => $l->price->value);

        // -----------------------------
        // Subtotal & Total
        // -----------------------------
        $subtotal = $quotation->lines->sum(fn ($line) => $line->quantity * $line->purchase_price->value); // already in cents
        $grandTotal = $subtotal - $discountTotal + $shippingTotal + $taxTotal;

        if ($grandTotal < 0) {
            return redirect()
                ->route('admin.quotation.item', $quotation->id)
                ->withErrors('danger', 'Grand total cannot be negative.');
        } else {
            $quotation->update([
                'shipping_breakdown' => $shippingBreakdown,
                'shipping_total' => $shippingTotal,
                'tax_breakdown' => $taxBreakdown,
                'tax_total' => $taxTotal,
                'discount_breakdown' => $discountBreakdown,
                'discount_total' => $discountTotal,
                'total' => $grandTotal,
            ]);

            return redirect()
                ->route('admin.quotation.item', $quotation->id)
                ->with('success', 'Adjustments applied successfully.');
        }

    }

    public function completed(Quote $quotation)
    {
        $quotation->update([
            'status' => QuoteStatus::COMPLETED,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('admin.quotation.item', $quotation->id)
            ->with('success', 'Status mark as completed successfully.');
    }

    public function draft(Quote $quotation)
    {
        $quotation->update([
            'status' => QuoteStatus::DRAFT,
            'draft_at' => now(),
        ]);

        return redirect()
            ->route('admin.quotation.item', $quotation->id)
            ->with('success', 'Status mark as draft successfully.');
    }

    public function cancelled(Quote $quotation)
    {
        $quotation->update([
            'status' => QuoteStatus::CANCELLED,
            'cancelled_at' => now(),
        ]);

        return redirect()
            ->route('admin.quotation.item', $quotation->id)
            ->with('success', 'Status mark as cancelled successfully.');
    }

    public function createOrder(Quote $quotation)
    {
        if ($quotation->order_id) {
            return redirect()
                ->route('admin.order.show', $quotation->order_id)
                ->with('info', 'This quotation already has a Sales Order.');
        }

        $quotation->load(['billingAddress', 'shippingAddress', 'lines', 'currency']);

        $order = DB::transaction(function () use ($quotation) {

            $discountBreakdown = $this->save_discountBreakdown( $quotation );

            $order = Order::create([
                'orderable_type' => $quotation->quotable_type,
                'orderable_id' => $quotation->quotable_id,
                'currency_code' => $quotation->currency->code,
                'status' => OrderStatus::PENDING,
                'sub_total' => $quotation->sub_total?->value ?? 0,
                'discount_total' => $quotation->discount_total?->value ?? 0,
                'discount_breakdown' => $discountBreakdown,
                'shipping_breakdown' => $quotation->shipping_breakdown,
                'shipping_total' => $quotation->shipping_total?->value ?? 0,
                'tax_breakdown' => $quotation->tax_breakdown,
                'tax_total' => $quotation->tax_total?->value ?? 0,
                'total' => $quotation->total?->value ?? 0,
            ]);

            // Clone billing address
            if ($quotation->billingAddress) {
                $order->addresses()->create(
                    collect($quotation->billingAddress->toArray())
                        ->only([
                            'country_id', 'title', 'first_name', 'last_name',
                            'company_name', 'line1', 'line2', 'barangay',
                            'city', 'province', 'postcode', 'email', 'phone',
                            'delivery_instructions', 'meta',
                        ])
                        ->merge(['type' => 'billing'])
                        ->toArray()
                );
            }

            // Clone shipping address
            if ($quotation->shippingAddress) {
                $order->addresses()->create(
                    collect($quotation->shippingAddress->toArray())
                        ->only([
                            'country_id', 'title', 'first_name', 'last_name',
                            'company_name', 'line1', 'line2', 'barangay',
                            'city', 'province', 'postcode', 'email', 'phone',
                            'delivery_instructions', 'meta',
                        ])
                        ->merge(['type' => 'shipping'])
                        ->toArray()
                );
            }

           // Clone quote lines → order lines
            foreach ($quotation->lines as $line) {

                $unitPrice = $line->purchase_price?->value ?? 0;
                $subTotal  = $unitPrice * $line->quantity;

                $meta = $line->meta ?? [];

                // Always keep names
                $newMeta = [
                    'names' => $meta['names'] ?? [],
                    'size'  => $meta['size'] ?? null
                ];

                // Include dimension only if size is custom (case-insensitive)
                if (
                    isset($meta['size']) &&
                    strtolower($meta['size']) === 'custom' &&
                    !empty($meta['custom_dimension'])
                ) {
                    $newMeta['custom_dimension'] = $meta['custom_dimension'];
                }

                $order->lines()->create([
                    'purchasable_type' => $line->purchasable_type,
                    'purchasable_id'   => $line->purchasable_id,
                    'type'             => 'physical',
                    'quantity'         => $line->quantity,
                    'unit_price'       => $unitPrice,
                    'unit_quantity'    => 1,
                    'sub_total'        => $subTotal,
                    'discount_total'   => $line->discount_total?->value ?? 0,
                    'tax_total'        => $line->tax_total?->value ?? 0,
                    'total'            => $subTotal,
                    'meta'             => $newMeta,
                ]);
            }

            // Link quotation to order
            $quotation->update(['order_id' => $order->id]);

            return $order;
        });

        return redirect()
            ->route('admin.order.show', $order->id)
            ->with('success', 'Sales Order created from quotation successfully.');
    }

    function save_discountBreakdown( $quotation ){

        $discountBreakdownArr = $quotation->discount_breakdown['amounts'] ?? [];

        $updatedDiscountLines = collect($discountBreakdownArr)->map(function ($line) use ($quotation) {

            $id = $line['identifier'] ?? null;
            $desc = $line['description'] ?? null;
            $currency = $quotation->currency;
            $subtotal = $quotation->sub_total?->value ?? 0;

            if (($line['type'] ?? 'fixed') === 'percentage') {

                return DiscountBreakdownLine::percentage(
                    $subtotal,
                    (float) $line['percentage'],
                    $currency,
                    $id,
                    $desc
                );
            }

            return DiscountBreakdownLine::fixed(
                (int) data_get($line, 'price.decimal', 0),
                $currency,
                $id,
                $desc
            );
        });

        $discountBreakdown = new DiscountBreakdown($updatedDiscountLines);

        return $discountBreakdown;
    }

    public function share(Quote $quotation)
    {
        return response()->json([
            'url' => route('quotation.shared', $quotation->share_token),
        ]);
    }

    private function findByToken(string $token): Quote
    {
        return Quote::where('share_token_hash', $token)->firstOrFail();
    }

    private function getQuoteLinesGrouped(Quote $quote)
    {
        $lines = QuoteLine::with(['purchasable'])
            ->where('quote_id', $quote->id)
            ->orderBy('created_at')
            ->get();

        // Eager-load `product` only for ProductVariant purchasables.
        // Product::product() does not exist, so calling with(['purchasable.product'])
        // unconditionally throws "Call to undefined relationship [product] on model [Product]".
        $variantPurchasables = $lines
            ->where('purchasable_type', 'App\\Models\\ProductVariant')
            ->pluck('purchasable')
            ->filter();

        if ($variantPurchasables->isNotEmpty()) {
            $variantPurchasables->loadMissing('product');
        }

        return $lines
            ->filter(
                fn ($line) => $line->purchasable &&
                ($line->purchasable_type === 'App\\Models\\Product' ||
                 ($line->purchasable_type === 'App\\Models\\ProductVariant' && $line->purchasable->product))
            )
            ->groupBy(function ($line) {
                return $line->purchasable_type === 'App\\Models\\Product'
                    ? $line->purchasable->id
                    : $line->purchasable->product->id;
            });
    }

    private function getQuoteLinesWithMedia(Quote $quote)
    {
        $lines = QuoteLine::with(['purchasable'])
            ->where('quote_id', $quote->id)
            ->orderBy('created_at')
            ->get();

        // Eager-load product.media only for ProductVariant purchasables (same
        // reason as getQuoteLinesGrouped — Product has no `product` relationship).
        $variantPurchasables = $lines
            ->where('purchasable_type', 'App\\Models\\ProductVariant')
            ->pluck('purchasable')
            ->filter();

        if ($variantPurchasables->isNotEmpty()) {
            $variantPurchasables->loadMissing('product.media');
        }

        // When purchasable IS a Product, resolve it as the product directly.
        return $lines
            ->filter(function ($line) {
                if (!$line->purchasable) return false;
                if ($line->purchasable_type === 'App\\Models\\Product') return true;
                return $line->purchasable_type === 'App\\Models\\ProductVariant'
                    && $line->purchasable->product;
            })
            ->groupBy(function ($line) {
                return $line->purchasable_type === 'App\\Models\\Product'
                    ? $line->purchasable->id
                    : $line->purchasable->product->id;
            });
    }

    public function sharedView(string $token)
    {

        $quotation = $this->findByToken($token);

        $quotation->load([
            'billingAddress.country',
            'lines.purchasable.product',
            'currency',
        ]);

        //$quoteLines = $this->getQuoteLinesWithMedia($quotation);
       $quoteLines = $quotation->lines()
        ->with(['purchasable.product.media'])
        ->orderBy('created_at') // preserve creation order
        ->get()
        ->filter(fn($line) => $line->purchasable && $line->purchasable->product)
        ->groupBy(fn($line) => $line->purchasable->product->id)
        ->map(fn($lines) => $lines->values()) // convert each product group to array
        ->values() // convert top-level to numeric array
        ->toArray(); // make sure it's an array for Vue


        return Inertia::render('share', [
            'quotation' => $quotation,
            'lines' => $quoteLines,
        ]);
    }

    public function send(Quote $quotation, $action = 'preview')
    {
        // Load your data
        $quotation->load([
            'billingAddress.country',
            'currency',
        ]);

       // $quoteLines = $this->getQuoteLinesWithMedia($quotation);
        $quoteLines = QuoteLine::with(['purchasable.product.media'])
            ->where('quote_id', $quotation->id)
            ->orderBy('created_at')
            ->get()
            ->filter(fn($line) => $line->purchasable && $line->purchasable->product)
            ->groupBy(fn($line) => $line->purchasable->product->id);

        $groupedLines = $this->groupQuoteLinesByProductNameAndSize($quoteLines, $quotation->currency);

        // Generate PDF
        $pdfContent = Pdf::loadView('email.invoice', [
            'quotation' => $quotation,
            'lines' => $groupedLines,
        ])->setPaper('legal', 'portrait')->output();
        
        //$email = 'bjohnalou@gmail.com';
       $email = $quotation->billingAddress->email;

        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($email)->send(new QuotationMail($quotation, $pdfContent, $groupedLines));

            $quotation->update([
                'status' => QuoteStatus::SENT,
                'sent_at' => now(),
            ]);

            return redirect()
                ->route('admin.quotation.item', $quotation->id)
                ->with('success', 'Email sent successfully.');
        }

        return redirect()
            ->route('admin.quotation.item', $quotation->id)
            ->with('danger', 'No valid email found.');

        // // Get PDF content as string
        // $pdfContent = $pdf->output();

        // // Save to storage/app/public/quotations/
        // $fileName = 'Quotation-' . $quotation->reference . '.pdf';
        // $filePath = 'public/quotations/' . $fileName;

        // Storage::put($filePath, $pdfContent);

        // return response()->json([
        //     'message' => 'PDF saved successfully',
        //     'path' => Storage::url($filePath) // gives public URL if you linked storage
        // ]);
    }

    public function groupQuoteLinesByProductNameAndSize($lines, $currency = null)
    {
        $grouped = [];

        // Flatten + enforce WITH NAME first then NO NAME
        $lines = $lines->flatten()
            ->sortBy(function ($line) {
                $description = $line->purchasable->description ?? '';

                $isWithName = str_starts_with($description, 'WITH-name')
                    || str_starts_with($description, 'WITH name');

                return $isWithName ? 0 : 1;
            })
            ->values();

        foreach ($lines as $line) {

            if (! $line->purchasable || ! $line->purchasable->product) {
                continue;
            }

            $product     = $line->purchasable->product;
            $productId   = $product->id;
            $description = $line->purchasable->description ?? '';

            $isWithName = str_starts_with($description, 'WITH-name')
                || str_starts_with($description, 'WITH name');

            $nameGroup = $isWithName ? 'withName' : 'noName';

            $parts   = explode('/', $description, 2);
            $rawSize = isset($parts[1]) ? trim($parts[1]) : 'noSize';

            $customDimension = $line->meta['custom_dimension'] ?? null;

            if (strcasecmp($rawSize, 'custom') === 0) {
                $size = $customDimension
                    ? 'Custom : ' . $customDimension
                    : 'Custom';
            } else {
                $size = $rawSize;
            }

            $priceSize = strcasecmp($rawSize, 'custom') === 0
                ? ($customDimension ?? 'Custom')
                : $rawSize;

            if (!isset($grouped[$productId])) {

                $media = $product->getFirstMedia('image');

                $grouped[$productId] = [
                    'product' => [
                        'details' => $product,
                        'image_url' => $media?->getPath('preview'),
                        'image_url_email' => $product->image->original_url ?? null,
                    ],
                    'oum' => [],
                    'prices' => [],
                    'check_prices' => [],
                    'price_total' => 0,
                    'price_total_format' => '',
                    'breakdown' => false,
                    'totalQuantity' => 0,
                ];
            }

            $uom = $line->purchasable->uom_code;
            $grouped[$productId]['oum'][$uom] = true;

            $grouped[$productId][$nameGroup] ??= [];
            $grouped[$productId][$nameGroup][$size] ??= [
                'totalQuantity' => 0,
                'namesWithNumbers' => [],
            ];


            $grouped[$productId][$nameGroup][$size]['totalQuantity'] += $line->quantity;
            $grouped[$productId]['totalQuantity'] += $line->quantity;


            if (!empty($line->meta['names'])) {
                $names = array_slice($line->meta['names'], 0, $line->quantity);
                $grouped[$productId][$nameGroup][$size]['namesWithNumbers'][] = $names;
            }

            $priceFormatted = $line->purchase_price->formatted ?? null;
            $priceDecimal   = $line->purchase_price->decimal ?? 0;

            if ($priceFormatted !== null) {

                $grouped[$productId]['prices'][] = [
                    'type'  => $nameGroup,
                    'size'  => $priceSize, 
                    'price' => $priceFormatted,
                    'created_at' => $line->created_at,
                ];

                $grouped[$productId]['price_total'] += $priceDecimal * $line->quantity;
                $grouped[$productId]['check_prices'][$priceFormatted] = true;
            }
        }


        foreach ($grouped as &$productGroup) {

            $productGroup['oum'] = array_keys($productGroup['oum']);

            if (count($productGroup['check_prices']) > 1) {
                $productGroup['breakdown'] = true;
            }

            if ($productGroup['price_total'] > 0) {
                $priceTotal = new Price(
                    (int) ($productGroup['price_total'] * 100),
                    $currency
                );
                $productGroup['price_total_format'] = $priceTotal->formatted;
            }

            usort($productGroup['prices'], function ($a, $b) {

                if ($a['type'] !== $b['type']) {
                    return $a['type'] === 'withName' ? -1 : 1;
                }

                return $a['created_at'] <=> $b['created_at'];
            });

            $sorted = [];

            if (!empty($productGroup['withName'])) {
                $sorted['withName'] = $productGroup['withName'];
            }

            if (!empty($productGroup['noName'])) {
                $sorted['noName'] = $productGroup['noName'];
            }

            $productGroup['grouped'] = $sorted;
        }

        return $grouped;
    }
    
    public function test(Quote $quotation)
    {

        $quotation->load([
            'billingAddress.country',
            'lines.purchasable.product',
            'currency',
        ]);

        $quoteLines = $this->getQuoteLinesWithMedia($quotation);

        $groupedLines = $this->groupQuoteLinesByProductNameAndSize($quoteLines, $quotation->currency);

        return view('email.template', [
            'quotation' => $quotation,
            'lines' => $groupedLines,
        ]);
    }

    public function fetchByStatus($status)
    {

        $statuses = explode(',', $status);
        $quotes = Quote::with([
            'billingAddress.country',
        ])
            ->whereIn('status', $statuses)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($quotes);

    }

    public function pdf(Quote $quotation)
    {
        $quotation = Quote::with([
            'billingAddress.country',
            'lines.purchasable.product',
            'currency',
        ])->findOrFail($quotation->id);

        $quoteLines = $this->getQuoteLinesWithMedia($quotation);

        $lines = $this->groupQuoteLinesByProductNameAndSize($quoteLines, $quotation->currency);

        return Pdf::loadView('pdf.quotation', compact('quotation', 'lines'))
            ->setPaper('legal', 'portrait')
            ->stream("quotation-{$quotation->reference}.pdf");
    }

    public function download(Quote $quotation)
    {

        $quotation = Quote::with([
            'billingAddress.country',
            'lines.purchasable.product',
            'currency',
        ])->findOrFail($quotation->id);

        $quoteLines = QuoteLine::with([
           'purchasable.product.media'
        ])
        ->where('quote_id', $quotation->id)
        ->orderBy('created_at') 
        ->get()
        ->filter(fn ($line) =>
            $line->purchasable &&
            $line->purchasable->product
        )
        ->groupBy(fn ($line) =>
            $line->purchasable->product->id
        );

        //$quoteLines = $this->getQuoteLinesWithMedia($quotation);

        $lines = $this->groupQuoteLinesByProductNameAndSize($quoteLines, $quotation->currency);

        return Pdf::loadView('pdf.quotation', compact('quotation', 'lines'))
            ->setPaper('legal', 'portrait')
            ->download("quotation-{$quotation->reference}.pdf");
    }

    public function addNote(Request $request, Quote $quotation)
    {
        $request->validate([
            'note' => ['required', 'string', 'max:5000'],
        ]);

        $quotation->logNote($request->input('note'));

        return response()->json([
            'message' => __('Note added successfully.'),
        ], 201);
    }

    public function upload(Request $request, Quote $quotation)
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

        $uploaded = array_map(function ($file) use ($quotation) {
            $originalName = $file->getClientOriginalName();
            $cleanName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();

            $finalFileName = "{$quotation->reference}_{$cleanName}.{$extension}";

            $media = $quotation
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

    public function destroyMedia(Quote $quotation, $mediaId)
    {
        $media = $quotation->media()->findOrFail($mediaId);
        $media->delete();

        return response()->noContent();
    }
}
