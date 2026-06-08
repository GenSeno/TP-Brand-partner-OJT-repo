<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BrandPartnerOrderLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorizeApiRequest($request);

        $lines = BrandPartnerOrderLine::query()
            ->where('meta->pre_order', true)
            ->with(['order.brandPartner', 'product'])
            ->get();

        $grouped = $lines->groupBy('product_id')->map(function ($productLines) {
            $first = $productLines->first();
            $product = $first->product;

            $variants = $productLines->groupBy(fn ($line) => json_encode([
                'color' => $line->meta['color'] ?? null,
                'size' => $line->meta['size'] ?? null,
            ]))->map(function ($variantLines, $key) {
                $meta = json_decode($key, true);

                return [
                    'color' => $meta['color'],
                    'size' => $meta['size'],
                    'quantity' => $variantLines->sum('quantity'),
                ];
            })->values();

            return [
                'product_id' => $first->product_id,
                'product_name' => $first->product_name,
                'product_sku' => $product?->sku,
                'brand_partner_id' => $first->order->brand_partner_id,
                'total_quantity' => $productLines->sum('quantity'),
                'variants' => $variants,
                'orders' => $productLines->groupBy('order_id')->map(function ($orderLines) {
                    $order = $orderLines->first()->order;

                    return [
                        'order_id' => $order->id,
                        'reference' => $order->reference,
                        'customer_name' => $order->customer_name,
                        'customer_email' => $order->customer_email,
                        'customer_phone' => $order->customer_phone,
                        'shipping_address' => collect([
                            $order->address_line1,
                            $order->address_line2,
                            $order->barangay,
                            $order->city,
                            $order->province,
                            $order->postcode,
                        ])->filter()->implode(', '),
                        'quantity' => $orderLines->sum('quantity'),
                        'status' => $order->status->value,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'data' => $grouped,
            'meta' => [
                'total_pre_order_quantity' => $lines->sum('quantity'),
                'total_products' => $grouped->count(),
                'total_orders' => $lines->pluck('order_id')->unique()->count(),
            ],
        ]);
    }

    protected function authorizeApiRequest(Request $request): void
    {
        if ($request->header('X-API-Key') !== config('services.tpinklab.api_key')) {
            abort(response()->json(['message' => 'Unauthorized.'], 401));
        }
    }
}
