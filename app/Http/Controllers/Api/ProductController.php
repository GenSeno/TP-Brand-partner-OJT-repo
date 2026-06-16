<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BrandPartnerProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorizeApiRequest($request);

        $products = BrandPartnerProduct::query()
            ->with(['category', 'event', 'images', 'brandPartner'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('approval_status'), fn ($q) => $q->where('approval_status', $request->approval_status))
            ->when($request->filled('brand_partner_id'), fn ($q) => $q->where('brand_partner_id', $request->brand_partner_id))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate($request->input('per_page', 15))
            ->withQueryString();

        return response()->json($products);
    }

    public function show(Request $request, BrandPartnerProduct $product): JsonResponse
    {
        $this->authorizeApiRequest($request);

        $product->load(['category', 'event', 'images', 'brandPartner']);

        return response()->json(['data' => $product]);
    }

    protected function authorizeApiRequest(Request $request): void
    {
        if ($request->header('X-API-Key') !== config('services.tpinklab.api_key')) {
            abort(response()->json(['message' => 'Unauthorized.'], 401));
        }
    }
}
