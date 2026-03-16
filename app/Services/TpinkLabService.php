<?php

namespace App\Services;

use App\Models\BrandPartnerOrder;
use App\Models\BrandPartnerProduct;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TpinkLabService
{
    /**
     * Submit a newly created product to TPInkAdmin for approval.
     */
    public function submitProductForApproval(BrandPartnerProduct $product): void
    {
        $url = config('services.tpinklab.admin_api_url');
        // Derive product submission URL from admin_api_url base
        $baseUrl = rtrim(preg_replace('#/api/.*#', '', $url), '/');
        $endpoint = $baseUrl . '/api/brand-partner-products';

        try {
            $product->loadMissing(['images', 'brandPartner']);

            $bp = $product->brandPartner;

            $payload = [
                'brand_partner_id'    => $product->brand_partner_id,
                'brand_partner_slug'  => $bp?->slug,
                'brand_partner_name'  => $bp?->name,
                'brand_partner_email' => $bp?->email,
                'external_id'         => $product->id,
                'name'               => $product->name,
                'slug'               => $product->slug,
                'sku'                => $product->sku,
                'price'              => $product->price,
                'compare_price'      => $product->compare_price,
                'short_description'  => $product->short_description,
                'description'        => $product->description,
                'status'             => $product->status instanceof \BackedEnum ? $product->status->value : $product->status,
                'product_image'      => $product->images->first()?->url ?? null,
                'callback_url'       => config('services.tpinklab.brandpartner_callback_url'),
            ];

            $response = Http::withHeaders([
                'X-API-Key' => config('services.tpinklab.api_key'),
            ])->post($endpoint, $payload);

            if ($response->failed()) {
                Log::error('TpinkLab Admin API: product submission failed', [
                    'product_id'  => $product->id,
                    'http_status' => $response->status(),
                    'body'        => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('TpinkLab Admin API: product submission exception', [
                'product_id' => $product->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    public function sendOrder(Order|BrandPartnerOrder $order): void
    {
        $isBrandPartner = $order instanceof BrandPartnerOrder;
        $integrationType = $isBrandPartner ? 'brand_partner' : 'standard';

        try {
            $payload = $isBrandPartner
                ? $this->buildBrandPartnerOrderPayload($order)
                : $this->buildStandardOrderPayload($order);

            $response = Http::withHeaders([
                'X-API-Key' => config('services.tpinklab.api_key'),
                'X-Integration-Type' => $integrationType,
            ])->post(config('services.tpinklab.api_url'), $payload);

            if ($response->failed()) {
                Log::error('TpinkLab API returned an error', [
                    'order_id' => $order->id,
                    'type' => $integrationType,
                    'http_status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('TpinkLab API request failed', [
                'order_id' => $order->id,
                'type' => $integrationType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendOrderToAdmin(BrandPartnerOrder $order): void
    {
        try {
            $payload = $this->buildAdminOrderPayload($order);

            $response = Http::withHeaders([
                'X-API-Key' => config('services.tpinklab.api_key'),
            ])->post(config('services.tpinklab.admin_api_url'), $payload);

            if ($response->failed()) {
                Log::error('TpinkLab Admin API returned an error', [
                    'order_id' => $order->id,
                    'http_status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('TpinkLab Admin API request failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function buildAdminOrderPayload(BrandPartnerOrder $order): array
    {
        $order->loadMissing(['lines.product.images', 'brandPartner']);

        $nameParts = explode(' ', trim($order->customer_name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? $firstName;

        $shippingAddress = $order->meta ? ($order->meta['shipping_address'] ?? null) : null;
        $addr = $shippingAddress instanceof \ArrayObject
            ? $shippingAddress
            : (is_array($shippingAddress) ? $shippingAddress : []);

        $payload = [
            'notes' => $order->notes,
            'status' => 'pending',
            'address' => [
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $order->customer_email,
                'phone'      => $order->customer_phone ?? null,
                'line1'      => $addr['line1'] ?? null,
                'line2'      => $addr['line2'] ?? null,
                'barangay'   => $addr['barangay'] ?? null,
                'city'       => $addr['city'] ?? null,
                'province'   => $addr['province'] ?? null,
                'postcode'   => $addr['postcode'] ?? null,
                'country_id' => $addr['country_id'] ?? 175,
            ],
            'items' => $order->lines->map(fn($line) => [
                'name'              => $line->product_name,
                'quantity'          => $line->quantity,
                'price'             => round($line->unit_price / 100, 2),
                'sku'               => $line->product?->sku ?? null,
                'short_description' => $line->product?->short_description ?? null,
                'product_image'     => $line->product?->images->first()?->url ?? null,
            ])->values()->all(),
        ];

        return $payload;
    }

    protected function buildBrandPartnerOrderPayload(BrandPartnerOrder $order): array
    {
        $order->loadMissing(['brandPartner', 'lines.product']);

        $nameParts = explode(' ', $order->customer_name, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        return [
            'brand_partner_slug' => $order->brandPartner->slug,
            'order_type' => 'brand_partner',
            'order' => [
                'reference' => $order->reference,
                'status' => $order->status->value,
                'placed_at' => $order->placed_at?->toIso8601String(),
                'notes' => $order->notes,
                'customer' => [
                    'type' => $order->customer_id ? 'registered' : 'guest',
                    'id' => (string) ($order->customer_id ?? ''),
                    'email' => $order->customer_email,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $order->customer_phone,
                ],
                'currency' => [
                    'code' => 'PHP',
                ],
                'totals' => [
                    'sub_total' => $order->sub_total,
                    'discount_total' => 0,
                    'shipping_total' => 0,
                    'tax_total' => $order->tax_total,
                    'grand_total' => $order->total,
                ],
                'line_items' => $order->lines->map(fn($line) => [
                    'type' => 'physical',
                    'purchasable' => [
                        'type' => 'brand_partner_product',
                        'id' => (string) $line->product_id,
                        'name' => $line->product_name,
                    ],
                    'description' => $line->product_name,
                    'identifier' => (string) $line->product_id,
                    'pricing' => [
                        'unit_price' => $line->unit_price,
                        'unit_quantity' => 1,
                        'quantity' => $line->quantity,
                        'sub_total' => $line->unit_price * $line->quantity,
                        'discount_total' => 0,
                        'tax_total' => 0,
                        'total' => $line->total,
                    ],
                    'meta' => $line->meta ? $line->meta->toArray() : null,
                ])->values()->all(),
                'meta' => $order->meta ? $order->meta->toArray() : null,
            ],
        ];
    }

    protected function buildStandardOrderPayload(Order $order): array
    {
        $order->loadMissing(['lines.purchasable', 'shippingAddress', 'billingAddress', 'orderable']);

        $orderable = $order->orderable;
        $isRegistered = $orderable instanceof Customer;

        $shippingAddr = $order->shippingAddress;
        $billingAddr = $order->billingAddress;

        return [
            'brand_partner_slug' => config('app.brand_partner_slug'),
            'order_type' => 'standard',
            'order' => [
                'reference' => $order->reference,
                'status' => $order->status->value,
                'placed_at' => $order->placed_at?->toIso8601String(),
                'expected_delivery' => $order->expected_delivery?->toIso8601String(),
                'notes' => $order->notes,
                'customer' => [
                    'type' => $isRegistered ? 'registered' : 'guest',
                    'id' => $isRegistered ? (string) $orderable->getKey() : null,
                    'email' => $shippingAddr?->email ?? $billingAddr?->email,
                    'first_name' => $isRegistered ? $orderable->first_name : $shippingAddr?->first_name,
                    'last_name' => $isRegistered ? $orderable->last_name : $shippingAddr?->last_name,
                    'phone' => $shippingAddr?->phone ?? $billingAddr?->phone,
                    'is_new' => $order->new_customer,
                ],
                'currency' => [
                    'code' => $order->currency_code,
                    'compare_code' => $order->compare_currency_code,
                    'exchange_rate' => (float) $order->exchange_rate,
                ],
                'totals' => [
                    'sub_total' => $order->sub_total->value,
                    'discount_total' => $order->discount_total->value,
                    'shipping_total' => $order->shipping_total->value,
                    'tax_total' => $order->tax_total->value,
                    'grand_total' => $order->total->value,
                ],
                'breakdowns' => [
                    'discount' => $order->discount_breakdown?->toArray(),
                    'shipping' => $order->shipping_breakdown?->items->map(fn($item) => [
                        'method' => $item->name,
                        'amount' => $item->price->value,
                        'option' => $item->identifier,
                    ])->first(),
                    'tax' => collect($order->tax_breakdown?->amounts ?? [])->map(fn($amount) => [
                        'name' => $amount->description,
                        'rate' => $amount->percentage * 100,
                        'amount' => $amount->price->value,
                    ])->values()->all(),
                ],
                'addresses' => [
                    'shipping' => $shippingAddr ? $this->formatAddress($shippingAddr) : null,
                    'billing' => $billingAddr ? $this->formatAddress($billingAddr) : null,
                ],
                'line_items' => $order->lines->map(fn($line) => [
                    'type' => $line->type->value,
                    'purchasable' => [
                        'type' => $line->purchasable_type ? class_basename($line->purchasable_type) : null,
                        'id' => (string) $line->purchasable_id,
                        'sku' => $line->identifier,
                        'name' => $line->description,
                        'option' => $line->option,
                    ],
                    'description' => $line->description,
                    'identifier' => $line->identifier,
                    'pricing' => [
                        'unit_price' => $line->unit_price->value,
                        'unit_quantity' => $line->unit_quantity,
                        'quantity' => $line->quantity,
                        'sub_total' => $line->sub_total->value,
                        'discount_total' => $line->discount_total->value,
                        'tax_total' => $line->tax_total->value,
                        'total' => $line->total->value,
                    ],
                    'tax_breakdown' => collect($line->tax_breakdown?->amounts ?? [])->map(fn($amount) => [
                        'name' => $amount->description,
                        'rate' => $amount->percentage * 100,
                        'amount' => $amount->price->value,
                    ])->values()->all(),
                    'notes' => $line->notes,
                    'meta' => $line->meta ? $line->meta->toArray() : null,
                ])->values()->all(),
                'meta' => $order->meta ? $order->meta->toArray() : null,
            ],
        ];
    }

    protected function formatAddress($address): array
    {
        return [
            'first_name' => $address->first_name,
            'last_name' => $address->last_name,
            'company_name' => $address->company_name,
            'line1' => $address->line1,
            'line2' => $address->line2,
            'barangay' => $address->barangay,
            'city' => $address->city,
            'province' => $address->province,
            'postcode' => $address->postcode,
            'country' => $address->country?->iso2 ?? null,
            'email' => $address->email,
            'phone' => $address->phone,
            'delivery_instructions' => $address->delivery_instructions,
            'shipping_option' => $address->shipping_option ?? null,
        ];
    }
}
