<?php

namespace App\Http\Controllers\BrandPartner;

use App\Http\Controllers\Controller;
use App\Models\BrandPartnerProduct;
use App\Models\BrandPartnerProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Get the authenticated brand partner.
     */
    protected function brandPartner()
    {
        return Auth::guard('brand_partner')->user();
    }

    /**
     * Store a new product image.
     */
    public function store(Request $request, BrandPartnerProduct $product)
    {
        $this->authorize($product);

        $request->validate([
            'image' => ['required', 'image', 'max:5120'], // 5MB max
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $path = $request->file('image')->store(
            "brand-partners/{$product->brand_partner_id}/products/{$product->id}",
            'public'
        );

        $isPrimary = !$product->images()->exists();

        $image = $product->images()->create([
            'path' => $path,
            'alt_text' => $request->alt_text,
            'position' => $product->images()->max('position') + 1,
            'is_primary' => $isPrimary,
        ]);

        return response()->json([
            'image' => $image,
            'message' => __('Image uploaded successfully.'),
        ], 201);
    }

    /**
     * Remove a product image.
     */
    public function destroy(BrandPartnerProduct $product, BrandPartnerProductImage $image)
    {
        $this->authorize($product);

        if ($image->product_id !== $product->id) {
            abort(403);
        }

        // Delete the file from storage
        Storage::disk('public')->delete($image->path);

        // If this was the primary image, make the next one primary
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $nextImage = $product->images()->orderBy('position')->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'message' => __('Image deleted successfully.'),
        ]);
    }

    /**
     * Make an image the primary image.
     */
    public function makePrimary(BrandPartnerProduct $product, BrandPartnerProductImage $image)
    {
        $this->authorize($product);

        if ($image->product_id !== $product->id) {
            abort(403);
        }

        $image->makePrimary();

        return response()->json([
            'message' => __('Primary image updated successfully.'),
        ]);
    }

    /**
     * Authorize that the product belongs to the current brand partner.
     */
    protected function authorize(BrandPartnerProduct $product)
    {
        if ($product->brand_partner_id !== $this->brandPartner()->id) {
            abort(403);
        }
    }
}
