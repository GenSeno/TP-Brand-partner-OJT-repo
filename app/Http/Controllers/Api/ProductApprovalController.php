<?php

namespace App\Http\Controllers\Api;

use App\Enums\BrandPartnerProductApprovalStatus;
use App\Enums\BrandPartnerProductStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartnerProduct;
use Illuminate\Http\Request;

class ProductApprovalController extends Controller
{
    /** 
     * Receive an approval/rejection decision from TPInkAdmin.
     *
     * Called by TPInkAdmin after an admin approves or rejects a product.
     * Authenticated via X-API-Key header matched against TPINKLAB_API_KEY.
     */
    public function update(Request $request, BrandPartnerProduct $product)
    {
        // Verify the request comes from TPInkAdmin
        $apiKey = $request->header('X-API-Key');
        if (! $apiKey || $apiKey !== config('services.tpinklab.api_key')) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'approval_status' => ['required', 'in:approved,rejected'],
            'approval_notes'  => ['nullable', 'string', 'max:1000'],
        ]);

        $updates = [
            'approval_status' => $request->approval_status,
            'approval_notes'  => $request->approval_notes,
            'approved_at'     => $request->approval_status === 'approved' ? now() : null,
        ];

        // Revert published → draft if product is being rejected
        if (
            $request->approval_status === 'rejected' &&
            $product->status === BrandPartnerProductStatus::PUBLISHED
        ) {
            $updates['status'] = BrandPartnerProductStatus::DRAFT->value;
        }

        $product->update($updates);

        return response()->json([
            'message' => "Product approval status updated to {$request->approval_status}.",
        ]);
    }
}
