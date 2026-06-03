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

        $query = BrandPartnerProduct::query()
            ->with(['category', 'collection', 'event', 'images']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->query('approval_status'));
        }

        if ($request->filled('brand_partner_id')) {
            $query->where('brand_partner_id', $request->query('brand_partner_id'));
        }

        if ($request->filled('search')) {
            $query->search($request->query('search'));
        }

        if ($request->boolean('published_only')) {
            $query->published();
        }

        $perPage = min(max((int) $request->query('per_page', 50), 1), 100);

        return response()->json($query->paginate($perPage));
    }

    public function show(Request $request, BrandPartnerProduct $product): JsonResponse
    {
        $this->authorizeApiRequest($request);

        $product->load(['category', 'collection', 'event', 'images']);

        return response()->json($product);
    }

    protected function authorizeApiRequest(Request $request): void
    {
        if ($request->header('X-API-Key') !== config('services.tpinklab.api_key')) {
            abort(response()->json(['message' => 'Unauthorized.'], 401));
        }
    }
}
